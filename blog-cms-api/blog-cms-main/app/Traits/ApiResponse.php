<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponse
{
    public function retrieveResponse($status = true, $message = null, mixed $data = null, $code = Response::HTTP_OK)
    {
        return $this->responseHandle(status: $status, code: $code, message: $message, data: $data);
    }

    private function responseHandle(bool $status, int $code, ?string $message = null, mixed $data = null)
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data ?? $this->response()->getData(true),
        ];
        return response($response, $code);
    }
}
