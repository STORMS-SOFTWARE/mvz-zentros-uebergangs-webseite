<?php

namespace STORMS\webframe\Modules\ImageManipulation;

class ObjectBasedHelper extends HelperBase {

    /**
     * @param UriConfig $uriConfig
     * @throws \InvalidArgumentException @see doc comment of getImgUri()
     * @return array
     */
    public static function getImageUris(UriConfig $uriConfig) : array {

        $path = $uriConfig->getPath();
        $method = $uriConfig->getMethod();
        $width = $uriConfig->getWidth();
        $height = $uriConfig->getHeight();
        $dest_format = $uriConfig->getDestFormat();
        $quality = $uriConfig->getQuality();
        $with_original = $uriConfig->getWithOriginal();
        $shuffle = $uriConfig->getShuffle();

        try {
            // TODO noch nicht getestet
            return SignatureBasedHelper::getImageUris($path, $method, $width, $height, $dest_format, $quality, $with_original, $shuffle);
        }
        catch(\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

    }

    /**
     * @param UriConfig $uriConfig
     * @throws \InvalidArgumentException if another method then 'convert' is used and no width and height is provided
     * @return string
     */
    public static function getImageUri(UriConfig $uriConfig, ?string $path = null) : string {

        if($path === null)
            $path = $uriConfig->getPath();
        $method = $uriConfig->getMethod();
        $width = $uriConfig->getWidth();
        $height = $uriConfig->getHeight();
        $dest_format = $uriConfig->getDestFormat();
        $quality = $uriConfig->getQuality();
        $source_format = $uriConfig->getSourceFormat();

        try {
            // TODO noch nicht getestet
            return SignatureBasedHelper::getImageUri($path, $method, $source_format, $width, $height, $quality, $dest_format);
        }
        catch(\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

    }

}
