<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefTahapan extends Model
{

    // Beritahu Laravel bahwa PK-nya bukan 'id'
    protected $primaryKey = 'step_number';

    // Jika PK kamu bukan auto-increment (karena kamu input manual 1, 2, 3)
    public $incrementing = false;

    // Jika PK-nya bertipe integer
    protected $keyType = 'int';
    protected $table = 'ref_tahapans';

    protected $fillable = [
        'step_name',
        'description',
        'sla_days',
    ];

    public function admisiRegistrations()
    {
        return $this->hasMany(Admisi_Registration::class, 'current_step_id');
    }

    public function refTahapan()
    {
        // foreign key-nya adalah current_step, merujuk ke step_number di ref_tahapans
        return $this->belongsTo(RefTahapan::class, 'current_step', 'step_number');
    }
}
