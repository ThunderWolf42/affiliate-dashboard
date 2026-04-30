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

    public function getLeads()
    {
        return Lead::whereNotNull('telegram_chat_id')->get();
    }

    // ini buat milih calon mahasiswa yg buat di chat
    public function selectLead($leadId)
    {
        $this->activeLeadId = $leadId;
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
}


