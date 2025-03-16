<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EndOfCooperationTagTranslation extends Model
{
    protected $fillable = [
        'end_of_cooperation_tag_id', 'language_ietf', 'is_default', 'tag_name', 'tag_description'
    ];

    public function tag() {
        return $this->belongsTo(EndOfCooperationTag::class);
    }

}
