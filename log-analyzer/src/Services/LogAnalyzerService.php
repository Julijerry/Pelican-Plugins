<?php

namespace Julijerry\LogAnalyzer\Services;

use Illuminate\Support\Facades\Http;

class LogAnalyzerService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('log-analyzer.gemini_api_key');
    }

    public function analyze(string $logContent): string
    {
        if (empty($this->apiKey)) {
            return 'Gemini API key is not configured.';
        }

        $prompt = "Analyze the following Minecraft server log. If a crash or error is found, summarize the cause and give a concrete solution in max 5 short sentences. If no crash is found, say so in one sentence. Be brief and direct.\n\n" . $logContent;

        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $this->apiKey;
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->failed()) {
            return 'Failed to analyze the log: ' . $response->body();
        }

        $result = $response->json('candidates.0.content.parts.0.text', 'No suggestion found.');
        // Optional: kürze die Antwort auf 800 Zeichen
        if (mb_strlen($result) > 800) {
            $result = mb_substr($result, 0, 800) . '...';
        }
        return $result;
    }
}
