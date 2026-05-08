<?php
/*
 * Color Class/Css Sheet generator Module
 */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\LimeJuice\Response;

$this->extend([
    'always' => function($cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {

        if(!_isDev())
            return;
        
        $current_base = $this->_base;

        $router->before('GET', '/color-classes', function() use ($current_base, $cfg, $blade) {
            $r = new Response();
            $r->mime = 'html';
            //$r->mime = 'css';
            $r->body = $blade->run("$current_base/views/color-gen.blade.php", [
                'cfg' => $cfg,
                'blade' => $blade,
                '_' => WebFrame::inst()
            ]);
            $r->flush();
            exit;
        });

    },
]);
