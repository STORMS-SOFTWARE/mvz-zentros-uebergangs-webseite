<?php

/*
 * Warning: Module complexity level: Hogwarts master degree
 */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\LimeJuice\Response;

/**
 * SpacingsUtil
 * ===============================
 *
 * allows to use margin and padding class names within the class attrib in the blade file markup - they will automatically be recognized and then served in a stylesheet.
 *
 * <div class="YOUR_PADDING_AND_MARGIN_CLASSES_HERE"></div>
 *
 * Examples:
 *
 * with cfg "includeNegativeSpacings" set to true:
 * mt-10 -> margin-top: -10px
 * "includeNegativeSpacings" @ false:
 * mt-10 -> margin-top: 10px (so the result will then be the same as using mt10)
 *
 * and simply:
 * mt10 -> margin-top: 10px
 *
 * mtsm10 -> @media only screen and [([<max|min>-width]px)] | [(<min-width>px) and <max-width>px)]{ margin-top: 10px }
 * mtmd10 -> @media only screen and <same for configured md breakpoint>{ margin-top: 10px }
 * ...
 *
 * m10 -> margin: 10px
 * my10 -> margin-top: 10px ; margin-bottom: 10px
 * mx10 -> same as above for X-axis
 *
 * all the examples above can also be used with "p" in order to use paddings instead of margins:
 *
 * p10 -> padding: 10px
 * pt10 -> padding-top: 10px
 *
 * and so on...
 *
 * further more you can use "-i" or just "i" at the end of each class to make the generated css having the !important suffix:
 * mt10i / mt10-i -> margin-top: 10px !important
 *
 * Further Nots:
 *  - if "includeNegativeSpacings" is set to false: using dashes leads to positive matching spacing classes: mt-20 -> same as mt20
 *
 * Available breakpoint identifier: xs, sm, md, lg, xl
 *
 */
class SpacingsUtil {

    const __GEN_LOGIC__MAXWIDTH                 = 'gl_max_width';
    const __GEN_LOGIC__MINWIDTH                 = 'gl_min_width';
    const __GEN_LOGIC__MINWIDTH_MAXWIDTH        = 'gl_min_max_width';
    const __GEN_LOGIC__MINWIDTH_FOCUSPOINT      = 'gl_min_width_focus';

    public static $storage_location = null; // will be set to the values that has been set in the config method

    public static $breakpoints_available = [
        // bs 3 breakpoints (in case we ever need them): xs => 480px ; sm => 768px ; md => 992px ; lg => 1200px (https://stackoverflow.com/a/30542215/9261261)
        /*
         * all following configurations are the same as the bootstrap 4 breakpoints (+ typical xs-breakpoint thats not present in bs4)
         */
        self::__GEN_LOGIC__MAXWIDTH => [
            'none' => null,
            'xs' => '480px',
            'sm' => '576px',
            'md' => '768px',
            'lg' => '992px',
            'xl' => '1200px'
        ],
        self::__GEN_LOGIC__MINWIDTH => [
            'none' => null,
            'xs' => null,
            'sm' => '576px',
            'md' => '768px',
            'lg' => '992px',
            'xl' => '1200px'
        ],
        self::__GEN_LOGIC__MINWIDTH_MAXWIDTH => [
            'none' => null,
            // https://getbootstrap.com/docs/4.6/layout/overview/ -> "targeting a single segment of screen"
            'xs' => [null,      '575.98px'], // Extra small devices (portrait phones, less than 576px)
            'sm' => ['576px',   '767.98px'], // Small devices (landscape phones, 576px and up)
            'md' => ['768px',   '991.98px'], // Medium devices (tablets, 768px and up)
            'lg' => ['992px',   '1199.98px'], // Large devices (desktops, 992px and up)
            'xl' => ['1200px',  null] // Extra large devices (large desktops, 1200px and up)
        ],
        self::__GEN_LOGIC__MINWIDTH_FOCUSPOINT => [
            // 'xs' -> guess this would be the same as using this module with __GEN_LOGIC__MINWIDTH - so we do not need it.
            'sm' => [
                'none' => null,
                'xs' => [null,      '575.98px'],
                'sm' => ['576px',   null],
                'md' => ['768px',   null],
                'lg' => ['992px',   null],
                'xl' => ['1200px',  null]
            ],
            'md' => [
                'none' => null,
                'xs' => [null,      '767.98px'],
                'sm' => ['576px',   '767.98px'],
                'md' => ['768px',   null],
                'lg' => ['992px',   null],
                'xl' => ['1200px',  null]
            ],
            'lg' => [
                'none' => null,
                'xs' => [null,      '991.98px'],
                'sm' => ['576px',   '991.98px'],
                'md' => ['768px',   '991.98px'],
                'lg' => ['992px',   null],
                'xl' => ['1200px',  null]
            ],
            'xl' => [
                'none' => null,
                'xs' => [null,      '1199.98px'],
                'sm' => ['576px',   '1199.98px'],
                'md' => ['768px',   '1199.98px'],
                'lg' => ['992px',   '1199.98px'],
                'xl' => ['1200px',  null]
            ],
        ],
    ];

    public static $breakpoints = null; // will be set to the concrete breakpoints to use (by the setup method)

    //public static $important_str_representation = '-i'; // css classes are marked important by this string

    public static $spacings = []; // internal var - do not touch

    /* @var $cfg SpacingsUtilConfig */
    public static $cfg = null;

    public static $paths = [ // those will be introduced within the setup method
        'ROOT'      => null,
        'data'      => null,
        'css-cache' => null
    ];

    public static $testingMode = null; // this can only set from outside (setting it in the setup method does not allow access through the processContent methods however)

    public static function setup(SpacingsUtilConfig $cfg, WebFrame $_, \Bramus\Router\Router $router) {

        self::$cfg = $cfg;

        // set the concrete breakpoints to use by the config definition
        self::$breakpoints = self::$breakpoints_available[$cfg->generatorLogic()];
        // ... special case for focusBreakPoint (this is an addition to the line before - this does NOT render the line above obsolete)
        if($cfg->generatorLogic() === self::__GEN_LOGIC__MINWIDTH_FOCUSPOINT)
            self::$breakpoints = self::$breakpoints[$cfg->focusBreakPoint()];

        // MAGIC ROUTE REGISTRATION
        $router->get('/spacings.min.css', __NAMESPACE__ . '\SpacingsUtil::getGeneratedStylesheet');

        self::$storage_location = ltrim($cfg->storageLocation(), '/');

        self::$paths = [
            'ROOT'          => self::$storage_location,
            'data'          => self::$storage_location . '/data',
            'css-cache'     => self::$storage_location . '/css-cache'
        ];

        // make sure module dirs exist
        if(!is_dir(self::$paths['ROOT'])) {
            mkdir(self::$paths['ROOT']);
            mkdir(self::$paths['data']);
            mkdir(self::$paths['css-cache']);
        }

        $adjust_bladefile_name = function ($fileName) {
            return str_replace('-blade-php', '', WebFrame::inst()->slugify($fileName));
        };

        $_->on('bladeone.compiling', function(&$contents, &$fileName) use ($adjust_bladefile_name) {
            SpacingsUtil::processContent($contents, $adjust_bladefile_name($fileName));
        });

        /*
         * store information of which views are included in the current page render construct/render-chain
         */
        $_->on('bladeone.run:before', function($view, $variables) use ($adjust_bladefile_name) {
            // we actually do not need this line... (even if this is very strange for we need it in the runChild method)
            SpacingsUtil::$spacings[$adjust_bladefile_name($view)] = 0; // 0 => marker that indicates that this view is unchanged
        });
        $_->on('bladeone.runChild:before', function($view, $variables) use ($adjust_bladefile_name) {
            SpacingsUtil::$spacings[$adjust_bladefile_name($view)] = 0;
        });
        // ---

        // before the closing head tag: add the css ref to the DOM
        $_->on($cfg->placeCssRef() ?? 'body.beforeClose', function() { // Note: dont use "head.beforeClose": we got something like a race condition - not everything is yet compiled here...

            /* @var $chain_identifier int */
            $chain_identifier = sprintf('%u', crc32(implode('_', array_keys(SpacingsUtil::$spacings)))); // reason for sprintf: https://www.php.net/manual/en/function.crc32.php

            // store information on which files are included in the current render-chain in the session - so the magic route is able to resolve & know which spacing-data files are relevant for the current render-chain
            // Note: without using a multidimensional array we may occur collisions when multiple requests run at the same time
            // Another note: From time to time again I come to the point asking my self why the heck I store this data within the session... The answer is simple: I just want to hide as much as possible internal data from the visitor's sight
            $_SESSION['module-spacings-util--spacings'][$chain_identifier] = SpacingsUtil::$spacings;

            printf(
                '<link href="/spacings.min.css?%s%s" type="text/css" rel="stylesheet">',
                $chain_identifier,
                array_sum(SpacingsUtil::$spacings) > 0 ? '&'.time() : '' // we use time() as param just so our browser won't deliver a cached version of the spacings
            );
        });

    }

    /**
     * Search for spacing-css classes within the given markup/dom.
     * The SpacingUtil module sets up an hook that is triggered when bladeone compiles files. That hook sends the markup/dom-content through the following method
     *
     * @param $fileName may be null for testing - but in no other case
     * @throws \Exception if the method is called without a filename on a prod env.
     */
    public static function processContent(string $contents, ?string $fileName = null) {

        if(!self::$testingMode && $fileName === null)
            throw new \Exception('[WF:SpacingUtil] $fileName @ processContent may NOT BE NULL');

        // Note: at this point $fileName contains the *adjusted name* of the view

        $default_breakpoint = 'none'; // when no sm, md, lg ... is given in the class name of the compiled file. This enables to put everythig to a default break point (like md) when using class names like "mb10"

        $spacings = [];
        $spacings['file'] = $fileName;

        /*
         * magic spot: find all spacing classes within the currently processed view
         * TODO make a simple preg_match + foreach here - using "preg_replace_callback" does not make any sense at all... (but I guess it consumes more performance then a simple foreach)
         */
        // note on the first part of the pattern:
        // /["\'\s](?<full_matc...
        // -> by this we require the class to be introduces by quotes OR an whitespace - so for example <div class="wrap-2"> will not lead to p-2 being generated...
        // allow two -- in front of a val: /["\'\s](?<full_match>(?<spacing>[mp]{1}[tbrlxy]?)(?<breakpoint>xs|sm|md|lg|xl)?(?<px_val>(?:\-{1,2})?\d{1,3})(?:\-?(?<params>[ie]+))?)/
        preg_replace_callback('/["\'\s]'.self::$cfg->classPrefix().'(?<full_match>(?<spacing>[mp]{1}[tbrlxy]?)(?<breakpoint>xs|sm|md|lg|xl)?(?<px_val>\-?\d{1,3})(?:\-?(?<params>[ie]+))?)/', function ($a) use ($default_breakpoint, $fileName, &$spacings) {

            $params = $a['params'] ?? ''; // may contain "i" or "e" or "ie" (or null if not passed)

            // populate array that is later persisted as json within a file
            $breakpoint = empty($a['breakpoint']) ? $default_breakpoint : $a['breakpoint'];
            $val = intval($a['px_val']); // TODO perhaps already apply abs() at this point if negative spacings are disabled? (see generateClassStringDev. method)

            //$isImportant = isset($a['important']) && !empty($a['important']);

            $isImportant = stripos($params, 'i') !== false;
            $isExclusive = stripos($params, 'e') !== false;
            if($isExclusive)
                $isExclusiveReasonable = $breakpoint !== $default_breakpoint; // because 'exclusive' does not make any sense at all if we do not have an breakpoint

            // using a key that may be duplicate makes all entries being absolute unique what is just perfect so we do not have same spacings multiple times within our data.
            // Important note: The "." (or any other string through the var $params) is needed so the index is string based, not numeric. This is important in later use.
            $key = sprintf('%d%s', $val, empty($params) ? '.' : $params);
            // according to the generated key above, this would be an alternative: https://stackoverflow.com/questions/4100488/a-numeric-string-as-array-key-in-php
            $spacings['spacings'][$breakpoint][$a['spacing']][$key] = [
                'val' => $val,
                'imp' => $isImportant,
                'ex' => $isExclusive,
                'exok' => $isExclusiveReasonable ?? null,
                'params' => $params // we need this for easier class-string generation
                //'full_match' => $a['full_match']
            ];

        }, $contents);

        // For the current handled file is running though the compile method: its source changed - so set a mark as indicator for this
        // (we need this mark later when generating the concrete spacing classes)
        self::$spacings[$fileName] = 1;

        if($fileName !== null) {
            // Store plain spacing information (without css string stuff) in a data-json file.
            // This information is later needed in order to build the concrete css bundle for the render-chain
            file_put_contents(self::$paths['data'] . "/$fileName.json", json_encode($spacings));
        }
        else // should only happen in testing case
            return $spacings;

    }

    /*
     * generates strings in the format:
     * .<abbreviation><spacingVal>{<spacing css>}
     * concrete return example:
     * .mb20{margin-bottom:20px}
     */
    public static function generateClassDefinitionString(array $spacing_info, string $spacing_classes_type, string $breakpoint) : string {

        // the following map is used to resolve the css property that shall be used for the generation
        $spacing_abbreviation_map = [
            // Note: $val already contains to *complete value string* (like for example 20px!important)
            'my' => function($val) { return sprintf('margin-top:%s;margin-bottom:%s', $val, $val); },
            'mx' => function($val) { return sprintf('margin-left:%s;margin-right:%s', $val, $val); },
            'py' => function($val) { return sprintf('padding-top:%s;padding-bottom:%s', $val, $val); },
            'px' => function($val) { return sprintf('padding-left:%s;padding-right:%s', $val, $val); },

            'mt' => 'margin-top',
            'mb' => 'margin-bottom',
            'mr' => 'margin-right',
            'ml' => 'margin-left',

            'pb' => 'padding-bottom',
            'pt' => 'padding-top',
            'pl' => 'padding-left',
            'pr' => 'padding-right',

            'm' => 'margin',
            'p' => 'padding',
        ];

        /** @var $val int */
        //$is_negative = substr($spacing_info['val'], 0, 2) === '--';
        //$val = self::$cfg->includeNegativeSpacings() && $is_negative ? intval(strrchr($spacing_info['val'],'-')) : abs(strrchr($spacing_info['val'], '-'));
        $val = self::$cfg->includeNegativeSpacings() ? $spacing_info['val'] : abs($spacing_info['val']);

        // use the modded value instead of the original value if contained in the $spacing_info
        if(isset($spacing_info['val_modded']) && !self::$cfg->includeNegativeSpacings())
            $spacing_info['val_modded'] = abs($spacing_info['val_modded']);
        $val = $spacing_info['val_modded'] ?? $val;

        $val_suffix = $val===0?'':'px'; // for zero values we do not need the 'px' (so we can spare 2 byte in the generated css for every zero value) - for everything else then 0 we do need a suffix

        // build the complete val string containing the number, the suffix (if we have one) and the important keyword
        // so for example: >20px!important< or >0!important< or >10px< or >0< and so on
        $val_str = sprintf('%d%s%s', $val, $val_suffix, ($spacing_info['imp'] ? '!important' : ''));

        //$class_val_pattern = $val; // if we use this: when using the class mb-5 in markup a class mb5 will be generated
        $class_val_pattern = $spacing_info['val']; // while using this will cause using mb-5 to generate a class mb-5 which contains the positive val if the config says to ignore negativ vals

        return sprintf(
            '.%s%s%s%s%s{%s%s%s}',
            self::$cfg->classPrefix(),
            $spacing_classes_type, // STRING 'mb', 'pt' ...
            $breakpoint === 'none' ? '' : $breakpoint, // if we have no breakpoint (determined by the fact that the entry is within the "none" group of the data array): set empty string here
            $class_val_pattern, // INT - only the plain val - always without "px" or something
            //$spacing_info['imp'] ? '-i' : '',
            isset($spacing_info['params']) && trim($spacing_info['params']) !== '' ? sprintf('-%s', $spacing_info['params']) : '',
            // 1. find the css property for the generation (for example "margin-bottom") in $spacing_abbreviation_map. The contained type may be a string or a closure.
            ($usedTransformCallback = is_callable($spacing_abbreviation_map[$spacing_classes_type])) // ... check if the property definition its either a callback that transforms the given value ...
                ? $spacing_abbreviation_map[$spacing_classes_type]($val_str) // ... then call that callback, passing our current (complete) value string ...
                : $spacing_abbreviation_map[$spacing_classes_type], // ... or just get the simple single property from the map and use it directly
            // 2. the transform callback must build the complete css-property string - so we don't need to add the ":" behind the property. But if we got a single simple property from the map - we need the ":"
            $usedTransformCallback ? '' : ':',
            // 3. ... same as [2.] for the concrete css val string
            $usedTransformCallback ? '' : $val_str
        );

    }

    public static function generateMediaQueryStringFor(string $generator, string $breakpoint /* none, xs, sm... */, string $classes_sting, ?array $breakpoints = null) : string {
        return [
            'none' => function ($breakpoint, $classes_sting) {
                return "\n$classes_sting";
            },
            'min-width' => function ($breakpoint, $classes_sting, $breakpoints = null) {
                $min_width = ($breakpoints ?: self::$breakpoints)[$breakpoint];
                if(is_array($min_width))
                    $min_width = $min_width[0];
                if($min_width === null)
                    return "\n$classes_sting";
                return sprintf(
                    "\n@media only screen and (min-width:%s){%s}",
                    $min_width,
                    $classes_sting
                );
            },
            'max-width' => function ($breakpoint, $classes_sting, $breakpoints = null) {
                $max_width = ($breakpoints ?: self::$breakpoints)[$breakpoint];
                if(is_array($max_width))
                    $max_width = $max_width[1];
                return sprintf(
                    "\n@media only screen and (max-width:%s){%s}",
                    $max_width,
                    $classes_sting
                );
            },
            'min-max-width' => function ($breakpoint, $classes_sting, $breakpoints = null) {
                $min_width = ($breakpoints ?: self::$breakpoints)[$breakpoint][0];
                $max_width = ($breakpoints ?: self::$breakpoints)[$breakpoint][1];
                return sprintf(
                    "\n@media only screen and (min-width:%s) and (max-width:%s){%s}",
                    $min_width,
                    $max_width,
                    $classes_sting
                );
            }
        ][$generator]($breakpoint, $classes_sting, $breakpoints);
    }


    /**
     * @param string $type [exclusive|default]
     * @return array the concrete breakpoints to use for the current configured generator logic
     */
    public static function getBreakpointsForType (string $type) : array {
        switch ($type) {
            case 'exclusive':
                return self::$breakpoints_available[self::__GEN_LOGIC__MINWIDTH_MAXWIDTH];
            case 'default':
                return self::$breakpoints; // we could also return false/null because the "$mediaQueryStrGenerators" function will use the breakpoints coming through the config then
        }
        return []; // this should actually never happen
    }

    /**
     * This function gets passed a <string> breakpoint and it will determine the needed generator query keys
     * @param $breakpoint [nome|xs|sm|md...]
     * @return string 'none' | 'max-width' | 'min-width' | 'min-max-width'
     * TODO this could use some logical cleanup ...
     */
    public static function determineGenerator (string $breakpoint, string $type) : string {
        //if (in_array($type, ['exclusive', 'reduction_additions'])) {
        if ($type === 'exclusive') {
            if ($breakpoint === 'xs')
                return 'max-width';
            elseif ($breakpoint === 'xl')
                return 'min-width';
            else
                return 'min-max-width';
        }

        if ($breakpoint === 'none')
            return $breakpoint; // (-> 'none')
        else {
            // simple min-width OR max-width
            if(in_array(self::$cfg->generatorLogic(), [self::__GEN_LOGIC__MAXWIDTH, self::__GEN_LOGIC__MINWIDTH])) { // so: if configured genLogic is min-width oder max-width
                return [
                    self::__GEN_LOGIC__MAXWIDTH => 'max-width',
                    self::__GEN_LOGIC__MINWIDTH => 'min-width'
                ][self::$cfg->generatorLogic()];
            }
            else { // little more complex min AND max-width logic
                if(self::$breakpoints[$breakpoint][0] === null)
                    return 'max-width';
                elseif(self::$breakpoints[$breakpoint][1] === null)
                    return 'min-width';
                else
                    return 'min-max-width';
            }

        }
    }

    public static function getGeneratedStylesheet() {

        // the var $chain_identifier won't contain concrete data but only the identifier that allows us to resolve the chain using the chain-identifier as key for the stored session data
        $chain_identifier = (array_keys($_GET)[0])??false; // the render-chain identifier

        // (try to) restore the concrete file render chain by the passed render-chain-identifier (which is nothing more then some kind of hash/checksum number)
        if(
            !is_bool($chain_identifier)
            && isset($_SESSION['module-spacings-util--spacings'])
            && isset($_SESSION['module-spacings-util--spacings'][$chain_identifier]) && !empty($_SESSION['module-spacings-util--spacings'][$chain_identifier])
        ) {
            $file_render_chain = $_SESSION['module-spacings-util--spacings'][$chain_identifier];
        }
        else {
            // if no render chain was found for the passed chain-identifier we likely have some stranger playing around with the get var - so just send out 404
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        // does the combined css file need to be generated new? (because the source of any file contained in the render chain changed)
        $needs_refresh = array_sum($file_render_chain) > 0;
        //$needs_refresh = (array_keys($_GET)[1])??false;

        // logging
        if(self::$cfg->logging()) {
            if(!is_file($f=self::$paths['ROOT'] . '/log.txt'))
                touch($f);
            else
                $log = file_get_contents($f);

            $log = print_r([
                'datetime' => date('d.m.Y H:i:s'),
                'needs_refresh' => $needs_refresh ? 'yes' : 'no',
                'cache_file_name' => "$chain_identifier.min.css",
                'component_chain' => $file_render_chain
            ], true) . $log;
            file_put_contents($f, $log);
        }
        // -- logging

        // internal vars - no need to touch
        $file_buffer_string = '';
        // ---

        //if($needs_refresh || !is_file(self::$storage_location . "/css-cache/$chain_identifier.min.css")) {
        if($needs_refresh || self::$testingMode) {

            $file_buffer_string = sprintf("/* UPDATED: %s */\n", date('d.m.Y H:i:s'));

            if(!self::$testingMode) { // USE PROD DATA
                // gather needed spacing files and data contained in that files and put them together into one array we can process in order to create concrete css
                $data_merged = [];
                foreach($file_render_chain as $file_name => $changed) {
                    if (is_file($f = sprintf('%s/%s.json', self::$paths['data'], $file_name))) {
                        $c = file_get_contents($f);
                        $data = json_decode($c, true);
                        if (!is_array($data['spacings']??false))
                            continue;
                        //$data_merged = array_merge_recursive($data_merged, $data);
                        $data_merged = array_replace_recursive($data_merged, $data);
                    }
                }
            }
            else { // USE TESTING DATA
                $data_merged = self::$cfg->testingData();
            }

            //dd($data_merged);

            // Make sure the order of the spacings to render always matches the order in which they are defined within the $breakpoints array.
            // Furthermore throw out breakpoint slots that have no concrete spacing entries
            $breakpoints = array_filter(array_merge(array_combine(array_keys(self::$breakpoints), array_fill(0, count(self::$breakpoints), null)), $data_merged['spacings'] ?? []));

            // the following vars are propagated within the next upcoming loop
            $grouped_classes_strs = ['default' => [], 'exclusive' => []]; // those will be sub-grouped by breakpoint
            $adjusted_classes_strs_by_breakpoint = []; // will (only if active through the config) hold the (by percentage) adjusted values grouped by their breakpoint (only for 'none' breakpoints)

            //$debug_class_counter = 0; // debug var in order to keep track of the classes that should have been generated

            /*
             * just iterate the (ordered) spacings that are needed for the current render chain and ...
             * 1. generate concrete css class strings for the given data ...
             * 2. ... and assign them to the "default"/"exclusive" grouping array which is processes through an extra loop after that
             *
             * var $breakpoint => STRING 'none', 'xs', 'sm' ...
             * var $spacing_group => mb[], pt[] ... or if it does not contain any data this will contain the breakpoint pixel string (coming as an unwanted side effect through the array_merge above)
             */
            foreach ($breakpoints as $breakpoint /* <- ...none,md,lg... */ => $spacing_group /* <- [mt=>['XY.'=>['val'=>...]], mb=>...] */) {
                /*
                 * var $spacing_data => ARRAY with the px-val + info on if this is important or not (and further params)
                 */
                foreach($spacing_group as $spacing_classes_type /* <- STRING 'mb', 'pt' */ => $spacing_data /* <- ['XY.'=>val,imp,ex... */) {

                    // make every spacing being unique (so we do not have duplicates to generate)
                    //$spacing_data = array_unique($spacing_data, SORT_REGULAR); // TODO I think we do not need this any more and can spare performance by commenting this out

                    foreach ($spacing_data as $spacing_info /* <- [val=>,imp=>,ex=>....] */) {

                        // generateClassDefinitionString() generates a single definition with class string/name and css props
                        $curr_classes_str = self::generateClassDefinitionString(
                            $spacing_info,              // <- ARRAY [val=>,imp=>....]
                            $spacing_classes_type,      // <- STRING 'mb', 'pt'
                            $breakpoint                 // <- STRING ...none,md,lg...
                        ); // example result: ".mt20{margin-top:20px}"

                        //$debug_class_counter++;

                        $exclusive = $spacing_info['ex'] ?? false; // just tells if the class had the -e parameter - nothing more
                        $exclusiveReasonable = $spacing_info['exok'] ?? null; // false if -e was used on a class without a breakpoint definition (eg. mb20-e (because that would make no sense))

                        if($exclusive) {
                            if($exclusiveReasonable) // prevent generation of pointless exclusive classes (eg. mt20-e)
                                $grouped_classes_strs['exclusive'][$breakpoint][] = $curr_classes_str;
                        }
                        else
                            $grouped_classes_strs['default'][$breakpoint][] = $curr_classes_str;

                        /*
                         * perhaps (defined by config) generate some (max-width) media queries with percentage reductions/additions
                         */
                        if(self::$cfg->autoBreakpointlessClassMedias() && !$exclusive && $breakpoint === 'none') {
                            $value_mod_info_collection = self::$cfg->autoBreakpointlessClassMedias()[self::$cfg->focusBreakPoint()];
                            foreach($value_mod_info_collection as $breakpoint_2 => $value_mod_info) { // NOTE: do not call the var "$breakpoint" - otherwise it will overwrite the outer loop's var
                                if(is_array($value_mod_info)) {
                                    $subtract = $value_mod_info[1]; // -1 => sub ; +1/1 => add

                                    $percentage_change = $value_mod_info[0];
                                    $percent_diff = ($spacing_info['val'] / 100) * $percentage_change;

                                    //$spacing_info['val_modded'] = round($spacing_info['val'] + ($subtract ? -1 * $percent_diff : $percent_diff)); // however this line won't work
                                    if($subtract === -1) // sub
                                        $spacing_info['val_modded'] = round($spacing_info['val'] - $percent_diff);
                                    else // add
                                        $spacing_info['val_modded'] = round($spacing_info['val'] + $percent_diff);

                                    $adjusted_classes_strs_by_breakpoint[$breakpoint_2][] = self::generateClassDefinitionString(
                                        $spacing_info,
                                        $spacing_classes_type,
                                        $breakpoint
                                    );
                                }
                            }
                        }

                    }
                }
            }

            /*
             * Generate the media query stings for (and with) the class strings pre-grouped by the loop above
             *
             * $break_point__css_string__map => array with the breakpoint (xs, md, ...) as key and an sub-array containing all the css strings for this breakpoint
             */
            $perhapsAddDebugLine = function (string $str) { // ... only when in testing mode
                return self::$testingMode ? $str : '';
            };
            foreach($grouped_classes_strs as $type /* <- 'exclusive' | 'default' */ => $breakpoint__css_string__map) {

                $file_buffer_string .= $perhapsAddDebugLine("\n/* $type */");
                foreach($breakpoint__css_string__map as $breakpoint /* none, xs, sm... */ => $aClassDefinitionStrings /* => array */) { // this loop runs ONCE for each BREAKPOINT in EACH GROUP ('exclusive' / 'default')

                    $full_css = implode($aClassDefinitionStrings); // will then contain multiple css class defs like ".mt20{margin-top:20px}.mb123{margin-bottom:123px}"

                    /* @var string $breakpoint 'none' | 'xs' | 'sm' ... */
                    /* @var string $type 'exclusive' | 'default' */
                    /* @var string $generator 'none' | 'max-width' | 'min-width' | 'min-max-width' */
                    /* @var array $breakpoints */
                    $generator = self::determineGenerator($breakpoint, $type);
                    $breakpoints = self::getBreakpointsForType($type);

                    $file_buffer_string .= self::generateMediaQueryStringFor($generator, $breakpoint, $full_css, $breakpoints);

                    /*
                     * Add the media query strings for the adjusted class values to the result (if needed (through config))
                     * This is only relevant / done for the 'none' breakpoint because it does not make any sense to generate adjusted classes for those classes with breakpoint definition
                     */
                    if($breakpoint === 'none' && $type === 'default') {
                        $file_buffer_string .= $perhapsAddDebugLine("\n/* % */");
                        foreach ($adjusted_classes_strs_by_breakpoint as $breakpoint_2 => $aClassDefStrings) {
                            $generator = self::determineGenerator($breakpoint_2, $type_2 = 'exclusive');
                            $breakpoints = self::getBreakpointsForType($type_2);

                            $file_buffer_string .= self::generateMediaQueryStringFor($generator, $breakpoint_2, implode($aClassDefStrings), $breakpoints);
                        }
                        $file_buffer_string .= $perhapsAddDebugLine("\n/* -% */");
                    }

                }
                $file_buffer_string .= $perhapsAddDebugLine("\n/* -$type */");

            }

            // only for testing - but don't remove
            if(_isDev() && self::$cfg->testingData() !== null && isset($_GET['test'])) {

                // TODO this is going to be a framework method (in the WebFrame class) soon (but not yet)... change this to WebFrame::trim_multiline in some time
                function trim_multiline(string $str, bool $remove_blank_lines = false) {
                    $array_filter__Proxy = function(array $a) use ($remove_blank_lines) {
                        return $remove_blank_lines ? array_filter($a) : $a;
                    };
                    return implode("\n", $array_filter__Proxy(array_map('trim', explode("\n", $str))));
                }

                $st_percent = null;
                $expectation_str = trim_multiline(self::$cfg->testingResultExpectation(), true);
                similar_text($file_buffer_string, $expectation_str, $st_percent);
                echo "/*\nGenerator logic: ".self::$cfg->generatorLogic() . (self::$cfg->generatorLogic() === self::__GEN_LOGIC__MINWIDTH_FOCUSPOINT ? ' (' . self::$cfg->focusBreakPoint() . ')' : '') .
                    "\n\nExpectation met @ $st_percent % \nExpected:\n\n".$expectation_str."\n\nConcrete result after the comment end:\n*/\n\n";
            }
            // --- testing

            file_put_contents(self::$paths['css-cache'] . "/$chain_identifier.min.css", $file_buffer_string);

            $ret = $file_buffer_string;

        }
        else {
            if(is_file($f=self::$paths['css-cache'] . "/$chain_identifier.min.css"))
                $ret = file_get_contents($f);
            else
                $ret = "/* error loading cached css: $f */";
        }

        $r = new Response();
        $r->mime = 'css';
        $r->body = $ret;
        $r->flush();

    }

}
