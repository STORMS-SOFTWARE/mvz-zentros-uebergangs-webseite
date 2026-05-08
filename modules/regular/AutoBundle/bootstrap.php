<?php
/**
 * Blade directive for script / css autobundle
 *
 * note that module won't work correctly at the moment if you use @bundle multiple times for either css or js
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use \Config as Config;

use \SimpleXMLElement as SimpleXMLElement;
use MatthiasMullie\Minify;

$this->extend([
    //'setupOnFourOFour' => true,
    'always' => function($cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {
        /* @var $moduleObj Module */
        $moduleObj = $this;

        $blade->directive('bundle', function() use ($blade, $_, $cfg, $moduleObj) {
            return '<?php ob_start(); ?>';
        });
        $blade->directive('endBundle', function() use ($blade, $_, $cfg, $moduleObj) {
            return '<?php echo $_->modules[\'regular\']->getModuleByName(\''.$moduleObj->getName().'\')->bundle(ob_get_clean()) ?>';
        });
    },
    'bundle' => function($srcs) {

        if(_isDevOrPreviewServer())
            return "<!-- Note: CSS/JS Bundle+Minify Module will only kick in within at envs --> \n $srcs";

        $_ = WebFrame::inst();

        $file_refs = array_values(array_filter(array_map('trim', explode("\n", $srcs)))); // parse the string file refs into an useable array

        $cache_dest_path = ltrim(Config::STORAGE_PATH, '/') . '/auto-bundle'; // TODO create config stuff for this

        /*$css_whitelist = [
            'ModCssUtil'
        ];*/

        /*
         * these hooks allow to add modifications to css-file-contents determined by the checker callback function
         * the callback functions will apply if a checked file-ref matches partially agains an key within the list of keys of the following array
         */
        $hooks = [
            'ModCssUtil' => [
                'checkFileExists' => function($file_ref) use ($_) {
                    //return file_exists($_->cleanUri(str_replace('ModCssUtil', '', $file_ref))); // this may be slower as a regular str_replace
                    return is_file(str_replace('ModCssUtil/', '', $file_ref));
                },
                'modifyRef' => function($file_ref) use ($_) {
                    //return $_->cleanUri(str_replace('ModCssUtil', '', $file_ref));
                    return str_replace('ModCssUtil/', '', $file_ref);
                },
                'modifyRefContent' => function($content) use ($_) {
                    /* @var $micro_modules Modules */
                    $micro_modules = $_->modules['micro'];
                    $modCssUtilCfg = $micro_modules->registerModule('modules/micro/ModCssUtil')->boot()->getConfig();
                    // ... C&P from mod css util module TODO
                    $color_key = key($modCssUtilCfg->cssMap());
                    $new_colors = $modCssUtilCfg->cssMap()[$color_key] ?? $modCssUtilCfg->cssMap()[key($modCssUtilCfg->cssMap())];
                    return str_ireplace( array_keys($new_colors), array_values($new_colors), $content); // replace the default accent color with the given color
                }
            ],
            // =================================================================================
            'modifiedRefs' => [] // will be filled by the logic - you don't need to set something here
        ];

        if(!is_dir($cache_dest_path)) // create dir for the minify cache if it does not exist
            mkdir($cache_dest_path, 0777, true);

        $css_change_time_last_generation = 0; // will be set to the time of the last changed file at the time of the last fresh minify-generation
        $js_change_time_last_generation = 0;
        // get the change time of the last changed file as the time of the last fresh generation
        foreach (scandir($cache_dest_path) as $file_name) {
            if(strpos($file_name, '.css') !== false) {
                //preg_match('/some-alternating-name-(\d*).min.css/', $file_name, $res);
                $css_change_time_last_generation = substr($file_name, 0, strpos($file_name, '.'));
            }
            elseif(strpos($file_name, '.js') !== false) {
                $js_change_time_last_generation = substr($file_name, 0, strpos($file_name, '.'));
            }
        }

        $css_files = [];
        $css_last_change_time = 0; // will later (after iteration) contain the change time of the file that has been changed very last
        $js_files = [];
        $js_last_change_time = 0;

        //$whitelist_files = []; // ... will be filled... for example used for the ModCssUtil ...

        /*
         * find all css/js files that were passed to this method and store their ref + find which file was changed very last at all
         */
        foreach($file_refs as $file_ref) {

            $is_css_ref = strpos($file_ref, '.css') !== false;
            $is_js_ref = strpos($file_ref, '.js') !== false;

            $is_comment = strpos($file_ref, '<!--') !== false;

            if((!$is_css_ref && !$is_js_ref) || $is_comment)
                continue; // skip entries that are neither css nor js references...

            $conform_link = str_replace(['/>', '</link>', '</script>'], ['>', '', ''], $file_ref) . ($is_css_ref ? '</link>' : '') . ($is_js_ref ? '</script>' : ''); // the SimpleXMLElement will cause exeptions if the src for example misses the closing link tag - so we create a conform tag here...
            $elem = new SimpleXMLElement($conform_link);

            if($is_css_ref) {
                $ref = $elem['href']->__toString();
                $ref = ltrim($ref, '/');

                $exists_through_hook = false;
                if($hook = $_->partial_in_array_reverse($ref, array_keys($hooks))) {
                    $hook = $hook[0]; // contains the hook key (for example "ModCssUtil")
                    $exists_through_hook = $hooks[$hook]['checkFileExists']($ref);
                }

                //if(file_exists($ref) || ($whitelist_file = $_->partial_in_array_reverse($ref, $css_whitelist))) {
                if(is_file($ref) || $exists_through_hook) {
                    /*if(isset($whitelist_file) && !empty($whitelist_file)){
                        $ref = str_replace( ($wl_key = array_pop($whitelist_file)) . '/', '', $ref);
                        $whitelist_files[$wl_key][] = $ref;
                    }*/
                    if($exists_through_hook) {
                        $ref = $hooks[$hook]['modifyRef']($ref);
                        $hooks['modifiedRefs'][$ref] = $hook;
                    }

                    if(($ct = filemtime($ref)) > $css_last_change_time)
                        $css_last_change_time = $ct;
                    $css_files[] = $ref;
                }
            }
            elseif($is_js_ref) {
                $ref = $elem['src']->__toString();
                $ref = ltrim($ref, '/');
                if(is_file($ref)) {
                    if(($ct = filemtime($ref)) > $js_last_change_time)
                        $js_last_change_time = $ct;
                    $js_files[] = $ref;
                }
            }
        }

        //dd($css_files, $hooks);

        /*
         * generate the cache file if need (and return file ref to it) or return the previously cached file ref
         */

        if(count($css_files) > 0) {
            if($css_last_change_time > $css_change_time_last_generation) {
                //d('fresh generation css');

                if(is_file($del_path = "$cache_dest_path/{$css_change_time_last_generation}.min.css"))
                    unlink($del_path);

                $css_minifier = new Minify\CSS();
                //$css_minifier->setMaxImportSize(100);
                foreach ($css_files as $css_file) {

                    $css_code = file_get_contents($css_file);
                    $css_code = str_replace(['../'], ['/'.pathinfo($css_file, PATHINFO_DIRNAME).'/../'], $css_code); // replace relative asset paths and map them absolute

                    // run the current file content through the hook modifier function
                    if(array_key_exists($css_file, $hooks['modifiedRefs']))
                        $css_code = $hooks[$hooks['modifiedRefs'][$css_file]]['modifyRefContent']($css_code);

                    /*if(in_array($css_file, $whitelist_files['ModCssUtil'])) {
                        / * @var $micro_modules Modules * /
                        $micro_modules = $_->modules['micro'];
                        $modCssUtilCfg = $micro_modules->registerModule('modules/micro/ModCssUtil')->boot()->getConfig();
                        //dd($modCssUtilCfg);
                        // ... C&P from mod css util module TODO
                        //$modCssUtilCfg = Config::setupModCssUtil(new Module, null);
                        $color_key = key($modCssUtilCfg->cssMap());
                        $new_colors = $modCssUtilCfg->cssMap()[$color_key] ?? $modCssUtilCfg->cssMap()[key($modCssUtilCfg->cssMap())]; // replace map array (key: the val that shall be replaced; value: the value that will replace the original)
                        // process requests css file on the fly: replace the vals within the css according to the defined replace map
                        $css_code = str_ireplace( array_keys($new_colors), array_values($new_colors), $css_code); // replace the default accent color with the given color
                    }*/

                    $css_minifier->add($css_code);
                }

                //dd($css_minifier->minify());

                $css_minifier->minify($concrete_dest_file = "{$cache_dest_path}/{$css_last_change_time}.min.css");

                //d('<link href="/'.$concrete_dest_file.'" rel="stylesheet"></link>');
                echo '<link href="/'.$concrete_dest_file.'" rel="stylesheet"></link>';
            }
            else {
                //d('cached css bundle');
                //d('<link href="/'.($cache_dest_path . '/'.$css_change_time_last_generation.'.min.css').'" rel="stylesheet"></link>');

                echo '<link href="/'.($cache_dest_path . '/'.$css_change_time_last_generation.'.min.css').'" rel="stylesheet"></link>';
                //echo '<link href="/assets/css/budle.css?file='.($cache_dest_path . '/'.$css_change_time_last_generation.'.min.css').'" rel="stylesheet"></link>';
            }
        }

        if(count($js_files) > 0) {
            if($js_last_change_time > $js_change_time_last_generation) {
                //d('fresh generation js');

                if(is_file($del_path = "$cache_dest_path/{$js_change_time_last_generation}.min.js"))
                    unlink($del_path);

                $js_minifier = new Minify\JS();
                foreach ($js_files as $js_file)
                    $js_minifier->add(file_get_contents($js_file));
                $js_minifier->minify($concrete_dest_file = "{$cache_dest_path}/{$js_last_change_time}.min.js");

                //d('<script src="/'.$concrete_dest_file.'"></script>');
                echo '<script src="/'.$concrete_dest_file.'"></script>';
            }
            else {
                //d('cached js bundle');
                echo '<script src="/'.($cache_dest_path . '/'.$js_change_time_last_generation.'.min.js').'"></script>';
            }
        }

    }
]);
