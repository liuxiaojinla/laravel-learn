<?php

namespace App\Http\Controllers;

use App\Traits\FrontPage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class BaseHomeController extends BaseController{

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests, FrontPage;
}
