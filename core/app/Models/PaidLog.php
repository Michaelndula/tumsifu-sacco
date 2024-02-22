<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaidLog extends Model
{
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }    
    public function savings()
    {
        return $this->belongsTo(Savings::class);
    }    
    public function user()
    {
        return $this->belongsTo(User::class);
    }    
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }    
}
