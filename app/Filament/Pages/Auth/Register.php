<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Hash;
use illuminate\Database\Eloquent\Model;
use illuminate\Support\Str;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                ])
                ->statePath('data'),
        ];
    }


    protected function handleRegistration(array $data): Model
    {
        preg_match('/\d+/', $data['email'], $matches);
        $nimFromEmail = $matches[0] ?? null;

        $mahasiswaUAA = \App\Models\UAA_Mahasiswa::where('nim', $nimFromEmail)
            ->where('is_active', true)
            ->first();

        if (!$mahasiswaUAA) {
            // Tampilkan Notifikasi Pop-up Merah
            Notification::make()
                ->title('Registrasi Gagal')
                ->body('NIM tidak valid atau status mahasiswa tidak aktif di sistem UAA.')
                ->danger() // Warna merah
                ->persistent() // Tidak hilang sampai di-close
                ->send();

            // Lempar kembali ke form agar user tahu kolom mana yang bermasalah
            throw ValidationException::withMessages([
                'email' => 'NIM/Email tidak memenuhi syarat sebagai Affiliate.',
            ]);
        } else {
            $user = $this->getUserModel()::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'nim' => $nimFromEmail,
                'role' => 'affiliate',// ini buat ngekunci dengan email domain UKRIDA bakal jadi affiliate . jadi ini bagian filter antara affiliate dan admin Marketing.
                'affiliate_code' => 'REF-' . strtoupper(Str::random(6)),
            ]);
        }

        return $user;
    }

}
