<?php

namespace App\Http\Controllers;

use App\Foundation\Hint\InteractsWithHint;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * @property-read Request $request
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, InteractsWithHint;

    /**
     * @var Request
     */
    protected $request;

    /**
     *
     */
    public function __construct()
    {
        $this->request = app('request');
    }
}
