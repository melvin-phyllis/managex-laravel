<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mistral AI Configuration
    |--------------------------------------------------------------------------
    */

    'mistral' => [
        'api_key' => env('MISTRAL_API_KEY'),
        'base_url' => env('MISTRAL_BASE_URL', 'https://api.mistral.ai/v1'),
        'model' => env('MISTRAL_MODEL', 'mistral-small-latest'),
        'max_tokens' => (int) env('MISTRAL_MAX_TOKENS', 1024),
        'temperature' => (float) env('MISTRAL_TEMPERATURE', 0.3),
        'timeout' => (int) env('MISTRAL_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache TTL pour les insights analytics (en secondes)
    |--------------------------------------------------------------------------
    */

    'cache_ttl' => (int) env('AI_CACHE_TTL', 900),

];
