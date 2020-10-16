<?php

namespace App\Exceptions;

use Exception;
use BadMethodCallException;
use ErrorException;
use InvalidArgumentException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if($e instanceof BadMethodCallException) {
            return view('errors.re', ['e' => $e ]);
        } else if($e instanceof QueryException) {
            return view('errors.index', ['e' => $e ]);
        } else if($e instanceof InvalidArgumentException) {
            return view('errors.index', ['e' => $e ]);
        } else if ($e instanceof \Swift_TransportException){
            return view('errors.InternetConnection', ['e' => $e ]);
        } else if ($e instanceof ErrorException){
            return response()->view('errors.500', ['e' => $e], 500);
        } else {
            return parent::render($request, $e);
        }
    }
}
