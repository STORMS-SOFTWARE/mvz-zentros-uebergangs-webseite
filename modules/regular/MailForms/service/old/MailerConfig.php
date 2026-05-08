<?php

namespace STORMS\webframe\Modules\MailForms;

class MailerConfig extends ConfigCommons  {

    private $created = null; // used for bot prot (calc time diff between form generation and post receive)
    private $min_delay = 10;

    private $recip=null;
    private $live_testing=false;

    private $log_mail = true;

    public function __construct() {
        $this->created = time();
    }

    /**
     * @return null
     */
    public function getRecip() {
        return $this->recip;
    }

    /**
     * @param null $recip
     */
    public function setRecip($recip) : void {
        $this->recip = $recip;
    }

    /**
     * @return bool
     */
    public function isLiveTestingMode() : bool {
        return $this->live_testing;
    }

    /**
     * @param bool $live_testing
     */
    public function setIsLiveTestingMode(bool $live_testing) : void {
        $this->live_testing = $live_testing;
    }

    /**
     * @return int
     */
    public function getCreated() : int {
        return $this->created;
    }

    /**
     * @param bool $log_mail
     */
    public function setLogMail(bool $log_mail) : void {
        $this->log_mail = $log_mail;
    }

    /**
     * @return bool
     */
    public function doLogMail() : bool {
        return $this->log_mail;
    }

    /**
     * Set the minimum delay that must fill the gap between initialization of the config object (coming together with the directive call) and receiving of the concrete post-data (in SECONDS)
     * @param int $min_delay
     */
    public function setMinimumDelay(int $min_delay) : void {
        $this->min_delay = $min_delay;
    }

    /**
     * @return int
     */
    public function getMinimumDelay() : int {
        return $this->min_delay;
    }

}
