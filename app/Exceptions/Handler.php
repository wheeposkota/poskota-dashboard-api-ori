<?php

namespace App\Exceptions;

use RuntimeException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Predis\Connection\ConnectionException as RedisConnectionException;
use Illuminate\Database\QueryException as DatabaseConnectionException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        MemberNotVerified::class,
        MemberNotSubs::class,
        ModelNotFoundException::class,
        NotFoundHttpException::class,
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson() || strpos($request->path(), 'api') !== false)
            return $this->renderExceptionAsJson($request, $exception);

        return parent::render($request, $exception);
    }

    protected function renderExceptionAsJson($request, Exception $exception)
    {
        // Currently converts AuthorizationException to 403 HttpException
        // and ModelNotFoundException to 404 NotFoundHttpException
        $exception = $this->prepareException($exception);
        // Default response

        $status = 400;
        // Build correct status codes and status texts
        switch ($exception) {
            case $exception instanceof ModelNotFoundException:
            case $exception instanceof NotFoundHttpException:
                $status = 404;
                $message = 'Data not found';
                break;
            case $exception instanceof \Illuminate\Validation\ValidationException:
                $status = 422;
                $message = $exception->validator->errors()->first();
                break;
            case $exception instanceof \Illuminate\Auth\AuthenticationException:
                $status = 401;
                $message = 'Session expired';
                break;
            case $exception instanceof MethodNotAllowedHttpException:
                $status = 405;
                $message = 'Method ' . strtolower($request->method()) . ' not allowed';
                break;
            case $exception instanceof RedisConnectionException:
                $status = 500;
                $message = 'Cache service down';
                if (true)
                    $message .= ": ".$exception->getMessage();
                break;
            case $exception instanceof DatabaseConnectionException:
                $status = 500;
                $message = 'Database problem';
                if (true)
                    $message .= ": ".$exception->getMessage();
                break;
            case $exception instanceof FileNotFoundException:
                $status = 404;
                $message = 'File not found';
                if (true)
                    $message .= ": ".$exception->getMessage();
                break;
            case $exception instanceof MemberNotVerified:
                $status = 400;
                $message = "Member not verified";
                break;
            case $exception instanceof MemberNotSubs:
                $status = 400;
                $message = "Member not subscribe to ePaper";
                break;
            case $exception instanceof RuntimeException:
                $status = 400;
                $message = $exception->getMessage();
                break;
            case $this->isHttpException($exception):
                $status = $exception->getStatusCode();
                $message = $exception->getMessage();
                break;
            default:
                $status = 500;
                $message = 'error on -> ' . $exception->getMessage() . ', file -> ' . $exception->getFile() . ', line -> ' . $exception->getLine();
                break;
        }

        $response = [
            'success' => false,
            'data' => [],
            'message' => $message,
            'code' => $status
        ];

        if (true)
            $response['payload'] = [
                'class' => class_basename($exception),
                'file' => basename($exception->getFile()),
                'line' => $exception->getLine(),
                'request' => $request->all()
            ];

        return response()->json($response, $status);
    }
}
