<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use App\Models\Marketing;
use App\Models\User;
use App\Models\UAA_Mahasiswa;
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
        //jagaan buat validasi email yg masuk itu email marketing atau marketing format mahasiswa
        if (str_ends_with($data['email'], '@admisiukrida.ac.id')) {
            // ini untuk register dengan email marketing , kalo cocok sama database jadi
            $marketing = Marketing::where('is_active', true)->where('email', $data['email'])->first();
            if (!$marketing) {
                // Tampilkan Notifikasi Pop-up Merah
                Notification::make()
                    ->title('Registrasi Gagal')
                    ->body('Email tidak valid atau status marketing tidak aktif.')
                    ->danger() // Warna merah
                    ->persistent() // Tidak hilang sampai di-close
                    ->send();

                // Lempar kembali ke form agar user tahu kolom mana yang bermasalah
                throw ValidationException::withMessages([
                    'email' => 'Email tidak memenuhi syarat sebagai Marketing.',
                ]);
            } else {
                $user = $this->getUserModel()::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'role' => 'admin',// ini buat ngekunci dengan email domain UKRIDA bakal jadi affiliate . jadi ini bagian filter antara affiliate dan admin Marketing.
                    'affiliate_code' => null,
                ]);
            }


        } else {

            if (!str_ends_with($data['email'], '@civitas.ukrida.ac.id')) {
                Notification::make()
                    ->title('Registrasi Gagal')
                    ->body('Pendaftaran hanya diperbolehkan menggunakan Email Resmi Civitas UKRIDA.')
                    ->danger()
                    ->persistent()
                    ->send();

                throw ValidationException::withMessages([
                    'email' => 'Domain email harus menggunakan @civitas.ukrida.ac.id',
                ]);
            }


            preg_match('/\d+/', $data['email'], $matches);
            $nimFromEmail = $matches[0] ?? null;

            $mahasiswaUAA = UAA_Mahasiswa::where('nim', $nimFromEmail)
                ->where('is_active', true)
                ->first();

            if (!$mahasiswaUAA) {
                // Tampilin notifikasi kalo ga cocok sama sistem UAA yg aktif
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
                    'role' => 'affiliate', // Mengunci role agar otomatis jadi affiliate
                    'affiliate_code' => 'REF-' . strtoupper(Str::random(6)),
                ]);
            }

            return $user;
        }



        return $user;
    }
}
