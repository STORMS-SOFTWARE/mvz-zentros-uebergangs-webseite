<?php
/* @var $_ \STORMS\webframe\Core\WebFrame */

$client_lang = $_->getClientLang();

//d($client_lang);
//d(Page::inst()->getLanguage());

// is user is coming here from google: log the URL he was trying to open and redirect to home
if(isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (str_contains($referer, 'google')) {
        if(!file_exists('404-log.txt'))
            touch('404-log.txt');
        file_put_contents('404-log.txt', date('d.m.Y H:i:s') . ' | ' . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);
        $_->reroute('/');
        exit;
    }
}
?>

<!--== Start Maintenance Content Area Wrapper ==-->
<div class="maintenance-area-wrap _bg-img" data-b-g="assets/img/extra/bg-maintenance.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 m-auto text-center">
                <div class="maintenance-content-wrap">

                    <div class="maintenance-header"></div>

                    <div class="maintenance-body">
                        <div class="maintenance-txt">
                            <h3>404</h3>
                            <p>Seite nicht gefunden.</p>
                        </div>
                    </div>

                    <div class="maintenance-footer mt-sm-40"></div>

                </div>
            </div>
        </div>
    </div>
</div>
<!--== End Maintenance Content Area Wrapper ==-->

<?php
return;
?>

<div class="padding-top-bottom-150px sm_thick-border-bottom">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12 center-col text-center">
                <h3 class="text-gray-extra-dark _text-extra-large text-weight-700 opensans margin-bottom-10px">404</h3>
                <h4 class="text-weight-200 text-gray-dark text-capitalize">Seite <span class="text-weight-800 text-red">nicht gefunden.</span></h4>
                <div class="separator width-10 bottom-border border-2px border-color-red margin-top-25px margin-bottom-25px center-col"></div>
                <p class="text-gray-dark">
                    @_e('404__text')
                </p>
                <a class="btn btn-large border-radius-50 btn-red" href="/home">Zur Startseite</a>
            </div>
        </div>
    </div>
</div>
