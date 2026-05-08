<?php
/*
 * Blade directive for ....... ( @_active )
 * TODO better description of this module
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame as WebFrame;
use STORMS\webframe\Modules\BladeDirectiveActivePageCssClassConfig as BladeDirectiveActivePageCssClassConfig;

$this->extend([
    'init' => function(BladeDirectiveActivePageCssClassConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {
        /* @var $moduleObj Module */
        $moduleObj = $this;

        $current_base = $moduleObj->_base;

        /*$current_page_active = function ($page, $active_class=null) use ($_) {
            $active_class = $active_class ?? \Config::setupBladeDirectiveActivePageCssClass()->active_class();
            //dd($_->currentPageHasParent(ltrim($page,'/')) ? $active_class : 'NOPE - ' . $page);
            return $_->currentPageHasParent(ltrim($page,'/')) ? $active_class : '';
        };*/

        $blade->directive('_active', function($expression) use ($blade, $_, $cfg, $moduleObj) {
            $str = $blade->stripParentheses($expression);
            //$str = $blade->stripQuotes($str);
            //return '<?php s(STORMS\webframe\Modules\Modules::inst("BladeDirectives")); exit; ? >';
            return '<?php echo STORMS\webframe\Modules\Modules::inst("BladeDirectives")->getModuleByName(\''.$moduleObj->getName().'\')->currentPageActive('.$str.') ?>';
        });
    },
    //'currentPageActive' => function ($page, $active_class=null) {
    'currentPageActive' => function ($check_page) {

        $_ = WebFrame::inst();

        /* @var $this Module */

        //$active_class = $active_class ?? \Config::setupBladeDirectiveActivePageCssClass()->active_class();
        //$active_class = \Config::setupBladeDirectiveActivePageCssClass()->active_class();
        $active_class = $this->getConfig()->active_class();
        //error_log(get_class($this));

        $check_page = ltrim($check_page,'/');

        /* @var $page Page */
        $page = $_->page;

        //return $_->currentPageHasParent($check_page) || $page->getName() === \Config::DEFAULT_PAGE ? "$active_class module-page-active--active" : 'module-page-active--inactive';
        //die($page->getName());

        return $_->currentPageHasParent($check_page) || ($page->getName() === \Config::DEFAULT_PAGE && $check_page === $page->getName()) ? "$active_class module-page-active--active" : 'module-page-active--inactive';
    }
]);
