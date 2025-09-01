<?php

use Illuminate\Support\Facades\Http;

if (! function_exists('sendWhatsappNotification')) {
    function sendWhatsappNotification(string $number, string $message, array $options = []): array
    {
        $token = env('WHATSAPP_TOKEN');

        $payload = array_merge([
            'target'  => preg_replace('/\D/', '', $number),
            'message' => $message,
        ], $options);

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->asForm()->post('https://api.fonnte.com/send', $payload);

        return [
            'ok'     => $response->successful(),
            'status' => $response->status(),
            'data'   => $response->json(),
        ];
    }
}
