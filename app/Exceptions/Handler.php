<?php

namespace App\Exceptions;

use App\Enums\Http\ResponseCode;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;
use App\Traits\HttpResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use HttpResponseTrait;

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
     * @param Throwable $exception
     * @return void
     *
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * 重写未授权方法
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Application|ResponseFactory|\Illuminate\Http\Response|Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response($this->type(ResponseCode::TOKEN_EXPIRED)->httpResponse());
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        // 改写找不到对象的数据返回类型
        if (!app()->environment('local')) {
            if ($exception instanceof NotFoundHttpException) {
                return response($this->type(ResponseCode::QUERY_VOID)->httpResponse($request->all(), '找不到页面'));
            }
            if ($exception instanceof ModelNotFoundException) {
                return response($this->type(ResponseCode::QUERY_VOID)->httpResponse($request->all(), '查询对象不存在'));
            }
            if ($exception instanceof TooManyRequestsHttpException) {
                return response($this->type(ResponseCode::TOO_MANY_REQUEST)->httpResponse($request->all()));
            }
        }
        return parent::render($request, $exception);
    }
}
