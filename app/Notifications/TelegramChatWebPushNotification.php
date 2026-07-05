<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TelegramChatWebPushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Lead $lead,
        public ChatMessage $chatMessage,
    ) {
    }

    public function via(object $notifiable): array
    {
        Log::info('WEB PUSH VIA DIPANGGIL', [
            'user_id' => $notifiable->id,
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->lead_name,
        ]);

        return [
            WebPushChannel::class,
        ];
    }

    public function toWebPush(
        object $notifiable,
        Notification $notification
    ): WebPushMessage {

        $url = route('filament.admin.pages.chat-room');

        Log::info('WEB PUSH PAYLOAD DIBUAT', [
            'user_id' => $notifiable->id,
            'title' => 'Pesan Telegram Baru',
            'body' => "Pesan dari Camaba: {$this->lead->lead_name}",
            'url' => $url,
        ]);

        return (new WebPushMessage)
            ->title('Pesan Telegram Baru')
            ->body("Pesan dari Camaba: {$this->lead->lead_name}")
            ->icon('/favicon.ico')
            ->tag('telegram-chat')
            ->data([
                'url' => $url,
            ]);
    }
}
