<?php
$google_tracking_id = $google_tracking_id ?? $gtid ?? Config::getProp('GOOGLE_TRACKING_ID');
$facebook_tracking_id = $facebook_tracking_id ?? $ftid ?? Config::getProp('FACEBOOK_TRACKING_ID');

/* @var $google_tracking_id string|null */
/* @var $facebook_tracking_id string|null */
?>

@if($google_tracking_id || $facebook_tracking_id)
    <?php
    $cookie_query = http_build_query([
        'gaId' => $google_tracking_id,
        'fbpId' => $facebook_tracking_id
    ]) ?>
    <!--== COOKIE POPUP STUFF ==-->
    <link rel="stylesheet" href="https://cookie-hint.storms-media.de/cookie-styles.min.css">
    <script type="text/javascript" src="https://cookie-hint.storms-media.de/vendor/cookie.min.js"></script>
    <script type="text/javascript" src="https://cookie-hint.storms-media.de/cookie-script.php?{!! $cookie_query !!}"></script>
    <!--== /COOKIE POPUP STUFF ==-->
@endif
