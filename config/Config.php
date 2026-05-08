<?php

use STORMS\webframe\Core\{WebFrame, ConfigAccessibility};
use STORMS\webframe\Core\SEO\{SEO, SEOEntry};
use STORMS\webframe\Modules;
use STORMS\webframe\Modules\Module;
use STORMS\webframe\Helpers\cockpit\CockpitHelper;
use STORMS\webframe\Core\ConditionalSetupInvoker;

class Config {

    use ConfigAccessibility;

    /*
     * CORE
     */

    const LAYOUT                        = 'default';

    const MULTI_LANGUAGE                = false;
    const LANGUAGES                     = [ // this will completely be ignored by the framework if MULTI_LANGUAGE is set to false
        'de', // Deutsch {{de}}
        'en', // Englisch {{en}}
    ];
    const DEFAULT_LANGUAGE              = 'de'; // this will completely be ignored by the framework if MULTI_LANGUAGE is set to false

    const PAGES_DIR                     = 'pages';
    const DEFAULT_PAGE                  = 'home'; // the default page that shall be shown when the website is accessed without any page-name behind the URL; for example "home"

    const ROUTE_MAP                     = [
        //'src' => 'dest'
    ];

    const USE_404_PAGE                  = true; // if false all requests to none-existing pages will cause the frmagework to show the default page instead (note that this is pretty unlikely for SEO)

    const COCKPIT_RESTAPI_TOKEN         = null; // null -> try using public access for all data access
    const COCKPIT_ADMIN_RESTAPI_TOKEN   = null; // the admin token is needed for some special operations like getting user information
    const COCKPIT_BACKEND_URL           = ''; // the url where cockpit is installed
    const COCKPIT_LOCAL_BS_PATH         = null; // typical val: ../admin.CUSTOMER-DOMAIN.de/bootstrap.php
    const COCKPIT_MAJOR_VERSION         = CockpitHelper::COCKPIT_MAJOR_VERSION_2;

    const WEB_URL_FULL                  = 'https://www.mvz-zentros.de'; // the COMPLETE customer live URL (including protocol, sublevel and toplevel domain)

    // the customer's / project's contact e-mail address - used for the spamspan e-mail obfuscation:
    const EMAIL_SUFFIX                  = null; // if NULL: the framework will automatically extract the needed part from the WEB_URL_FULL
    const EMAIL_TLD                     = null; // if NULL: ... same as above

    const GMAPS_API_KEY                 = 'AIzaSyApi5uM-33FbKpen_ntTWPtziEJTK2Fpz4'; // GENERIC DEV KEY (@teamstormsmedia@gmail.com) - this will only work in all dev envs

    const RECAPTCHA_ENABLED             = false; // setting this to true will automatically do everything for you needed to use google's recaptcha
    const RECAPTCHA_SITE_KEY            = null;
    const RECAPTCHA_SECRET_KEY          = null;
    const RECAPTCHA_VERSION =           3; // NOTE: v3 is currently the only version supported - backwards comp. is planned for the future
	
    const GOOGLE_TRACKING_ID           = '';

    const STORAGE_PATH                  = '/storage';

    const CUSTOMER_NAME                 = '';

    const SEO_AUTO_INJECT_TITLE_DESCR   = true;

    /*
     * modules / extensions
     *
     * Notes:
     * - As the framework loads modules, it automatically tries to call a configurator method within this config class called "setup<ModuleName>()"
     * - When there is no configurator method within this config class OR the configurator method return FALSE: the framework considers the module to not being loaded
     *   So: The configurator methods must return Truthy in order to turn loading for the according module on
     */

    const BLADE_FILE_EXTENSION          = '.blade.php';

    const PRINT_FRAMEWORK_NOTICES       = false;

    public static function setupPreviewProtection(Module $moduleObj, $setupArgs) {
        return new class extends Modules\PreviewProtectionConfig {
            public function isEnabled() {
                //return $_SERVER['HTTP_HOST'] === 'some.special-case-url.com';
                return WebFrame::inst()->isPreviewServer();
            }
            public function password() {
                return 'kundenlogin';
            }
            public function protectionPageTitle() {
                return sprintf('%s | STORMS|MEDIA Webseiten Vorschau', 'Vorschau');
            }
        };
    }

    public static function setupImageManipulation (Module $moduleObj, $setupArgs) {
        return new class extends Modules\ImageManipulationConfig {
            public function isEnabled() {
                return true;
            }
            public function exposeHelperFunctions() {
                return true;
            }
        };
    }

    public static function setupWebFrameAssets(Module $moduleObj, $setupArgs) {
        return new class implements Modules\WebFrameAssetsConfig {
            public function isEnabled() {
                return true;
            }
            public function JSlibs() : array {
                return [
                    Modules\WebFrameAssetsConfig::WFASSET__SPAMSPAN
                ];
            }
        };
    }

    public static function setupModCssUtil(Module $moduleObj, $setupArgs) {
        return new class extends Modules\ModCssUtilConfig {
            public function isEnabled() {
                return true;
            }
            public function cssMap() {
                return [
                    'set1' => [
                        // replace          // with
                        'white'           => 'black',
                    ],
                ];
            }
            public function useCache() {
                return !WebFrame::isDevOrPreviewServer();
                //return false;
            }
        };
    }

    public static function setupSpacingsUtil(Module $moduleObj, $setupArgs) {
        return new class extends Modules\SpacingsUtilConfig {
            public function isEnabled() {
                return true;
            }
            public function includeNegativeSpacings() {
                return false;
            }
            public function logging() {
                return false;
            }
            public function generatorLogic() {
                return Modules\SpacingsUtil::__GEN_LOGIC__MINWIDTH_FOCUSPOINT;
            }
            public function focusBreakPoint() {
                return 'lg';
            }
        };
    }

    public static function setupRandomKeywordImgAlt(Module $moduleObj, $setupArgs) {
        return new class extends Modules\RandomKeywordImgAltConfig {
            public function isEnabled() {
                return true;
            }
            public function keywords() : array {
                return [
                    [
                        'keywords' => [
                            'WA1',
                            'WA2'
                        ],
                        'use' => 1
                    ],
                    [
                        'keywords' => [
                            'WB1',
                            'WB2'
                        ],
                        'use' => 1
                    ]
                ];
            }
            public function always_append() {
                return ' | ' . Config::CUSTOMER_NAME;
            }
        };
    }

    public static function setupDynamicSitemap(Module $moduleObj, $setupArgs) {
        return new class extends Modules\DynamicSitemapConfig {
            public function isEnabled() {
                return true;
            }
        };
    }

    public static function setupAutoFavIcon(Module $moduleObj, $setupArgs) {
        return new class implements Modules\AutoFavIconConfig {
            public function bg_color() {
                return [255, 255, 255];
            }
            public function isEnabled() {
                return true;
            }
            public function text_color() {
                return [255, 255, 255];
            }
            public function text_pos() {
                return [
                    'x' => 22,
                    'y' => 88
                ];
            }
            public function text() {
                return '?';
            }
            public function font_size() {
                return 80;
            }
        };
    }

    public static function setupBladeDirectives($moduleObj = null, $setupArgs = null) {
        return new class implements Modules\BladeDirectivesConfig {
            public function directives(): array {
                return [
                    Modules\BladeDirectivesConfig::BladeDirective__ActivePageCssClass => function(Module $moduleObj, $setupArgs) {
                        return new class implements Modules\BladeDirectiveActivePageCssClassConfig {
                            public function active_class() {
                                return 'active';
                            }
                            public function isEnabled() {
                                return true;
                            }
                        };
                    },
                    Modules\BladeDirectivesConfig::BladeDirective__Year => function(Module $moduleObj, $setupArgs) {
                        return true;
                    },
                    Modules\BladeDirectivesConfig::BladeDirective__LoremIpsumName => function(Module $moduleObj, $setupArgs) {
                        return true;
                    },
                    Modules\BladeDirectivesConfig::BladeDirective__Emphasis => function(Module $moduleObj, $setupArgs) {
                        return true;
                    },
                    Modules\BladeDirectivesConfig::BladeDirective__SpamSpan => function(Module $moduleObj, $setupArgs) {
                        return true;
                    }
                ];
            }
            public function isEnabled() {
                return true;
            }
        };
    }
	
    public static function setupMailForms() {
        return new class extends Modules\MailFormsConfig {
            public function isEnabled() {
                return true;
            }
            public function formConfigs(string $config_key, ?Closure &$success_callback = null, ?string $visitor_mail_addr = null) : STORMS\webframe\Modules\MailForms\MailerConfig|null|false {
	    	    // concrete example @ /modules/regular/MailForms/example-config.php
		    
                $mail_transport = STORMS\webframe\Helpers\PHPMailerSMTPCredentialFactory::createMailtrap('USER', 'PASS');

                return (new Modules\MailForms\MailerConfig())
                    ->setTransport($mail_transport);
            }
        };
    }

    public static function setup($_, ConditionalSetupInvoker $invoker, $is_micro_module_call) : ?ConditionalSetupInvoker {

	    date_default_timezone_set('Europe/Berlin');
        ini_set('date.timezone', 'Europe/Berlin');

        $invoker->onRegular(function () use ($_) {

            // location currently open helper
            $_->_OPENING_HOURS = $ot = [
                // 0:00 to 0:00 -> closed this complete day
                // Note that the ending-time must be greater then the beginning timespan - so if you want to say that closing time is midnight use 23:59:59
                'Mon' => ['8:00', '17:30'],
                'Tue' => ['8:00', '17:30'],
                'Wed' => ['8:00', '17:30'],
                'Thu' => ['8:00', '17:30'],
                'Fri' => ['8:00', '17:30'],
                'Sat' => ['0:00', '0:00'], // closed in complete
                'Sun' => ['0:00', '0:00'], // closed in complete
            ];
            $_->_isLocationOpen = (new \STORMS\webframe\Helpers\BusinessTimesHelper($ot))
                ->isLocationOpenAt();

            /*
             * SEO
             */
            SEO::setTitleBounds(10, 60);
            SEO::setDescriptionBounds(50, 156);

            SEO::setEntries(array_map(fn(SEOEntry $e) => str_starts_with($e->getTitle(), '!') ? $e->setTitle(substr($e->getTitle(), 1)) : $e->setTitle(sprintf('%s | %s', $e->getTitle(), Config::CUSTOMER_NAME)), [

                (new SEOEntry('impressum'))
                    ->setTitle('Impressum')
                    ->setDescription('Impressum zu unserer Seite. Bitte nutzen Sie bei Fragen das Formular auf der Kontaktseite, um mit uns in Verbindung zu treten.'),

                // the 'Datenschutz' page will be auto-set to "noindex" (german page version only)
            ]));

            if($_->isDev())
                SEO::check();

        });

        return $invoker;

    }

}
