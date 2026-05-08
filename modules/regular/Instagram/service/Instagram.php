<?php

namespace STORMS\webframe\Modules;

/*
 * Note that this class is designed to provide cached instagram posts (but this aspect may be bypassed by using the update() method with $useTimeBlocked = false)
 */

use STORMS\webframe\Core\WebFrame;

class Instagram {

    const MAX_POSTS_INFINITE = -1;

    public static function getModuleConfig () : InstagramConfig {
        /* @var $cfg InstagramConfig */
        $cfg = \Config::forModule('regular', 'Instagram');
        return $cfg;
    }

    public static function getPostCacheFile () : string {
        return ltrim(self::getModuleConfig()->postCacheFile(), '/');
    }

    public static function getTokenCacheFile () : string {
        return ltrim(self::getModuleConfig()->tokenCacheFile(), '/');
    }

    public static function postCacheFileExists () : bool {
        return file_exists(self::getPostCacheFile());
    }

    public static function tokenCacheFileExists () : bool {
        return file_exists(self::getTokenCacheFile());
    }

    public static function getPostCacheTime () : int {
        return self::getModuleConfig()->postCacheTime();
    }

    // TODO use those methods above in the following methods

    /**
     * returns the token while respecting possible custom logic defined in the config
     */
    public static function getToken () : ?string {
        $cfg = self::getModuleConfig();

        $token = $cfg->currentToken();

        if($token === null) {
            $token_cache_file = ltrim($cfg->tokenCacheFile(), '/');
            if(is_file($token_cache_file))
                $token = file_get_contents($token_cache_file);
        }

        return $token;
    }

    /**
     * @param bool $useTimeBlocked shall the cache time configured be respected when trying to update the posts cache? (>false< could also be considered a "force update"). Will be false'd through /refresh-instagram?force (this is a special impl.)
     * @return array status of the update
     */
    public static function update (bool $useTimeBlocked = true) : array {

        $cfg = self::getModuleConfig();

        $post_cache_time = $cfg->postCacheTime();
        $post_cache_file = ltrim($cfg->postCacheFile(), '/');
        $token_cache_file = ltrim($cfg->tokenCacheFile(), '/');

        if(!is_file($post_cache_file))
            touch($post_cache_file);

        $access_token = self::getToken();

        if(filemtime($token_cache_file) < strtotime('-30 days')) { // request a new token after some time and store it
            $res = json_decode(file_get_contents('https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token='.$access_token), true);
            $access_token = $res['access_token'];
            file_put_contents($token_cache_file, $access_token);
        }

        if($useTimeBlocked) { // when update() is triggered through '/refresh-instagram' route the time blocking can be bypassed by using >force< in the query (/refresh-instagram?force)
            if(filemtime($post_cache_file) > time() - $post_cache_time * 60 ) {
                return [
                    'updated' => false,
                    'next' => $post_cache_time*60 - (time() - filemtime($post_cache_file))
                ];
            }
        }

        define(
            /*
             * TESTING_MODE is implemented only for dev purposes - as soon as the module works completely, this will never be used again.
             * Setting TESTING_MODE to true will cause that real instagram api data is just fetched once (!). The response then will be cached. All further calls to the update() method will then use the cached data instead of requesting the real instagram api.
             * This is really only meant for testing while developing this update method
             * When TESTING_MODE is false, the instagram api will be requested every time the update() method is called (while respecting the blocking time)
             */
            'TESTING_MODE', false                     /* don't touch this -> */ && WebFrame::isDev() // < it makes sure this never happens in live envs
            // Note if you want to force update (ignore the time blocking) you can use >force< as query on the refresh url: /refresh-instagram?force
        );
        { // stuff that is only relevant when TESTING_MODE is true
            $responsePersistenceDirBase = ltrim(\Config::STORAGE_PATH, '/') . '/instagram-results'; // only used in this >if< scope - make sure it does NOT end with a tailing '/'
            $iterations=0; // only used in $perhapsPersistResponse()
            $testingModeCacheExists = is_dir($responsePersistenceDirBase) && count($resPerDirEntities = glob("$responsePersistenceDirBase/*", GLOB_ONLYDIR)) >= 1;
            if($testingModeCacheExists)
                $responsePersistenceDir = $resPerDirEntities[0];
            else
                $responsePersistenceDir = $responsePersistenceDirBase . '/' . time(); // do not move into perhapsPersistResponse() ! the call to time() will cause multiple dirs being created because instagram api requests take a few seconds to complete
        }
        $perhapsPersistResponse = function($data) use ($responsePersistenceDir, &$iterations, $testingModeCacheExists) { // < currently only for DEBUGGING/DEV purposes. This function is only called in TESTING_MODE. It just stores the instagram api responses to the file system for later dev use
            if(TESTING_MODE && !$testingModeCacheExists) {
                error_log('persisting instagram api response ' . $iterations);
                $ext = pathinfo(Instagram::getPostCacheFile(), PATHINFO_EXTENSION);
                $file_num = $iterations++;
                // create a file name with the same scheme as the post cache file but with an identifier for the current request
                $dest_file_name = substr(basename(Instagram::getPostCacheFile()), 0, -strlen($ext)-1).'-'.$file_num.'.'.$ext;
                if(!is_dir($responsePersistenceDir))
                    mkdir($responsePersistenceDir, 0777, true);
                file_put_contents($responsePersistenceDir . '/' . $dest_file_name, json_encode($data));
            }
        };
        /*
         * generate request url of prod or testing/dev
         */
        if(TESTING_MODE && $testingModeCacheExists) {
            error_log('using cached instagram api responses');
            $cacheBaseDir = str_replace('/', '.', $resPerDirEntities[0]); // path to the cache dir (which contains the dirs named by timestamps) (convert to dot notation, so it can be used in the url)
            $request_chunks = parse_url($_SERVER['REQUEST_URI']); // get url chunks of current request
            parse_str($request_chunks['query'] ?? '', $qry); // parse query of the current request into array so we can pass it on to the next request
            $requestUrl = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://".$_SERVER['HTTP_HOST'] . "/instagram/cache/api/$cacheBaseDir?". http_build_query($qry);
        }
        else { // for PROD
            // // https://developers.facebook.com/docs/instagram-basic-display-api/reference/media/#fields
            $requestUrl = "https://graph.instagram.com/me/media?fields=id,media_type,media_url,timestamp,permalink,caption,thumbnail_url&access_token=$access_token"; // &limit=$max_posts
            if(WebFrame::isDev()) error_log('fetching data from instagram api (not locally)');
        }

        // function just here for recursion (this function is relevant for dev+prod)
        $getInstagramMedia = function($url, &$data) use ($perhapsPersistResponse, &$getInstagramMedia, $cfg) : void {
            $max = Instagram::getModuleConfig()->maxPosts();

            set_error_handler(function ($severity, $message, $file, $line) use ($cfg) {
                if($cfg->errorReportMail() !== false && count($cfg->errorReportMail()->getAllRecipientAddresses()) > 0) {
                    $mailer = $cfg->errorReportMail();
                    if(!$mailer->Subject)
                        $mailer->Subject = '['.\Config::CUSTOMER_NAME.'] Instagram request quota überschritten';
                    if(!$mailer->Body)
                        $mailer->Body = "Für die Webseite " . \Config::WEB_URL_FULL . " wurde in den letzten 5 Stunden mindestens ein Mal die Request Quota für Instagram überschritten.";
                    WebFrame::throttleClosure(function() use ($mailer) {
                        $mailer->send();
                    }, 18000 /* 5 hours */);
                }
                else
                    error_log('instagram quota exceeded');
            });
            // ----------
            $data_tmp = json_decode(file_get_contents($url), true); // error-trapped by set_error_handler above
            // ----------
            if($cfg->errorReportMail() !== false)
                restore_error_handler();

            $perhapsPersistResponse($data_tmp); // will only do something when TESTING_MODE is true

            $page_next = $data_tmp['paging']['next'] ?? null;
            if(is_array($data_tmp['data']))
                $data = array_merge($data, $data_tmp['data']);

            if($page_next && (count($data) < $max || $max === Instagram::MAX_POSTS_INFINITE))
                $getInstagramMedia($page_next, $data);
            if(count($data) > $max)
                $data = array_slice($data, 0, $max);
        };
        $media = []; // modified by calls to getInstagramMedia (as passed as reference)
        $getInstagramMedia($requestUrl, $media);

        file_put_contents($post_cache_file, json_encode($media));

        return [
            'updated' => true
        ];
    }

    // constants for $types (media_types) @ getCachedPosts() params
    const MEDIA_TYPE_IMAGE             = 'IMAGE';
    const MEDIA_TYPE_VIDEO             = 'VIDEO';
    const MEDIA_TYPE_CAROUSEL_ALBUM    = 'CAROUSEL_ALBUM';

    /**
     *
     * @param array<string> $types media types to filter for. Available types: IMAGE, VIDEO, CAROUSEL_ALBUM. An empty array will return all types.
     * @return array|false
     * @throws \Exception
     */
    public static function getCachedPosts (array|string $types = []) : array|false {

        $cfg = self::getModuleConfig();

        if(is_string($types))
            $types = [$types];

        $post_cache_file = ltrim($cfg->postCacheFile(), '/');

        if(!is_file($post_cache_file))
            return false;

        $posts = json_decode(file_get_contents($post_cache_file), true);

        if(!empty($types)) {
            $posts = array_values(array_filter($posts, function($entry) use ($types) {
                return in_array($entry['media_type'], $types);
            }));
        }

        if($class = $cfg->postWrapperClass()) {
            if(!class_exists($class))
                throw new \Exception('The class desired post wrapper class >'.$class.'< does not exist or has not been loaded!');

            array_walk($posts, function(&$entry) use ($class) {
                $entry = new $class($entry);
            });
        }

        return $posts;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public static function getMostCurrentPosts () : void {
        throw new \Exception('
            In sake of performance this class does not allow to directly update & return data to prevent *accidentally* doing this (it would be extremely slow). 
            If you would like to have most recent data do this: 
            > Instagram::update(false); Instagram::getCachedPosts() < 
            instead!
            ');
    }

}
