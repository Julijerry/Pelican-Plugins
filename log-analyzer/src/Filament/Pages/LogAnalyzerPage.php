<?php

namespace Julijerry\LogAnalyzer\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Gemini\LogAnalyzer\Services\LogAnalyzerService;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;

class LogAnalyzerPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'log-analyzer::filament.pages.log-analyzer-page';
    
    protected static ?string $slug = 'log-analyzer';

    public ?string $logContent = '';
    public ?string $analysisResult = '';

    public function mount(): void
    {
        $this->form->fill([
            'logContent' => "Your Minecraft server has crashed. \n[Server thread/ERROR]: This is a test error.",
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Textarea::make('logContent')
                ->label('Server Log')
                ->rows(20)
                ->required(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('analyze')
                ->label('Analyze Log')
                ->submit('analyzeLog'),
        ];
    }

    public function analyzeLog(): void
    {
        $logContent = $this->form->getState()['logContent'];

        $analyzer = new LogAnalyzerService();
        $this->analysisResult = $analyzer->analyze($logContent);

        Notification::make()
            ->title('Analysis complete')
            ->success()
            ->send();
    }
}
