<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Services\Uploader\UploadManager;
use Illuminate\Http\File;

class UploadController extends Controller
{
    public function index()
    {
        $manager = new UploadManager(app('filesystem'), config('upload'));
        $result = $manager->file('image', new File(public_path('00.png')));
        // $result = $manager->token('image', 'helloworld.png');
        dd($result);
    }
}