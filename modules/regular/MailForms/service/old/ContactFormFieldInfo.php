<?php

//namespace STORMS\webframe\Modules;
namespace STORMS\webframe\Modules\MailForms;

class ContactFormFieldInfo {

    public static ?ContactFormConfig $_CF_FIELD_CONFIG = null;
    public static $_CF_FIELD_LIST = null;

    private $possible_field_names = [];
    private $required_by_default = null;

    public function __construct($possible_field_names, $required_by_default = false) {
        $this->possible_field_names = $possible_field_names;
        $this->required_by_default = $required_by_default;
    }

    private function processFieldName($originalFieldName) {
        return trim(str_replace('*', '', $originalFieldName));
    }

    private function isUseDefault() {
        return self::$_CF_FIELD_LIST === [] || (self::$_CF_FIELD_CONFIG->isInverseFieldListBehavior());
    }

    public function getField() {
        if( $this->isUseDefault() && !(self::$_CF_FIELD_CONFIG->isInverseFieldListBehavior()) )
            return $this->possible_field_names[0];

        $possibleNames = $this->possible_field_names;
        $inverse = self::$_CF_FIELD_CONFIG->isInverseFieldListBehavior();
        return array_values(array_filter(self::$_CF_FIELD_LIST, function($name) use ($possibleNames, $inverse) {
            $isNameInArray = in_array($this->processFieldName($name), $possibleNames);
            return $inverse ? !$isNameInArray : $isNameInArray;
        }))[0] ?? false;
    }

    public function isEnabled() {
        return $this->getField() !== false;
    }
    public function isRequired() {
        if($this->isUseDefault())
            return $this->required_by_default;
        else {
            if($this->isEnabled())
                return strpos($this->getField(), '*') !== false;
            else
                return null;
        }
    }
    public function getName() {
        return $this->getField() !== false ? $this->processFieldName($this->getField()) : false;
    }

}
