<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $e)
    {
        $e = $this->prepareException($e);

        if ($e instanceof NotFoundHttpException) {
            $message = $e->getMessage();
            $modelNotFoundRegex = 'No query results for model \[(.*)\]\s?(.*)?';
            $matches = [];
            mb_eregi($modelNotFoundRegex, $message, $matches);
            if (!empty($matches)) {
                $className = substr($matches[1], strrpos($matches[1], '\\') + 1);
                if ($matches[2] !== '.') {
                    $message = 'Not found ' . $className . ' ##' . $matches[2];
                } else {
                    $message = $className . ' not found';
                }
            } else {
                $message = 'Resource not found';
            }

            return response()->json(['success' => false, 'message' => $message], $e->getStatusCode());
        }
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }
        if ($e instanceof AuthenticationException) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        }
        if ($e instanceof AuthorizationException) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }
        if ($e instanceof ValidationException) {
            return $this->invalidJson($request, $e);
        }
        if ($e instanceof DeleteException) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors], 400);
        }

        return $this->prepareJsonResponse($request, $e);
    }

    /**
     * @inheritdoc
     */
    protected function convertExceptionToArray(Exception $e)
    {
        return config('app.debug') ?
            [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]
            : ['message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error'];
    }
}
