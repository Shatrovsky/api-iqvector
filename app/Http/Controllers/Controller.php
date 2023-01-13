<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Отправляет ответ
     * @param mixed $result ответ
     * @param string $message описание ответа
     * @return JsonResponse
     */
    public function sendResponse($result, $message = null)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        if (defined('LARAVEL_START') && config('app.debug')) {
            $response['profiling']['response_time'] = (float)sprintf('%01.4f', microtime(true) - LARAVEL_START);
        }

        return response()->json($response, 200);
    }

    /**
     * Отправляет ответ-коллекцию с метаинформацией о паджинации результатов
     *
     * @param ResourceCollection $collection ответ с коллекцией данных
     * @param string $message описание ответа
     * @return ResourceCollection
     */
    public function sendPaginatedCollectionResponse(ResourceCollection $collection, $message = null)
    {
        $additional = [
            'success' => true,
            'message' => $message,
        ];

        if (defined('LARAVEL_START') && config('app.debug')) {
            $additional['profiling']['response_time'] = (float)sprintf('%01.4f', microtime(true) - LARAVEL_START);
        }

        return $collection->additional($additional);
    }

    /**
     * Отправляет ошибку
     * @param string $error сообщение о ошибке
     * @param array $errorMessages массив ошибок
     * @param integer $code код ошибки
     * @return JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }
}
