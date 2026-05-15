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
            | 1. HANDLE /start (link ke lead)
            |--------------------------------------------------------------------------
            */
            if (strpos($messageText, '/start') === 0) {
                $parts = explode(' ', $messageText);
                $leadId = $parts[1] ?? null;

                if ($leadId) {
                    $lead = Lead::find($leadId);

                    if ($lead) {
                        $lead->update([
                            'telegram_chat_id' => $chatId
                        ]);

                        $this->sendReply(
                            $chatId,
                            "Halo perkenalkan namaku *{$lead->lead_name}*. Akunmu sudah terhubung!"
                        );

                        return response()->json(['status' => 'linked']);
                    } else {
                        $this->sendReply($chatId, "Lead tidak ditemukan.");
                    }
                } else {
                    $this->sendReply($chatId, "Format salah. Gunakan: /start ID_LEAD");
                }

                return response()->json(['ok' => true]);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. SIMPAN CHAT MASUK
            |--------------------------------------------------------------------------
            */
            $lead = Lead::where('telegram_chat_id', $chatId)->first();

            if ($lead && $messageText !== '') {
                ChatMessage::create([
                    'lead_id' => $lead->id,
                    'message_text' => $messageText,
                    'direction' => 'inbound',
                ]);

                /*
                |--------------------------------------------------------------------------
                | 3. NOTIFICATION (optional, bisa di-disable dulu)
                |--------------------------------------------------------------------------
                */
                $admin = User::where('role', 'affiliate')->first();

                if ($admin) {
                    try {
                        Notification::make()
                            ->title('Pesan Telegram Baru')
                            ->body("Pesan dari: " . $lead->lead_name)
                            ->sendToDatabase($admin)
                            ->broadcast($admin); // matikan dulu kalau belum setup realtime
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
            $token = config('services.telegram.bot_token'); // lebih aman dari env()

            if (!$token) {
                Log::error('TELEGRAM TOKEN KOSONG');
                return;
            }

            $response = Http::timeout(5)->post(
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
    // public function handleWebhook(Request $request)
    // {
    //     $data = $request->all();

    //     // Log data yang masuk untuk debugging
    //     Log::info('Data Telegram Masuk:', $data);

    //     if (!isset($data['message']))
    //         return response()->json(['ok']);

    //     $chatId = $data['message']['chat']['id'];
    //     $messageText = $data['message']['text'] ?? '';

    //     // 1. Logic start dengan ID Lead (Handshake)
    //     if (strpos($messageText, '/start') === 0) {
    //         $parts = explode(' ', $messageText);
    //         $leadId = $parts[1] ?? null;

    //         if ($leadId) {
    //             $lead = Lead::find($leadId);
    //             if ($lead) {
    //                 $lead->update(['telegram_chat_id' => $chatId]);

    //                 // PERBAIKAN DI SINI: Masukkan nama lead-nya
    //                 $this->sendReply($chatId, "Halo perkenalkan namaku *{$lead->lead_name}*. Akunmu sudah terhubung!");

    //                 return response()->json(['status' => 'linked']);
    //             }
    //         }
    //     }

    //     // 2. Simpan pesan masuk (Inbound)
    //     $lead = Lead::where('telegram_chat_id', $chatId)->first();

    //     // Pastikan tidak menyimpan pesan /start ke database chat agar tidak kotor
    //     if ($lead && strpos($messageText, '/start') !== 0) {
    //         ChatMessage::create([
    //             'lead_id' => $lead->id,
    //             'message_text' => $messageText,
    //             'direction' => 'inbound',
    //         ]);

    //         $admin = User::all()->firstWhere('role', 'affiliate');

    //         if ($admin) {
    //             Notification::make()
    //                 ->title('Pesan Telegram Baru')
    //                 ->icon('heroicon-o-chat-bubble-left-right')
    //                 ->iconColor('success')
    //                 ->body("Pesan dari: " . $lead->lead_name)
    //                 ->actions([
    //                     \Filament\Notifications\Actions\Action::make('Lihat Chat')
    //                         ->url(fn () => route('filament.admin.resources.leads.view', $lead->id)) // Sesuaikan link detail chat kamu
    //                 ])
    //                 ->sendToDatabase($admin) // Masuk ke lonceng dashboard
    //                 ->broadcast($admin);    // INI YANG TRIGGER REVERB (REALTIME)
    //         }
    //     }

    //     return response()->json(['status' => 'success']);
    // }



}

