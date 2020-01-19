<?php

namespace App\Http\Controllers\Home;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class BaseController extends Controller{

    // use AuthorizesRequests, DispatchesJobs;
    use ValidatesRequests;
}
