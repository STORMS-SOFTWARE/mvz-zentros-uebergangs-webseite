<?php

namespace STORMS\webframe\Modules;

use PHPMailer\PHPMailer\PHPMailer;

abstract class InstagramConfig implements ModuleConfig {

    /*
     * the password that needs to be entered to update the token after it was initially set
     * > ?set-instagram-token&password=foobar
     *
     * The def. of this method is required because otherwise everyone would be able to set the token
     */
    abstract public function tokenUpdatePassword() : string;

    /**
     * Posts are always cached in sake of performance. This method defines the time that needs to pass until the post-cache is updated.
     * @return int cache time in *MINUTES*
     */
    public function postCacheTime() : int {
        return 5; // ... minutes
    }

    /**
     * The file where the instagram posts are cached (as json).
     * @return string
     */
    public function postCacheFile() : string {
        return \Config::STORAGE_PATH . '/instagram-post-cache.json';
    }

    /**
     * The file where the initial token and also the auto-refreshed token is stored.
     * This token is used for requesting the instagram api.
     * @return string
     */
    public function tokenCacheFile() : string {
        return \Config::STORAGE_PATH . '/instagram-token.txt';
    }

    /*
     * This method CAN be defined in case you need some crazy own logic for referencing the token.
     * If this method returns null (default), the module will use the default way of reading the token (so it will take the token from the file storage defined through tokenCacheFile()).
     *
     * If you override this method, you need to make sure that the concrete token is returned
     */
    public function currentToken() : ?string {
        return null;
    }

    /**
     * Note that this should not be too high because posts are paginated and the api returns the url of the next page which requires us to make another request for every page. This quickly exhausts the api rate limit.
     * @return int
     */
    public function maxPosts () : int {
        return 20;
    }

    /**
     * Whether the module shall force users to update instagram data (via an ajax request) or not.
     * You might want to set this to false if you are wanting to let a cronjob update the data.
     * Note that even doing this throug ajax will exhaust the server. So using a cronjob is always the best way
     * @return bool
     */
    public function letVisitorsTriggerUpdate () : bool {
        return true;
    }

    /**
     * if you put a fully qualified class name here, the module will instance it for every post in the api response
     * @return string|null null: don't wrap - just return an array ; string: fully qualified class name the module shall instance for every post in the api response
     */
    public function postWrapperClass () : ?string {
        return null;
    }

    /**
     * Allows to set up a PHPMailer object to be used to send error reports to if the instagram quota is exceeded.
     * @return PHPMailer|false
     */
    public function errorReportMail () : PHPMailer|false {
        return false;
    }

}
