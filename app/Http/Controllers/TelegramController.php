<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class TelegramController extends Controller
{
    public function handleWebhook(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('WEBHOOK MASUK', $data);

            // Validasi minimal
            if (!isset($data['message']['chat']['id'])) {
                return response()->json(['ok' => true]);
            }

            $chatId = $data['message']['chat']['id'];
            $messageText = $data['message']['text'] ?? '';

            /*
            |--------------------------------------------------------------------------
            | 1. HANDLE HANDSHAKE AUTOMATICALLY VIA /start
            |--------------------------------------------------------------------------
            | Calon mahasiswa klik link affiliate -> masuk Telegram -> klik tombol Start.
            | Telegram mengirim teks: "/start 14" (14 adalah ID Lead)
            */
            if (strpos($messageText, '/start') === 0) {
                $parts = explode(' ', $messageText);
                $leadId = $parts[1] ?? null; // Menangkap ID Lead dari database simulasi

                if ($leadId) {
                    $lead = Lead::find($leadId);

                    if ($lead) {
                        // Jalankan proses update jabat tangan (Handshake)
                        $lead->update([
                            'telegram_chat_id' => $chatId
                        ]);

                        // 📝 EDIT KATA-KATA BALASAN BOT TELEGRAM DI SINI WAK:
                        $welcomeMessage = "Halo *{$lead->lead_name}*! Selamat datang di Pusat Informasi & Admisi UKRIDA ✨\n\n" .
                            "Senang sekali bisa terhubung dengan kamu. Akun Telegram kamu saat ini sudah *resmi terverifikasi* di sistem kami.\n\n" .
                            "Untuk melanjutkan pengisian berkas dan simulasi pendaftaran kuliah, silakan langsung klik tautan resmi di bawah ini ya:\n" .
                            "👉 [Sistem Pendaftaran Kampus UKRIDA](https://register.ukrida.ac.id/admisi/public/register/register/registerEmail)\n\n" .
                            "Jika ada pertanyaan selama proses pendaftaran, ketik saja langsung di sini. Kakak tingkat (Affiliate) kamu siap membantu! 🤝";

                        $this->sendReply($chatId, $welcomeMessage);

                        return response()->json(['status' => 'linked']);
                    } else {
                        $this->sendReply($chatId, "Maaf, data pendaftaran kamu tidak ditemukan di sistem. Silakan ulangi pengisian dari link web.");
                    }
                } else {
                    $this->sendReply($chatId, "Format verifikasi salah. Silakan masuk melalui tautan resmi dari web.");
                }

                return response()->json(['ok' => true]);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. SIMPAN CHAT MASUK (INBOUND)
            |--------------------------------------------------------------------------
            | Menyimpan chat biasa dari calon mahasiswa & mengabaikan command /start
            */
            $lead = Lead::where('telegram_chat_id', $chatId)->first();

            if ($lead && $messageText !== '' && strpos($messageText, '/start') !== 0) {
                ChatMessage::create([
                    'lead_id' => $lead->id,
                    'message_text' => $messageText,
                    'direction' => 'inbound',
                ]);

                /*
                |--------------------------------------------------------------------------
                | 3. NOTIFIKASI REALTIME HANYA KE MAHASISWA PEMILIK LEAD TERSEBUT
                |--------------------------------------------------------------------------
                */
                // 🔒 AMAN & PRIVAT: Ambil user pemilik (affiliate) dari relasi data Lead secara dinamis
                $affiliateOwner = $lead->user;

                if ($affiliateOwner) {
                    try {
                        Notification::make()
                            ->title('Pesan Telegram Baru')
                            ->body("Pesan dari Camaba: " . $lead->lead_name)
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->iconColor('success')
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('balas')
                                    ->button()
                                    // Melempar rute notifikasi langsung ke Custom Page Chat Room di panel affiliate
                                    ->url('/affiliate/chat-room')
                            ])
                            ->success()
                            ->sendToDatabase($affiliateOwner) // Masuk ke lonceng dashboard mahasiswa yang tepat
                            ->broadcast($affiliateOwner); // Trigger Reverb Realtime
                    } catch (\Throwable $e) {
                        Log::error('Notif error: ' . $e->getMessage());
                    }
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Throwable $e) {
            Log::error('WEBHOOK ERROR', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return response()->json(['error' => true], 200);
        }
    }

    private function sendReply($chatId, $message)
    {
        try {
            $token = config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN');

            if (!$token) {
                Log::error('TELEGRAM TOKEN KOSONG');
                return;
            }

            $response = Http::withoutVerifying()->timeout(5)->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown'
                ]
            );

            if (!$response->successful()) {
                Log::error('GAGAL KIRIM TELEGRAM', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('ERROR SEND TELEGRAM', [
                'message' => $e->getMessage()
            ]);
        }
    }
}
