<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Services\Canvas\Canvas;
use App\Services\Canvas\Color;
use App\Services\Canvas\Shapes\Image;
use App\Services\Canvas\Texts\Text;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function app;

class CanvasController extends Controller
{
    public function index()
    {
        $canvas = new Canvas(300, 300, new Color(0, 255, 0, 0.1));
        $canvas->draw(Image::fromPath('https://fanyi-cdn.cdn.bcebos.com/static/translation/img/header/logo_e835568.png'));
        $canvas->save('00.png');
        $canvas->draw(Text::from("Hello World", 0, 0));
        $canvas->save('00.png');
    }

    public function __invoke($action)
    {
        if (method_exists($this, $action)) {
            return app()->call([$this, $action]);
        }

        throw new NotFoundHttpException("Bad method $action.");
    }
}
