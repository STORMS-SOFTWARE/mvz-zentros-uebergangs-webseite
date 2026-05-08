<?php
/* @var $_ \STORMS\webframe\Core\WebFrame */
/* @var $_ ->blade eftec\bladeone\BladeOne */
?>

<!doctype html>
<html lang="de">
    <head>
        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{$_->getTitle()}}</title>
        <meta name="description" content="{{$_->getDescription()}}">

        {{--just to make things more realistic--}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

        <style>
            body, html {
                margin: 0;
                padding: 0;
                height: 100vh;
                width: 100vw;
                overflow: hidden;
            }
        </style>

    </head>
    <body>
        <img src="{{$img}}" alt="Preview Img" style="width: 100%; height: 100%">

        {{--just to make things more realistic--}}
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    </body>
</html>
