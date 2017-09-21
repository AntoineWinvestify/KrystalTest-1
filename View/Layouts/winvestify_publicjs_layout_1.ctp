<?php
/**
 * +---------------------------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                                               |
 * +---------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 				 |
 * | it under the terms of the GNU General Public License as published by  			 |
 * | the Free Software Foundation; either version 2 of the License, or                           |
 * | (at your option) any later version.                                      			 |
 * | This file is distributed in the hope that it will be useful   				 |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of                          	 |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                                |
 * | GNU General Public License for more details.        			              	 |
 * +---------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.8
 * @date 2017-09-14
 * @package
 * 
 * New Static Page design.
 * 
 * [2017-09-14] version 0.1
 * 
 */
?>

<!DOCTYPE html>
<html lang="es-ES">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Winvestify</title>
        <link rel="icon" href="/img/logo_winvestify/Logo_favicon.png">

        <!-- Bootstrap 3.3.6 -->
        <link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css">

        <!-- Web Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,400i|Montserrat:400,700" rel="stylesheet">
        <link href="/megaKit/vendor/themify/themify.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">

        <!-- Theme Styles -->
        <link href="/megaKit/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/megaKit/css/global.css" rel="stylesheet" type="text/css"/>
        <link href="/megaKit/css/custom.css" rel="stylesheet" type="text/css"/>
        <link href="/css/compare_styles.css" rel="stylesheet" type="text/css"/>

        <?php
            $file = APP . "Config" . DS . "googleCode.php";
            if (file_exists($file)) {
                include_once($file);
            }
        ?>
        <style>
            body {
                background-color: black !important;
            }
        </style>
    </head>
    <body>
        <!--========== HEADER ==========-->
        <header class="navbar-fixed-top s-header js__header-sticky js__header-overlay">
            <!-- Navbar -->
            <nav class="navbar navbar-inverse">
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
                            <li><a href="/"><i class="g-padding-r-5--xs ti-home"></i></a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                                <ul class="dropdown-menu inverse-dropdown">
                                  <li><a href="#">Action</a></li>
                                  <li><a href="#">Another action</a></li>
                                  <li><a href="#">Something else here</a></li>
                                  <li role="separator" class="divider"></li>
                                  <li><a href="#">Separated link</a></li>
                                  <li role="separator" class="divider"></li>
                                  <li><a href="#">One more separated link</a></li>
                                </ul>
                            </li>
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
        <!-- Content -->
        <div id="js__scroll-to-section" style="margin: 50px 50px 10px 50px">
            <?php echo $this->fetch('content'); ?>
        </div>
        <!-- /Content -->
        <script type="text/javascript" src="/modals/assets/js/jquery-2.2.4.min.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/local.js"></script>
    </body>
</html>
