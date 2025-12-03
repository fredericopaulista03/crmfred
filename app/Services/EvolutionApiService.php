<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class EvolutionApiService
{
    protected $baseUrl;
    protected $token;
    protected $instance;

    public function __construct()
    {
        // Load from database settings instead of config
        $this->baseUrl = Setting::get('evolution_api_url', config('services.evolution.url'));
        $this->token = Setting::get('evolution_api_token', config('services.evolution.token'));
        $this->instance = Setting::get('evolution_instance_name', config('services.evolution.instance'));
    }

    public function sendText($number, $text)
    {
        return Http::withHeaders([
            'apikey' => $this->token,
        ])->post("{$this->baseUrl}/message/sendText/{$this->instance}", [
            'number' => $number,
            'options' => [
                'delay' => 1200,
                'presence' => 'composing',
                'linkPreview' => false
            ],
            'textMessage' => [
                'text' => $text
            ]
        ]);
    }

    public function sendMedia($number, $mediaUrl, $mediaType, $caption = '')
    {
        // Implementation for media sending
        // Endpoint might vary based on Evolution API version
        return Http::withHeaders([
            'apikey' => $this->token,
        ])->post("{$this->baseUrl}/message/sendMedia/{$this->instance}", [
            'number' => $number,
            'options' => [
                'delay' => 1200,
                'presence' => 'composing'
            ],
            'mediaMessage' => [
                'mediatype' => $mediaType,
                'caption' => $caption,
                'media' => $mediaUrl
            ]
        ]);
    }
}
