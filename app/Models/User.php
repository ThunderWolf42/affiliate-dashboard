<?php

namespace App\Models;

use App\Models\UAA_Mahasiswa;
use Filament\Panel;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'role',
        'affiliate_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function u_a_a_mahasiswa()
    {
        return $this->belongsTo(UAA_Mahasiswa::class, 'nim', 'nim');
    }

    public function syncAllLeads()
    {
        // Ambil semua leads milik dia yang belum punya nomor registrasi
        $this->leads()->whereNull('no_registrasi')->get()->each(function ($lead) {
            $lead->findMatchInAdmisi();
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        // Ini adalah 'alamat' channel tempat browser admin mendengarkan notif
        return 'App.Models.User.' . $this->id;
    }

    public function leads()
    {
        return $this->hasMany(\App\Models\Lead::class, 'user_id');
    }
    public function getTotalRewardAttribute()
    {
        return $this->leads()
            ->whereHas('admisiRegistration.refTahapan', function ($query) {
                // Sesuai screenshot terakhirmu, nama kolomnya adalah step_number
                // Dan kita mau ambil tahap ke-6
                $query->where('ref_tahapans.step_number', 6);
            })
            // Sesuai screenshot table leads, nama kolomnya adalah jurusan_id
            ->join('ref_jurusans', 'leads.jurusan_id', '=', 'ref_jurusans.id')
            ->sum('ref_jurusans.reward_amount');
    }



    public function canAccessPanel(Panel $panel): bool// jagaan buat akses panel admin dan affiliate biar gak bisa diakses sembarangan
    {
        // // Kalau ada yang mau masuk rute /admin, wajib punya role admin
        // if ($panel->getId() === 'admin') {
        //     return $this->role === 'admin';
        // }

        // // Kalau ada yang mau masuk rute /affiliate, wajib punya role affiliate
        // if ($panel->getId() === 'affiliate') {
        //     return $this->role === 'affiliate';
        // }

        return true; 
    }
}
