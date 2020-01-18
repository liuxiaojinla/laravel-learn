<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class Handler extends ExceptionHandler{

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
     * @param \Exception $exception
     * @throws \Exception
     */
    public function report(Exception $exception){
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function render($request, Exception $e){
        if($e instanceof HttpJumpException){
            return $this->httpJumpHandle($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * 跳转异常处理
     *
     * @param \Illuminate\Http\Request          $request
     * @param \App\Exceptions\HttpJumpException $e
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    private function httpJumpHandle(Request $request, HttpJumpException $e){
        $baseData = [
            'msg'  => $e->getMessage(),
            'code' => $e->getCode(),
        ];

        $isApi = $request->route()->computedMiddleware;
        if($request->ajax() || ($isApi && isset($isApi[0]) && $isApi[0] == 'api')){
            $data = &$e->getData();
            if($data['data'] instanceof LengthAwarePaginator){
                $paginator = $data['data'];
                $data = array_merge($data, $paginator->toArray());
            }
            return response()->json(array_merge($baseData, $data));
        }elseif($request->has('_jsonp')){
            $data = &$e->getData();
            if($data['data'] instanceof LengthAwarePaginator){
                $paginator = $data['data'];
                $data = array_merge($data, $paginator->toArray());
            }
            return response()->jsonp($request->query('_jsonp'), array_merge($baseData, $e->getData()));
        }else{
            return view('layouts.jump', $baseData);
        }
    }
}
