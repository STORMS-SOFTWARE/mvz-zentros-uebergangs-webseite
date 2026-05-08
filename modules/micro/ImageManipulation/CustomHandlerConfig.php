<?php

namespace STORMS\webframe\Modules\ImageManipulation;

class CustomHandlerConfig {

    private \Closure $handler;
    private string $filename;

    public function __construct($filename, $handler) {
        $this->filename = $filename;
        $this->handler = $handler;

    }

}
