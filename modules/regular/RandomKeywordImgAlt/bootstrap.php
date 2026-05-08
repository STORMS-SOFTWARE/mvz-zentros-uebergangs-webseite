<?php
/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;

$this->extend([
    'init' => function(RandomKeywordImgAltConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {
        $_->getRandomAltTag = $getRandomAltTag = function() use ($_, $cfg) {
            $alt_str = '';
            foreach ($cfg->keywords() as $i => $keyword_grp){
                $alt_str .= $_->getRandomEntryFrom($keyword_grp['keywords'], $keyword_grp['use']);
                if($i < count($cfg->keywords())-1)
                    $alt_str .= ' | ';
            }
            return $alt_str . $cfg->always_append();
        };
        $_->on('bladeone.compiling', function(&$contents, &$fileName) use ($getRandomAltTag, $_) {
            $replace_map = [
                'alt=""' => 'alt="<?= $_->getRandomAltTag() ?>"'
            ];
            $contents = str_replace(array_keys($replace_map), array_values($replace_map), $contents);
        });
    },
]);
