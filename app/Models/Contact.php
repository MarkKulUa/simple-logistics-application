<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'company_name', 'email', 'phone', 'notes', 'crm_contact_id'
    ];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getGoogleSyncData(): array
    {
        $givenName = $this->first_name;
        $familyName = $this->last_name;
        $email = trim($this->email);

        return [
            'givenName'  => "{$familyName}",
            'familyName' => "{$givenName}",
            'email'      => "{$email}",
        ];
    }

    public function getName(): array
    {
        return $this->company_name ? $this->company_name : "{$this->first_name} {$this->last_name}";
    }
}
