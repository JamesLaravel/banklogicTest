<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
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

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
          
        });
    }

     /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {

         if ($exception instanceof AuthorizationException) {


            if (($exception->getCode() == 403 || $exception->getCode() == 0) && getenv("APP_DEBUG")) {

                return response()->json([
                    "message" => "Unauthorised",
                    "status" => 0,
                    "status_code" => 403
                ], 403);
            }
        }

        if ($exception instanceof MethodNotAllowedHttpException && getenv("APP_DEBUG")) {
            return response()->json([
                "message" => $exception->getMessage(),
                "status" => 0,
                "status_code" => 405
            ], 405);
        }

        if($exception instanceof QueryException && getenv("APP_DEBUG")){
            return response()->json([
                "message"=> $exception->getMessage(),
                "status"=>0,
                "status_code"=> 500
            ], 500);
        }



        return parent::render($request, $exception);
    }
    
}
