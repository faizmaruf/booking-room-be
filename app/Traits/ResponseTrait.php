<?php

namespace App\Traits;

trait ResponseTrait
{
    /**
     * Format the API response.
     *
     * @param int $statusCode
     * @param string $message
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function formatResponse($statusCode, $message, $data = null)
    {
        $response = [
            'code'    => $statusCode,
            'status'  => $this->getStatusText($statusCode),
            'message' => $message,
            'data'    => $data
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Get the textual representation of the HTTP status code.
     *
     * @param int $statusCode
     * @return string
     */
    protected function getStatusText($statusCode)
    {
        $statusTexts = [
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
        ];

        return $statusTexts[$statusCode] ?? 'Unknown Status';
    }
}
