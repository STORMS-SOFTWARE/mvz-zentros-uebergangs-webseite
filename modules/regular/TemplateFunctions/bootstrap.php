<?php

/*
 * Global template helper functions / aliases
 * ===========================================
 */

use STORMS\webframe\Core\WebFrame as WebFrame;

/*
 * alias for: WebFrame->baseUrl($path)
 * TODO method signature has changed...
 */
function _b($path, $omitFileExistCheck = false, $omitLeadingSlash = false, $globStr = '') {
    return WebFrame::inst()->baseUrl($path, $omitFileExistCheck, $omitLeadingSlash, $globStr);
}
function _base($path, $omitFileExistCheck = false, $omitLeadingSlash = false, $globStr = '') { return _b($path, $omitFileExistCheck, $omitLeadingSlash, $globStr); }
function _baseUrl($path, $omitFileExistCheck = false, $omitLeadingSlash = false, $globStr = '') { return _b($path, $omitFileExistCheck, $omitLeadingSlash, $globStr); }

/*
 * alias for: WebFrame::obfuscateMail
 */
function _spamspan(string $email_pre_at, mixed $label = null, string $css_classes = null) {
    return call_user_func_array(['STORMS\webframe\Core\WebFrame', 'obfuscateMail'], func_get_args());
}

/*
 * alias for: $blade->yieldContent(...
 */
function _getSection($sectionName) {
    return WebFrame::inst()->blade->yieldContent($sectionName);
}
function _sec($sectionName) { return _getSection($sectionName); }
function _section($sectionName) { return _getSection($sectionName); }

/*
 * alias for: WebFrame->getContent()
 */
function _C() {
    return WebFrame::inst()->getContent();
}
function _content() { return _C(); }
function _getContent() { return _C(); }

/*
 * alias for: $blade->getSlots()
 */
function _getSlots($withKeys = true) {
    return WebFrame::inst()->blade->getSlots($withKeys);
}
function _slots($withKeys = true) { return _getSlots($withKeys); }
function _getCoponentSlots($withKeys = true) { return _getSlots($withKeys); }
/*
 * alias for: $blade->getComponent()
 * this method is needed for (within) components that have no slots
 */
function _getComponent() {
    return WebFrame::inst()->blade->getComponent();
}

/*
 * alias for: WebFrame->browser->getName()
 */
function _getBrowserName($slugify=true) {
    $browser = new Wolfcast\BrowserDetection();
    $bn = $browser->getName();
    return $slugify ? WebFrame::slugify($bn) : $bn;
}
function _browserName($slugify=true) { return _getBrowserName($slugify); }

/*
 * alias for: WebFrame->browser->isMobile()
 */
function _isMobile() : bool {
    $browser = new Wolfcast\BrowserDetection();
    return $browser->isMobile();
}

/*
 * alias for: WebFrame::md()
 */
function _md($text, $linebreaks=true) {
    Parsedown::instance()->setBreaksEnabled($linebreaks);
    return WebFrame::md($text);
}

/*
 * alias for: WebFrame->slugify
 */
function _slugify($str) : string {
    return WebFrame::slugify($str);
}

/*
 * alias for: WebFrame::isDev()
 */
function _isDev() : bool {
    return WebFrame::isDev();
}
/*
 * alias for: WebFrame::isPreviewServer()
 */
function _isPreviewServer() : bool {
    return WebFrame::isPreviewServer();
}
/*
 * alias for: WebFrame::isDevOrPreviewServer()
 */
function _isDevOrPreviewServer() : bool {
    return WebFrame::isDevOrPreviewServer();
}

function _isIE() : bool {
    $browser = new Wolfcast\BrowserDetection(); // TODO
    return $browser->getName() === $browser::BROWSER_IE;
}

function _tl($yaml, $default='') {
    return WebFrame::inst()->translate($yaml, $default);
}

function _getGitHash() : string {
    return WebFrame::inst()->getGitHash();
}

function _frameworkBase() : string {
    return WebFrame::frameworkBase();
}

function _wfTemplate(string $template) : ?string {
    return WebFrame::wfTemplate($template);
}

function _render(string $pageFile, ?string $layout = null, array $view_vars = [], bool $output = true) {
    $ret = WebFrame::inst()->render($pageFile, $layout, $view_vars);
    if($output)
        echo $ret;
    return $ret;
}

function _loremIpsum(int $words = 100) : string {
    return \STORMS\webframe\Modules\Modules::inst('BladeDirectives')->getModuleByName('LoremIpsumName')->getLoremIpsum($words);
}
function _rndFirstName(string $gender) : string {
    return \STORMS\webframe\Modules\Modules::inst('BladeDirectives')->getModuleByName('LoremIpsumName')->getFirstName($gender);
}
function _rndLastName() : string {
    return \STORMS\webframe\Modules\Modules::inst('BladeDirectives')->getModuleByName('LoremIpsumName')->getLastName();
}
function _placeholderImgUrl(int $w, int $h, string $bgColor = '#f0f0f0', string $fgColor = '#666', string $text = '') : string {
    return \STORMS\webframe\Modules\Modules::inst('BladeDirectives')->getModuleByName('LoremIpsumName')->getPlaceholderImgUrl(...func_get_args());
}
