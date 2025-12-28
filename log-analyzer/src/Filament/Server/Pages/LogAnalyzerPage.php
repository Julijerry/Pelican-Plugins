<?php

namespace Julijerry\LogAnalyzer\Filament\Server\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use Julijerry\LogAnalyzer\Services\LogAnalyzerService;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Models\Server;
use Illuminate\Support\Facades\Http;
use BackedEnum;

class LogAnalyzerPage extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Log Analyzer';

    protected string $view = 'log-analyzer::filament.server.pages.log-analyzer-page';

    protected static ?string $slug = 'log-analyzer';

    public ?string $analysisResult = '';
    public string $selectedModel = '';

    public function mount(): void
    {
        $this->selectedModel = config('log-analyzer.model', 'gemini-2.5-flash');
    }

    public function getTitle(): string
    {
        return 'Log Analyzer';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('analyze')
                ->label('Analyze Log')
                ->action('analyzeLog'),
        ];
    }

    public function analyzeLog(): void
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        try {
            $logs = Http::daemon($server->node)
                ->get("/api/servers/{$server->uuid}/logs")
                ->throw()
                ->json('data');

            $logs = is_array($logs) ? implode(PHP_EOL, $logs) : $logs;

            $analyzer = new LogAnalyzerService();
            $this->analysisResult = $analyzer->analyze($logs, $this->selectedModel);

            Notification::make()
                ->title('Analysis complete')
                ->success()
                ->send();
        } catch (\Exception $exception) {
            report($exception);

            Notification::make()
                ->title('Failed to analyze the log')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
