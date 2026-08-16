<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiProductAssistant
{
    public function parseProduct(string $instruction, array $categories = []): array
    {
        $key = trim((string) config('services.openai.key'));
        if ($key === '') {
            throw new RuntimeException('OPENAI_API_KEY is not configured on the server.');
        }

        $isOpenRouter = str_starts_with($key, 'sk-or-');
        $baseUrl = config('services.openai.base_url');
        $categoryList = !empty($categories) ? implode(', ', $categories) : 'General';
        $systemPrompt = "You are a retail catalog assistant for Indian shop owners. Extract only facts stated by the owner. Do not invent price, weight, purity, material or stock. Use concise customer-friendly Hinglish/English. Available categories: {$categoryList}. Return valid JSON with keys: name (string), description (string), category (string or null), price (number or null), offer_price (number or null), price_visible (boolean), in_stock (boolean), featured (boolean), tags (array of strings), whatsapp_text (string), social_caption (string).";

        if ($isOpenRouter || $baseUrl) {
            $endpoint = $baseUrl ?: 'https://openrouter.ai/api/v1/chat/completions';
            $configuredModel = config('services.openai.model');
            $model = ($configuredModel && $configuredModel !== 'gpt-5-mini') ? $configuredModel : 'openai/gpt-4o-mini';

            $headers = [
                'Authorization' => "Bearer {$key}",
                'HTTP-Referer' => config('app.url', 'https://pocketshowroom.com'),
                'X-Title' => config('app.name', 'Pocket Showroom'),
            ];

            $response = Http::timeout(30)->withHeaders($headers)->post($endpoint, [
                'model' => $model,
                'max_tokens' => 1200,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $instruction],
                ],
            ]);

            if (!$response->successful()) {
                throw new RuntimeException('AI service error: ' . $response->status() . ' ' . $response->body());
            }

            $json = $response->json();
            $content = data_get($json, 'choices.0.message.content');
            $decoded = is_string($content) ? json_decode($content, true) : null;
            if (!is_array($decoded)) {
                throw new RuntimeException('AI returned an unreadable product draft.');
            }
            return $this->normalizeProductDraft($decoded);
        }

        // Direct OpenAI API fallback
        $schema = [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'name' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'category' => ['type' => ['string', 'null']],
                'price' => ['type' => ['number', 'null']],
                'offer_price' => ['type' => ['number', 'null']],
                'price_visible' => ['type' => 'boolean'],
                'in_stock' => ['type' => 'boolean'],
                'featured' => ['type' => 'boolean'],
                'tags' => ['type' => 'array', 'items' => ['type' => 'string']],
                'whatsapp_text' => ['type' => 'string'],
                'social_caption' => ['type' => 'string'],
            ],
            'required' => ['name', 'description', 'category', 'price', 'offer_price', 'price_visible', 'in_stock', 'featured', 'tags', 'whatsapp_text', 'social_caption'],
        ];

        $response = Http::timeout(30)->withToken($key)->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $instruction],
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'product_draft',
                    'strict' => true,
                    'schema' => $schema,
                ],
            ],
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('AI service error: ' . $response->status() . ' ' . $response->body());
        }

        $json = $response->json();
        $content = data_get($json, 'choices.0.message.content');
        $decoded = is_string($content) ? json_decode($content, true) : null;
        if (!is_array($decoded)) {
            throw new RuntimeException('AI returned an unreadable product draft.');
        }

        return $this->normalizeProductDraft($decoded);
    }

    private function normalizeProductDraft(array $data): array
    {
        return [
            'name' => (string) ($data['name'] ?? ''),
            'description' => (string) ($data['description'] ?? ''),
            'category' => isset($data['category']) ? (string) $data['category'] : null,
            'price' => isset($data['price']) && is_numeric($data['price']) ? (float) $data['price'] : null,
            'offer_price' => isset($data['offer_price']) && is_numeric($data['offer_price']) ? (float) $data['offer_price'] : null,
            'price_visible' => (bool) ($data['price_visible'] ?? true),
            'in_stock' => (bool) ($data['in_stock'] ?? true),
            'featured' => (bool) ($data['featured'] ?? false),
            'tags' => is_array($data['tags'] ?? null) ? array_values(array_map('strval', $data['tags'])) : [],
            'whatsapp_text' => (string) ($data['whatsapp_text'] ?? ''),
            'social_caption' => (string) ($data['social_caption'] ?? ''),
        ];
    }
}
