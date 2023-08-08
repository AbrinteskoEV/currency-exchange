<?php

declare(strict_types=1);

namespace Application\Http\Middleware;

use Application\Exceptions\ApplicationException;
use Application\Exceptions\BaseException;
use Application\Exceptions\ValidationException;
use Application\Http\Formatter\ResponseFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MainMiddleware
{
    private ResponseFormatter $responseFormatter;

    /**
     * @param ResponseFormatter $responseFormatter
     */
    public function __construct(
        ResponseFormatter $responseFormatter,
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        $request->headers->set('Accept', '*/*');

        $startTime = microtime(true);
        $response = $next($request);
        $endTime = microtime(true);

        $executionTime = $endTime - $startTime;

        if ($response instanceof Response) {
            $exception = $response->exception;

            if ($exception) {
                $response = $this->returnErrorResponse(
                    $request,
                    $exception,
                    $executionTime
                );
            } elseif (is_array($response->original)) {
                $response = $this->responseFormatter->format(
                    true,
                    $response->original,
                    $response->getStatusCode(),
                    $executionTime
                );
            }
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param \Throwable $exception
     * @param float $executionTime
     *
     * @return JsonResponse|null
     */
    private function returnErrorResponse(Request $request, \Throwable $exception, float $executionTime): ?JsonResponse
    {
        switch ($exception instanceof \Throwable) {
            case $exception instanceof ValidationException:
                /** @var ValidationException $exception  */
                $response = $this->responseFormatter->format(
                    false,
                    $this->formatValidationErrorData($exception->getMessage(), $exception->getContext()),
                    $exception->getCode(),
                    $executionTime
                );
                break;

            case $exception instanceof ApplicationException:
                /** @var $exception ApplicationException */

                $message = $exception->getMessage();

                $response = $this->responseFormatter->format(
                    false,
                    $this->formatErrorResponseData($message),
                    $exception->getCode(),
                    $executionTime
                );
                break;

            case $exception instanceof BaseException:
                /** @var $exception BaseException */

                $response = $this->responseFormatter->format(
                    false,
                    $this->formatErrorResponseData('Internal server error'),
                    $exception->getCode(),
                    $executionTime
                );
                break;

            default:
                $response = $this->responseFormatter->format(
                    false,
                    $this->formatErrorResponseData('Internal server error'),
                    $exception->getCode(),
                    $executionTime
                );
        }

        return $response;
    }

    /**
     * @param string $message
     *
     * @return array
     */
    private function formatErrorResponseData(string $message): array
    {
        return [
            'message' => $message,
        ];
    }

    /**
     * @param string $message
     * @param array $errors
     *
     * @return array
     */
    private function formatValidationErrorData(string $message, array $errors): array
    {
        return [
            'message' => $message,
            'errors' => $errors,
        ];
    }
}
