<?php
/*
 * ModCssUtil
 *
 * allows to get a css file with replaced colors defined by a map
 */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\LimeJuice\Response;

use MatthiasMullie\Minify\CSS as MinifyCSS;

$this->extend([
    'init' => function(ModCssUtilConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {
        $router->all("{$this->_name}/(.*)", function($uri) use ($cfg) {
            $css_file = $uri;

            if(!is_file($css_file))
                die('Nope.');

            parse_str($_SERVER['QUERY_STRING'], $query_vars); // parse query string vars into array

            if(isset($query_vars) && !empty($query_vars))
                $color_key = $query_vars['c'] ?? key($cfg->cssMap());
            else // if the color-set is not passed: just use the first defined set from the config
                $color_key = key($cfg->cssMap()); // get the very first color map key

            // replace map array (key: the val that shall be replaced; value: the value that will replace the original)
            $new_colors = $cfg->cssMap()[$color_key] ?? $cfg->cssMap()[key($cfg->cssMap())];

            /* @var $css ?string */
            $css = null;

            if($cfg->useCache()) {
                $cache_path = ltrim(\Config::STORAGE_PATH . "/compiled/{$this->_name}", '/');
                if(!is_dir($cache_path))
                    mkdir($cache_path, 0777, true);

                $orig_file_time = filemtime($css_file);
                $css_file_name = pathinfo($css_file, PATHINFO_FILENAME);
                $color_map_hash = crc32(json_encode($new_colors)); // without this changes to the color map will not cause a fresh generation...
                $cache_file_name = "$css_file_name.$color_key.$color_map_hash.css";
                if(is_file("$cache_path/$cache_file_name") && filemtime("$cache_path/$cache_file_name") >= $orig_file_time)
                    $css = file_get_contents("$cache_path/$cache_file_name");
            }

            $isFromCache = $css !== null; // has the css been restored from cache?

            // if there was nothing restored from cache -> process requested css file on the fly (replace the vals within the css according to the defined replace map)
            if(!$isFromCache) 
                $css = str_ireplace( array_keys($new_colors), array_values($new_colors), file_get_contents($css_file));

            $r = new Response();
            $r->mime = 'css';

            $info_header = ($isFromCache ? 'RESTORED FROM CACHE' : 'FRESH GENERATED (Caching: ' . ($cfg->useCache() ? 'en' : 'dis') . 'abled)');

            if($cfg->useMinify() && !$isFromCache) {
                $minifier = new MinifyCSS();
                $minifier->add($css);
                $cacheable_css_result = $minifier->minify();
                $r->body = sprintf("/* %s */\n%s", $info_header, $cacheable_css_result);
            }
            else {
                $cacheable_css_result = $css;
                $r->body = sprintf("/* %s */\n%s", $info_header, $cacheable_css_result);
            }

            $r->flush();

            if($cfg->useCache() && !$isFromCache)
                file_put_contents("$cache_path/$cache_file_name", $cacheable_css_result);

        });
    }
]);
