<?php

namespace STORMS\webframe\Modules;

trait SpacingsUtilTestingConfig {

    public function focusBreakPoint() {
        return 'md'; // TODO md focus breakpoint is broken... it works from lg up...
    }

    /*
     * NOTE: you can only test one logic per page view.
     * So if you want to test all logics you have to do this one after the other and have to reload the page every time you set up another logic here
     */
    public function generatorLogic() {
        //return SpacingsUtil::__GEN_LOGIC__MAXWIDTH;
        //return SpacingsUtil::__GEN_LOGIC__MINWIDTH;
        //return SpacingsUtil::__GEN_LOGIC__MINWIDTH_MAXWIDTH;
        return SpacingsUtil::__GEN_LOGIC__MINWIDTH_FOCUSPOINT;
    }

    public function testingData() {
        /*
         * duplicates: mt20 (x2), mtmd30 (x2)
         */
        return SpacingsUtil::processContent('class="mt20 mtmd30 mt20 mtmd30 mtmd40 mtlg50 mbsm80 mrxs100 mbxl120 p20-i mmd30-e py40-ie mb-123"');
        //return SpacingsUtil::processContent('class="mmd30-e"');
    }
    public function testingResultExpectation() {
        return [
            /* TESTING MAX WIDTH:
            'none' => null,
            'xs' => '480px',
            'sm' => '576px',
            'md' => '768px',
            'lg' => '992px',
            'xl' => '1200px'
             */
            SpacingsUtil::__GEN_LOGIC__MAXWIDTH => '
                ???
            ',

            /* TESTING MIN WIDTH:
            'none' => null,
            'xs' => '0px',
            'sm' => '576px',
            'md' => '768px',
            'lg' => '992px',
            'xl' => '1200px'
             */
            SpacingsUtil::__GEN_LOGIC__MINWIDTH => '
                ???
            ',

            /* TESTING MIN WIDTH & MAX WIDTH FENCING:
            'none' => null,
            'xs' => [null,      '575.98px'],
            'sm' => ['576px',   '767.98px'],
            'md' => ['768px',   '991.98px'],
            'lg' => ['992px',   '1199.98px'],
            'xl' => ['1200px',  null]
             */
            SpacingsUtil::__GEN_LOGIC__MINWIDTH_MAXWIDTH => '
                ???
            ',

            /* TESTING MIN WIDTH FOCUS-BREAKPOINT:
            'sm' => [
                'none' => null,
                'xs' => [null,      '575.98px'],
                'sm' => ['576px',   null],
                'md' => ['768px',   null],
                'lg' => ['992px',   null],
                'xl' => ['1200px',  null]
            ],
            'md' => [
                'none' => null,
                'xs' => [null,      '767.98px'],
                'sm' => ['576px',   '767.98px'],
                'md' => ['768px',   null],
                'lg' => ['992px',   null],
                'xl' => ['1200px',  null]
            ],
            'lg' => [
                'none' => null,
                'xs' => [null,      '991.98px'],
                'sm' => ['576px',   '991.98px'],
                'md' => ['768px',   '991.98px'],
                'lg' => ['992px',   null],
                'xl' => ['1200px',  null]
            ],
            'xl' => [
                'none' => null,
                'xs' => [null,      '1199.98px'],
                'sm' => ['576px',   '1199.98px'],
                'md' => ['768px',   '1199.98px'],
                'lg' => ['992px',   '1199.98px'],
                'xl' => ['1200px',  null]
            ],
             */
            SpacingsUtil::__GEN_LOGIC__MINWIDTH_FOCUSPOINT => [
                'sm' => '
                    ??? 
                ',
                'md' => '
                    ???
                ',
                'lg' => '
                    ???
                ',
                'xl' => '
                    ???
                '
            ][self::focusBreakPoint()]

        ][self::generatorLogic()];
    }

    // ------------------

    public function isEnabled() {
        return true;
    }

    public function includeNegativeSpacings() {
        return false;
    }

    public function logging() {
        return false;
    }

}
