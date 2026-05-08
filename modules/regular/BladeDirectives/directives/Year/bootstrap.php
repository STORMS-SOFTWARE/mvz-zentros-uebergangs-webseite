<?php
/*
 * Blade directive for @year (simply printing the current year)
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;

$this->extend([
    'always' => function($ret, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {
        $blade->directive('year', function() use ($blade) {
            return '<?= date("Y"); ?>';
        });
    }
]);
