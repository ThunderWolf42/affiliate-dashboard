<x-filament-panels::page>

    <div x-data="chatResize()" x-init="init()"
        class="flex h-[75vh] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm relative"
        wire:poll.3s>

        <!-- LEFT: CHAT LIST -->
        <div :style="isMobile() ? 'width:100%' : `width:${leftWidth}%`"
            class="{{ $showChatDetail ? 'hidden' : 'flex' }} md:flex flex-col border-r border-gray-200 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/50">
            <div class="p-4 border-b font-bold text-lg bg-white dark:bg-gray-900">
                Chats
            </div>

            <div class="overflow-y-auto flex-1">
                @foreach ($this->getLeads() as $lead)
                    <div wire:click="selectLead({{ $lead->id }})"
                        class="p-4 cursor-pointer border-b flex items-center gap-4 hover:bg-gray-100 dark:hover:bg-gray-800 transition
                        {{ $activeLeadId == $lead->id ? 'bg-white dark:bg-gray-800 shadow-sm border-l-4 border-l-primary-600' : '' }}">

                        <div
                            class="w-12 h-12 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold">
                            {{ substr($lead->lead_name, 0, 1) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between">
                                <p class="font-bold text-sm truncate">
                                    {{ $lead->lead_name }}
                                </p>
                                <span class="text-[10px] text-gray-400">Telegram</span>
                            </div>
                            <p class="text-xs text-gray-500 truncate mt-1">
                                Klik untuk melihat riwayat...
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- RESIZER (DESKTOP ONLY) -->
        <div @mousedown="startDrag"
            class="hidden md:block w-1 cursor-col-resize bg-gray-200 dark:bg-gray-700 hover:bg-primary-500 transition">
        </div>

        <!-- RIGHT: CHAT AREA -->
        <div class="{{ $showChatDetail ? 'flex' : 'hidden' }} md:flex flex-1 flex-col bg-white dark:bg-gray-950 w-full">
            @if ($activeLeadId)

                <!-- HEADER -->
                <div class="p-4 border-b flex items-center gap-3 bg-white dark:bg-gray-900">
                    <button wire:click="backToList" class="md:hidden p-2">
                        ←
                    </button>

                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold">
                        {{ substr(\App\Models\Lead::find($activeLeadId)->lead_name, 0, 1) }}
                    </div>

                    <p class="font-bold truncate">
                        {{ \App\Models\Lead::find($activeLeadId)->lead_name }}
                    </p>
                </div>

                <!-- CHAT BODY -->
                <div id="chatBody"
                    class="flex-1 p-4 overflow-y-auto flex flex-col gap-4 bg-[#f0f2f5] dark:bg-gray-950">

                    @foreach ($this->getMessages() as $msg)
                        <div class="flex {{ $msg->direction == 'outbound' ? 'justify-end' : 'justify-start' }}">
                            <div
                                class="max-w-[80%] px-4 py-2 rounded-2xl text-sm
                                {{ $msg->direction == 'outbound' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800' }}">

                                {{ $msg->message_text }}

                                <div class="text-[10px] text-right opacity-60">
                                    {{ $msg->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- INPUT -->
                <div class="p-4 border-t bg-white dark:bg-gray-900">
                    <div class="flex gap-2">
                        <input type="text" wire:model="newMessage" wire:keydown.enter.prevent="sendMessage"
                            placeholder="Tulis pesan..."
                            class="flex-1 rounded-full border border-gray-300 dark:border-gray-700
                                bg-gray-100 dark:bg-gray-800
                                text-gray-900 dark:text-white
                                placeholder-gray-400 dark:placeholder-gray-500
                                focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                                px-4 py-2 text-sm transition" />

                        <button wire:click="sendMessage" class="bg-primary-600 text-white p-2 rounded-full w-10 h-10">
                            ➤
                        </button>
                    </div>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center text-gray-400">
                    Pilih chat dulu
                </div>
            @endif
        </div>

    </div>

    <!-- ALPINE LOGIC -->
    <script>
        function chatResize() {
            return {
                leftWidth: 50,
                isDragging: false,

                init() {
                    let saved = localStorage.getItem('chatWidth');
                    if (saved) this.leftWidth = saved;
                },

                isMobile() {
                    return window.innerWidth < 768;
                },

                startDrag() {
                    if (this.isMobile()) return;

                    this.isDragging = true;
                    document.body.classList.add('resizing');

                    window.addEventListener('mousemove', this.onDrag);
                    window.addEventListener('mouseup', this.stopDrag);
                },

                onDrag: function(e) {
                    if (!this.isDragging) return;

                    let rect = document.querySelector('[x-data]').getBoundingClientRect();
                    let newWidth = ((e.clientX - rect.left) / rect.width) * 100;

                    if (newWidth < 20) newWidth = 20;
                    if (newWidth > 80) newWidth = 80;

                    this.leftWidth = newWidth;
                    localStorage.setItem('chatWidth', newWidth);
                },

                stopDrag: function() {
                    this.isDragging = false;
                    document.body.classList.remove('resizing');

                    window.removeEventListener('mousemove', this.onDrag);
                    window.removeEventListener('mouseup', this.stopDrag);
                }
            }
        }
    </script>

    <style>
        body.resizing {
            cursor: col-resize;
        }
    </style>

</x-filament-panels::page>


{{-- <x-filament-panels::page>
    <div class="flex h-[75vh] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm relative" wire:poll.3s>

        <div class="{{ $showChatDetail ? 'hidden' : 'flex' }} md:flex w-full md:w-80 border-r border-gray-200 dark:border-gray-700 flex-col bg-gray-50/30 dark:bg-gray-900/50">
            <div class="p-4 border-b dark:border-gray-700 font-bold text-lg">Chats</div>
            <div class="overflow-y-auto flex-1">
                @foreach ($this->getLeads() as $lead)
                    <div wire:click="selectLead({{ $lead->id }})"
                         class="p-4 cursor-pointer border-b dark:border-gray-700 flex items-center gap-3 hover:bg-gray-100 dark:hover:bg-gray-800 transition {{ $activeLeadId == $lead->id ? 'bg-white dark:bg-gray-800 shadow-sm border-l-4 border-l-primary-600' : '' }}">
                        <div class="w-10 h-10 rounded-full bg-primary-500 flex-shrink-0 flex items-center justify-center text-white font-bold">
                            {{ substr($lead->lead_name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ $lead->lead_name }}</p>
                            <p class="text-xs text-gray-500 truncate">Klik untuk membalas...</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="{{ $showChatDetail ? 'flex' : 'hidden' }} md:flex flex-1 flex-col bg-white dark:bg-gray-950">
            @if ($activeLeadId)
                <div class="p-4 border-b dark:border-gray-700 flex items-center gap-3 bg-white dark:bg-gray-900 shadow-sm">
                    <button wire:click="backToList" class="md:hidden p-2 -ml-2 text-gray-500 hover:text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex-shrink-0 flex items-center justify-center text-xs font-bold text-primary-600">
                        {{ substr(\App\Models\Lead::find($activeLeadId)->lead_name, 0, 1) }}
                    </div>
                    <p class="font-bold truncate">{{ \App\Models\Lead::find($activeLeadId)->lead_name }}</p>
                </div>

                <div class="flex-1 p-4 md:p-6 overflow-y-auto flex flex-col gap-4 bg-[#f0f2f5] dark:bg-gray-950">
                    @foreach ($this->getMessages() as $msg)
                        <div class="flex {{ $msg->direction == 'outbound' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] md:max-w-[70%] px-4 py-2 rounded-2xl shadow-sm text-sm
                                {{ $msg->direction == 'outbound'
                                    ? 'bg-primary-600 text-white rounded-tr-none'
                                    : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-tl-none' }}">
                                <p class="whitespace-pre-wrap leading-relaxed">{{ $msg->message_text }}</p>
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
                               class="flex-1 rounded-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-primary-500 text-sm">
                        <button wire:click="sendMessage"
                                class="bg-primary-600 hover:bg-primary-700 text-white p-2 rounded-full w-10 h-10 flex-shrink-0 flex items-center justify-center transition shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </div>
                </div>
            @else
                <div class="flex-1 hidden md:flex flex-col items-center justify-center text-gray-500">
                    <div class="p-6 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        <svg class="w-16 h-16 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <p>Pilih camaba untuk memulai percakapan</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page> --}}


{{-- <x-filament-panels::page>
    <div class="flex h-[75vh] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm" wire:poll.3s>

        <div class="w-80 border-r border-gray-200 dark:border-gray-700 flex flex-col bg-gray-50/30 dark:bg-gray-900/50">
            <div class="p-4 border-b dark:border-gray-700 font-bold text-lg">Chats</div>
            <div class="overflow-y-auto flex-1">
                @foreach ($this->getLeads() as $lead)
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
            @if ($activeLeadId)
                <div class="p-4 border-b dark:border-gray-700 flex items-center gap-3 bg-white dark:bg-gray-900">
                    <p class="font-bold">{{ \App\Models\Lead::find($activeLeadId)->lead_name }}</p>
                </div>

                <div class="flex-1 p-6 overflow-y-auto flex flex-col gap-4 bg-[#f0f2f5] dark:bg-gray-950">
                    @foreach ($this->getMessages() as $msg)
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

 --}}
