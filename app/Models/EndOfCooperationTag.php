<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EndOfCooperationTag extends Model
{
    protected $fillable = ['key'];

    public function translations() {
        return $this->hasMany(EndOfCooperationTagTranslation::class);
    }

}
