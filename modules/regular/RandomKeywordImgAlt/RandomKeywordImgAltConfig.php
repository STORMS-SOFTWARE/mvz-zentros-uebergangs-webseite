<?php

namespace STORMS\webframe\Modules;

abstract class RandomKeywordImgAltConfig implements ModuleConfig {

    abstract public function keywords() : array;

    public function always_append(){
        return sprintf(' | %s', \Config::CUSTOMER_NAME);
    }

}
