<?php
/*
 * Blade directive for directly generating spamspan markup
 * Usage Examples:
    1. @_spamspan(foobar)
        -> Generiert foobar@<in der Config eingestellte Domain>.<... "" tld> & das Label sieht genau so aus wie die EMail-Adresse
    2. @_spamspan(foobar, quxxing)
        -> Genau wie 1. nur, dass als Label "quxxing" verwendet wird
    3. @_spamspan(foobar@quxx.de)
        -> Generiert einen Mail-Link für den die in der Config definierte Customer-URL komplett ignoriert wird.
           Die EMail-Adresse hinter dem Link wird 1:1 wie das Label sein: foobar@quxx.de
    4. @_spamspan(foobar@quxx.de, foobar)
        -> Generiert einen Link mit dem Label 'quxx@bar.de' auf die EMail-Adresse 'foobar@quxx.de'
    (5. @_spamspan(foobar@quxx.de, quxx@bar.de) )
        -> Eigentlich ein überflüssiges Beispiel...
    6. @_spamspan(foobar@quxx.de, <i class="fa fa-envelope"></i>)
        -> Verwendet als Label für den Link ein FA Icon
    7. @_spamspan(foobar@quxx.de, <img style="width: 100px" src="/assets/images/logo/logo-vectorized.svg">)
        -> Verwendet für das Label des Links custom HTML

    Als dritter Parameter kann ein String mit Klassennamen übergeben werden, die auf den generierten Spamspan-Link angewendet werden.:
    @_spamspan(foobar@quxx.de, null, meine-extra-klasse) oder @_spamspan(foobar@quxx.de, , meine-extra-klasse)

 * Beispiele für: Auf "SpamSpan-Ready Event" reagieren:
 *   https://github.com/STORMS-SOFTWARE/faf-hempsch-webseite/blob/d97529481d36f964f119058f1726776d32c98cc2/views/default.blade.php#L283
 *   https://github.com/STORMS-SOFTWARE/cusanus-gymnasium-webseite/blob/b48df7acc2a896204e4b1cd83b03f324640e59e2/assets/js/cusanus-additional.js#L99
 *   https://github.com/STORMS-SOFTWARE/hausarztpraxis-goch-webseite/blob/313a636917b80f366be63e5a74e254bfc1518a33/assets/js/cuSTORMS.js#L78
 */

/* @var $this Module */

namespace STORMS\webframe\Modules;

use STORMS\webframe\Core\WebFrame;

$this->extend([
    'init' => function($ret, WebFrame $_, \Bramus\Router\Router $router, \eftec\bladeone\BladeOne $blade)  {

        /*
         * @_<one directive out of the directive list>($mail_OR_mailPreAt, $label=null)
         */
        $handler = function ($expression) use ($_, $blade) {
                $str = $blade->stripParentheses($expression);
                $str = $blade->stripQuotes($str);
                $params = array_map('trim', explode(',', $str));
                return call_user_func_array('_spamspan', $params);
        };

        foreach(['_spamspan', '_spaspa', '_mailaddr', '_obfuscatemail'] as $directive)
            $blade->directive($directive, $handler);

    }
]);
