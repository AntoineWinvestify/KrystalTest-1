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
        <?php /*
          <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
          <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
          <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
          <![endif]-->
         */
        ?>
        */
        <?php
            $file = APP . "Config" . DS . "googleCode.php";
            if (file_exists($file)) {
                include_once($file);
            }
        ?>
        <style>
        .blue {
            border-radius: 10px !important;
        }
        label {
            font-size: large;
        }
        .s-form-v2__input {
            height: auto !important;
        }
        small {
            font-size: 14px;
        }
        placeholder {
            font-size: 25px;
        }
        </style>
    </head>


    <body>
        <!--========== HEADER ==========-->
        <header class="navbar-fixed-top s-header js__header-sticky js__header-overlay">
            <!-- Navbar -->
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#principal_navbar" aria-expanded="false">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div> <!-- /navbar-header-->
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="principal_navbar">
                        <ul class="nav navbar-nav navbar-left">
                            <li><a href="https://www.winvestify.com"><i class="g-padding-r-5--xs ti-home"></i></a></li>
                            <li><a href="http://www.facebook.com/winvestify/"><i class="g-padding-r-5--xs ti-facebook"></i></a></li>
                            <li><a href="https://www.linkedin.com/company-beta/11000640/"><i class="g-padding-r-5--xs ti-linkedin"></i></a></li>
                            <li><a href="https://twitter.com/Winvestify"><i class="g-padding-r-5--xs ti-twitter"></i></a></li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div> <!--/container-fluid-->
            </nav>
            <!-- End Navbar -->
        </header>
        <!--========== END HEADER ==========-->

        <!--========== PAGE CONTENT ==========-->
        <!-- Parallax -->
        <div class="js__parallax-window" style="background: url(/megaKit/img/1920x1080/03.jpg) 50% 0 no-repeat fixed;">
            <div class="container g-text-center--xs">
                <div class="g-margin-b-80--xs" style="padding-top: 100px;">
                    <img src="/img/logo_winvestify/Logo_texto.png" class="center-block responsiveImg" style="max-width:600px;"/>                 
                </div>
            </div>
        </div>
        <!-- Terms & Conditions -->
        <div id="js__scroll-to-section" style="margin: 50px 50px 10px 50px">
            <?php echo $this->fetch('content'); ?>
        </div>
        <!-- End Terms & Conditions -->
        <script type="text/javascript" src="/modals/assets/js/jquery-2.2.4.min.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/local.js"></script>
    </body>
</html>
