<x-filament-panels::page>
    <!-- Model selection moved to global settings. -->
    @if($analysisResult)
        <x-filament::card class="mt-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                Analysis Result
            </h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ $analysisResult }}
            </p>
        </x-filament::card>
    @else
        <x-filament::card class="mt-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                No analysis yet.
            </h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Click the "Analyze Log" button to start the analysis.
            </p>
        </x-filament::card>
    @endif
</x-filament-panels::page>
