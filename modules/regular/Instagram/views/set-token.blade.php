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

        <title>Instagram Token Config</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

        <link href="https://fonts.googleapis.com/css?family=Niramit" rel="stylesheet">

    </head>
    <body class="instagram-token-setup    d-flex align-items-center vh-100">
        <div class="container">
            <div class="p-5 mb-4 bg-light rounded-3">
                <div class="container-fluid py-5">
                    <h1 class="display-5 fw-bold">STORMS webframe Instagram <u>Token-Config</u></h1>
                    <p class="fs-4">
                        Du hast das webframe Instagram Modul aktiviert.
                        <br>
                        Bitte gib hier das {{$initial ? 'initiale' : ''}} Token ein mit dem das Modul Anfragen an die Instagram API schicken soll.
                    </p>
                    <p>
                        Falls die Webseite bereits live ist, solltest du dir das Token via FTP von der Live-Seite beziehen.
                    </p>

                    @if(!$initial)
                        <div class="alert alert-danger" role="alert">
                            Es wurde bereits ein Token festgelegt - du bist im Begriff dieses zu überschreiben.
                        </div>
                    @endif

                    <form method="post">
                        <div class="mb-3">
                            <label for="wfim-input-1" class="form-label">Instagram Token</label>
                            <input type="text" class="form-control" id="wfim-input-1" name="instagram-token">
                        </div>

                        <button class="btn btn-primary btn-lg" type="submit">Token speichern</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
