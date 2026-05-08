<?php

namespace STORMS\webframe\Modules;

abstract class SpacingsUtilConfig implements ModuleConfig {

    abstract public function includeNegativeSpacings();
    /*public function includeNegativeSpacings() {
        return false;
    }*/

    public function generatorLogic() {
        return SpacingsUtil::__GEN_LOGIC__MINWIDTH_FOCUSPOINT;
        //return SpacingsUtil::__GEN_LOGIC__MAXWIDTH;
        //return SpacingsUtil::__GEN_LOGIC__MINWIDTH;
        //return SpacingsUtil::__GEN_LOGIC__MINWIDTH_MAXWIDTH;
    }

    public function logging() {
        return false;
    }

    public function storageLocation() {
        return \Config::STORAGE_PATH . '/compiled/spacing-util';
    }

    // Note: dont use "head.beforeClose": we got something like a race condition - not everything is yet compiled here...
    public function placeCssRef() {
        return 'body.beforeClose';
    }

    /*
     * this method allows to set the REQUIRED prefix for a spacing class to match
     * for example:
     * - set return to 'FOO-'
     * - only css classes that are prefixed with 'FOO-' will be recognized (so for example FOO-mb20)
     */
    public function classPrefix() {
        return '';
    }

    /**
     * Definition what Focus Breakpoint actually means:
     * You can consider the Focus Breakpoint to match that resolution you are building the website in. This enables you to eg. use mb20 instead of mblg20.
     * So if you are formly building the website while the browser is within the lg breakpoint -> set the focus breakpoint to lg...
     * This will cause the spacing util to priorize your breakpoint-less (eg. mb20) spacing classes to absolutely keep that spacing you defined even if a usage of a lower breakpoint would actually override your breakpoint-less definition.
     * So if you set the focus breakpoint to 'lg' all other usages of spacing classes *that contain a breakpoint* will flow to a maximum of 991.98px (which is the beginning of the lg breakpoint).
     * Spacing classes with breakpoints lower then your configured focus breakpoint can never override your breakpoint-less class definition's spacings.
     * So adding eg. a mbmd10 next to mb20 wihile you have 'lg' configured as focus breakpoint will cause the spacing util to generate the "margin-bottom:10px" class only up to 991.98px (where the lg breakpoint begins)
     * The advantage of this is that eg. mb20 will still affect lower viewports other then mb-lg-20 would do. So also mobile phones will get those 20px
     */
    public function focusBreakPoint() {
        return 'lg';
    }

    /**
     * If active (no falsy return) this will cause the generation of additional media query classes according to the current configured focus breakpoint with additions/reductions to the value of the generated class
     * under the use of the default boostrap breakpoints:  xs => 480px, sm => 576px, md => 768px, lg => 992px, xl => 1200px
     *
     * Example for configured focus breakpoint > LG < and the class > mb20 < :
     *
     * CSS result:
     * .mb20{margin-bottom:20px}
     * @media only screen and (max-width:480px){.mb20{margin-bottom:calc( 20px - 40% )}}
     * @media only screen and (max-width:576px){.mb20{margin-bottom:calc( 20px - 25% )}}
     * @media only screen and (max-width:768px){.mb20{margin-bottom:calc( 20px - 20% )}}
     * @media only screen and (max-width:992px){.mb20{margin-bottom:calc( 20px - 0% )}} << this should be omitted by the generator
     * @media only screen and (max-width:1200px){.mb20{margin-bottom:calc( 20px + 10% )}}
     *
     * @return [array|null|false]
     */
    public function autoBreakpointlessClassMedias () {
        return [
            // TODO add configurations for all other focus breakpoints
            'md' => [
                //       %   +/-
                'xs' => [30, -1], // -1 = substraction value ; 1 addition
                'sm' => [10, -1],
                'md' => 0,
                'lg' => [10, 1],
                'xl' => [20, 1]
            ],
            'lg' => [
                'xs' => [40, -1],
                'sm' => [25, -1],
                'md' => [20, -1],
                'lg' => 0,
                'xl' => 0
            ],
        ];
    }

    public function testingData () {
        return null;
    }
    public function testingResultExpectation () {
        return null;
    }

}
