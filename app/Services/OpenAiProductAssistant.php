<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiProductAssistant
{
    public function parseProduct(string $instruction, array $categories = []): array
    {
        $key = trim((string) config('services.openai.key'));
        if ($key === '') throw new RuntimeException('OPENAI_API_KEY is not configured on the server.');

        $schema = [
            'type'=>'object','additionalProperties'=>false,
            'properties'=>[
                'name'=>['type'=>'string'], 'description'=>['type'=>'string'],
                'category'=>['type'=>['string','null']], 'price'=>['type'=>['number','null']],
                'offer_price'=>['type'=>['number','null']], 'price_visible'=>['type'=>'boolean'],
                'in_stock'=>['type'=>'boolean'], 'featured'=>['type'=>'boolean'],
                'tags'=>['type'=>'array','items'=>['type'=>'string']],
                'whatsapp_text'=>['type'=>'string'], 'social_caption'=>['type'=>'string'],
            ],
            'required'=>['name','description','category','price','offer_price','price_visible','in_stock','featured','tags','whatsapp_text','social_caption'],
        ];

        $response = Http::timeout(30)->withToken($key)->post('https://api.openai.com/v1/responses', [
            'model'=>config('services.openai.model','gpt-5-mini'),
            'input'=>[[
                'role'=>'system','content'=>[[
                    'type'=>'input_text','text'=>'You are a retail catalog assistant for Indian shop owners. Extract only facts stated by the owner. Do not invent price, weight, purity, material or stock. Use concise customer-friendly Hinglish/English. Available categories: '.implode(', ', $categories)
                ]]
            ],[
                'role'=>'user','content'=>[['type'=>'input_text','text'=>$instruction]]
            ]],
            'text'=>['format'=>['type'=>'json_schema','name'=>'product_draft','strict'=>true,'schema'=>$schema]],
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('AI service error: '.$response->status().' '.$response->body());
        }
        $json=$response->json();
        $text=data_get($json,'output.0.content.0.text') ?? data_get($json,'output_text');
        $decoded=is_string($text)?json_decode($text,true):null;
        if(!is_array($decoded)) throw new RuntimeException('AI returned an unreadable product draft.');
        return $decoded;
    }
}
