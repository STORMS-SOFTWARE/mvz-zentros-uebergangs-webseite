<?php

/**
 * ...
 */

namespace STORMS\webframe\Modules;

interface AutoFavIconConfig extends ModuleConfig {

    public function bg_color();
    public function text_color();
    public function text_pos();
    public function text();
    public function font_size();

}
