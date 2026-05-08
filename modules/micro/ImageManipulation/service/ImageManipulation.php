<?php

namespace STORMS\webframe\Modules\ImageManipulation;

class ImageManipulation {

    /**
     * Delegator method. This delegates the call to the correct helper (ObjectBasedHelper / SignatureBasedHelper) based on the type of the first argument
     * @return ObjectBasedHelper|SignatureBasedHelper
     * Note that this method does not actually return instances of the classes hinted, but as we do so reasonable IDEs will show both implementations of the methods in the autocomplete which we really want
     */
    public static function helper() {
        return new class extends HelperBase {
            public static function __callStatic ($name, $arguments) {
                if(($arguments[0] ?? null) instanceof UriConfig)
                    return ObjectBasedHelper::$name(...$arguments);
                else
                    return SignatureBasedHelper::$name(...$arguments);
            }
        };
    }

}
