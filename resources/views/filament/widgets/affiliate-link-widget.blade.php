<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-y-3">
            <h2 class="text-lg font-bold">🚀 Siap Ajak Teman/Saudara ke UKRIDA?</h2>
            <p class="text-gray-500">Gunakan link di bawah ini untuk mendaftarkan teman kamu ke UKRIDA:</p>
            <div class="flex items-center gap-x-2">
                <x-filament::input.wrapper class="flex-1">
                    <x-filament::input
                        type="text"
                        readonly
                        value="{{ url('/join/' . auth()->user()->affiliate_code) }}"
                    />
                </x-filament::input.wrapper>

                <x-filament::button
                    icon="heroicon-m-clipboard-document"
                    onclick="navigator.clipboard.writeText('{{ url('/join/' . auth()->user()->affiliate_code) }}'); alert('Link berhasil disalin!')">
                    Salin Link
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
