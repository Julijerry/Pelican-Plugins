<x-filament-panels::page>
    <x-filament-panels::form wire:submit="analyzeLog">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
    </x-filament-panels::form>

    @if($analysisResult)
        <x-filament::card class="mt-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                Analysis Result
            </h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ $analysisResult }}
            </p>
        </x-filament::card>
    @endif

</x-filament-panels::page>
