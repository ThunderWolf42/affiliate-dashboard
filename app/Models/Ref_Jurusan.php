<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ref_Jurusan extends Model
{
    protected $table = 'ref_jurusans';

    protected $fillable = [
        'nama_jurusan',
        'reward_amount',
    ];

    

    public function admisiRegistrations()
    {
        return $this->hasMany(Admisi_Registration::class, 'jurusan_id');
    }
}
