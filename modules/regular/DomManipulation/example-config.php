<?php
/*
* THIS IS ONLY AN EXAMPLE CONFIG 4 DomManipulation
*/

class exampleConfig_MailForms {
  
  public static function setupDomManipulation(Module $moduleObj, $setupArgs) {
      return new class extends Modules\DomManipulationConfig {
          public function isEnabled() {
              return true;
          }
          public function searchJquery() {
              return true;
          }
      };
  }
  
}
