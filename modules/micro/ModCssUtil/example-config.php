<?php
/*
 * THIS IS ONLY AN EXAMPLE CONFIG
 */

class exampleConfig_ModCssUtil {
    public static function setupModCssUtil(Module $moduleObj, $setupArgs) {
        return new class extends Modules\ModCssUtilConfig {
            public function isEnabled() {
                return true;
            }
            public function useMinify () {
                return false; // if you do the minifying your self you can set this to false in order to gain some performance
            }
            public function cssMap() {
                return [
                    'set1' => [
                        // replace          // with
                        'a'              => 'b',
                    ],
                ];
            }
            public function useCache() {
                return true;
            }
        };
    }
}
