<?php
/*
 * THIS IS ONLY AN EXAMPLE CONFIG
 */

class exampleConfig_MailForms {

    public static function setupSpacingsUtil(Module $moduleObj, $setupArgs) {
        return new class extends Modules\SpacingsUtilConfig {

            public function isEnabled() {
                return true;
            }

            public function includeNegativeSpacings() { // HAS NO DEFAULT - MUST BE DEFINED
                return false;
            }

            public function generatorLogic() {
                /*
                 * Available logics:
                 *
                 * SpacingsUtil::__GEN_LOGIC__MINWIDTH_FOCUSPOINT => DEFAULT
                 * SpacingsUtil::__GEN_LOGIC__MAXWIDTH
                 * SpacingsUtil::__GEN_LOGIC__MINWIDTH
                 * SpacingsUtil::__GEN_LOGIC__MINWIDTH_MAXWIDTH
                 */
                return Modules\SpacingsUtil::__GEN_LOGIC__MINWIDTH_FOCUSPOINT; // default
            }

            public function logging() {
                return false; // default
            }

            public function focusBreakPoint() {
                return 'lg'; // default
            }

            public function placeCssRef() {
                /*
                 * Avail values:
                 * - head.beforeOpen
                 * - head.afterOpen
                 * - head.beforeClose
                 * - head.afterClose
                 * - body.beforeClose
                 * - body.afterClose
                 * - body.beforeOpen
                 * - body.afterOpen
                 *
                 * Note: spacing seem to get lost when the ref is placed within the head of the document.
                 */
                return 'body.beforeClose'; // default
            }

            public function autoBreakpointlessClassMedias () {
                return [
                    'lg' => [ // < focus breakpoint
                        //       %   +/-
                        'xs' => [40, -1],
                        'sm' => [25, -1],
                        'md' => [20, -1],   // - 20% on values on MD breakpoint
                        'lg' => 0,          // no reduction because this is exactly the focus breakpoint
                        'xl' => 0
                    ],
                ];
            }
        };
    }

}
