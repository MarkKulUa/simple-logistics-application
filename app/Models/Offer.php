<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'user_id', 'client_id', 'building_id', 'offer_state_id', 'rejected_state_id',
        'end_of_cooperation_tag_id', 'price', 'currency_iso_4217', 'date_issued',
        'date_signed', 'date_confirmed', 'date_active_from', 'date_active_to', 'notes',
        'header_client_name', 'header_client_street', 'header_client_postal_code',
        'header_client_city'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

}
