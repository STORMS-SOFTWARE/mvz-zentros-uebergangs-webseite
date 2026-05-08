<?php
/*
 * Bootstrap Viewport helper
 */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\LimeJuice\Response;

$this->extend([
    'always' => function($ret, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {

        $current_base = $this->_base;

        if(!_isDev())
            return;

        $_->router->post('/bs-grid-helper--set-opacity', function() {
            $_SESSION['bs-grid-helper--default-opacity'] = $_POST['new_opacity'];
        });

        $_->on('body.beforeClose', function() use ($blade, $current_base) {
            //$blade->compile("$current_base/views/bs-generic.blade.php", [ // .. would also work
            echo $blade->runChild("$current_base/views/bs-generic.blade.php", [ // don't use run()! it will destroy render buffers... Use runChild (and @include does)!
                'blade' => $blade,
                '_' => WebFrame::inst(),
                'current_base' => "/$current_base"
            ]);
        });

    }
]);
