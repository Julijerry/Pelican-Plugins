<?php

namespace Julijerry\LogAnalyzer;

use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Schemas\Components\Utilities\Get;

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

    public function getSettingsFormData(): array
    {
        return [
            'provider' => config('log-analyzer.provider', 'gemini'),
            'api_key' => config('log-analyzer.api_key') ?: config('log-analyzer.gemini_api_key'),
            'base_url' => config('log-analyzer.base_url', 'https://api.openai.com/v1'),
            'model' => config('log-analyzer.model', 'gemini-3.5-flash'),
            'custom_model' => config('log-analyzer.custom_model'),
        ];
    }

    public function getSettingsForm(): array
    {
        return [
            Select::make('provider')
                ->label('AI Provider')
                ->options(collect(config('log-analyzer.providers', []))
                    ->mapWithKeys(fn (array $provider, string $key) => [$key => $provider['label'] ?? $key])
                    ->all())
                ->required()
                ->live()
                ->afterStateUpdated(function (callable $set, ?string $state): void {
                    if ($state === 'openai_compatible') {
                        $set('model', 'custom');

                        return;
                    }

                    $models = array_keys(config("log-analyzer.providers.{$state}.models", []));
                    $set('model', $models[0] ?? 'custom');
                })
                ->default(fn () => config('log-analyzer.provider', 'gemini')),

            TextInput::make('api_key')
                ->label('API Key')
                ->password()
                ->revealable()
                ->required()
                ->default(fn () => config('log-analyzer.api_key') ?: config('log-analyzer.gemini_api_key')),

            TextInput::make('base_url')
                ->label('Base URL')
                ->helperText('OpenAI-compatible API base URL, e.g. https://api.openai.com/v1')
                ->url()
                ->visible(fn (Get $get) => $get('provider') === 'openai_compatible')
                ->required(fn (Get $get) => $get('provider') === 'openai_compatible')
                ->default(fn () => config('log-analyzer.base_url', 'https://api.openai.com/v1')),

            Select::make('model')
                ->label('AI Model')
                ->options(fn (Get $get) => $this->modelOptionsForProvider($get('provider') ?? 'gemini'))
                ->required()
                ->live()
                ->default(fn () => config('log-analyzer.model', 'gemini-3.5-flash')),

            TextInput::make('custom_model')
                ->label('Custom Model')
                ->helperText('Exact model id for the selected provider (e.g. gpt-5.6-luna or gemini-3.5-flash)')
                ->visible(fn (Get $get) => ($get('model') ?? '') === 'custom' || ($get('provider') ?? '') === 'openai_compatible')
                ->required(fn (Get $get) => ($get('model') ?? '') === 'custom' || ($get('provider') ?? '') === 'openai_compatible')
                ->default(fn () => config('log-analyzer.custom_model')),
        ];
    }

    public function saveSettings(array $data): void
    {
        $model = $data['model'] ?? 'custom';
        $customModel = $data['custom_model'] ?? null;

        if (($data['provider'] ?? '') === 'openai_compatible') {
            $model = 'custom';
        }

        $payload = [
            'LOG_ANALYZER_PROVIDER' => $data['provider'],
            'LOG_ANALYZER_API_KEY' => $data['api_key'],
            'LOG_ANALYZER_MODEL' => $model,
            'LOG_ANALYZER_CUSTOM_MODEL' => $customModel ?? '',
        ];

        if (($data['provider'] ?? '') === 'openai_compatible') {
            $payload['LOG_ANALYZER_BASE_URL'] = $data['base_url'] ?? 'https://api.openai.com/v1';
        }

        // Keep legacy env in sync for older installs
        if (($data['provider'] ?? '') === 'gemini') {
            $payload['GEMINI_API_KEY'] = $data['api_key'];
        }

        $this->writeToEnvironment($payload);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    protected function modelOptionsForProvider(string $provider): array
    {
        $models = config("log-analyzer.providers.{$provider}.models", []);

        return array_merge($models, ['custom' => 'Custom model…']);
    }
}
