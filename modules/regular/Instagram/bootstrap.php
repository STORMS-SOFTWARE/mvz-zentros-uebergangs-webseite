<?php
/*
 * Instagram Module
 */

/* @var $this \STORMS\webframe\Modules\Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\Core\Page;

use \Config as Config;
use STORMS\webframe\LimeJuice\Response;

$this->extend([
    'init' => function(InstagramConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {

        $current_base = $this->_base;

        $token_file = ltrim($cfg->tokenCacheFile(), '/');

        $password = $_GET['password'] ?? null;

        $view_params = [
            'cfg' => $cfg,
            'blade' => $blade,
            '_' => $_,
            'page' => Page::inst()->getName(false),
            'base' => $current_base
        ];

        /*
         * token setup & update stuff
         */
        if(isset($_POST['instagram-token'])) {
            if(!file_exists($token_file) || $password === $cfg->tokenUpdatePassword()) {
                file_put_contents($token_file, $_POST['instagram-token']);
                $router->all("/(.*)", function($original_req) use ($view_params, $blade, $current_base) {
                    echo $blade->run("$current_base/views/token-stored.blade.php", $view_params);
                });
            }
        }

        if(!file_exists($token_file) || isset($_GET['force-set-instagram-token'])) {
            if(!file_exists($token_file) || $password === $cfg->tokenUpdatePassword()) {
                $router->all("/(.*)", function($original_req) use ($view_params, $blade, $current_base, $token_file) {
                    echo $blade->run("$current_base/views/set-token.blade.php", $view_params + [
                        'initial' => !file_exists($token_file) ? true : false
                    ]);
                });
            }
        }
        elseif(isset($_GET['set-instagram-token']) && $password === $cfg->tokenUpdatePassword()) {
            $router->all("/(.*)", function($original_req) use ($view_params, $blade, $current_base) {
                echo $blade->run("$current_base/views/token-exists.blade.php", $view_params);
            });
        }
        // --- end token setting & updating stuff

        // for testing: host some kind of copy of the instagram api locally which uses the cached file fetched previously
        if(WebFrame::isDev()) {
            $router->get('/instagram/cache/api/{localResultPath}', function(string $localResultPath) use ($router) {
                $localResultPath = str_replace('.', '/', $localResultPath);
                $result_files = glob("$localResultPath/*.json");
                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 0;
                if($page === 0)
                    $result_file = $result_files[0];
                else
                    $result_file = $result_files[$page] ?? null;
                if($result_file && is_file($result_file)) {
                    header('Content-type: ' . Response::$mimeTypes['json']);
                    $d = json_decode(file_get_contents($result_file), true);
                    $hasMore = $page < count($result_files) - 1;
                    $page_next = null;
                    if($hasMore) {
                        $full_request = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                        $request_chunks = parse_url($full_request);
                        parse_str($request_chunks['query'] ?? '', $qry); // parse query into array
                        $qry['page'] = $page + 1;
                        $page_next = $request_chunks['scheme'] . '://' . $request_chunks['host'] . $request_chunks['path'] . '?' . http_build_query($qry);
                    }
                    $d['paging'] = ['next' => $page_next];
                    die(json_encode($d));
                }
            });
        }
        // end testing local cache api

        $current_token = Instagram::getToken();

        /*
         * if the token is set up properly: add the cache update mechanism
         */
        if($current_token !== null) {
            if($cfg->letVisitorsTriggerUpdate())
                DomManipulation::addScript("$current_base/assets/instagram.js");

            $router->get('/refresh-instagram', function() use ($router) { // Note query params are respected. So you could for example call /refresh-instagram?page-variant=<your variant>&page=0&force
                $forceUpdate = !(WebFrame::isDev() && isset($_GET['force']));
                echo json_encode(Instagram::update($forceUpdate)); // if $forceUpdate is true, the blocking time is ignored by the update() method
            });
        }

    }
]);
