/*
 * STORMS MOD VERSION
 * - now fires an event when spamspan is ready
 *   examples:
 *   https://github.com/STORMS-SOFTWARE/faf-hempsch-webseite/blob/d97529481d36f964f119058f1726776d32c98cc2/views/default.blade.php#L283
 *   https://github.com/STORMS-SOFTWARE/cusanus-gymnasium-webseite/blob/b48df7acc2a896204e4b1cd83b03f324640e59e2/assets/js/cusanus-additional.js#L99
 *   https://github.com/STORMS-SOFTWARE/hausarztpraxis-goch-webseite/blob/313a636917b80f366be63e5a74e254bfc1518a33/assets/js/cuSTORMS.js#L78
 *   ( -> $('body').on('spamspan', function (a) { var tar = $(a.detail.new_elem) }); <- )
 * - now allows html (for images and icons) as label
 */

/*
 * Version that was used for a long long time (before adding markup feature (for icons and imgs etc)):
 * https://github.com/STORMS-SOFTWARE/webframe/blob/428ebbd3b7bebae96093f9dc8c2c20c07bc93570/modules/regular/WebFrameAssets/js/additional-vendors/spamspan.js#L1
 */

/*
 * CustomEvent constructor polyfill for IE
 * (currently used for the spamspan callback)
 */
(function () {
    if (typeof window.CustomEvent === "function")
        return false; // If not IE

    function CustomEvent(event, params) {
        params = params || {bubbles: false, cancelable: false, detail: undefined};
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
        return evt;
    }

    CustomEvent.prototype = window.Event.prototype;

    window.CustomEvent = CustomEvent;
})();
// ---------------

/*
 --------------------------------------------------------------------------
 $Id: spamspan.js 5 2007-09-29 15:56:26Z moltar $
 --------------------------------------------------------------------------
 Version: 1.03
 Release date: 13/05/2006
 Last update: 07/01/2007

 (c) 2006 SpamSpan (www.spamspan.com)

 This program is distributed under the terms of the GNU General Public
 Licence version 2, available at http://www.gnu.org/licenses/gpl.txt
 --------------------------------------------------------------------------
 */

var spamSpanMainClass = 'spamspan';
var spamSpanUserClass = 'u';
var spamSpanDomainClass = 'd';
var spamSpanAnchorTextClass = 't';
var spamSpanParams = new Array('subject', 'body');

/*
 --------------------------------------------------------------------------
 Do not edit past this point unless you know what you are doing.
 --------------------------------------------------------------------------
 */

addEvent(window, 'load', spamSpan);

function spamSpan() {
    var allSpamSpans = getElementsByClass(spamSpanMainClass, document, 'span');

    for (var i = 0; i < allSpamSpans.length; i++) {
        // get data
        var user = getSpanValue(spamSpanUserClass, allSpamSpans[i]);
        var domain = getSpanValue(spamSpanDomainClass, allSpamSpans[i]);
        var anchorText = getSpanValue(spamSpanAnchorTextClass, allSpamSpans[i]);
        // prepare parameter data
        var paramValues = new Array();
        for (var j = 0; j < spamSpanParams.length; j++) {
            var paramSpanValue = getSpanValue(spamSpanParams[j], allSpamSpans[i]);
            if (paramSpanValue) {
                paramValues.push(spamSpanParams[j] + '=' + encodeURIComponent(paramSpanValue));
            }
        }
        // create new anchor tag
        var at = String.fromCharCode(32 * 2);
        var email = cleanSpan(user) + at + cleanSpan(domain);

        var anchorTagText = null
        if(typeof anchorText === 'string')
            anchorTagText = document.createTextNode(anchorText);
        else if (typeof anchorText === 'boolean')
            anchorTagText = document.createTextNode(email);
        else if (typeof anchorText === 'object')
            anchorTagText = htmlToElement(anchorText.outerHTML)

        var mto = String.fromCharCode(109, 97, 105, 108, 116, 111, 58);
        var hrefAttr = mto + email;
        hrefAttr += paramValues.length ? '?' + paramValues.join('&') : '';
        var anchorTag = document.createElement('a');
        anchorTag.className = spamSpanMainClass +  ' ' + allSpamSpans[i].getAttribute('data-css-class');
        anchorTag.setAttribute('href', hrefAttr);
        anchorTag.appendChild(anchorTagText);
        // replace the span with anchor
        allSpamSpans[i].parentNode.replaceChild(anchorTag, allSpamSpans[i]);

        // SM mod: trigger callback when spamspan is done
        if(typeof window.CustomEvent !== 'undefined') { // IE does not know about "CustomEvent" - so lets catch errors here
            document.querySelector('body').dispatchEvent(new CustomEvent('spamspan', {
                detail: {
                    new_elem: anchorTag
                }
            }))
        }

    }
}

// https://stackoverflow.com/a/35385518
function htmlToElement(html) {
    var template = document.createElement('template');
    html = html.trim(); // Never return a text node of whitespace as the result
    template.innerHTML = html;
    return template.content.firstChild;
}

function getElementsByClass(searchClass, scope, tag) {
    var classElements = new Array();
    if (scope == null)
        node = document;
    if (tag == null)
        tag = '*';
    var els = scope.getElementsByTagName(tag);
    var elsLen = els.length;
    var pattern = new RegExp("(^|\s)" + searchClass + "(\s|$)");
    for (var i = 0, j = 0; i < elsLen; i++) {
        if (pattern.test(els[i].className)) {
            classElements[j] = els[i];
            j++;
        }
    }
    return classElements;
}

function getSpanValue(searchClass, scope) {
    var span = getElementsByClass(searchClass, scope, 'span');
    if (span[0]) {
        var node = span[0].firstChild
        if(node.nodeName === '#text')
            return node.nodeValue
        else // else: HTML node
            return node
    } else
        return false;

}

function cleanSpan(string) {
    // string = string.replace(//g, '');
    // replace variations of [dot] with .
    string = string.replace(/[\[\(\{]?[dD][oO0][tT][\}\)\]]?/g, '.');
    // replace spaces with nothing
    string = string.replace(/\s+/g, '');
    return string;
}

// http://www.quirksmode.org/blog/archives/2005/10/_and_the_winner_1.html
function addEvent(obj, type, fn) {
    if (obj.addEventListener)
        obj.addEventListener(type, fn, false);
    else if (obj.attachEvent)
    {
        obj['e' + type + fn] = fn;
        obj[type + fn] = function () {
            obj['e' + type + fn](window.event);
        }
        obj.attachEvent('on' + type, obj[type + fn]);
    }
}
