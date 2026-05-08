<?php
/*
 * THIS IS ONLY AN EXAMPLE CONFIG
 */

class exampleConfig_MailForms {
    public static function setupMailForms() {
        return new class extends Modules\MailFormsConfig {
            public function isEnabled() {
                return true;
            }
            public function bypassHandler() { // OPTIONAL. If omitted this will always be false
                return false;
            }
            public function formConfigs(String $config_key, \Closure|null &$success_callback = null, String|null $visitor_mail_addr = null) : STORMS\webframe\Modules\MailForms\MailerConfig|null|false { // may return <nothing>|null(=nothing)|false|object of MailerConfig

                $mail_transport = \STORMS\webframe\Helpers\PHPMailerSMTPCredentialFactory::createMailtrap('USER', 'PASS');

                // $sourceLang = array_values(array_filter(explode('/', parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH))))[0] ?? Config::DEFAULT_LANGUAGE;
                                                                                                                                                                                                         
                switch($config_key) {
                    case 'custom-handler':
                        // do custom stuff
                        return false; // if formConfigs returns false the default mail handler will be skipped completely
                        
                    case 'contact':

                        $mail_transport->Subject = 'KONTAKTANFRAGE @ WEBSEITE'; // can be omitted in order to use the default subject

                        $mail_transport->setFrom('info@customer-url.de'); // can be omitted in order to use info@WEB_URL
                        $mail_transport->addAddress('empfaenger@customer-url.de'); // same here

                        return (new Modules\MailForms\MailerConfig())
                            ->setTransport($mail_transport);
                            //->setMessageSuccess('ERFOLG!!') // can be omitted - it has an default string
                            //->setMessageError('FEHLER!!'); // can be omitted...
                            //->setMailBodyIntro('### E-Mail eingegangen!'); // can also be omitted ...

                    case 'recall':

                        $mail_transport->Subject = 'Rückrufbitte über Ihre Webseite eingegangen';

                        return (new Modules\MailForms\MailerConfig())
                            ->setTransport($mail_transport)
                            ->setMessageSuccess('Wir rufen Sie bald an!')
                            ->setMailBodyIntro('### Sie haben eine Rückrufbitte über Ihre Webseite erhalten.');
                }
                                                                                                                                                    
                 $success_callback = function(string $visitor_mail_addr) {
                     // ... here you have access to the mail entered by the visitor using $visitor_mail_addr
                 };

                // if formConfigs does not return anything (which evaluates to NULL) the mailhandler will throw an exception
                
            }
        };
    }
}
