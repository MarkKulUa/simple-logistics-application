<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email_work', 'email_personal', 'phone_work',
        'phone_personal', 'company_number', 'date_deactivated'
    ];

    public function user() {
        return $this->hasOne(User::class);
    }

}
