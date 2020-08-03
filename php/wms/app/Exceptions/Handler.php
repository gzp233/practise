<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        UnauthorizedHttpException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        // 
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
        // 参数验证错误的异常，我们需要返回 400 的 http code 和一句错误信息
        if ($exception instanceof ValidationException) {
            // 数据验证报错
            foreach ($exception->errors() as $error) {
                return response()->json(['code' => 402, 'message' => $error[0]]);   
            }
        }
        // 用户认证的异常，我们需要返回 401 的 http code 和错误信息
        if ($exception instanceof UnauthorizedHttpException) {
            return response()->json(['code' => 401, 'message' => '未授权，请重新登录']);   
        }

        $code = $exception->getCode() == 0 ? 500 : $exception->getCode();
        $msg = sprintf("%s:%s:%s", $exception->getFile(), $exception->getLine(), $exception->getMessage()); 
        
        return response()->json(array('code' => $code, 'message' => $msg));
    }
}
