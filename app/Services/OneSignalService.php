<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    protected string $appId;

    protected string $apiKey;

    protected string $apiUrl = 'https://api.onesignal.com/notifications';

    public function __construct()
    {
        $this->appId = config('services.onesignal.app_id', '');
        $this->apiKey = config('services.onesignal.rest_api_key', '');
    }

    /**
     * Send a push notification to a specific user via their external_user_id (= Laravel user ID).
     */
    public function sendToUser(int|string $userId, string $title, string $body, array $data = [], ?string $url = null): bool
    {
        return $this->send([
            'include_aliases' => [
                'external_id' => [(string) $userId],
            ],
            'target_channel' => 'push',
            'headings' => ['en' => $title],
            'contents' => ['en' => $body],
            'data' => $data,
            'url' => $url,
            'chrome_web_icon' => asset('icons/icon-192x192.png'),
            'chrome_web_badge' => asset('icons/icon-72x72.png'),
        ]);
    }

    /**
     * Send a push notification to multiple users.
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = [], ?string $url = null): bool
    {
        $stringIds = array_map('strval', $userIds);

        return $this->send([
            'include_aliases' => [
                'external_id' => $stringIds,
            ],
            'target_channel' => 'push',
            'headings' => ['en' => $title],
            'contents' => ['en' => $body],
            'data' => $data,
            'url' => $url,
            'chrome_web_icon' => asset('icons/icon-192x192.png'),
            'chrome_web_badge' => asset('icons/icon-72x72.png'),
        ]);
    }

    /**
     * Send a push notification to all subscribed users.
     */
    public function sendToAll(string $title, string $body, array $data = [], ?string $url = null): bool
    {
        return $this->send([
            'included_segments' => ['Subscribed Users'],
            'headings' => ['en' => $title],
            'contents' => ['en' => $body],
            'data' => $data,
            'url' => $url,
            'chrome_web_icon' => asset('icons/icon-192x192.png'),
            'chrome_web_badge' => asset('icons/icon-72x72.png'),
        ]);
    }

    /**
     * Send the notification via OneSignal REST API.
     */
    protected function send(array $payload): bool
    {
        if (empty($this->appId) || empty($this->apiKey)) {
            Log::warning('[OneSignal] Missing app_id or rest_api_key in config.');

            return false;
        }

        $payload['app_id'] = $this->appId;

        try {
            $response = Http::withHeaders([
                'Authorization' => "Key {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, $payload);

            if ($response->successful()) {
                Log::info('[OneSignal] Notification sent successfully.', [
                    'recipients' => $response->json('recipients', 0),
                ]);

                return true;
            }

            Log::error('[OneSignal] Failed to send notification.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('[OneSignal] Exception while sending notification.', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
