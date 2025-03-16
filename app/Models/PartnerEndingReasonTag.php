<?php

namespace App\Models;

class PartnerEndingReasonTag extends Tag
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::addGlobalScope(function ($query) {
            $query->where('type', 'partnerEndingReason');
        });
    }
}
