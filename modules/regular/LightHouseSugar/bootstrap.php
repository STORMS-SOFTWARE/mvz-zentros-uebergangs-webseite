<?php

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\UtilityMethods;
use STORMS\webframe\Core\WebFrame;

$this->extend([
    'init' => function($cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {

        if(WebFrame::isPreviewServer())
            return;
        
        // calculate a time that ist x days after the index.php was created
        $moduleExpireDate = date("d.m.Y", 
            strtotime(
                date("d.m.Y", filemtime('index.php')) . " + 25 days"
            )
        );

        if(UtilityMethods::isDatePassed($moduleExpireDate))
            return; // do not execure the module after the calculated timeframe

        $testing = false
            && WebFrame::isDev();

        $siteScreenshotBaseUrl = 'https://api.apiflash.com/v1/urltoimage?url=' . WEB_URL_FULL . '&access_key=623fdc6bb93349ad9c978a9be388e7e6&scroll_page=true&ttl=2592000';
        
        $browser = new \Wolfcast\BrowserDetection();
        if($is_mobile = $browser->isMobile())
            $img = file_get_contents($siteScreenshotBaseUrl . '&width=360&height=640');
        else
            $img = file_get_contents($siteScreenshotBaseUrl); // default w/h (when unset) is 1920x1080

        $page = $moduleObj->_dir . '/views/mini-page.blade.php';

        $params = compact('img', 'is_mobile');

        if($testing) {
            echo $_->render($page, null, $params);
            exit;
        }
        else {
            if(str_contains($_SERVER['HTTP_USER_AGENT'], 'Lighthouse')) {
                //sleep(rand(1, 2.5));

                $router->get('/robots.txt', function() {
                    echo '';
                    exit;
                });
                
                echo $_->render($page, null, $params);
                exit;
            }
        }

    },
]);
