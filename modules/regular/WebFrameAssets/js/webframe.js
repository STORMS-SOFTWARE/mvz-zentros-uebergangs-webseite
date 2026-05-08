/*
 * these can be considered "system" scripts that are required by the boilerplate or just serve some helping functionality
 */
$(document).ready(function() {

    $('.nl2br').each(function () {
        $(this).html(
            $(this).html().trim().replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br/>' + '$2')
        );
    });
    
    /*
     * match height / eq height cols /// flex box replacement
     */
    (window.matchHeight = function() {
        $.each($('.eq-height-grp'), function () {
            var byRow = typeof $(this).data('mh-by-row') !== 'undefined' ? $(this).data('mh-by-row') : true;
            $(this).find('.eq-height').matchHeight({
                // https://github.com/liabru/jquery-match-height
                property: 'min-height',
                byRow : byRow  // "enable/disable row detection"
            });
        });
    })();

    /*
     * make all links to external pages open in new tab
     */
    (window.initOpenLinksInNewsTab = function(selector) {
        if(typeof selector === 'undefined')
            selector = 'a';
        $(selector).each(function () {
            if(!$(this).attr('href')) return;
            if($(this).hasClass('no-ext')) return;
            if(!$(this).attr('href').trim().match('http(s)?://')) return;
            var a = new RegExp('/' + window.location.host + '/');
            if (!a.test(this.href)) {
                $(this).click(function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    window.open(this.href, '_blank');
                });
            }
        });
    })();

    /*
     * make empty links having no function but being displayed as link (with link pointer icon) by setting 'javascript:;' as href
     * -> this is better then using an empty hash; empty hashes cause the page to jump to the very top
     */
    $('a[href=""]').each(function () {
        $(this).attr('href', 'javascript:;');
    });
    
    /*
     * make pdf links open in new tab (useful for MD generated pdf links)
     */
    $('a[href]:not(.no-new-tab)').each(function () {
        if($(this).attr('href').substr(-3) === 'pdf')
            $(this).attr('target', '_blank');
    });

    /*
     * softscroll to an target element
     */
    $('.sc').click(function () {
        var offset = $(this).data('sc-offset') || $(this).data('scroll-offset') || 0;
        var delay = $(this).data('sc-delay')

        var tar = null
        if($(this).data('sc-target') === undefined && $(this).data('scroll-target') === undefined)
            tar = $(this).attr('href').substr($(this).attr('href').indexOf('#'))
        else
            tar = $(this).data('sc-target') || $(this).data('scroll-target') || ''

        if($(tar).length === 0) {
            console.info('SoftScroll-Anchor not found')
            return
        }

        setTimeout(() => {
            $('html, body').animate({
                scrollTop: $(tar).offset().top - offset
            }, 800);
        }, delay ?? 0)
        return false;
    });

    /*
     * bootstrap (3+4) workaround to enable any element (link, span, div, whatever) to toggle the active tab of an tab component 
     * TODO check if one of these versions is newer/improved:
     * - https://github.com/STORMS-SOFTWARE/the-crew-webseite/blob/master/assets/js/cuSTORMS.js
     * - https://github.com/STORMS-SOFTWARE/trubel-webseite/blob/master/assets/js/cuSTORMS.js
     * - https://github.com/STORMS-SOFTWARE/wendelina-wendenburg-uebergangseite/blob/master/assets/js/cuSTORMS.js
     */
    $('.tab-toggle').click(function(e) {
        e.preventDefault();

        // activate the tab the stand-alone-link refers to
        let tarRef = $(this).data('target-tab') || $(this).attr('href');
        let tarLinkElem = $('[data-toggle="tab"]').filter('[href="'+tarRef+'"]');
        tarLinkElem.tab('show');

        let activeClass = $(this).data('tab-link-active-class') || 'active';

        if($(this).data('tab-toggle-callback'))
            window[$(this).data('tab-toggle-callback')]($(this), tarRef, tarLinkElem);

        // remove the active class from all delegator links in the current set and add it to the delegator link we just clicked
        tarLinkElem.parents('ul').find('a[data-toggle="tab"]').each(function(){
            var tarRef = $(this).attr('href');
            $('.tab-toggle[href="'+tarRef+'"]').removeClass(activeClass);
        });
        $(this).addClass(activeClass).trigger('activated').siblings().removeClass(activeClass);
    });
    
    /*
     * add list classes to all ULs that are found within a container with a certain class
     */
    (window.style_lists = function () {
        $('.sm_list-within').each(function(){
            var list_class = $(this).data('list-class');
            $(this).find('ul').addClass(list_class);
        });
    })();

    /*
     * scroll down to panel group content on mobile devices when clicked
     */
    $('.panel-group').on('shown.bs.collapse', function (event) {
        if(window.isMobile) {
            setTimeout(function(){
                $('html, body').animate({
                    scrollTop: $(event.target).offset().top - 180
                }, 800);
            }, 100);
        }
    });
    
    /*
     * remove empty table-heads (those may come through markdown tables)
     */
    $('thead').each(function(){
        if($(this).find('th:empty').length === $(this).find('th').length)
            $(this).remove()
    });

});

if(typeof $.magnificPopup !== 'undefined') {
    $.extend(true, $.magnificPopup.defaults, {
        tClose: 'Schließen (Esc)',
        tLoading: 'Lade...',
        gallery: {
            tPrev: 'Zurück',
            tNext: 'Weiter',
            tCounter: '%curr% / %total%'
        }
    });
}

if(window.location.host.substr(-2) === 'de') {
    console.info(
        '%cSTORMS%c|MEDIA\n%c~~ W   E   B   S   I   T   E   S ~~\n%cwww.storms-media.de',
        'color:#303030; font-size: 60px',
        'color:#96d20a; font-size: 60px',
        'font-size: 20px',
        'font-size: 13px'
    )
}
