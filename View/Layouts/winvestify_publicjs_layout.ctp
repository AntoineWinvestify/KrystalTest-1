<!DOCTYPE html>
<html lang="es-ES">
    <head>
        <title>Winvestify</title>
        <?php
            echo $this->element('meta');
            echo $this->element('csspubliclayout');
         /*
          <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
          <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
          <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
          <![endif]-->
         */
            $file = APP . "Config" . DS . "googleCode.php";
            if (file_exists($file)) {
                include_once($file);
            }
            echo $this->element('favicon');
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
        <?php
            echo $this->element('headerPublicPage');
        ?>
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
        <div id="footer">
          <?php echo $this->element('publicfooter'); ?>  
        </div>
        <!-- End Terms & Conditions -->
        <script type="text/javascript" src="/modals/assets/js/jquery-2.2.4.min.js"></script>
        <?php
            echo $this->element('jsPublicLandingPage');
            echo $this->element('jsPublicFunctions');
        ?>
    </body>
</html>
