<?php
/* @var $this Module */

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\Modules\AutoFavIconConfig;

$this->extend([
    'init' => function(AutoFavIconConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {

        $moduleObj = $this;

        $_->on('head.beforeClose', function() use ($blade) {
            echo '<link rel="shortcut icon" type="image/x-icon" href="/auto-favicon.ico">';
        });

        $router->get('/auto-favicon.ico', function() use ($moduleObj, $cfg, $_) {

            function isHex($colorCode) {
                $colorCode = ltrim($colorCode, '#');
                if (ctype_xdigit($colorCode) && (strlen($colorCode) == 6 || strlen($colorCode) == 3))
                    return true;

                return false;
            }

            // create a blank image
            $image = imagecreatetruecolor(100, 100);

            // Transparent Background
            imagealphablending($image, false);
            $transparency = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparency);
            imagesavealpha($image, true);

            $bg_color = $cfg->bg_color();

            if(!is_array($bg_color)) {
                if(isHex($bg_color))
                    $bg_color = $_->hex2rgba($bg_color, false, false);
                else
                    throw new InvalidArgumentException('The BG color needs to be either a valid hex-color value or an RGB array');
            }

            // choose a color for the ellipse
            $col_ellipse = imagecolorallocate($image, $bg_color[0], $bg_color[1], $bg_color[2]);

            // draw the ellipse
            // int $cx , int $cy , int $width , int $height
            // cx -> x-coordinate of the center.
            // cy -> y-coordinate of the center.
            imagefilledellipse($image, 50, 50, 95, 95, $col_ellipse);

            $textcolor = imagecolorallocate($image, $cfg->text_color()[0], $cfg->text_color()[1], $cfg->text_color()[2]);

            $text = $cfg->text();
            $font = sprintf('%s/OpenSans.ttf', $moduleObj->_base);
            $angle = 0;
            $font_size = $cfg->font_size();

            imagettftext ( $image , $font_size, $angle, $cfg->text_pos()['x'], $cfg->text_pos()['y'], $textcolor, $font, $text);

            // output the picture
            header("Content-type: image/png");
            imagepng($image);
        });
    }
]);
