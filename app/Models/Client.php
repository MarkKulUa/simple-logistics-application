<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'user_id', 'representative_client_id', 'client_lead_id', 'name', 'company_number',
        'billing_address_city', 'billing_address_street', 'billing_address_postal_code',
        'account_prefix', 'account_number', 'account_bank_code', 'account_iban', 'account_swift',
        'billing_address_house_number', 'tax_identification_number', 'balance', 'units_count',
        'date_lead_conversion', 'notes', 'crm_client_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function representative() {
        return $this->belongsTo(Client::class, 'representative_client_id');
    }

}
