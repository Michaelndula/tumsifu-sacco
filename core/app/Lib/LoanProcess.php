<?php

namespace App\Lib;

use App\Models\Loan;
use App\Rules\FileTypeValidate;

class LoanProcess
{
    public $plan;
    public $user;
    public $staffId;
    public $installment;
    public $lateFee;
    public $amountWithLateFee;

    public function __construct($data)
    {
        $this->plan = @$data['plan'];
        $this->user = @$data['user'];
        $this->staffId = @$data['staff_id'];
    }

    public function apply()
    {
        $plan = $this->plan;
        $user = $this->user;
        $staffId = $this->staffId;
        $loan = new Loan();
        $request = request();

        $directory = date("Y")."/".date("m")."/".date("d");
        $path = imagePath()['verify']['loan']['path'].'/'.$directory;
        $collection = collect($request);

        $reqField = [];
        if ($plan->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($plan->user_data as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory.'/'.uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $request[$inKey]];
                                    return $notify;
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }

            $loan->user_information = $reqField;
        } else {
            $loan->user_information = null;
        }

        $loan->user_id              = $user->id;
        $loan->staff_id             = $staffId ?? 0;
        $loan->loan_plan_id         = $plan->id;
        $loan->loan_amount          = $plan->loan_amount;
        $loan->payable_amount       = $plan->payable_amount;
        $loan->installment          = $plan->installment;
        $loan->installment_interval = $plan->installment_interval;
        $loan->total_installment    = $plan->total_installment;
        $loan->late_fee             = $plan->fixed_late_fee + ($plan->installment * $plan->percent_late_fee / 100);
        $loan->status               = 0;
        $loan->save();

        $notify[] = ['success', 'Successfully applied for loan'];
        return $notify;
    }

    public function applyValidation()
    {
        $plan = $this->plan;
        $request = request();
        $rules = [];
        $inputField = [];
        if ($plan->user_data != null) {
            foreach ($plan->user_data as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg','jpeg','png']));
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        return $rules;
    }

    public function installment($loan)
    {
        $request = request();
        if($loan->status == 0){
            $notify[] = ['error', 'This loan has not been approved yet'];
            return [
                'error'=>true,
                'notify'=>$notify
            ];
        }elseif($loan->status == 2){
            $notify[] = ['error', 'All installments have been paid for this loan'];
            return [
                'error'=>true,
                'notify'=>$notify
            ];
        }elseif($loan->status == 3){
            $notify[] = ['error', 'This loan already closed'];
            return [
                'error'=>true,
                'notify'=>$notify
            ];
        }

        $this->installmentCalculation($loan);

        return [
            'error'=>false,
            'loan'=>$loan
        ];
    }

    public function updateLoan($loan)
    {
        $this->installmentCalculation($loan);

        $loan->total_paid += $this->installment;
        $loan->installment_given += 1;
        $loan->total_late_fee_paid += $this->lateFee;
        $loan->last_installment = now();
        $loan->next_installment = $loan->next_installment->addDays($loan->installment_interval);

        if($loan->total_installment == $loan->installment_given){
            $loan->status = 2;
        }
        $loan->save();
    }

    public function installmentCalculation($loan)
    {
        $lateFee = $loan->next_installment < now()->format('Y-m-d') ? $loan->late_fee : 0;
        $amountWithLateFee = $loan->installment + $lateFee;
        $this->installment = $loan->installment;
        $this->lateFee = $lateFee;
        $this->amountWithLateFee = $amountWithLateFee;
    }
}
