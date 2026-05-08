<?php
/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;

$this->extend([
    'init' => function(WebFrameAssetsConfig $ret, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {
        $moduleObj = $this;
        $_->on('body.beforeClose', function() use ($moduleObj, $ret){

            /* @var $this WebFrame */
            $base = $moduleObj->_base;

            /*
             * JS
             */
            $js_libs_to_load = $ret->JSlibs();
            // vendors (don't name the dir "vendor" - otherwise the git ignore will kick in and throw these dirs out when committing)
            if(in_array(WebFrameAssetsConfig::WFASSET__SPAMSPAN, $js_libs_to_load))
                echo $this->script("/$base/js/additional-vendors/spamspan.js");
            if(in_array(WebFrameAssetsConfig::WFASSET__MATCH_HEIGHT, $js_libs_to_load))
                echo $this->script("/$base/js/additional-vendors/jquery.matchHeight-min.js");

            // framework
            echo $this->script("/$base/js/webframe.js");

            // ==========================================

            /*
             * CSS
             */
            echo $this->style("/$base/css/webframe.css");

        });
    },
]);
