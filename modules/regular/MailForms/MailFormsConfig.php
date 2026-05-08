<?php

namespace STORMS\webframe\Modules;

abstract class MailFormsConfig implements ModuleConfig {

    abstract public function formConfigs (String $config_key, \Closure/* params: [0: visitor email address] */|null &$success_callback = null, String|null $visitor_mail_addr = null) : MailForms\MailerConfig|null|false;
    
    /*
     * Setting this to true make the module break before the handler that does all the magic is called.
     * This way we can write our very own project specific handler
     */
    public function bypassHandler () {
        return false;
    }

}
