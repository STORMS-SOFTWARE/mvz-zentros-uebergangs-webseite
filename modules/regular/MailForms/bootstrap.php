<?php
/*
 * MailForms
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\Core\Page;

use \Config as Config;
use STORMS\webframe\Modules\MailForms\ContactFormConfig;
use STORMS\webframe\Modules\MailForms\MailerConfig;

$this->forms_count = 0;

$this->extend([
    'init' => function(MailFormsConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {

        /* @var $moduleObj Module */
        $moduleObj = $this;

        $current_base = $moduleObj->_base;

        //if(!$_->requestIs('ajax'))
            //$_SESSION['mailer_configs'] = [];

        // declare blade-directive
        $blade->directive('defaultContactForm', function($expression) use ($blade, $_, $cfg, $moduleObj, $current_base) {
            return $moduleObj->buildInvoker('default_cf', $expression, true, null);
        });

        //$this->setup_route(func_get_args());
        //call_user_func_array([$this, 'setup_route'], func_get_args());

        // setup route for the mailhandler
        /*$_->router->post('(.*)mail/(\d{1,3})', function($request_src, $config_index) use ($moduleObj, $blade) {
            $cfg = unserialize($_SESSION['mailer_configs'][$config_index], ['allowed_classes' => ['STORMS\webframe\Modules\MailForms\MailerConfig']]);
            echo $blade->runChild($moduleObj->_base . '/mail-handler.blade.php', ['src' => $request_src, 'config' => $cfg]);
        });*/
        // setup route for the mailhandler
        $_->router->post('/mail', function() use ($moduleObj, $blade, $cfg, $_, $current_base) {
            echo $blade->runChild("$current_base/mail-handler.blade.php", compact('_', 'cfg', 'blade', 'current_base'));
        });

        /*
         * add scripting & css for the default all-in-one "for every template" mail handling
         */
        DomManipulation::addScript("/$current_base/assets/jquery-form-validate/jquery.validate.min.js");
        if($_->lang === 'de' || !$_->lang)
            DomManipulation::addScript("/$current_base/assets/jquery-form-validate/jquery.validate-translations-de.js");
        DomManipulation::addScript("/$current_base/assets/mailforms.js");
        DomManipulation::addStylesheet("/$current_base/assets/jquery-form-validate/validate-custom-styles.css");

        if(Config::getProp('RECAPTCHA_ENABLED')) {

            /*
             * load recaptcha js api
             * TODO optin!! (perhaps load the js api as soon as one hovers a form in order to hide the loading a bit more...)
             */
            DomManipulation::addScript('https://www.google.com/recaptcha/api.js?render=' . Config::getProp('RECAPTCHA_SITE_KEY')); // TODO this is for v3 - check if this is also fine for v2

            /*
             * expose recaptcha site key (because it's used by the recaptcha api)
             */
            $_->on('body.afterOpen', function() { ?>
                <script>
                    // @see RECAPTCHA_SITE_KEY in Config
                    window.grecaptcha_site_key = '<?= Config::getProp('RECAPTCHA_SITE_KEY', '-') ?>';
                    // @see RECAPTCHA_VERSION in Config
                    window.grecaptcha_version = <?= Config::getProp('RECAPTCHA_VERSION', 'null') ?>;
                </script><?php
            });

            /*
             * hide the recaptcha badge if the body class "show-grecaptcha-badge" is not set
             */
            $_->on('head.beforeClose', function() { ?>
                <style>
                    body:not(.show-grecaptcha-badge) .grecaptcha-badge {
                        display: none;
                    }
                </style><?php
            });
        }

    },
    /*
     * old (but functional) - will perhaps be removed
     */
    'default_cf' => function($blade, $extra_params = null, $fields = null, ?MailerConfig $mailerConfig, ?ContactFormConfig $cfConfig = null) {
        //d($fields, $config, $extra_params);
        //d($config); exit;

        /* @var $this Module */
        $current_base = $this->_base;

        /*
         * $fields will hold the concrete fields (as array OR string) that have been passed to @defaultContactForm as first parameter
         * $cfConfig instance of STORMS\webframe\Modules\MailForms\ContactFormConfig OR null if not given
         * $mailerConfig instance of STORMS\webframe\Modules\MailForms\MailerConfig
         */

        if($cfConfig === null)
            $cfConfig = new ContactFormConfig();

        if($cfConfig->getStyle() === ContactFormConfig::STYLE_01_FULL)
            DomManipulation::addStylesheet("/$current_base/assets/style01-full.css");
        elseif($cfConfig->getStyle() === ContactFormConfig::STYLE_01_BASIC)
            DomManipulation::addStylesheet("/$current_base/assets/style01-basic.css");
        DomManipulation::addScript("/$current_base/assets/style01.js");

        $mailerConfig->setRecaptchaKey($cfConfig->getRecaptchaKey());

        if(_isDev())
            $mailerConfig->setLogMail(false);

        if(!WebFrame::inst()->requestIs('ajax') && $this->forms_count === 0) {
            $_SESSION['mailer_configs'] = [];
            $this->forms_count++;
        }
        $_SESSION['mailer_configs'][] = serialize($mailerConfig);

        if(is_string($fields))
            if(strpos($fields, ',') !== false)
                $fields = explode(',', $fields);
            else
                $fields = explode(' ', $fields);
        if(!isset($fields) || empty($fields)) // if there are no parameters provided in for the blade directive..
            $fields = []; // an empty array will be treated as "use all fields" by the default-contact-form implementation

        $fields = array_map('trim', array_map('strtolower', $fields));

        return $blade->runChild("$current_base/views/default-contact-form.blade.php", array_merge(compact('fields', 'cfConfig', 'blade'), [
            'config_index' => count($_SESSION['mailer_configs'])-1
        ]));
    },
    /*'setup_route' => function(MailFormsConfig $cfg, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade) {
        $moduleObj = $this;
        $_->router->post('(.*)mail/(\d{1,3})', function($request_src, $config_index) use ($moduleObj, $blade) {
            $cfg = unserialize($_SESSION['mailer_configs'][$config_index], ['allowed_classes' => ['STORMS\webframe\Modules\MailForms\MailerConfig']]);
            echo $blade->runChild($moduleObj->_base . '/mail-handler.blade.php', ['src' => $request_src, 'config' => $cfg]);
        });
    }*/
]);
