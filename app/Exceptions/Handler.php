<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use App\Exceptions\Custom\ConflictException;
use App\Exceptions\Custom\NotFoundException;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\Custom\BadRequestException;
use Illuminate\Validation\ValidationException;
use App\Exceptions\Custom\UnprocessableException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
        'current_password',
        'password',
        'password_confirmation',
    ];

    private function error ($path, Throwable $exception, $message, $code) {
        return response()->json([
            'timestamps' => date('Y-m-d H:i:s'),
            'path' => $path,
            'exception' =>  basename(str_replace('\\', '/', get_class($exception))),
            'detail' => $message,
            'code' => $code
        ], $code);
	}

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $exception, $request) {
            if($request->is('api/*')) {
                if ($exception instanceof ModelNotFoundException) {
                    $model = strtolower(class_basename($exception->getModel()));
                    return $this->error($request->getPathInfo(), $exception,
                        __('messages.no-exist-instance', ['model' => $model]), Response::HTTP_NOT_FOUND);
                }

                if ($exception instanceof NotFoundHttpException) {
                    $code = $exception->getStatusCode();
                    return $this->error($request->getPathInfo(), $exception, __('messages.not-found'), $code);
                }

                if ($exception instanceof MethodNotAllowedHttpException) {
                    return $this->error($request->getPathInfo(), $exception, __('messages.method-not-allowed'), Response::HTTP_METHOD_NOT_ALLOWED);
                }

                if ($exception instanceof HttpException) {
                    $code = $exception->getStatusCode();
                    return $this->error($request->getPathInfo(), $exception, __('messages.method-not-allowed'), $code);
                }

                if ($exception instanceof AuthenticationException) {
                    return $this->error($request->getPathInfo(), $exception,
                        __('messages.no-authorize'), Response::HTTP_UNAUTHORIZED);
                }

                if ($exception instanceof AuthorizationException) {
                    return $this->error($request->getPathInfo(), $exception,
                        __('messages.forbidden'), Response::HTTP_FORBIDDEN);
                }

                if ($exception instanceof ValidationException) {
                    $errors = $exception->validator->errors()->getMessages();

                    return $this->error($request->getPathInfo(), $exception,
                        $errors, Response::HTTP_BAD_REQUEST);
                }

                if ($exception instanceof UnprocessableException
                    || $exception instanceof ConflictException
                    || $exception instanceof BadRequestException
                    || $exception instanceof NotFoundException
                    ) {

                    $code = $exception->getStatusCode();
                    $message = $exception->getMessage();
                    return $this->error($request->getPathInfo(), $exception, $message, $code);
                }

                if (config('app.debug')) {
                    //return parent::render($request, $exception);
                    return $this
                        ->error($request->getPathInfo(), $exception, $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                return $this->error($request->getPathInfo(), $exception, __('messages.internal-server-error'), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }
}
