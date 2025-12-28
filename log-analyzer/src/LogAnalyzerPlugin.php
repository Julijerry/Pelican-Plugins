<?php

namespace Julijerry\LogAnalyzer;

use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;

class LogAnalyzerPlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'log-analyzer';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        if ($panel->getId() === 'server') {
            $panel->discoverPages(plugin_path($this->getId(), "src/Filament/$id/Pages"), "Julijerry\\LogAnalyzer\\Filament\\$id\\Pages");
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function getSettingsForm(): array
    {
        return [
            TextInput::make('gemini_api_key')
                ->label('Gemini API Key')
                ->required()
                ->default(fn () => config('log-analyzer.gemini_api_key')),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'GEMINI_API_KEY' => $data['gemini_api_key'],
        ]);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
