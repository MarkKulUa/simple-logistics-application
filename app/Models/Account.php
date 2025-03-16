<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name', 'account_prefix', 'account_number', 'account_bank_code', 'account_iban',
        'account_swift', 'access_token', 'access_token_valid_until', 'control_token',
        'control_token_valid_until', 'account_abbr'
    ];

}
