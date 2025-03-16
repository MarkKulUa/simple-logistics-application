<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRruleTemplate extends Model
{
    protected $fillable = ['rrule', 'parity', 'service_ratio'];
}
