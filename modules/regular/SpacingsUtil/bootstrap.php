<?php
/*
 * SpacingsUtil
 * - See the SpacingUtil class (lib/SpacingUtil.php) header comment for detailed information on what it does
 *
 * Dev-Note: do not make a micro module out of this!
 * Micro-Modules are only loaded if they are explicitly requested with an URL that contains their name. So as a micro module this module would not be loaded when a normal request to any page is done.
 * But the module's core functionality:
 * - to add the reference to the dynamic generation stylesheet to the body
 * - ! and hook into the compile method of blade in order to find spacing classes that needs a concrete css generation
 * need to be there in a normal (none-micro) request. So we cannot easily make a micro module out of this (without perhaps splitting this module in a regular + micro-module dir)
 *
 */

/*
 * long working legacy version: https://github.com/STORMS-SOFTWARE/webframe/blob/483846ef81ca6eb7eed5829e0351d374a16b05af/modules/regular/SpacingsUtil/legacy.tar.gz
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;

$this->extend([
    'init' => function(SpacingsUtilConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {

        $_->appendIncludePath($this->_base.'/lib');

        // TESTING MODE
        define('SpacingUtil_TESTING', false); // use testing mode?? (will not be ignored on prod servers)

        if(SpacingUtil_TESTING && _isDev() && isset($_GET['test'])) { // override the config with a testing config if we are in testing mode
            $cfg = new class extends SpacingsUtilConfig {
                use SpacingsUtilTestingConfig;
            };
            SpacingsUtil::$testingMode = true;
        }
        // --- TESTING MODE

        SpacingsUtil::setup($cfg, $_, $router);

    },
]);
