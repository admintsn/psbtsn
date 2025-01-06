<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasPendidikan extends Model
{
    public function santris()
    {
        return $this->hasMany(Santri::class, 'aktivitaspend_id');
    }
}
