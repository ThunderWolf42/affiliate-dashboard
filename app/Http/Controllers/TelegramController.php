<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $data = $request->all();

        // Log data yang masuk untuk debugging
        Log::info('Data Telegram Masuk:', $data);

        if (!isset($data['message']))
            return response()->json(['ok']);

        $chatId = $data['message']['chat']['id'];
        $messageText = $data['message']['text'] ?? '';

        // 1. Logic start dengan ID Lead (Handshake)
        if (str_starts_with($messageText, '/start')) {
            $parts = explode(' ', $messageText);
            $leadId = $parts[1] ?? null;

            if ($leadId) {
                $lead = Lead::find($leadId);
                if ($lead) {
                    $lead->update(['telegram_chat_id' => $chatId]);

                    // PERBAIKAN DI SINI: Masukkan nama lead-nya
                    $this->sendReply($chatId, "Halo perkenalkan namaku *{$lead->lead_name}*. Akunmu sudah terhubung!");

                    return response()->json(['status' => 'linked']);
                }
            }
        }

        // 2. Simpan pesan masuk (Inbound)
        $lead = Lead::where('telegram_chat_id', $chatId)->first();

        // Pastikan tidak menyimpan pesan /start ke database chat agar tidak kotor
        if ($lead && !str_starts_with($messageText, '/start')) {
            ChatMessage::create([
                'lead_id' => $lead->id,
                'message_text' => $messageText,
                'direction' => 'inbound',
            ]);
        }

        return response()->json(['status' => 'success']);
    }


    private function sendReply($chatId, $message)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
    }
}

