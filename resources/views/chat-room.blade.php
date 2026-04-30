<x-filament-panels::page>
    <div class="flex h-[75vh] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm" wire:poll.3s>

        <div class="w-80 border-r border-gray-200 dark:border-gray-700 flex flex-col bg-gray-50/30 dark:bg-gray-900/50">
            <div class="p-4 border-b dark:border-gray-700 font-bold text-lg">Chats</div>
            <div class="overflow-y-auto flex-1">
                @foreach($this->getLeads() as $lead)
                    <div wire:click="selectLead({{ $lead->id }})"
                         class="p-4 cursor-pointer border-b dark:border-gray-700 flex items-center gap-3 hover:bg-gray-100 dark:hover:bg-gray-800 transition {{ $activeLeadId == $lead->id ? 'bg-white dark:bg-gray-800 shadow-sm border-l-4 border-l-primary-600' : '' }}">
                        <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold">
                            {{ substr($lead->lead_name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-sm">{{ $lead->lead_name }}</p>
                            <p class="text-xs text-gray-500 truncate">Klik untuk membalas...</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex-1 flex flex-col bg-white dark:bg-gray-950">
            @if($activeLeadId)
                <div class="p-4 border-b dark:border-gray-700 flex items-center gap-3 bg-white dark:bg-gray-900">
                    <p class="font-bold">{{ \App\Models\Lead::find($activeLeadId)->lead_name }}</p>
                </div>

                <div class="flex-1 p-6 overflow-y-auto flex flex-col gap-4 bg-[#f0f2f5] dark:bg-gray-950">
                    @foreach($this->getMessages() as $msg)
                        <div class="flex {{ $msg->direction == 'outbound' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[70%] px-4 py-2 rounded-2xl shadow-sm text-sm
                                {{ $msg->direction == 'outbound'
                                    ? 'bg-primary-600 text-white rounded-tr-none'
                                    : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-tl-none' }}">
                                {{ $msg->message_text }}
                                <span class="block text-[10px] text-right mt-1 opacity-60 italic">
                                    {{ $msg->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 bg-white dark:bg-gray-900 border-t dark:border-gray-700">
                    <div class="flex gap-2">
                        <input type="text" wire:model="newMessage" wire:keydown.enter="sendMessage"
                               placeholder="Tulis pesan..."
                               class="flex-1 rounded-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-primary-500">
                        <button wire:click="sendMessage"
                                class="bg-primary-600 hover:bg-primary-700 text-white p-2 rounded-full w-10 h-10 flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </div>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-gray-500">
                    <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <p>Pilih camaba untuk memulai percakapan</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
