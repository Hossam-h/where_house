<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Queue\EntityNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Exception $exception,Request $request) {

            if ($request->is('api/*')) {
           
       
                if($exception->getPrevious()){
                    if ($exception->getPrevious() instanceof ModelNotFoundException) {
                        $model = strtolower(class_basename($exception->getPrevious()->getModel()));
                        return returnError("Does not exist any instance of {$model} with the given id", Response::HTTP_NOT_FOUND);
                    }
                }
                


                if ($exception instanceof HttpException) {
                    $code = $exception->getStatusCode();
                    $message = Response::$statusTexts[$code];
                    return returnError($message, $code);
                }

                if ($exception instanceof ModelNotFoundException) {
                    $model = strtolower(class_basename($exception->getModel()));
                    return returnError("Does not exist any instance of {$model} with the given id", Response::HTTP_NOT_FOUND);
                }
        
                if ($exception instanceof AuthorizationException) {
                    return returnError($exception->getMessage(), Response::HTTP_FORBIDDEN);
                }
        
                if ($exception instanceof AuthenticationException) {
                    return returnError($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
                }
        
                if ($exception instanceof ValidationException) {

                    $errors = is_string($exception->validator) ? $exception->validator : $exception->validator->errors()->first();
                    return returnError($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                if ($exception instanceof EntityNotFoundException) {
                    return returnError($exception->getMessage(), Response::NOT_FOUND);
                }
        
                if ($exception instanceof ClientException) {
                    $message = $exception->getResponse()->getBody();
                    $code = $exception->getCode();
                    return returnError($message, $code);
        
                }
        
                if (env('APP_DEBUG', true)) {
                    return returnError($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);                
                }


                
            }
        });
    }
}
