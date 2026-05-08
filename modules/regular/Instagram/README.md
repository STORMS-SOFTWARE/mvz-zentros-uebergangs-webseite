# Instagram Module for STORMS webframe

Allow easy embedding of Instagram posts.

Initially, when the module is activated, the framework asks for the API token automatically. 

After that you can call ?set-instagram-token&password=<password from project-config> to re-enter the token.

## Usage

    use STORMS\webframe\Modules\Instagram;
    $posts = Instagram::getCachedPosts([Instagram::MEDIA_TYPE_IMAGE]);

### Config

    public static function setupInstagram () {
        return new class extends Modules\InstagramConfig {
            public function isEnabled() {
                return true;
            }
            // further methods here...
        };
    }

**mandatory config methods:**

    public function tokenUpdatePassword() : string {
        return 'YOUR PASSWORD HERE';
    }

This is the password required to re-enter the token and overwrite the one currently configured (perhaps you will never need it)

**optional config methods:**

those have default values, but you can overwrite them if you want to

    public function cacheTime() : int
    public function tokenCacheFile() : string
    public function postCacheFile() : string
    public function currentToken() : ?string 

see the doc comments @ InstagramConfig class for more information on what the methods do. 
