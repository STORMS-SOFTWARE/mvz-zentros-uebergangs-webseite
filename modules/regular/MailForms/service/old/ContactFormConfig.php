<?php

namespace STORMS\webframe\Modules\MailForms;

class ContactFormConfig extends ConfigCommons  {

    const STYLE_01_BASIC    = 0;
    const STYLE_01_FULL     = 1;
    
    private $submit_button_caption = 'SENDEN';
    private $inverse_field_list_behavior = false;
    private $show_dsgvo_check = false;

    private $style = self::STYLE_01_BASIC;

    private $message_success = 'Danke für Ihr Interesse. Ihre Nachricht wurde erfolgreich versendet!';
    private $message_error = 'Es ist ein nicht weiter spezifizierter Fehler aufgetreten. Bitte versuchen Sie es später erneut, oder wenden sich direkt per E-Mail an uns.';
    private $message_validation_error = 'Es wurden nicht alle Felder ordnungsgemäß ausgefüllt. Bitte korrigieren Sie die rot umrandeten Felder.';

    /**
     * @return string
     */
    public function getSubmitButtonCaption() : string {
        return $this->submit_button_caption;
    }

    /**
     * @param string $submit_button_caption
     */
    public function setSubmitButtonCaption(string $submit_button_caption) : ContactFormConfig {
        $this->submit_button_caption = $submit_button_caption;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInverseFieldListBehavior() : bool {
        return $this->inverse_field_list_behavior;
    }

    /**
     * by default the field list given to the blade directive tells which fields to show in the form. Settings this to TRUE causes the form show ALL fields except those passed to the blade directive
     * @param bool $inverse_field_list_behavior
     */
    public function setInverseFieldListBehavior(bool $inverse_field_list_behavior) : ContactFormConfig {
        $this->inverse_field_list_behavior = $inverse_field_list_behavior;
        return $this;
    }

    /**
     * @return bool
     */
    public function showDsgvoCheck() : bool {
        return $this->show_dsgvo_check;
    }

    /**
     * @param bool $show_dsgvo_check
     */
    public function setShowDsgvoCheck(bool $show_dsgvo_check) : ContactFormConfig {
        $this->show_dsgvo_check = $show_dsgvo_check;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageSuccess() : string {
        return $this->message_success;
    }

    /**
     * @param string $message_success
     */
    public function setMessageSuccess(string $message_success) : ContactFormConfig {
        $this->message_success = $message_success;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageError() : string {
        return $this->message_error;
    }

    /**
     * @param string $message_error
     */
    public function setMessageError(string $message_error) : ContactFormConfig {
        $this->message_error = $message_error;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageValidationError() : string {
        return $this->message_validation_error;
    }

    /**
     * @param string $message_validation_error
     */
    public function setMessageValidationError(string $message_validation_error) : ContactFormConfig {
        $this->message_validation_error = $message_validation_error;
        return $this;
    }

    /**
     * @return int
     */
    public function getStyle() : int {
        return $this->style;
    }

    /**
     * @param int $style
     */
    public function setStyle(int $style) : ContactFormConfig {
        $this->style = $style;
        return $this;
    }

}
