<?php

namespace Julijerry\LogAnalyzer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LogAnalyzerService
{
    protected string $provider;
    protected ?string $apiKey;
    protected ?string $baseUrl;

    public function __construct()
    {
        $this->provider = config('log-analyzer.provider', 'gemini');
        $this->apiKey = config('log-analyzer.api_key') ?: config('log-analyzer.gemini_api_key');
        $this->baseUrl = rtrim((string) config('log-analyzer.base_url', 'https://api.openai.com/v1'), '/');
    }

    public function analyze(string $logContent, ?string $model = null): string
    {
        if (empty($this->apiKey)) {
            return 'API key is not configured.';
        }

        $modelToUse = $this->resolveModel($model);
        if ($modelToUse === '') {
            return 'No AI model is configured.';
        }

        $prompt = "Analyze the following server log. If a crash or error is found, summarize the cause and give a concrete solution in max 5 short sentences. If no crash is found, say so in one sentence. Be brief and direct.\n\n" . $logContent;

        return match ($this->provider) {
            'gemini' => $this->analyzeWithGemini($prompt, $modelToUse),
            'openai', 'openai_compatible' => $this->analyzeWithOpenAI($prompt, $modelToUse),
            'anthropic' => $this->analyzeWithAnthropic($prompt, $modelToUse),
            default => "Unsupported AI provider: {$this->provider}",
        };
    }

    public function resolveModel(?string $model = null): string
    {
        $selected = $model ?? config('log-analyzer.model', 'gemini-3.5-flash');

        if ($selected === 'custom' || $this->provider === 'openai_compatible') {
            return (string) (config('log-analyzer.custom_model') ?: '');
        }

        return (string) $selected;
    }

    protected function analyzeWithGemini(string $prompt, string $model): string
    {
        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent?key=' . $this->apiKey;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ]);

        if ($response->failed()) {
            return 'Failed to analyze the log: ' . $response->body();
        }

        return $this->truncate($response->json('candidates.0.content.parts.0.text', 'No suggestion found.'));
    }

    protected function analyzeWithOpenAI(string $prompt, string $model): string
    {
        $baseUrl = $this->provider === 'openai'
            ? 'https://api.openai.com/v1'
            : $this->baseUrl;

        $response = Http::withToken($this->apiKey)
            ->acceptJson()
            ->post("{$baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.2,
            ]);

        if ($response->failed()) {
            return 'Failed to analyze the log: ' . $response->body();
        }

        return $this->truncate($response->json('choices.0.message.content', 'No suggestion found.'));
    }

    protected function analyzeWithAnthropic(string $prompt, string $model): string
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $model,
            'max_tokens' => 1024,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        if ($response->failed()) {
            return 'Failed to analyze the log: ' . $response->body();
        }

        $parts = $response->json('content', []);
        $text = collect($parts)
            ->where('type', 'text')
            ->pluck('text')
            ->filter()
            ->implode("\n");

        return $this->truncate($text !== '' ? $text : 'No suggestion found.');
    }

    protected function truncate(string $result): string
    {
        if (Str::length($result) > 800) {
            return Str::substr($result, 0, 800) . '...';
        }

        return $result;
    }
}
