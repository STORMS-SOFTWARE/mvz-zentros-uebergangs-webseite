<?php
/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;

$this->extend([
    'setupOnFourOFour' => true, // without this for example the page active directive will cause errors on 404
    'init' => function(BladeDirectivesConfig $bdc, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {

        $moduleObj = $this;
        $base = $moduleObj->_base;

        // register all directives
                                      // inst name       // module base      // prefix
        $m_directives = Modules::inst('BladeDirectives', "$base/directives", 'BladeDirective');

        // bypass the default config method from project Config class and try to use a config method that has been passed back from the generic BladeDirectives-Module config; then boot the found modules
        foreach($bdc->directives() as $directive => $configMethod) {
            if( ($moduleObj = $m_directives->getModuleByName($directive)) !== null) {
                $moduleObj->setConfigMethod($configMethod);
                $moduleObj->boot();
            }
        }

        //$m_directives->bootModules(); // TODO this will boot all modules (event those that won't/can't be used because they should not be loaded through config (due config missing or returning false))
        $m_directives->setupModules(); // .. but this will only setup modules that should really be used...

    },
]);
