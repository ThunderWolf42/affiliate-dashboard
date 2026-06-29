<?php

namespace App\Notifications;

use App\Models\ChatMessage;
use App\Models\Lead;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Support\Facades\Log;

class TelegramChatWebPushNotification extends Notification
{
    public function __construct(
        private readonly Lead $lead,
        private readonly ChatMessage $message,
    ) {
    }

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $message = (new WebPushMessage)
            ->title('Pesan Telegram Baru')
            ->body(
                $this->lead->lead_name .
                ': ' .
                str($this->message->message_text)->limit(90)
            )
            ->tag('telegram-chat-' . $this->lead->id)
            ->data([
                'url' => route('filament.admin.pages.chat-room'),
                'lead_id' => $this->lead->id,
                'message_id' => $this->message->id,
            ]);

        Log::info('WEB PUSH PAYLOAD', [
            'payload' => $message->toArray(),
        ]);

        return $message;
    }
}
