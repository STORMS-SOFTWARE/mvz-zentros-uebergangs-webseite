<?php
/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\Core\Page;

$this->extend([
    'always' => function(bool $ret, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {

        //$moduleObj = $this;

        // append some magic stuff to the the beginning of the body
        $_->on('body.afterOpen', function() {
            ?>
            <script>
                window.isMobile = <?= _isMobile() ? 'true' : 'false' ?>;
                window.isDev = <?= _isDev() ? 'true' : 'false' ?>;
                window.page = '<?= Page::inst()->getName() ?>';
                
                // All touch devices (landscape, portrait)
                window.isTouchDevice = window.matchMedia("(pointer:coarse)").matches

                // Smartphone (landscape, portrait)
                window.isSmartphone = window.matchMedia("(pointer:coarse) and (max-width: 575.98px)").matches

                // Tablet (landscape+portrait)
                window.isTabletLP = window.matchMedia("(pointer:coarse) and (min-width: 576px)").matches

                // Tablet (landscape)
                window.isTabletLandscape = window.matchMedia("(pointer:coarse) and (min-width: 576px) and (orientation: landscape)").matches

                // Tablet (portrait)
                window.isTabletPortrait = window.matchMedia("(pointer:coarse) and (min-width: 576px) and (orientation: portrait)").matches
            </script>
            <?php
        });

        // add the current page-name as class to the doc-body
        /*$_->on('body.beforeClose', function() {
            ?>
            <script>
                if(typeof $ === "function")
                    $(document).ready(function(){
                        $('body').addClass('PAGE-<?= Page::inst()->getName() ?>')
                    });
            </script>
            <?php
        });*/
        DomManipulation::inst()->addBodyClass('page-'.Page::inst()->getName());
        
        DomManipulation::inst()->addBodyClass('browser-'._getBrowserName());

    },
]);
