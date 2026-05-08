<?php

namespace STORMS\webframe\Modules\ImageManipulation;

use STORMS\webframe\LimeJuice\Response;

class HelperBase {

    const METHOD_CONVERT    = 'convert';
    //const METHOD_RESIZE     = 'resize'; // up to come - but actually crop can be used to achieve the same
    const METHOD_SQUAREFIT  = 'squarefit'; // what it actually does: https://play-lh.googleusercontent.com/jXCGuyLJlJ8QnHwXqE6On1mpAj7cURUDCxp_RlS5T7nl40Yf86zTZYkGp4qM9XdCoRg
    const METHOD_CROP       = 'crop';

    public static string $DEFAULT_DEST_FORMAT = 'webp';

}
