<?php
//use STORMS\webframe\Modules\ContactFormFieldInfo;
use STORMS\webframe\Modules\MailForms\ContactFormFieldInfo;
use STORMS\webframe\Modules\MailForms\ContactFormConfig;

//d($cfConfig);

/* @var $fields array */
/* @var $cfConfig ContactFormConfig */

$config = $cfConfig ?? new ContactFormConfig();

//d('>>', $fields);

/*
$foo = new ContactFormFieldsInfoFactory($fields);
d($foo->check(['vorname', 'vname', 'vn', 'prename', 'forename']));
*/

// let the FieldInfo class know about the fields that are desired to be shown (so this may for example look like: [<name|nachname|surname>, ...])
ContactFormFieldInfo::$_CF_FIELD_LIST = $fields;
// ... an also make it know the config
ContactFormFieldInfo::$_CF_FIELD_CONFIG = $config;

// field we use more then one time (all those we only use once are defined inline way down)
$fieldMail = new ContactFormFieldInfo(['mail', 'email', 'e-mail', 'e mail'], true);
$fieldTel = new ContactFormFieldInfo(['tel', 'tele', 'telefon', 'phone']);

/*
$foo = new ContactFormFieldInfo(['vorname', 'vname', 'vn', 'prename', 'forename']);
//$foo = $fieldMail;
dd(
    $foo->getField(),
    $foo->isEnabled(),
    $foo->isRequired(),
    $foo->getName()
);
*/
?>
@switch($bootstrap_version??4)
    @case(4)
    <div class="contact">
        <div class="contact-wrapper font-secondary">
            <form class="contact-form" method="post" action="mail/{{$config_index}}" autocomplete="off">
                <div class="row">
                    @php($field = new ContactFormFieldInfo(['vorname', 'vname', 'vn', 'prename', 'forename'], true))
                    @if($field->isEnabled())
                        <div class="input-wrapper col-sm">
                            <input type="text" name="Vorname" id="forename" {{$field->isRequired()?'required':''}} class="contact-input">
                            <label for="forename"><span>Vorname {{$field->isRequired()?'*':''}}</span></label>
                        </div>
                    @endif
                    @php($field = new ContactFormFieldInfo(['nachname', 'nname', 'nn', 'surname'], true))
                    @if($field->isEnabled())
                        <div class="input-wrapper col-sm">
                            <input type="text" name="Nachname" id="surname" required class="contact-input">
                            <label for="surname"><span>Nachname {{$field->isRequired()?'*':''}}</span></label>
                        </div>
                    @endif
                    @if($fieldMail->isEnabled() && !$fieldTel->isEnabled())
                        <div class="input-wrapper col-sm">
                            <input type="email" name="E-Mail" id="email" required class="contact-input">
                            <label for="email"><span>E-Mail {{$fieldMail->isRequired()?'*':''}}</span></label>
                        </div>
                    @endif
                </div>
                @if($fieldMail->isEnabled() && $fieldTel->isEnabled())
                    <div class="row">
                        <div class="input-wrapper col-sm">
                            <input type="text" name="Telefon-Nummer" id="phone" required class="contact-input">
                            <label for="phone"><span>Telefon {{$fieldTel->isRequired()?'*':''}}</span></label>
                        </div>
                        <div class="input-wrapper col-sm">
                            <input type="email" name="E-Mail" id="email" required class="contact-input">
                            <label for="email"><span>E-Mail {{$fieldMail->isRequired()?'*':''}}</span></label>
                        </div>
                    </div>
                @endif
                <div class="row">
                    @php($field = new ContactFormFieldInfo(['betreff', 'subject', 'thema', 'topic']))
                    @if($field->isEnabled())
                        <div class="input-wrapper col ml-0" data-animation="fadeInUp" data-animation-delay="100">
                            <input type="text" name="Betreff" id="subject" class="contact-input">
                            <label for="subject"><span>Betreff {{$field->isRequired()?'*':''}}</span></label>
                        </div>
                    @endif
                    @php($field = new ContactFormFieldInfo(['msg', 'message', 'nachricht', 'comment'], true))
                    @if($field->isEnabled())
                        <div class="input-wrapper textarea-wrapper col-12 mr-0 ml-0" data-animation="fadeInUp" data-animation-delay="200">
                            <textarea name="message" id="message" class="contact-input" required></textarea>
                            <label for="message"><span>Nachricht {{$field->isRequired()?'*':''}}</span></label>
                        </div>
                    @endif
                    <div class="col-12" data-animation="fadeInUp" data-animation-delay="300">
                        <button type="submit" id="submit" class="send-button">{{$config->getSubmitButtonCaption()}}</button>
                    </div>
                </div>
            </form>
            <div class="error-messages">
                <div id="error_message" class="error_message clearfix">
                    <span>{{$config->getMessageValidationError()}}</span>
                </div>
                <div id="submit_message" class="submit_message clearfix">
                    <span>{{$config->getMessageSuccess()}}</span>
                </div>
            </div>
        </div>
    </div>
    @break
    @case(3)
        BS3 FORM NYI
    @break
@endswitch
