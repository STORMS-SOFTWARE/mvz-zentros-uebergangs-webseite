<?php
/*
 * module that generates css classes by a given sass file
 */

call_user_func(function() { // because the function scope allow us to get vars that were defined locally by parsing the css file and evaling it

    if(is_dir('assets/css/scss'))
        $src_dir = 'scss';
    elseif(is_dir('assets/css/sass'))
        $src_dir = 'sass';
    else
        throw new Exception('SCSS source dir not found');

    // filter out lines that do not contain a scss var with an hex val (like: >> $foo : #fff <<)
    $colors = implode(array_filter(file("assets/css/{$src_dir}/_colors.scss"), function($row) {
        return preg_match('/\$[a-zA-Z0-9_-]*.*:.*#[0-9a-fA-F]{3,6}/', $row) !== 0;
    }));

    // remove characters that would destroy eval
    $colors_processed = str_replace(["\t", ' ', ':', ';', 'ä', 'ü', 'ö', 'ß', '-'], ['', '', '= "', '";', 'ae', 'ue', 'oe', 'ss', '_'], $colors);
    eval($colors_processed);
    // get the array of vars that have been defined through the eval but remove vars we don't want there
    $css_colors = array_diff_key(get_defined_vars(), ['colors' => '', 'colors_processed' => '', 'src_dir' => '']);

    define('WITH_IMPORTANT', true);

    echo '/* <br/> &nbsp;* GENERATED VIA <br/> &nbsp;* http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].' <br/> &nbsp;* '.count($css_colors).' SCSS/SASS VARS FOUND (and used for generation) <br/> &nbsp;*/<br/><br/>';

    foreach ([
        // namespace    => css-attribute
        'text'          => 'color',
        'color'         => 'color',
        'bg'            => 'background-color',
        ] as $ns => $attrib) {
        foreach ($css_colors as $var_name => $color) {
            ob_start();
            ?>
            .sm_{{$ns}}-{{$var_name}} {
                {!!str_repeat('&nbsp;', 4)!!}{{$attrib}}: {{$color}};
            }
            @if(WITH_IMPORTANT)
            .sm_{{$ns}}-{{$var_name}}-i {
                {!!str_repeat('&nbsp;', 4)!!}{{$attrib}}: {{$color}} !important;
            }
            @endif
            <?php
            echo nl2br(ob_get_clean());
        }
    }

});
