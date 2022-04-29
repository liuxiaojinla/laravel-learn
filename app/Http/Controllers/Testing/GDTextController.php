<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use GDText\Box;
use GDText\Color;

class GDTextController extends Controller
{
    public function index()
    {
        $im = imagecreatetruecolor(500, 500);
        $backgroundColor = imagecolorallocate($im, 0, 18, 64);
        imagefill($im, 0, 0, $backgroundColor);

        $box = new Box($im);
        $box->setFontFace(public_path('simhei.ttf')); // http://www.dafont.com/minecraftia.font
        $box->setFontColor(new Color(255, 75, 140));
        $box->setTextShadow(new Color(0, 0, 0, 50), 2, 2);
        $box->setFontSize(16);
        $box->setLineHeight(1.5);
        //$box->enableDebug();
        $box->setBox(20, 20, 460, 460);
        $box->setTextAlign('left', 'top');
        $text = '白在这个现实世界中是永远无法被明确的，我们可能感觉自己接触到了白，但那只是一种幻觉。在这个现实世界中，白总是被污染的、不纯的。它只是一道痕迹，一个指向其本源的标志而已。白是娇嫩、脆弱的，从它诞生的时刻起，它就不再是完美的白，而当我们触摸它，我们就进一步玷污它，只是我们可能并不了解。而正是由于这一点，它清晰的存在于我们的意识中。';
        // $box->draw(
        //     "    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eleifend congue auctor. Nullam eget blandit magna. Fusce posuere lacus at orci blandit auctor. Aliquam erat volutpat. Cras pharetra aliquet leo. Cras tristique tellus sit amet vestibulum ullamcorper. Aenean quam erat, ullamcorper quis blandit id, sollicitudin lobortis orci. In non varius metus. Aenean varius porttitor augue, sit amet suscipit est posuere a. In mi leo, fermentum nec diam ut, lacinia laoreet enim. Fusce augue justo, tristique at elit ultricies, tincidunt bibendum erat.\n\n    Aenean feugiat dignissim dui non scelerisque. Cras vitae rhoncus sapien. Suspendisse sed ante elit. Duis id dolor metus. Vivamus congue metus nunc, ut consequat arcu dapibus vel. Ut sed ipsum sollicitudin, rutrum quam ac, fringilla risus. Phasellus non tincidunt leo, sodales venenatis nisl. Duis lorem odio, porta quis laoreet ut, tristique a justo. Morbi dictum dictum est ut facilisis. Duis suscipit sem ligula, at commodo risus pulvinar vehicula. Sed quis quam ac quam scelerisque dapibus id non justo. Sed mollis enim id neque tempus, a congue nulla blandit. Aliquam congue convallis lacinia. Aliquam commodo eleifend nisl a consectetur.\n\n    Maecenas sem nisl, adipiscing nec ante sed, sodales facilisis lectus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Ut bibendum malesuada ipsum eget vestibulum. Pellentesque interdum tempor libero eu sagittis. Suspendisse luctus nisi ante, eget tempus erat tristique sed. Duis nec pretium velit. Praesent ornare, tortor non sagittis sollicitudin, dolor quam scelerisque risus, eu consequat magna tellus id diam. Fusce auctor ultricies arcu, vel ullamcorper dui condimentum nec. Maecenas tempus, odio non ullamcorper dignissim, tellus eros elementum turpis, quis luctus ante libero et nisi.\n\n    Phasellus sed mauris vel lorem tristique tempor. Pellentesque ornare purus quis ullamcorper fermentum. Curabitur tortor mauris, semper ut erat vitae, venenatis congue eros. Ut imperdiet arcu risus, id dapibus lacus bibendum posuere. Etiam ac volutpat lectus. Vivamus in magna accumsan, dictum erat in, vehicula sem. Donec elementum lacinia fringilla. Vivamus luctus felis quis sollicitudin eleifend. Sed elementum, mi et interdum facilisis, nunc eros suscipit leo, eget convallis arcu nunc eget lectus. Quisque bibendum urna sit amet varius aliquam. In mollis ante sit amet luctus tincidunt."
        // );
        $box->setTextWrapping(true);
        $box->draw($text);

        header('Content-type: image/png;');
        imagepng($im, null, 9, PNG_ALL_FILTERS);
    }
}