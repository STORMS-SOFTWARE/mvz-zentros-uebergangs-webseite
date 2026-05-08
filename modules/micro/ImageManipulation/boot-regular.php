<?php
namespace {

    if(false) { // just here so phpstorm hints the existence of the function (of course the function is now also hinted if global functions are not be exposed and the function therefor does not actually exist for php)
        function imageManipulation () { }
    }

    function _imageManipulationModule__ExposeHelperFunctions () {

        /**
         * Global helper function for quicker namespace-less access to the ImageManipulation module service/helper methods
         * @return STORMS\webframe\Modules\ImageManipulation\ObjectBasedHelper | STORMS\webframe\Modules\ImageManipulation\SignatureBasedHelper
         */
        function imageManipulation () {
            return STORMS\webframe\Modules\ImageManipulation\ImageManipulation::helper();
        }

    }

}

namespace STORMS\webframe\Modules\ImageManipulation {

    use STORMS\webframe\Modules\ImageManipulationConfig;

    $this->extend([
        'init' => function (ImageManipulationConfig|bool $cfg) {
            if(is_object($cfg) && get_parent_class($cfg) === ImageManipulationConfig::class && $cfg->exposeHelperFunctions())
                _imageManipulationModule__ExposeHelperFunctions();
        },
    ]);

}


