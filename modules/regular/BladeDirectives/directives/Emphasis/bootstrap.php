<?php
/*
 * Blade directive for text highlighting/emphasis ( @_em )
 * lately this module just wraps the text passed to @_em into a span having some extra class that can be styled
 */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;

$this->extend([
    'init' => function($ret, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {

        /*
         * 1. param => the string to em
         * 2. param => a class name to be added to the em dom elem
         */
        $blade->directive('_em', function ($expression) use ($_, $blade) {
            $str = $blade->stripParentheses($expression);
            $str = $blade->stripQuotes($str);
            $params = array_map('trim', explode(',', $str));

            $text = $params[0];
            $extra_class = trim((isset($params[1]) && !empty($params[1])) ? $params[1] : null);

            return sprintf(
                '<span class="_EM%s">%s</span>',
                !empty($extra_class)? (' ' . $extra_class) : '',
                $text
            );
        });

    }
]);
