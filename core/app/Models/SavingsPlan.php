<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsPlan extends Model
{
    protected $casts = [
        'user_data' => 'object'
    ];
}
