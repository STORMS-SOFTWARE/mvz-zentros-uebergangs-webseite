<?php
/* @var $_ WebFrame */
/* @var $cfg \STORMS\webframe\Modules\InstagramConfig */

use STORMS\webframe\Core\WebFrame;

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex, nofollow">

        <title>TOKEN GESPEICHERT - Instagram Token Config</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

        <link href="https://fonts.googleapis.com/css?family=Niramit" rel="stylesheet">

    </head>
    <body class="instagram-token-setup success   d-flex align-items-center vh-100">
        <div class="container">
            <div class="p-5 mb-4 bg-light rounded-3">
                <div class="container-fluid py-5">
                    <h1 class="display-5 fw-bold">Warnung</h1>

                    <div class="alert alert-warning" role="alert">
                        Es existiert bereits ein Token. Wenn du ein neues Token speichern willst, bestätige bitte den nachfolgenden Button
                    </div>

                    <a class="btn btn-primary btn-lg" href="?force-set-instagram-token&password={{$_GET['password']??''}}">Neues Token festlegen</a>
                </div>
            </div>
        </div>
    </body>
</html>
