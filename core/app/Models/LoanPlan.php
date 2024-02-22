<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPlan extends Model
{
    protected $casts = [
        'user_data' => 'object',
    ];
}
