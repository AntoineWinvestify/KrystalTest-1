<!DOCTYPE html>
<html lang="es-ES">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Winvestify</title>

        <!-- Bootstrap 3.3.6 -->
        <link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css">

        <!-- Web Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,400i|Montserrat:400,700" rel="stylesheet">

        <!-- Vendor Styles -->
        <link href="/megaKit/vendor/themify/themify.css" rel="stylesheet" type="text/css"/>

        <!-- Theme Styles -->
        <link href="/megaKit/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/megaKit/css/global.css" rel="stylesheet" type="text/css"/>
        <link href="/megaKit/css/custom.css" rel="stylesheet" type="text/css"/>
        <link href="/css/compare_styles.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <?php
            $file = APP . "Config" . DS . "googleCode.php";
            if (file_exists($file)) {
                include_once($file);
            }
        ?>
    </head>
    <style>
        body {
            background-color: white;
        }
        input {
            border: none;
        }
    </style>


    <body>
            <?php echo $this->fetch('content'); ?>
        </div>
        
        <script type="text/javascript" src="/modals/assets/js/jquery-2.2.4.min.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/local.js"></script>
    </body>
</html>