<?php
/*
 * DocReady Module
 *
 * NOTE
 * the js minifier seems to fail on script that contain vue.. dunno why
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\Page;
use STORMS\webframe\Core\WebFrame;

use \Config as Config;

$this->extend([
    'always' => function($ret, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {
        /* @var $moduleObj Module */
        $moduleObj = $this;

        //$current_base = $moduleObj->_base;

        /*
         * Signatures:
         * @dr
         * @dr(true->default if not passed|false)
         * => If false the block will not be compressed/minified
         */
        $blade->directive('dr', function ($expression) use ($blade, $moduleObj) {
            return $moduleObj->buildInvoker(
                'setCompress',
                ( ($expression === null || trim($expression) === '' ) ? 'true' : $expression ),
                false,
                null
            ) . $blade->phpTag . ' ob_start(); ?>';
        });

        $blade->directive('enddr', function() use ($blade, $_, $moduleObj) {
            return $moduleObj->buildInvoker('processScriptString', 'ob_get_clean()', false, null);
        });

        /*
         * append an inline script section on dev ; or write out a scirpt file on prod-servers to be appended at the end of the body
         */
        $_->on('body.beforeClose', function() use ($blade) {

            if(!isset($blade->inline_scripts) || empty($blade->inline_scripts))
                return;

            $scriptContent = sprintf('
                $(document).ready(function() {
                    %s
                });
            ', implode($blade->inline_scripts));

            if(_isDev()) { // on dev do not send out a file (mainly because this messes hotreload dev applications up)
                printf('<script>%s</script>', '/* NOT MINIFYING & NOT USING EXTERNAL FILE ON DEV */'.$scriptContent);
            }
            else {

                $module_base_storage_path = ltrim(Config::STORAGE_PATH, '/') . '/doc-ready';

                // create generic container dir if needed
                if(!is_dir($module_base_storage_path))
                    mkdir($module_base_storage_path, 0777, true);

                /*
                 * note on the PageName part in the generated script file name:
                 * the generated / written doc ready content may differ from page to page (for example if we use the dr-directive in the default view and in some of the pages)
                 * If we do not have the page name @ the doc-ready script-name the browser may use the file from its cache (which may be not the correct one for the current page request).
                 * By adding the pagename, we force the browser to always load the correct dr-generated script.
                 * We could also append a timestampe to the script src - this would force the browser to always reload the script.
                 *
                 * -----------------------------------
                 *
                 * Note according to minify:
                 * On prod servers each @dr block makes the script block within being minified or not (according to the bool passed to @dr)
                 * Because minifying vue code seems to cause problems there is a parameter that prevents minifying for a specific block)
                 */
                $dr_script_src = sprintf('%s/dr-%s.js', $module_base_storage_path, Page::inst()->getName());
                if(!is_file($dr_script_src) || filemtime(Page::inst()->getFilePath()) > filemtime($dr_script_src))
                    file_put_contents($dr_script_src, sprintf('/* last gen: %d */%s', time(), $scriptContent));

                echo $this->script("/$dr_script_src?".filemtime(Page::inst()->getFilePath()));
            }
        });

    },
    'setCompress' => function($blade, $extra_params = null, $state) {
        $this->compress = $state;
    },
    'processScriptString' => function($blade, $extra_params = null, $str) { // invoked method

        // remove script tags if there are any
        $remove = [ // script tags to remove
            '<script>',
            '<script type="text/javascript">',
            '</script>',
        ];
        $replace = array_fill(0, count($remove), ''); // generate array with empty-string slots matching the number of strings that shall be replaced
        $script = str_replace($remove, $replace, $str); // now replace the things that we do not need with empty strings
        // -- script tag remove

        // minify the passed block if needed
        if(!_isDev() && $this->compress) {
            $minifier = new \MatthiasMullie\Minify\JS($script);
            $script = $minifier->minify() . ';';
        }
        // -- minify

        $blade->inline_scripts[] = $script;
    }
]);
