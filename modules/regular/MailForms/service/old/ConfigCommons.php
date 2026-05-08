<?php

namespace STORMS\webframe\Modules\MailForms;

/*
 * this class contains properties and methods that are relevant to MailerConfig AND ContactFormConfig
 */

class ConfigCommons {

    private $recaptcha_key = null; // can be null (then recaptcha won't be used) or string

    /**
     * @return string
     */
    public function setRecaptchaKey(?string $key) : self {
        $this->recaptcha_key = $key;
        return $this;
    }

    /**
     *
     */
    public function getRecaptchaKey() : ?string {
        return $this->recaptcha_key;
    }
    
    /**
     * 
     */
    public function usesRecaptcha() : bool {
        return $this->recaptcha_key !== null;
    }

}
