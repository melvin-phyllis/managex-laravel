<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MistralService
{
    protected string $apiKey;

    protected string $baseUrl;

    protected string $model;

    protected int $maxTokens;

    protected float $temperature;

    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = config('ai.mistral.api_key', '');
        $this->baseUrl = config('ai.mistral.base_url', 'https://api.mistral.ai/v1');
        $this->model = config('ai.mistral.model', 'mistral-small-latest');
        $this->maxTokens = config('ai.mistral.max_tokens', 2048);
        $this->temperature = config('ai.mistral.temperature', 0.3);
        $this->timeout = config('ai.mistral.timeout', 30);
    }

    /**
     * Vérifier si le service est configuré et disponible.
     */
    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }

    /**
     * Envoyer une requête de chat à l'API Mistral.
     *
     * @param  string  $system  Prompt système
     * @param  array  $messages  Historique [{role: 'user'|'assistant', content: '...'}]
     * @return string|null Réponse de l'IA ou null en cas d'erreur
     */
    public function chat(string $system, array $messages): ?string
    {
        if (! $this->isAvailable()) {
            Log::warning('MistralService: clé API non configurée');

            return null;
        }

        $payload = [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
            'messages' => array_merge(
                [['role' => 'system', 'content' => $system]],
                $messages
            ),
        ];

        $startTime = microtime(true);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout($this->timeout)
                ->post($this->baseUrl.'/chat/completions', $payload);

            $duration = round((microtime(true) - $startTime) * 1000);

            if ($response->failed()) {
                $status = $response->status();
                $body = $response->body();

                Log::error('MistralService: erreur API', [
                    'status' => $status,
                    'body' => mb_substr($body, 0, 500),
                    'duration_ms' => $duration,
                ]);

                return null;
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            Log::info('MistralService: requête réussie', [
                'model' => $this->model,
                'duration_ms' => $duration,
                'tokens_prompt' => $data['usage']['prompt_tokens'] ?? 0,
                'tokens_completion' => $data['usage']['completion_tokens'] ?? 0,
            ]);

            return $content;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('MistralService: timeout ou connexion échouée', [
                'message' => $e->getMessage(),
                'duration_ms' => round((microtime(true) - $startTime) * 1000),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('MistralService: erreur inattendue', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
