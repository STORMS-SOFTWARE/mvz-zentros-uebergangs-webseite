<?php

namespace STORMS\webframe\Modules;

use STORMS\webframe\Modules\ImageManipulation\CustomHandlerConfig;

abstract class ImageManipulationConfig implements ModuleConfig {

    public function exposeHelperFunctions () {
        return true;
    }

    public function customHandlers () {
        return null;

        // just an example how this can be used:
        return new class {
            // now you can use '@colorize' as method in your image manipulation uri
            public function colorize (\Intervention\Image\Image $img, string $img_path, $originalWidth, $originalHeight, $desiredWidth, $desiredHeight, $quality, $destFormat) : CustomHandlerConfig {
                // copied from the original just for demo
                $filename = sprintf('%s.%dx%dx%d.%s.MANIP.%s',
                    pathinfo($img_path,PATHINFO_DIRNAME).DIRECTORY_SEPARATOR.pathinfo($img_path,PATHINFO_FILENAME),
                    $desiredWidth, $desiredHeight, $quality,
                    'colorize',
                    $destFormat
                );
                return new CustomHandlerConfig($filename, function (\Intervention\Image\Image $img) {
                    $img->colorize(100, 0, 100);
                });
            }
        };
        /*
         * /\
         *  |
         * TODO das ist nur wie es funktionieren soll - das ist im moment noch nicht implementiert
         */
    }

}
