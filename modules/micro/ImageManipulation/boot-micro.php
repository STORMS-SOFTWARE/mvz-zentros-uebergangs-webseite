<?php

namespace STORMS\webframe\Modules\ImageManipulation;

use STORMS\webframe\Core\WebFrame as WebFrame;
use STORMS\webframe\LimeJuice\Response;

use Intervention\Image\ImageManager;

/*
 * TODO
 *
 * mit diesen modul kann ein angreifer sowohl den server lahm legen als auch mit dem cache gecropter bilder den server zumüllen.
 * Das sollten wir irgendwie unterbinden.
 * zB indem nur bilder auf DEV generiert werden können
 * oder dadurch das für die bildurl eine funktion verwendet werden muss die live etwas anderes zurück gibt als auf DEV
 */

$this->extend([
    'init' => function($ret, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {
        $router->match('GET|POST', $this->_name.'/{method}/(.*)', function($method, $request) {

            if(!in_array($method, $vm = ['crop', 'squarefit', 'convert']))
                die('Invalid method: ' . $method . '. Valid methods are: ' . implode(', ', $vm));

            if($method === 'convert') {
                preg_match('/(?<quality>[0-9]{1,3})?\/?(?<img_path>.*)/m', $request, $request);
                $width = -1; // so just the calc does not crash
                $height = -1;
            }
            else
                preg_match('/(?<width>[0-9\*]{1,4})?(?:x(?<height>[0-9\*]{1,4}))?(?:x(?<quality>[0-9]{1,3}))?\/(?<img_path>.*)/m', $request, $request);

            $request = array_filter($request, function ($val, $i) {
                return !is_int($i);
            }, ARRAY_FILTER_USE_BOTH);

            extract($request);

            $width = is_numeric($width) ? intval($width) : null;
            $height = is_numeric($height) ? intval($height) : null;

            if($width > 6000 || $height > 4000)
                die('Unrealistic dimensions requested.');

            $img_path = ltrim($img_path, '/');
            $dest_format = pathinfo($img_path, PATHINFO_EXTENSION);
            $img_path = pathinfo($img_path, PATHINFO_DIRNAME) . '/' . pathinfo($img_path, PATHINFO_FILENAME); // file path passed through query (without extension)

            /*
             * TODO use this list directly (and perhaps merge it with the one from Response): !! and then sort!!
             *         'bmp' => 'image/bmp',
                    'gif' => 'image/gif',
                    'jpeg' => 'image/jpeg',
                    'jpg' => 'image/jpeg',
                    'jpe' => 'image/jpeg',
                    'png' => 'image/png',
                    'tiff' => 'image/tiff',
                    'tif' => 'image/tiff',
                    'ico' => 'image/vnd.microsoft.icon',
                    'svg' => 'image/svg+xml',
                    'webp' => 'image/webp',
             */

            $imgMimes = array_filter(Response::$mimeTypes, function($item) {
                return str_contains($item, 'image/');
            }); // TODO sort by likelyness of img format - for example jpg at very first... because this array is iterated when the source format is not passed and tested against the FS  - so it may speed up things when entries are in a better order
            // according to the comment above: https://w3techs.com/technologies/overview/image_format
            // https://scanse.io/blog/usage-statistics-image-file-formats/

            if(!in_array(strtolower($dest_format), array_keys($imgMimes)))
                die('Invalid destination format: ' . $dest_format . '. Valid formats are: ' . implode(', ', array_keys($imgMimes)));

            $source_format = null;
            if(!empty($_GET['sf']) || !empty($_GET['source-format']) || !empty($_GET['source_format']))
                $source_format = $_GET['sf'] ?? $_GET['source-format'] ?? $_GET['source_format'];
            elseif(($sf = array_keys($_GET)[0] ?? null) !== null) // allows to just pass the sourceformat without a named query param ...webp?jpg (instead of ...webp?sf=jpg)
                $source_format =  $sf;

            if(!in_array(strtolower($source_format), array_keys($imgMimes)) && $source_format !== null)
                die('Invalid source format: ' . $source_format . '. Valid formats are: ' . implode(', ', array_keys($imgMimes)));

            if($source_format)
                $img_path = $img_path.'.'.$source_format;
            else { // source format was not passed: find the path of physical image
                foreach (array_keys($imgMimes) as $ext_lower) {
                    $ext_upper = strtoupper($ext_lower);
                    if(file_exists($img_path.'.'.$ext_lower)) {
                        $img_path = $img_path.'.'.$ext_lower;
                        break;
                    }
                    elseif (file_exists($img_path.'.'.$ext_upper)) {
                        $img_path = $img_path.'.'.$ext_upper;
                        break;
                    }
                }
            }

            // check for valid img
            $imgInfo = @getimagesize($img_path);
            $is_valid = file_exists($img_path) && stripos($imgInfo['mime'], 'image/') !== false;
            if(!$is_valid)
                die('Destination is no valid image or does not even exist.');

            // proportional calc new size if only one dimension is given ( http://whats-online.info/science-and-tutorials/77/resize-image-proportionally-on-upload-and-save-in-PHP/ )
            //if(empty($width) || empty($height)) {
            //    /*$o_width = $imgInfo[0];
            //    $o_height = $imgInfo[1];
//
            //    if ($o_width > $o_height) {
            //        if($o_width < $width)
            //            $newwidth = $o_width;
            //        else
            //            $newwidth = $width;
//
            //        $divisor = $o_width / $newwidth;
            //        $newheight = floor( $o_height / $divisor);
            //    }
            //    else {
            //        if ($o_height < $height)
            //            $newheight = $o_height;
            //        else
            //            $newheight = $height;
//
            //        $divisor = $o_height / $newheight;
            //        $newwidth = floor( $o_width / $divisor );
            //    }*/
            //    function calculateNewDimensions($originalWidth, $originalHeight, $newWidth = 0, $newHeight = 0) { // << chat-gpt
            //        // Check if only one of the dimensions is given
            //        if ($newWidth !== 0 && $newHeight === 0) {
            //            // Calculate new height to maintain proportions
            //            $newHeight = ($newWidth / $originalWidth) * $originalHeight;
            //        } elseif ($newHeight !== 0 && $newWidth === 0) {
            //            // Calculate new width to maintain proportions
            //            $newWidth = ($newHeight / $originalHeight) * $originalWidth;
            //        } else {
            //            // Error case: If both dimensions or neither dimension is given
            //            die("Either only new width or only new height should be specified.");
            //        }
//
            //        // Return the calculated dimensions
            //        return ['width' => $newWidth, 'height' => $newHeight];
            //    }
            //    $neueDimensionen = calculateNewDimensions($imgInfo[0], $imgInfo[1], $width, $height);
            //    $h = floor($neueDimensionen['height']);
            //    $w = floor($neueDimensionen['width']);
//
            //    /*$h = $newheight;
            //    $w = $newwidth;*/
            //}
            //else { // use both given proportions if they are given
            //    $h = $height;
            //    $w = $width;
            //}
            $h = $height;
            $w = $width;

            if(isset($quality) && !empty($quality))
                $q = intval($quality);
            else
                $q = 85;

            // default w+h
            //if($w === 0) $w = 150;
            //if($h === 0) $h = 150;

            // make sure a image can never be scaled up (only down)
            if($w > $imgInfo[0]) $w = $imgInfo[0];
            if($h > $imgInfo[1]) $h = $imgInfo[1];

            // make sure the image keeps its proportions if one dimension is omitted
            if($width === null || $height === null) {
                $originalWidth = $imgInfo[0];
                $originalHeight = $imgInfo[1];
                $originalAspectRatio = $originalWidth / $originalHeight;

                $newHeight = $h;
                $newWidth = $w;
                if ($newHeight != 0) {
                    $newWidth = $newHeight * $originalAspectRatio;
                } elseif ($newWidth != 0) {
                    $newHeight = $newWidth / $originalAspectRatio;
                } else {
                    $newWidth = $originalWidth;
                    $newHeight = $originalHeight;
                }
                $w = floor($newWidth);
                $h = floor($newHeight);
            }

            $method_short_mapping = [
                'crop' => 'cr',
                'squarefit' => 'sf',
                'convert' => 'co'
            ];

            $cache_img_name = sprintf('%s.%s%d.%s.MANIP.%s',
                pathinfo($img_path,PATHINFO_DIRNAME).DIRECTORY_SEPARATOR.pathinfo($img_path,PATHINFO_FILENAME),
                (function() use ($method, $w, $h) : string { // part for width and height
                    if($method !== 'convert')
                        return sprintf('%dx%dx', $w, $h);
                    return '';
                })(),
                $q,
                $method_short_mapping[$method],
                $dest_format
            );

            $manager = new /*Intervention*/ImageManager(['driver' => \Config::getProp('INTERVENTION_DRIVER', 'gd')]);

            if (is_file($cache_img_name)) {
                $filesize = filesize($cache_img_name);
                header('Content-Type: ' . mime_content_type($cache_img_name)); // oder Response::$mimeTypes[$dest_format]
                header('Content-Length: ' . $filesize);
                // Caching headers (optional)
                header('Cache-Control: public, max-age=31536000'); // anpassen
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($cache_img_name)) . ' GMT');
                readfile($cache_img_name);
                exit;
            }

            if((!is_file($cache_img_name) && strpos($img_path, 'MANIP') === false) || isset($_GET['regen'])) { // the image does not exist: run $method on it and save it to disk

                $img = $manager->make($img_path);

                $callback = null;
                /*if($h === null || $w === null) {
                    $callback = function (\Intervention\Image\Constraint $constraint) {
                        //$constraint-> aspectRatio();
                        dd($constraint->);
                    };
                }*/

                if($method === 'crop') {
                    $img->fit($w, $h, $callback);
                }
                elseif ($method === 'squarefit') {
                    $blurredImage = $manager->make($img_path);
                    $blurredImage->fit($w, $h);
                    $blurredImage->blur(100);
                    $blurredImage->brightness(50);

                    $img->resize($w-20, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $blurredImage->insert($img, 'center');

                    $img = $blurredImage;
                }
                /*elseif ($method === 'resize') {
                    // TODO up to come... but actually crop can do resizing
                }*/


                // add callback functionality to retain maximal original image size
                /*$img->fit(800, 600, function ($constraint) {
                    $constraint->upsize();
                });*/

                $img->save($cache_img_name, $q);

            }
            else { // just deliver img directly from disk (because if was cached before)
                $img = $manager->make($cache_img_name);
            }

            $r = new Response();
            $r->mime = $dest_format;
            $r->body = $img->encode($dest_format, $q)->getEncoded();
            $r->flush();

        });

    }
]);
