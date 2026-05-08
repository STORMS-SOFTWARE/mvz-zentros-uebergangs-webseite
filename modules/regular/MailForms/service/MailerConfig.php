<?php

namespace STORMS\webframe\Modules\MailForms;

use PHPMailer\PHPMailer\PHPMailer;

class MailerConfig {

    //private $created = null; // used for bot prot (calc time diff between form generation and post receive)
    private $min_delay = 0; // seconds

    //private $recip=null;
    private $live_testing=false;

    private $log_mail = true;

    private $message_success = 'Danke für Ihr Interesse. Ihre Nachricht wurde erfolgreich versendet!';
    private $message_error = 'Es ist ein nicht weiter spezifizierter Fehler aufgetreten. Bitte versuchen Sie es später erneut, oder wenden sich direkt per E-Mail an uns.';
    //private $message_validation_error = 'Es wurden nicht alle Felder ordnungsgemäß ausgefüllt. Bitte korrigieren Sie die rot umrandeten Felder.';

    private $transport = null;

	private $empty_message_note = 'Der Besucher hat keine Nachricht eingegeben.';

    // this will run through markdown (when using the default mailer template)
    private $mail_body_intro = '### Sie haben eine Nachricht über Ihre Webseite erhalten.' . "\n" . '_Es wurden folgende Daten eingegeben:_';
    private $append_default_mail_footer = true;
    private $custom_mail_footer = null;

    private $mail_template = null;

    // NYI:
    private $wrap_mail_template = true; // should the passed templated be used inside the default mailhander so that just the concrete content comes through the given template or should the given template be used as standalone..

    private $tpl_label_map = [];
    private $tpl_value_map = [];

    private $hidden_form_fields = [];

    private $attachments_allowed = false;

    private $table_data_config = null;

    private $hide_empty_message_note = false;

    public static $default_mail_subject = 'Sie haben eine E-Mail über Ihre Webseite erhalten.'; // because the actual subject is set through the mail transport

    // TODO quick add -> generate getter and setter
    public $default_mail_template_chunks = [ // those are used by the mail-template.blade.php if set

/*
TODO this is just a preperation - these fields are not yet used in the mail-template.blade.php!! 
*/
	
	'style' => '', // will be rendered within the <style> tags of the mail template
	
	'before-MailBodyIntro' => '', 
	'after-MailBodyIntro' => '',

	'before-PostFieldGeneration' => '',
	'after-PostFieldGeneration' => '',

	'before-TableDataGeneration' => '',
	'after-TableDataGeneration' => '',

	'before-VisitorEmail' => '',
	'after-VisitorEmail' => '',

	// this is always rendered to the dom (it is outside of the condition)
	'before-MessageBlock' => '',
	'after-MessageBlock' => '',

	// may never be rendered because its contained by a condition
	'before-MessageHeadline' => '',
	'after-MessageHeadline' => '',

	'before-MessageBlockquote' => '',
	'after-MessageBlockquote' => '',

	'before-EmptyMessageNote' => '',
	'after-EmptyMessageNote' => '',

	'before-DefaulMailFooter' => '',
	'after-DefaulMailFooter' => '',

	'tpl-end' => '' // always rendered at the very end of the template
    ]; 

    public function __construct() {
        //$this->created = time();
        $this->setMinimumDelay(_isDev() ? 0 : 10);
        return $this;
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
    public function setIsLiveTestingMode(bool $live_testing) : self {
        $this->live_testing = $live_testing;
        return $this;
    }

    /**
     * @return int
     */
    /*public function getCreated() : int {
        return $this->created;
    }*/

    /**
     * @param bool $log_mail
     */
    public function setLogMail(bool $log_mail) : self {
        $this->log_mail = $log_mail;
        return $this;
    }

    /**
     * @return bool
     */
    public function getLogMail() : bool {
        return $this->log_mail;
    }

    /**
     * Set the minimum delay that must fill the gap between initialization of the config object (coming together with the directive call) and receiving of the concrete post-data (in SECONDS)
     * @param int $min_delay
     */
    public function setMinimumDelay(int $min_delay) : self {
        $this->min_delay = $min_delay;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinimumDelay() : int {
        return $this->min_delay;
    }

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

    /**
     * @return string
     */
    public function getMessageSuccess() : string {
        return $this->message_success;
    }

    /**
     * @param string $message_success
     */
    public function setMessageSuccess(string $message_success) : self {
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
    public function setMessageError(string $message_error) : self {
        $this->message_error = $message_error;
        return $this;
    }

    /**
     * @return string
     */
    /*public function getMessageValidationError() : string {
        return $this->message_validation_error;
    }*/

    /**
     * @param string $message_validation_error
     */
    /*public function setMessageValidationError(string $message_validation_error) : self {
        $this->message_validation_error = $message_validation_error;
        return $this;
    }*/

    /**
     * @param PHPMailer|SwiftMailer $transport
     * set the phpmailer instance
     */
    public function setTransport($transport) : self {
        $this->transport = $transport;
        return $this;
    }

    /**
     * @return null
     */
    public function getTransport() {
        return $this->transport;
    }

    public function getAttachmentsAllowed() : bool {
        return $this->attachments_allowed;
    }
    public function setAttachmentsAllowed(bool $attachments_allowed) : self {
        $this->attachments_allowed = $attachments_allowed;
        return $this;
    }

    public function setHideEmptyMessageNote(bool $hide_empty_message_note) : self {
        $this->hide_empty_message_note = $hide_empty_message_note;
        return $this;
    }
    public function getHideEmptyMessageNote() : bool {
        return $this->hide_empty_message_note;
    }

    public function setEmptyMessageNote(string $empty_message_note) : self {
        $this->empty_message_note = $empty_message_note;
        return $this;
    }
    public function getEmptyMessageNote() : string {
        return $this->empty_message_note;
    }

    /**
     * Example config:
     * ...->setTableDataConfig([
     *       ['Datum' => '_date', 'Uhrzeit' => '_time']
     *    ])
     * Note the two-dimensional array! It allows to set multiple data groups connected to an table.
     * You should define names of fields with an underscore as prefix. This will make them not being rendered beside the table.
     *
     * @param array $table_data_config
     * @return $this
     */
    public function setTableDataConfig(array $table_data_config) : self {
        $this->table_data_config = $table_data_config;
        return $this;
    }
    public function getTableDataConfig() : ?array {
        return $this->table_data_config;
    }

    /**
     * @param string $mail_body_intro
     */
    public function setMailBodyIntro(string $mail_body_intro) : self {
        $this->mail_body_intro = $mail_body_intro;
        return $this;
    }

    /**
     * @return string
     */
    public function getMailBodyIntro() : string {
        return $this->mail_body_intro;
    }

    /**
     * @param null $mail_template
     * TODO $wrap_template NYI
     */
    public function setMailTemplate(string $mail_template, bool $wrap_template = true) : self {
        $this->mail_template = $mail_template;
        $this->wrap_mail_template = $wrap_template;
        return $this;
    }

    /**
     * @return
     */
    public function getMailTemplate() {
        return $this->mail_template;
    }
    /**
     * TODO NYI
     * @return
     */
    public function getWrapMailTemplate() {
        return $this->wrap_mail_template;
    }

    /**
     * @param bool $append_default_mail_footer
     */
    public function setAppendDefaultMailFooter(bool $append_default_mail_footer) : self {
        $this->append_default_mail_footer = $append_default_mail_footer;
        return $this;
    }

    /**
     * @return bool $append_default_mail_footer
     */
    public function getAppendDefaultMailFooter() : bool {
        return $this->append_default_mail_footer;
    }

    public function setCustomMailFooter(string $custom_mail_footer) : self {
        $this->custom_mail_footer = $custom_mail_footer;
        return $this;
    }

    public function getCustomMailFooter() : string|null {
        return $this->custom_mail_footer;
    }

    /**
     * @return array
     */
    public function getTplLabelMap() : array {
        return $this->tpl_label_map;
    }

    /**
     * @param array $tpl_label_map
     */
    public function setTplLabelMap(array $tpl_label_map) : self {
        $this->tpl_label_map = $tpl_label_map;
        return $this;
    }

    /**
     * @return array
     */
    public function getTplValueMap() : array {
        return $this->tpl_value_map;
    }

    /**
     * @param array $tpl_value_map
     */
    public function setTplValueMap(array $tpl_value_map) : self {
        $this->tpl_value_map = $tpl_value_map;
        return $this;
    }

    /**
     * @return array
     */
    public function getHiddenFormFields() : array {
        return $this->hidden_form_fields;
    }

    /**
     * @param array $hidden_form_fields
     */
    public function setHiddenFormFields(array $hidden_form_fields) : self {
        $this->hidden_form_fields = $hidden_form_fields;
        return $this;
    }

    /*
     * Hide the output for the main message in the mail generation.
     * This makes sense if the sent message is a type of message that does not need a actual message (for example recall forms)
     *
     * this is a macro / alias for:
     * ->setHiddenFormFields(['msg'])
     */
    public function setMessageVisible (bool $state) : self {
        if($state)
            $this->hidden_form_fields = array_diff($this->hidden_form_fields, ['msg']);
        else {
            if(!in_array('msg', $this->hidden_form_fields))
                $this->hidden_form_fields[] = 'msg';
        }
        return $this;
    }

}
