<?php

namespace STORMS\webframe\Modules;

abstract class ModCssUtilConfig implements ModuleConfig {

    abstract function cssMap();

    public function useCache() {
        return true;
    }

    public function useMinify() {
        return true;
    }

}
