<?php

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\Helpers\Storage;

class DomManipulation {

    protected static $instance = null;

    /**
     *
     * @return DomManipulation
     */
    public static function inst() {
        if(!isset(self::$instance))
            self::$instance = new self();
        return self::$instance;
    }

    // ==========================

    /*
     * according to body class...
     */
    protected $body_classes = [];

    public function addBodyClass($class) : array {
        if(!in_array($class, $this->body_classes))
            $this->body_classes[] = $class;
        return $this->body_classes;
    }
    public function removeBodyClass($class) : array {
        $index = array_search($class, $this->body_classes);
        if($index !== false){
            unset($this->body_classes[$index]);
        }
        return $this->body_classes;
    }

    public function getBodyClasses() : array {
        return $this->body_classes;
    }

    public function getBodyClassesStringed() : string {
        return sprintf(' %s ', implode(' ', $this->body_classes)) ;
    }
    //--------------------------


    /*
     * according to stylesheets and scripts
     */
    public $stylesheets = [];
    public $scripts = [];

    public static function addStylesheet($stylesheet_name, $base = 'assets/css') {
        if(is_file(ltrim($stylesheet_name, '/')))
            $stylesheet = $stylesheet_name;
        elseif(strpos($stylesheet_name, 'http') === 0)
            $stylesheet = $stylesheet_name;
        else
            $stylesheet = Storage::asset($stylesheet_name, $base, ['css']);

        $inst = self::inst();
        $bn = basename($stylesheet);
        if(!array_key_exists($bn, $inst->stylesheets))
            $inst->stylesheets[$bn] = $stylesheet;

    }

    public static function addScript($script_name, $base = 'assets/js') {
        if(is_file(ltrim($script_name, '/')))
            $script = $script_name;
        elseif(strpos($script_name, 'http') === 0)
            $script = $script_name;
        else
            $script = Storage::asset($script_name, $base, ['js']);

        $inst = self::inst();
        $bn = basename($script);
        if(!array_key_exists($bn, $inst->scripts))
            $inst->scripts[$bn] = $script;

    }
    //--------------------------

    /*
     * according to stylesheets / scripts
     */
    public static function afterJquery(callable|string $closure_or_asset_uri) {
        WebFrame::inst()->on('body.afterJquery', function() use ($closure_or_asset_uri) {
            if(is_callable($closure_or_asset_uri))
                $closure_or_asset_uri();
            elseif(is_string($closure_or_asset_uri)) {
                if(str_ends_with($closure_or_asset_uri, '.js'))
                    echo WebFrame::script($closure_or_asset_uri);
                elseif(str_ends_with($closure_or_asset_uri, '.css'))
                    echo WebFrame::style($closure_or_asset_uri);
                else
                    echo $closure_or_asset_uri;
            }
        });
    }

}
