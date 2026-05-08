<?php

namespace STORMS\webframe\Modules\ImageManipulation;

use STORMS\webframe\LimeJuice\Response;

class SignatureBasedHelper extends HelperBase {

    private static ?string $moduleName = null;
    private static ?array $imgExtensions = null;
    private static bool $initialized = false;

    private static function ensureInternalIntegrity () { // consider this a static constructor

        if(self::$initialized)
            return;

        self::$imgExtensions = array_keys(array_filter(Response::$mimeTypes, function($mime) {
            return str_starts_with($mime, 'image/');
        }));

        self::$moduleName = basename(pathinfo(__DIR__, PATHINFO_DIRNAME));

        self::$initialized = true;
    }

    /**
     * @param string $path the path to the directory containing the images that shall be processed
     * @param string|null $common_method null can be used to get none thumbnail images without any additional img processing identifier
     * @param ?int $common_width @see doc comment of getImgUri() - this applies to all images found by this method
     * @param ?int $common_height @see doc comment for $common_width
     * @param ?string $common_dest_format set the destination format for all images found by this method
     * @param ?int $common_quality @see doc comment for $common_width
     * @param bool $with_original will return an array for each entry containing the original image path as well
     * @param bool $shuffle if the returned array should be shuffled
     *
     * @throws \InvalidArgumentException @see doc comment of getImgUri()
     *
     * @return array
     */
    public static function getImageUris(
        string $path,
        string|null $common_method      = null,
        ?int $common_width              = null,
        ?int $common_height             = null,
        ?string $common_dest_format     = null,
        ?int $common_quality            = null,
        bool $with_original             = false,
        bool $shuffle                   = false
    ) : array {

        self::ensureInternalIntegrity();

        $noneThumbImgs = array_filter(glob(rtrim(ltrim($path, '/'), '/').'/*.*'), function ($filename) { // (Note: GLOB_BRACE is not avail. in PHP on every OS)
            $isThumb = str_contains($filename, '.MANIP.');
            $isExtensionValid = in_array(pathinfo($filename, PATHINFO_EXTENSION), self::$imgExtensions);
            return !$isThumb && $isExtensionValid;
        });

        if($common_method !== null) {
            $imgs = array_map(function($entry) use ($common_method, $common_width, $common_height, $common_quality, $common_dest_format, $with_original) {
                $url = self::getImageUri(
                    img_path: $entry,
                    method: $common_method,
                    source_format: pathinfo($entry, PATHINFO_EXTENSION),
                    width: $common_width,
                    height: $common_height,
                    quality: $common_quality,
                    override_dest_format: $common_dest_format
                );
                if($with_original)
                    return [
                        'original' => sprintf("/%s", ltrim($entry,'/')),
                        'processed' => $url
                    ];
                else
                    return $url;
            }, $noneThumbImgs);
        }

        $imgs = array_values($imgs ?? $noneThumbImgs);

        if($shuffle)
            shuffle ($imgs);

        /*array_walk($imgs, function(&$item) {
            $item = sprintf("/%s", ltrim($item,'/')); // add a slash to the beginning of every image path
        });*/

        return $imgs;
    }

    /**
     * Generate the request uri for the image manipulation module
     * In some cases, you are well advised using named parameters for calling this method.
     * @param string $img_path the path to the image that shall be processed - IMPORTANT: the extension is considered the DESTINATION format. So you can put .web in the path that actually refers to a jpg to get a webp image back from the jpg
     * @param ?string $method see the constants of this class for possible values
     * @param ?string $source_format just the actual/real extension of the image (without the dot). Can be >null< to auto-detect extension (which is slower, so it's recommended to exactly define the extension)
     * @param ?int $width optional if $method is 'convert' - otherwise required (method will throw exception if you don't provide w/h but use something else then convert)
     * @param ?int $height @see $width param
     * @param ?int $quality absolutely optional. Use value between 0-100
     * @param ?string $override_dest_format note that this is not required at all. You better determine your destination format through the file extension of $img_path. This param is mainly for easier integration of this method within the getImageUris method
     *
     * @throws \InvalidArgumentException if another method then 'convert' is used and no width and height is provided
     *
     * @return string the ImageManipulation module request uri
     */
    public static function getImageUri(
        string $img_path,
        ?string $method                 = null,
        ?string $source_format          = null,
        ?int $width                     = null,
        ?int $height                    = null,
        ?int $quality                   = null,
        ?string $override_dest_format   = null
    ) : string {

        self::ensureInternalIntegrity();

        if($method === null)
            $method = self::METHOD_CONVERT;

        // TODO hier muss was geändert werden - das führt dazu dass das zielformat einfach IMMER webp ist es nicht explizit anders definiert ist und die endung in $img_path nicht berücksichtigt wird
        // man müsste hier irgendwie unterscheiden ob null bedeutet, dass dsa format nicht geändert werden soll oder ob das defaultformat verwendet werden soll bzw ob einfach das format aus $img_path verwendet werden soll
        if($override_dest_format === null)
            $override_dest_format = self::$DEFAULT_DEST_FORMAT;

        $url = '/' . self::$moduleName;

        if($method) {
            if($method !== 'convert' && (!is_numeric($width) && !is_numeric($height)))
                throw new \InvalidArgumentException('If you want to apply an manipulation action on the image, you have to provide at least one dimension');

            $url .= '/' . $method;
        }

        $url .= '/' ;

        if($method !== 'convert') {
            if($width)
                $url .= $width;
            else
                $url .= '*';

            $url .= 'x';

            if($height)
                $url .= $height;
            else
                $url .= '*';

            if($quality)
                $url .= 'x' . $quality;

            $url .= '/';
        }
        elseif($method === 'convert' && isset($quality)) {
            $url .= sprintf('%d/', $quality);
        }

        if($override_dest_format)
            $img_path = str_replace(pathinfo($img_path, PATHINFO_EXTENSION), ltrim($override_dest_format, '.'), $img_path);

        $url .= ltrim($img_path, '/');

        if($source_format) // if >null< the module will try to auto-detect the source format (which is slower than passing it directly)
            $url .= "?sf=$source_format";

        return $url;
    }

    public static function getConvertedImageUri(string $img_path, ?string $source_format = null, ?int $quality = null, ?string $override_dest_format = null) : string {
        return self::getImageUri(
            $img_path,
            self::METHOD_CONVERT,
            $source_format,
            quality: $quality,
            override_dest_format: $override_dest_format
        );
    }

    /**
     * @deprecated use getImageUris instead
     *
     * Returns an array containing the image paths of all none-thumbnail images found in the passed path without any further processing url
     */
    public static function getImages(string $path) {
        return self::getImageUris($path, null);
    }

}
