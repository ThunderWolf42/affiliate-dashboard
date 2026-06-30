<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admisi_Registration extends Model
{
    protected $table = 'admisi__registrations';

    // Primary key tabel ini adalah no_registrasi (bukan id)
    protected $primaryKey = 'no_registrasi';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'no_registrasi',
        'nama_calon',
        'email',
        'no_hp',
        'jurusan_id',
        'current_step',
        'step_start_at',
        'last_update_at',
        'total_tagihan',
        'total_dibayar',
        'batch_number',
        'batch_year',
    ];

    public function refJurusan()
    {
        return $this->belongsTo(Ref_Jurusan::class, 'jurusan_id');
    }

    public function refTahapan()
    {
        // Pastikan foreign key di tabel admisi merujuk ke step_number di ref_tahapans
        return $this->belongsTo(RefTahapan::class, 'current_step', 'step_number');
    }


}
