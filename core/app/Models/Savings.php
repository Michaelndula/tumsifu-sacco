<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Savings extends Model
{
    protected $casts = [
        'user_information' => 'object',
        'last_installment' => 'date',
        'next_installment' => 'date'
    ];

    public function savingsPlan()
    {
        return $this->belongsTo(SavingsPlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function paidLogs()
    {
        return $this->hasMany(PaidLog::class);
    }

    //Scope
    public function scopePending()
    {
        return $this->where('status', 0);
    }

    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopePaid()
    {
        return $this->where('status', 2);
    }

    public function scopeClosed()
    {
        return $this->where('status', 3);
    }

    public function scopePendingMatured(){
        return $this->where('status', 2)->where('transfer_user', 0);
    }

    public function scopePaidMatured(){
        return $this->where('status', 2)->where('transfer_user', 1);
    }
}
