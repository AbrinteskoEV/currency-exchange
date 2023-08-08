<?php

namespace Application\Http\Formatter;

use Illuminate\Http\JsonResponse;

class ResponseFormatter
{
    /**
     * @param bool $successStatus
     * @param array $response
     * @param int|string $code
     * @param float $executionTime
     *
     * @return JsonResponse
     */
    public function format(bool $successStatus, array $response, int|string $code, float $executionTime)
    {
        $data = [
            'success' => $successStatus,
            'code' => $code === 0 ? 200 : $code,
            'time' => (new \DateTimeImmutable('now'))->format('Y:m:d H:i:s'),
            'executionTime' => $executionTime,
            'data' => $response,
        ];

        return new JsonResponse($data);
    }
}
