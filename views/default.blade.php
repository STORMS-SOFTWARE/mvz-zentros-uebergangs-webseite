<?php
/* @var $_ \STORMS\webframe\Core\WebFrame */
/* @var $_->blade eftec\bladeone\BladeOne */
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVZ - ZenTrOS GbR</title>
    <meta name="description" content="Sehr geehrte Patientinnen und Patienten, wir informieren Sie, dass unsere Praxen in 41836 Hückelhoven, Vielhauerweg 1 und in 41844 Wegberg, Antoniusweg 4 zum 4 Quartal 2026 zusammengelegt werden.">
    <link rel="stylesheet" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" href="/assets/css/font-awesome-pro.css">
    <link rel="stylesheet" href="/assets/css/cuSTORMS.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/assets/css/cuSTORMSMEDIA.css">

</head>

<body>

<main>
    @content
</main>


<footer>
    <div class="container">
        <div class="row">
            <div class="col-12 d-flex justify-content-end">
                <a href="/impressum">Impressum</a>&nbsp;|&nbsp;<a href="/datenschutz">Datenschutz</a>
            </div>
        </div>
    </div>
</footer>

<script type="text/javascript" src="/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/assets/js/cuSTORMS.js"></script>
</body>

</html>