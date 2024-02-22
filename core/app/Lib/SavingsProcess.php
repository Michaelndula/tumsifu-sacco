<?php

namespace App\Lib;

use App\Models\Savings;
use App\Rules\FileTypeValidate;

class SavingsProcess
{
    public $plan;
    public $user;
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
        $savings = new Savings();
        $request = request();

        $directory = date("Y")."/".date("m")."/".date("d");
        $path = imagePath()['verify']['savings']['path'].'/'.$directory;
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

            $savings->user_information = $reqField;
        } else {
            $savings->user_information = null;
        }

        $savings->user_id              = $user->id;
        $savings->staff_id             = $staffId ?? 0;
        $savings->savings_plan_id      = $plan->id;
        $savings->savings_amount       = $plan->savings_amount;
        $savings->installment          = $plan->installment;
        $savings->giveable_amount      = $plan->giveable_amount;
        $savings->installment_interval = $plan->installment_interval;
        $savings->total_installment    = $plan->total_installment;
        $savings->late_fee             = $plan->fixed_late_fee + ($plan->installment * $plan->percent_late_fee / 100);
        $savings->status               = 0;
        $savings->save();

        $notify[] = ['success', 'Successfully applied for savings'];
        return $notify;
    }

    public function applyValidation()
    {
        $plan = $this->plan;
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

    public function installment($savings)
    {
        $request = request();
        $user = $this->user;
        if($savings->status == 0){
            $notify[] = ['error', 'This savings has not been approved yet'];
            return [
                'error'=>true,
                'notify'=>$notify
            ];
        }elseif($savings->status == 2){
            $notify[] = ['error', 'All installments have been paid for this savings'];
            return [
                'error'=>true,
                'notify'=>$notify
            ];
        }elseif($savings->status == 3){
            $notify[] = ['error', 'This savings already closed'];
            return [
                'error'=>true,
                'notify'=>$notify
            ];
        }

        $this->installmentCalculation($savings);

        return [
            'error'=>false,
            'savings'=>$savings
        ];
    }

    public function updateSavings($savings)
    {
        $this->installmentCalculation($savings);

        $savings->total_paid += $this->installment;
        $savings->installment_given += 1;
        $savings->total_late_fee_paid += $this->lateFee;
        $savings->last_installment = now();
        $savings->next_installment = $savings->next_installment->addDays($savings->installment_interval);

        if($savings->total_installment == $savings->installment_given){
            $savings->status = 2;
        }
        $savings->save();
    }

    public function installmentCalculation($savings)
    {
        $lateFee = $savings->next_installment < now()->format('Y-m-d') ? $savings->late_fee : 0;
        $amountWithLateFee = $savings->installment + $lateFee;
        $this->installment = $savings->installment;
        $this->lateFee = $lateFee;
        $this->amountWithLateFee = $amountWithLateFee;
    }
}
