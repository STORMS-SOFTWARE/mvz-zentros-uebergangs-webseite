<?php
/*
 * Module for generic DOM manipulations from within PHP
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use \ConfigBase as Config;

$this->extend([
    'init' => function(DomManipulationConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {
        // find jquery and add trigger after the script src
        if($cfg->searchJquery()) {
            $_->on('bladeone.compiling', function(&$contents, &$fileName) {
                if (strpos($fileName, \Config::LAYOUT) !== false) {
                    libxml_use_internal_errors(true);
                    $doc = new \DOMDocument();
                    $doc->loadHTML($contents);
                    $scripts = iterator_to_array($doc->getElementsByTagName('script')); // all script tags that can be found in the markup (array with elements of class DOMElement)

                    /* @var $script \DOMElement */
                    $jquery_info = false;
                    foreach($scripts as $script) {
                        $script_src = $script->getAttribute('src');
                        if (stripos($script_src, 'jquery') !== false) {
                            $jquery_info = [
                                'src'      => $script_src,
                                'full_tag' => $script->ownerDocument->saveHTML($script)
                            ];
                            break;
                        }
                    }

                    if($jquery_info !== false) {
                        $contents = str_replace(
                            $jquery_info['full_tag'],
                            $jquery_info['full_tag'] . '<?php $_->trigger(\'body.afterJquery\') ?>',
                            $contents
                        );
                    }
                }
            });
        }
    },
    'always' => function($cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {

        /*
         * so note that this will also be done if the module is disabled through the project config
         * This means that on demand body class additions & stylesheet + script reference can ALWAYS be used.
         * Decided so because thats pretty awesome feature that may be considered so important that it may be seen as core feature
         */

        // add all on demand css classes to the body
        $_->on('body.class', function() {
            echo DomManipulation::inst()->getBodyClassesStringed();
        });

        // append "on-demand" stylesheets
        $_->on('head.beforeClose', function() use ($_) {
            echo WebFrame::style(DomManipulation::inst()->stylesheets);
        });
        // same for scripts
        $_->on('body.beforeClose', function() use ($_) {
            echo WebFrame::script(DomManipulation::inst()->scripts);
        });

    }
]);
