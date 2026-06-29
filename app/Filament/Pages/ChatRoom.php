<?php
namespace App\Filament\Pages;

use App\Models\Lead;
use App\Models\ChatMessage;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;

class ChatRoom extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left';
    protected static string $view = 'chat-room';
    protected static ?string $title = 'Chat Room';

    public $activeLeadId = null;
    public $newMessage = '';
    public $showChatDetail = false;

    public function getLeads()
    {
        // Mengunci data leads: Hanya yang punya telegram_chat_id DAN dibawa oleh mahasiswa yang sedang login
        return Lead::whereNotNull('telegram_chat_id')
            ->where('user_id', auth()->id()) // 🔒 GEMBOK SAKTI DI SINI WAK
            ->get();
    }

    public static function canAccess(): bool
    {
        // Hanya akun mahasiswa (affiliate) yang punya menu chat ini wak!
        return auth()->user()?->role === 'affiliate';
    }

    // ini buat milih calon mahasiswa yg buat di chat
    public function selectLead($leadId)
    {
        $this->activeLeadId = $leadId;
        $this->showChatDetail = true;
    }

    //fungsi buat kirim pesan dari admin ke calon mahasiswa
    public function sendMessage()
    {
        if (!$this->newMessage || !$this->activeLeadId)
            return;

        $lead = Lead::find($this->activeLeadId);
        $token = env('TELEGRAM_BOT_TOKEN');

        //kirim pesan ke telegram lewat API
        Http::withoutVerifying()->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $lead->telegram_chat_id,
            'text' => $this->newMessage,
        ]);

        // simpen ke database
        ChatMessage::create([
            'lead_id' => $lead->id,
            'message_text' => $this->newMessage,
            'direction' => 'outbound',
        ]);

        $this->newMessage = '';
    }

    public function getMessages()
    {
        if (!$this->activeLeadId)
            return [];
        return ChatMessage::where('lead_id', $this->activeLeadId)
            ->orderBy('created_at')
            ->get();
    }

    public function backToList()
    {
        $this->showChatDetail = false;
        $this->activeLeadId = null;
    }
}


// protected function handleRegistration(array $data): Model
//     {
//         preg_match('/\d+/', $data['email'], $matches);
//         $nimFromEmail = $matches[0] ?? null;

//         $mahasiswaUAA = \App\Models\UAA_Mahasiswa::where('nim', $nimFromEmail)
//             ->where('is_active', true)
//             ->first();

//         if (!$mahasiswaUAA) {
//             // Tampilkan Notifikasi Pop-up Merah
//             Notification::make()
//                 ->title('Registrasi Gagal')
//                 ->body('NIM tidak valid atau status mahasiswa tidak aktif di sistem UAA.')
//                 ->danger() // Warna merah
//                 ->persistent() // Tidak hilang sampai di-close
//                 ->send();

//             // Lempar kembali ke form agar user tahu kolom mana yang bermasalah
//             throw ValidationException::withMessages([
//                 'email' => 'NIM/Email tidak memenuhi syarat sebagai Affiliate.',
//             ]);
//         } else {
//             $user = $this->getUserModel()::create([
//                 'name' => $data['name'],
//                 'email' => $data['email'],
//                 'password' => $data['password'],
//                 'nim' => $nimFromEmail,
//                 'role' => 'affiliate',// ini buat ngekunci dengan email domain UKRIDA bakal jadi affiliate . jadi ini bagian filter antara affiliate dan admin Marketing.
//                 'affiliate_code' => 'REF-' . strtoupper(Str::random(6)),
//             ]);
//         }

//         return $user;
//     }

