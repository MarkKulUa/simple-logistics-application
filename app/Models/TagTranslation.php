<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagTranslation extends Model
{
    protected $fillable = [
        'tag_id', 'language_ietf', 'is_default', 'tag_name', 'tag_description'
    ];

    public function tag() {
        return $this->belongsTo(Tag::class);
    }
}
