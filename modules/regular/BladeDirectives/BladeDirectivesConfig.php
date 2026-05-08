<?php

/**
 * ...
 */

namespace STORMS\webframe\Modules;

interface BladeDirectivesConfig extends ModuleConfig {

    // note that the values must match an module dir - those module dirs are auto prefixed with 'BladeDirective'
    const BladeDirective__ActivePageCssClass = 'BladeDirectiveActivePageCssClass';
    const BladeDirective__Emphasis = 'BladeDirectiveEmphasis';
    const BladeDirective__LoremIpsumName = 'BladeDirectiveLoremIpsumName';
    const BladeDirective__SpamSpan = 'BladeDirectiveSpamSpan';
    const BladeDirective__Year = 'BladeDirectiveYear';

    public function directives() : array;

}
