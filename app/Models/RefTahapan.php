<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefTahapan extends Model
{
    protected $table = 'ref_tahapans';
    protected $primaryKey = 'step_number';
    public $incrementing = false; // Karena step_number kita input manual 1,2,3,4
    protected $keyType = 'int';

    protected $fillable = [
        'step_number',
        'step_name',
        'sla_days',
    ];

    // Relasi ke tabel pendaftaran leads
    public function leads()
    {
        return $this->hasMany(Lead::class, 'current_step', 'step_number');
    }
}
