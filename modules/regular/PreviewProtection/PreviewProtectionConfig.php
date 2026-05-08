<?php

/**
 * impl. @ Config:
 *   public static function setupPreviewProtection(Module $moduleObj, $setupArgs) {
 *     return new class extends Modules\PreviewProtectionConfig {
 *       // ... methods of this class here
 */

namespace STORMS\webframe\Modules;

abstract class PreviewProtectionConfig implements ModuleConfig {

    abstract public function password();
    public function protectionPageTitle() { return 'STORMS|MEDIA Webseiten Vorschau'; }
    public function usePagePreviewBackgroundOnLogin() { return true; }
    public function whiteLabel() { return false; }
    public function enableHelp() { return true; }
    public function mainColor() { return '#96d20a'; /* < STORMS MEDIA green */ }
    public function loginCardHeadline() { return null; }

}
