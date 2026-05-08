# PreviewProtection Module for STORMS WebFrame

If the current page is deployed on a Customer-Preview Server: Prevent direct access by showing a login-page

## Usage

### Config

    public static function setupPreviewProtection(Lime\Module $moduleObj, $setupArgs) {
        return new class implements PreviewProtectionConfig {
            public function isEnabled() {
                return Helpers::isPreviewServer();
            }
            public function password() {
                return 'kundenlogin';
            }
        };
    }

### Util

  * When you are logged in press CTRL+SHIFT+ENTER in order to show a direct access token which allows to bypass the login page
