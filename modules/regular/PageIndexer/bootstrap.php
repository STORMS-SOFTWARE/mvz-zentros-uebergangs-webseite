<?php
/*
 * ...
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use \ConfigBase as Config;

$this->extend([
    'init' => function(bool $active=true, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {

        // index all pages (and try to find indexer callback defs)
        PageIndexer::init();

    },
    'always' => function($cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {}
]);
