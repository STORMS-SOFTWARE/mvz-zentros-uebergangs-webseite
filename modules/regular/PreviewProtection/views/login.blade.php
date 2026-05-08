<?php
/*
 * Preview Server Login-page
 */

/* @var $_ WebFrame */
/* @var $page string */
/* @var $getDirectAccessUrl \Closure */
/* @var $cfg \STORMS\webframe\Modules\PreviewProtectionConfig */

$_->setTitle($cfg->protectionPageTitle());
$_->setDescription("STORMS|MEDIA Webseiten Vorschau");

use STORMS\webframe\Core\WebFrame;
use STORMS\webframe\Core\UtilityMethods;

$show_help = $cfg->enableHelp()
    && !$cfg->whiteLabel() // << actually this condition is only here because i've got no time to enable help page customization for whitelabel pages
    && ( ( ($_SESSION['preview__log_in_trys'] ?? 0) > 5 && $page !== 'login') || $page === 'preview-help');

if($cfg->usePagePreviewBackgroundOnLogin()) {
    $bg_img_cache_path = sprintf('%s/preview-prot-bg-img.jpg', ltrim(Config::STORAGE_PATH, '/'));
    $bg_img_exists = is_file($bg_img_cache_path);

    if(!$bg_img_exists || isset($_GET['refresh'])) {
        $bg_img = file_get_contents('https://api.apiflash.com/v1/urltoimage?url=' . (_isDev() ? WEB_URL_FULL : urlencode($getDirectAccessUrl() . '&bypass&no_redirect')) . '&access_key=623fdc6bb93349ad9c978a9be388e7e6&full_page=true&scroll_page=true&fresh=true');
        file_put_contents($bg_img_cache_path, $bg_img);
    }
    else
        $bg_img = file_get_contents($bg_img_cache_path);

    $bg_img_dims = getimagesize($bg_img_cache_path);
}

$mainColor = $cfg->mainColor();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex, nofollow">

        <title>{{$_->getTitle()}}</title>
        <meta name="description" content="{{$_->getDescription()}}">

        @if($show_help)
            {{--note: currently the help page is automaticall disabled when we are @ whiteLabel mode --}}
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        @endif

        <link href="https://fonts.googleapis.com/css?family=Niramit" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">

        <script>
            /*function calculateAspectRatioFit(srcWidth, srcHeight, maxWidth, maxHeight) {
                var ratio = Math.min(maxWidth / srcWidth, maxHeight / srcHeight);
                return { width: srcWidth*ratio, height: srcHeight*ratio };
            }

            document.addEventListener('DOMContentLoaded', function(event) {
                document.querySelector('#page-preview-bg').style.height = calculateAspectRatioFit({{$bg_img_dims[0]}}, {{$bg_img_dims[0]}}, window.innerWidth, {{$bg_img_dims[1]}}).height + 'px'
            });*/
        </script>

        <style>
            body {
                font-family: 'Niramit', sans-serif;
                /*overflow: hidden;*/
            }

            #page-preview-bg {
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                right: 0;
                overflow: hidden;
            }

            @if(!_isIE() && $cfg->usePagePreviewBackgroundOnLogin())
                #page-preview-bg:before, #page-preview-bg:after {
                    content : "";
                    background-image: url("data:image/jpeg;base64,{{base64_encode($bg_img)}}");
                    background-size: 100%;
                    background-position: top center;
                    position: absolute;
                    width: 100%;
                    filter: blur(10px) grayscale(80%);
                    animation-name: MOVE-BG;
                    animation-duration: 15s;
                    animation-timing-function: linear;
                    animation-iteration-count: infinite;
                    height: {{$bg_img_dims[1]}}px;
                    /*height: 100%;*/
                }
                #page-preview-bg:before {
                    top : {{$bg_img_dims[1]}}px;
                    /*top: 100%;*/
                }
                @keyframes MOVE-BG {
                    from {
                        transform: translateY(0%);
                    }
                    to {
                        transform: translateY(-100%);
                    }
                }
            @endif

            .sm_main {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
            }
            #logo-header {
                margin-bottom: 20px;
            }
            #logo-header img {
                width: 50%;
                display: block;
                margin: auto;
                transform: translateX(-8px);
            }
        </style>

        {{--note: currently the help page is automaticall disabled when we are @ whiteLabel mode --}}
        @if($show_help)
        <style>
            /*
             * HELP STYLES
             */
            .sm_main h3 {
                line-height: 40px;
            }
            .sm_main .btn {
                /*width: 50%;*/
                position: relative;
            }
            .sm_main i.fas {
                color: #ef7b00;
                font-size: 25px;
                position: absolute;
                top: 2px;
                left: 5px;
            }
            /*.sm_main i.fas +a {
                padding-left: 20px;
            }*/
            .sm_main .btn-grp-container {
                display: flex;
                flex-flow: column;
                align-items: center;
                margin-bottom: 10px;
            }

            a {
                color: #ef7b00;
                transition: all 0.5s;
            }
            a:hover {
                color: {{WebFrame::adjustBrightness('#ef7b00', 20)}};
                text-decoration: none;
            }

            .jumbotron {
                box-shadow: 3px 4px 11px #2b2b2b1f;
                background-color: #ef7b00;
                color: white;
                padding-left: 30px;
                padding-right: 30px;
                margin-bottom: 10px;
            }
            .jumbotron + a {
                background-color: rgba(255,255,255, 0.8);
                padding: 5px 10px;
            }

            .help-email {
                display: inline-block;
                margin-bottom: 5px;
            }
            .help-email a {
                padding-left: 25px;
            }
            .help-phone a {
                padding-left: 15px;
            }
        </style>
        @else
        <style>
            /*
             * LOGIN PAGE STYLES
             */
            .thumbur {
                width: 150px;
                height: 150px;
                position: relative;
                background-color: #efefef;
                zoom: 1;
                filter: progid:DXImageTransform.Microsoft.gradient(gradientType=1, startColorstr='#FFEFEFEF', endColorstr='#FFE1E1E1');
                background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuMCIgeTE9IjAuNSIgeDI9IjEuMCIgeTI9IjAuNSI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2VmZWZlZiIvPjxzdG9wIG9mZnNldD0iNTAlIiBzdG9wLWNvbG9yPSIjZWZlZmVmIi8+PHN0b3Agb2Zmc2V0PSI1MCUiIHN0b3AtY29sb3I9IiNlMWUxZTEiLz48c3RvcCBvZmZzZXQ9IjEwMCUiIHN0b3AtY29sb3I9IiNlMWUxZTEiLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWQpIiAvPjwvc3ZnPiA=');
                background-size: 100%;
                background-image: linear-gradient(to right, #efefef 0%, #efefef 50%, #e1e1e1 50%, #e1e1e1 100%);
                margin: auto;
                border-radius: 100%;
            }
            .thumbur:before {
                content: '';
                position: absolute;
                width: 6px;
                height: 12px;
                background-color: #efefef;
                left: 50%;
                bottom: 50px;
                z-index: 5;
                -webkit-transform: translateX(-50%);
                transform: translateX(-50%);
                border-bottom-left-radius: 2px;
                border-bottom-right-radius: 2px;
            }

            /* main lock 'body' */
            <?php
            $main_lock_color = $cfg->whiteLabel() ? '#ffe200' /* < any unidentifiable yellow */ : '#FFA000' /* < STORMS SOFTWARE orange*/;
            ?>
            .icon-lock {
                position: relative;
                width: 80px;
                height: 60px;
                background: {{$main_lock_color}};
                margin: auto;
                -webkit-transform: translateY(60px);
                transform: translateY(60px);
                border-radius: 8px;
                box-shadow: 0 0 2px {{UtilityMethods::adjustBrightness($main_lock_color, -50)}} inset;
            }
            /* lock clip */
            .icon-lock:after {
                content: '';
                position: absolute;
                width: 50px;
                height: 35px;
                border: 9px solid {{UtilityMethods::adjustBrightness($main_lock_color, -30)}};
                border-bottom: none;
                bottom: 100%;
                left: 50%;
                -webkit-transform: translateX(-50%);
                transform: translateX(-50%);
                border-top-left-radius: 50px;
                border-top-right-radius: 50px;
            }
            .icon-lock:before {
                content: '';
                position: absolute;
                width: 12px;
                height: 12px;
                background-color: #efefef;
                left: 50%;
                top: 20px;
                -webkit-transform: translateX(-50%);
                transform: translateX(-50%);
                border-radius: 100%;
            }

            .panel-lite {
                margin: auto;
                width: 360px;
                background: #fff;
                padding: 45px 20px;
                padding-bottom: 10px;
                border-radius: 4px;
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
                position: relative;
            }
            .panel-lite h4 {
                font-weight: 400;
                font-size: 24px;
                text-align: center;
                color: {{$mainColor}};
                margin: 15px auto;
            }
            .panel-lite a {
                display: inline-block;
                margin-top: 25px;
                text-decoration: none;
                color: {{$mainColor}};
                font-size: 14px;
            }

            .form-group {
                position: relative;
                font-size: 15px;
                color: #666;
            }
            .form-group + .form-group {
                margin-top: 30px;
            }
            .form-group .form-label {
                position: absolute;
                z-index: 1;
                left: 0;
                top: 5px;
                transition: 0.3s;
            }
            .form-group .form-control {
                width: 100%;
                position: relative;
                z-index: 3;
                height: 35px;
                background: none;
                border: none;
                padding: 5px 0;
                transition: 0.3s;
                border-bottom: 1px solid #777;
            }
            .form-group .form-control:invalid {
                outline: none;
            }
            .form-group .form-control:focus, .form-group .form-control:valid {
                outline: none;
                color: {{$mainColor}};
                box-shadow: 0 1px {{$mainColor}};
                border-color: {{$mainColor}};
            }
            .form-group .form-control:focus + .form-label, .form-group .form-control:valid + .form-label {
                font-size: 12px;
                -webkit-transform: translateY(-15px);
                transform: translateY(-15px);
            }

            .floating-btn {
                background: {{$mainColor}};
                width: 60px;
                height: 60px;
                border-radius: 50%;
                color: #fff;
                font-size: 32px;
                border : 1px solid darkgrey;
                position: absolute;
                margin: auto;
                transition: 0.3s;
                margin: auto;
                right: -30px;
                bottom: 90px;
                cursor: pointer;
            }
            .floating-btn:hover {
                box-shadow: 0 0 0 rgba(0, 0, 0, 0.3) inset, 0 3px 6px rgba(0, 0, 0, 0.16), 0 5px 11px rgba(0, 0, 0, 0.23);
            }
            .floating-btn:hover .icon-arrow {
                -webkit-transform: rotate(45deg) scale(1.2);
                transform: rotate(45deg) scale(1.2);
            }
            .floating-btn:focus, .floating-btn:active {
                outline: none;
            }

            .panel-lite .fas {
                color: white;
            }
        </style>
        @endif

    </head>
    <body class="page-{{$_->page->getName()}} preview-protection">

        <div id="page-preview-bg"></div>

        <!-- =============================================== -->
        <div class="sm_main">
            {{--note: currently the help page is automaticall disabled when we are @ whiteLabel mode --}}
            @if($show_help)
            <div class="">
                <div class="jumbotron">
                    <div class="text-center">
                        @md(true)
                        **Sie haben Probleme mit dem Login?**
                        Dann wenden Sie sich bitte direkt an uns!
                        @endmd

                        <div class="btn-grp-container">
                            <span class="btn btn-default help-email"><i class="fas fa-envelope"></i> @_spamspan('info@storms-media.de')</span>
                            <span class="btn btn-default help-phone"><i class="fas fa-mobile-alt"></i> <a href="tel:024319839000">02431 . 983 9000</a></span>
                        </div>

                        <small>STORMS|MEDIA - wir helfen Ihnen gerne.</small>
                    </div>
                </div>
                <a href="/login">&laquo; Zurück zum Login</a>
            </div>
            @else
            <div class="">
                <div class="panel-lite">
                    @if(!$cfg->whiteLabel())
                        <div class="text-center" id="logo-header">
                            <img src="https://www.storms-media.de/assets/img/logos/logo_head.svg" alt="STORMS Media Werbe-Agentur Erkelenz Heinsberg">
                        </div>
                    @endif
                    @if($cfg->loginCardHeadline())
                        <h4>{!! $cfg->loginCardHeadline() !!}</h4>
                    @endif
                    <div class="thumbur mb40">
                        <div class="icon-lock"></div>
                    </div>
                    <form method="post" action="">
                        <div class="form-group">
                            <input tabindex="0" class="form-control" name="password" type="text" autocomplete="off" required="required" id="login-input"/>
                            <label class="form-label">Passwort</label>
                        </div>
                        @if($cfg->enableHelp() && !$cfg->whiteLabel())
                            <a href="/preview-help">Benötigen Sie Hilfe?</a>
                        @else
                            <div style="height: 20px"><!-- spacer --></div>
                        @endif
                        <button type="submit" class="floating-btn"><i class="fas fa-chevron-right"></i></button>
                    </form>
                </div>
            </div>
            @endif
        </div>
        <!-- =============================================== -->

        <!--<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>-->
        <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>-->

        {!!$_->js([
            'spamspan.js',
        ], $base . '/assets')!!}

        <script>
            document.getElementById("login-input").focus();
        </script>

    </body>

</html>
