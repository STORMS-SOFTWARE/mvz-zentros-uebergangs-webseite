<?php
/*
 * PreviewServer Protection Module
 *
 * Note: use ctrl+shift+enter on the preview page protection / login page in order to get the direct access token.
 * Then you can access the preview using <CUSTOMER_SUBDOMAIN>.storms-media.de?storms_preview=<TOKEN YOU JUST OBTAINED HERE>
 * one can also use ?bypass as querystring in order to omit the login and sign in
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\Core\Page;

use \Config as Config;

$this->extend([
    'init' => function(PreviewProtectionConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {

        $module = $this;
        $current_base = $this->_base;

        // allow logout
        if(isset($_GET['preview-logout'])) {
            unset($_SESSION['preview__logged_in']);
            unset($_SESSION['preview__log_in_trys']);
            $_->reroute('/');
        }

        // check correct auth
        if(@$_POST['password'] === $cfg->password() || ($_GET['storms_preview']??false) === md5(basename($_SERVER['DOCUMENT_ROOT'])) || ($_GET['bypass']??null) !== null || strpos($_SERVER['QUERY_STRING'], 'bypass') !== false) {
            $_SESSION['preview__logged_in'] = true;
            $_SESSION['preview__log_in_trys'] = 0;
            if(!isset($_GET['no_redirect']) && ($_GET['bypass']??null) === null) {
                if(isset($_SERVER['REQUEST_URI'])) {
                    $query = http_build_query(array_filter($_GET, function($key) { // build a new query string without the vars causing an redirect
                        return !in_array($key, ['storms_preview', 'bypass']);
                    }, ARRAY_FILTER_USE_KEY));
                    $_->reroute(strtok($_SERVER['REQUEST_URI'], '?') . '?' . $query); // and reroute to the same page without the loop-causing query string parts (all others remain)
                }
                else
                    $_->reroute(Config::DEFAULT_PAGE);
            }
        }
        elseif(isset($_POST['password']))
            $_SESSION['preview__log_in_trys'] = ($_SESSION['preview__log_in_trys']??1) +1;

        // append quick access token view if logged in
        if($_SESSION['preview__logged_in'] ?? false) {
            $_->on('body.beforeClose', function() use ($current_base, $blade, $cfg, $_, $module){
                echo $blade->run("$current_base/views/preview-token.blade.php", [
                    'cfg' => $cfg,
                    'blade' => $blade,
                    '_' => $_,
                    'getDirectAccessUrl' => $module->getDirectAccessUrl,
                ]);
            });
        }

        // render login page if currently not logged in
        if(!($_SESSION['preview__logged_in'] ?? false)) {
            $router->all("/(.*)", function($original_req) use ($current_base, $blade, $cfg, $_, $module) {
            //$router->before('GET', '/.*', function() use ($current_base, $blade, $cfg, $_) {

                header('HTTP/1.0 403 Forbidden');

                if($original_req === 'login') //  reset login trys if the user used the back link to /login
                    $_SESSION['preview__log_in_trys'] = 0;

                //$_->page = new Page(null, 'preview-prot'); // TODO after changing the page class
                echo $blade->run("$current_base/views/login.blade.php", [
                    'cfg' => $cfg,
                    'blade' => $blade,
                    '_' => $_,
                    'page' => Page::inst()->getName(false),
                    'base' => $current_base,
                    'getDirectAccessUrl' => $module->getDirectAccessUrl,
                ]);

            });
        }

    },
    'getDirectAccessUrl' => function () {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?storms_preview=" . md5(basename($_SERVER['DOCUMENT_ROOT']));
    }
]);
