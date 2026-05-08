<?php

/**
 * ...
 */

namespace STORMS\webframe\Modules;

interface WebFrameAssetsConfig extends ModuleConfig {

    const WFASSET__MATCH_HEIGHT = 'wfa_matchheight';
    const WFASSET__SPAMSPAN = 'wfa_spamspan';

    public function JSlibs() : array;
    //public function CSSlibs() : array; // we currently do not have any...

}
