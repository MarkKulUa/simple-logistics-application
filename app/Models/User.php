<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'employee_id', 'username', 'email', 'email_verified_at', 'password',
        'password_last_changed', 'is_admin', 'is_activated', 'date_deactivated', 'remember_token'
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }
}
