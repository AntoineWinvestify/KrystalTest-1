<!DOCTYPE html>
<html>
    <head>  
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <title>Winvestify</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1" name="viewport">
       
        <!-- Theme STYLES -->
        <link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css">
        <link type="text/css" rel="stylesheet" href="/megaKit/css/style.css"/>
        <link type="text/css" rel="stylesheet" href="/megaKit/css/global.css"/>
        <link type="text/css" rel="stylesheet" href="/css/compare_styles.css"/>
        <!-- Ionicons -->
        <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"/>
        <!-- Web Fonts -->
        <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"/>
        <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400,400i/Montserrat:400,700"/>
        <link type="text/css" rel="stylesheet" href='https://fonts.googleapis.com/css?family=Muli:400,300'/>
        <!-- Plugins -->
        <link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
        <link type="text/css" rel="stylesheet" href="/megaKit/css/animate.css"/>
        <link type="text/css" rel="stylesheet" href="/megaKit/vendor/themify/themify.css"/>
        <link type="text/css" rel="stylesheet" href="/megaKit/vendor/scrollbar/scrollbar.min.css"/>
        <link type="text/css" rel="stylesheet" href="/megaKit/vendor/swiper/swiper.min.css"/>

        <?php
        $file = APP . "Config" . DS . "googleCode.php";
        if (file_exists($file)) {
            include_once($file);
        }
        ?>
        <script type="text/javascript" src="/modals/assets/js/jquery-2.2.4.min.js"></script>
        <link rel="icon" href="/img/logo_winvestify/Logo_favicon.png">
    </head>
    <body>
        <?php echo $this->Html->script(array('local')); ?>
        <!--========== HEADER ==========-->
        <header class="s-header js__header-sticky js__header-overlay">
            <!-- Navbar -->
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header navbar-right">
                        <ul class="nav pull-left navbar-nav collapse-tablet navGreen">
                            <li id="liLogin" style="float:left; display:inline-block" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Login <span class="caret"></span></a>
                                <div id="loginDropdown" class="dropdown-menu dropdown-menu-left">
                                    <div class="row">
                                        <div class="col-sm-offset-1 col-sm-10 col-xs-12" style="margin-top:10px;">
                                            <?php echo $this->Form->create('User', array('url' => "/users/loginAction"));
                                            ?>
                                            <?php
                                            $authMsg = $this->Session->consume('Message.auth.message');
                                            ?>
                                            <div class="errorInputMessage ErrorInactiveAccount col-xs-offset-1">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo "ABC" . $authMsg; ?>
                                                </span>
                                            </div>
                                            <?php
                                            if (!empty($authMsg)) {            // Authentication Error
                                                $errorClassesForTexts = "errorInputMessage ErrorUsernameLogin col-xs-offset-1";
                                                if (array_key_exists('username', $validationResult)) {
                                                    $errorClassesForTexts .= " " . "actived";
                                                }
                                                ?>
                                                <div class="<?php echo $errorClassesForTexts ?>">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                    <span id="ContentPlaceHolder_ErrorPassword" class="errorMessage"><?php echo $authMsg ?></span>
                                                </div>
                                                <?php
                                            }
                                            ?>									
                                            <?php
                                            $errorClass = "";
                                            if (!empty($authMsg)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue center-block userNameLogin" . ' ' . $errorClass;
                                            ?>
                                            
                                            <div>
                                                <label><?php echo __('Username')?></label>
                                                <input type="email" id="btnLoginUsername" name="data[User][username]" class="<?php echo $class ?>" placeholder="<?php echo __('Email') ?>">
                                            </div>
                                            <?php
                                            $errorClassesText = "errorInputMessage ErrorInactiveaccount col-xs-offset-1";
                                            if (!empty($authMsg)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $authMsg ?>
                                                </span>
                                            </div>									
                                            <?php
                                            $errorClass = "";
                                            if (!empty($authMsg)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue center-block passwordLogin" . ' ' . $errorClass;
                                            ?>		
                                            <div style="margin-top:30px">
                                                <label><?php echo __('Password')?></label>
                                                <input type="password" id="btnLoginPassword" name="data[User][password]" class="<?php echo $class ?>" placeholder="<?php echo __('Password') ?>">
                                            </div>

                                            <div class="errorInputMessage ErrorPassword">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $error ?>
                                                </span>
                                            </div>									

                                            <?php
                                            if (!empty($authMsg)) {            // Authentication Error as detected by server
                                                $errorClassesForTexts = "errorInputMessage ErrorPasswordLogin col-xs-offset-1";
                                                if (!empty($authMsg)) {
                                                    $errorClassesForTexts .= " " . "actived";
                                                }
                                                ?>
                                                <div class="<?php echo $errorClassesForTexts ?>">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                    <span id="ContentPlaceHolder_ErrorPassword" class="errorMessage"><?php echo $authMsg ?></span>
                                                </div>
                                                <?php
                                            }
                                            ?>									

                                            <div class="pull-right"> 
                                                <?php /*<a href="#" class="center-block"><?php echo __('Forgot your password?')  ?></a>*/?>
                                                <button type="submit" id="loginBtn" style="margin-top:10px; margin-bottom: 10px;" class="text-uppercase btn"><?php echo __('Send') ?>
                                                </button><br/>
                                            </div>
                                        </div>
                                    <!--	</form> -->
                                </div> <!-- /login -->
                                <div class="clearfix"></div>
                            </li>
                            <li style="float:left; display:inline-block">
                                <a href="/users/registerPanel">
                                    <?php echo __('Register')?>
                                </a>
                            </li>
                            <li class="dropdown" style="margin-top:0px; display:inline-block">
                                <?php echo $this->element('languageWidget') ?>
                                <div class="visible-xs-block clearfix"></div>
                            </li>
                        </ul>
                        <button type="button" style="margin-top:12px; margin-left: 20px; float:right;" class="navbar-toggle" data-toggle="collapse" data-target="#principal_navbar" aria-expanded="false">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div> <!-- /navbar-header-->
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="visible-xs-block clearfix"></div>
                    <div class="collapse navbar-collapse" id="principal_navbar">
                        <ul class="nav navbar-nav navbar-right navGreen">
                            <li style="float:left"><a href="http://www.facebook.com/winvestify/"><small><i class="g-padding-r-5--xs ti-facebook"></i></small></a></li>
                            <li style="float:left"><a href="https://www.linkedin.com/company-beta/11000640/"><small><i class="g-padding-r-5--xs ti-linkedin"></i></small></a></li>
                            <li style="float:left"><a href="https://twitter.com/Winvestify"><small><i class="g-padding-r-5--xs ti-twitter"></i></small></a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-left navGreen">
                            <li><a href="/pages/investors"><?php echo __('Investors') ?></a></li>
                            <li><a href="/pages/platforms"><?php echo __('Platforms') ?></a></li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo __('About Us')?>
                                    <span class="caret"></span>
                                </a>
                                <ul style="margin-top:0px;" class="dropdown-menu">
                                    <li><a href="/pages/aboutUs "><?php echo __('Nuestra historia') ?></a></li>
                                    <li><a href="/pages/team"><?php echo __('Nuestro equipo') ?></a></li>
                                </ul>
                            </li>
                            <li><a href="/pages/faq"><?php echo __('FAQ') ?></a></li>
                            <li><a href="/Contactforms/form"><?php echo __('Contact') ?></a></li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div> <!--/container-fluid-->
            </nav>
            <!-- End Navbar -->
        </header>

        <!--========== END HEADER ==========-->
        <!--========== PAGE CONTENT ==========-->
        <!-- Toolbox -->
        <a name="mark_ftb"></a>
        <div class="js__parallax-window" id="parallaxFeatures" style="background: url(/megaKit/img/1920x1080/03.jpg) 50% 0 no-repeat fixed; padding-bottom: 150px;">
            <div class="container g-text-center--xs g-padding-y-80--xs g-padding-y-125--sm">
                <div class="g-margin-b-80--xs" style="margin-top: -50px !important;">
                </div>
            </div>
            <!-- Mockup -->
            <div class="container_features row">
                <div id="features_right" class="col-lg-offset-1 col-lg-6 col-md-10 col-sm-offset-1 col-sm-11 col-xs-offset-1 col-xs-12">
                    <p class="featuresP" data-wow-duration="1s" data-wow-delay=".1s"><br/>
                        <img src="/img/logo_winvestify/logo_texto.png" class="center-block imgResponsive" width="40%;"/><br/>
                        <span id="headerTitle"><?php echo __('Creemos que tienes derecho a sacar el máximo partido a tus inversiones') ?></span>
                        <span class="headerText"><?php echo __('Entérate de qué pasa con todas tus inversiones, de un modo organizado y estandarizado. Conecta con tus plataformas desde Winvestify y obtén el control total de todas tus cuentas.')?></span><br/>
                        <span class="headerText"><?php echo __('Somos la herramienta líder española en Crowdlending que te ayuda a controlar todas tus inversiones de un modo preciso y eficaz')?></span>
                    </p>
                    <div class="row" id="featuresButton">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <a class="center-block" style="text-align: center;" href="/users/registerPanel">
                                <button class="btn btn-lg" id="registerButton" type="button">
                                    <?php echo __('Register NOW') ?>
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="features_left" class="col-lg-1 col-md-12 hidden-sm hidden-xs">
                    <div class="s-mockup-v1" style="position:absolute;">
                        <div id="screen" data-wow-duration=".3" data-wow-delay=".1s">
                            <iframe style="position: absolute; top: 50px; left: 170px;" width="480" height="255" src="https://www.youtube.com/embed/Bk4o89mTG2U?rel=0" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Mockup -->
        <div class="reestructure"></div>
        <div id="prizes" class="row">
            <div class="col-xs-12 col-sm-12 col-sm-10 col-lg-10 col-sm-offset-1 col-lg-offset-1">
                <div class="col-xs-12 col-sm-12 col-sm-3 col-lg-3">
                    <div class="row">
                        <img src="/img/logo/BBVA.png" class="pull-left imgResponsive" width="150px;"/>
                    </div>
                    <div class="row"><?php echo __('Premio Mejor Fintech Andaluza')?></div>
                </div>
                <div class="col-xs-12 col-sm-12 col-sm-3 col-lg-3">
                    <div class="row">
                        <span style="font-weight: bold; font-size: xx-large; display:inline-block; margin-bottom: 7px;">+ 1.000.000 €</span>
                    </div>
                    <div class="row"><?php echo __('Asset Under Administration')?></div>
                </div>
            </div>
        </div>
        <div id="schema" class="row">
            <hr class="specialHr" width="90%">
            <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                <div class="divmiddle" style="height: 500px;">
                    <img src="/img/LandingPage/schema_spa.png" class="imgResponsive schemaImg center-block" style="height: 100%; margin-top: -50px"/>
                </div>
                <div class="divmiddle">
                    <h3><?php echo __('What is CrowdLending?')?></h3>
                    <p><?php echo __('El Crowdlending es un modelo de Financiación Alternativa, por el cual inversores particulares y profesionales utilizando Plataformas de Financiación Participativa prestan su dinero a cambio de recibir un interés acorde al riesgo asumido.')?></p>
                    <p><?php echo __('El resultado es que el prestatario consigue financiarse a un precio más competitivo y el inversor recibe una rentabilidad superior a otros productos financieros.')?></p>
                    <h3><?php echo __('Basic Rules for Investing in P2P Lending')?></h3>
                    <p>
                        <strong><?php echo __('Buy fractions of loans')?></strong><br/>
                        <?php echo __('Make wise fractional investments in several loans across different P2P Platforms')?>
                    </p>
                    <p>
                        <strong><?php echo __('Diversify your investments')?></strong><br/>
                        <?php echo __('Spread your money over many loans to reduce the risk of your investment.')?>
                    </p>
                </div>
            </div>
        </div>
        <!-- Popup -->
        <div id="popUp" class="g-box-shadow__bluegreen-v1 wow fadeInLeft" data-wow-duration="5" data-wow-delay=".1s" style="position:fixed;">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <a class="closePopUp pull-right"><i class="ion ion-close-circled"></i></a>
                <p align="justify">
                    <?php echo __('Register for FREE and get ALL the advantages of a unified marketplace and single dashboard') ?>
                    <br/>
                    <a class="center-block" style="text-align: center; margin-top:5px;" href="/users/registerPanel">
                        <button class="btn" style="margin-top:5px;" type="button">
                            <?php echo __('Register NOW') ?>
                        </button>
                    </a>
                </p>
            </div>
        </div>
        <!-- /popUp -->
        
        <!--========== END PAGE CONTENT ==========-->
        
        <?php echo $this->fetch('content'); ?>
        <?php echo $this->element('publicfooter') ?>

        <!-- Back To Top -->
        <a href="javascript:void(0);" class="s-back-to-top js__back-to-top"></a>

        <!--========== JAVASCRIPTS (Load javascripts at bottom, this will reduce page load time) ==========-->
        <!-- Plugins -->
        <script type="text/javascript" src="/megaKit/vendor/jquery.back-to-top.min.js"></script>
        <script type="text/javascript" src="/megaKit/vendor/jquery.wow.min.js"></script>
        <script type="text/javascript" src="/megaKit/js/wow.min.js"></script>
        <script type="text/javascript" src="/modals/assets/js/jquery.bootstrap.wizard.js"></script>
        <script type="text/javascript" src="/modals/assets/js/paper-bootstrap-wizard.js"></script>
        <script type="text/javascript" src="/modals/assets/js/jquery.validate.min.js"></script>
        <!-- Cookies script -->
        <script type="text/javascript" id="cookieinfo"
                src="//cookieinfoscript.com/js/cookieinfo.min.js"
                data-bg="#000000"
                data-fg="#FFFFFF"
                data-link="#87e14b"
                data-divlinkbg="#87e14b"
                data-cookie="CookieScript"
                data-message="<?php echo __('We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies') ?>"
                data-moreinfo="https://www.winvestify.com/pages/privacyPolicy#cookies"
                data-height="50"
                data-close-text="<?php echo __('Got it!') ?>">
        </script>
        <!-- General Components and Settings -->
        <script type="text/javascript" src="/megaKit/js/global.min.js"></script>
        <script type="text/javascript" src="/js/local.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            function successLanguage(data) {
                location.reload(true);
            }

            function sendLocationDataSuccess(data) {
            }

            function sendLocationDataError(data) {
            }
            
            $(document).ready(function () {
                $.getJSON('https://freegeoip.net/json/?callback=?', function (data) {		// 10.000 /hour, only IP
                    var link = "/marketplaces/location";
                    console.log(JSON.stringify(data, null, 2));
                    getServerData(link, data, sendLocationDataSuccess, sendLocationDataError);
                    console.log("Send location Data to server");
                });

                $(".flag-language").on("click", function () {
                    var id = $(this).attr("id");
                    var link = $(this).attr("href");
                    var params = {id: id};
                    var data = jQuery.param(params);
                    getServerData(link, data, successLanguage, successLanguage);
                    return false;
                });

                $("#loginBtn").bind("click", function (event) {
                    if (app.visual.checkFormLogin() === true) {				// continue with default action
                        return true;
                    }
                    console.log("Error detected in input parameters of login function");
                    event.stopPropagation();
                    event.preventDefault();
                    return false;
                });
                //dismiss popup
                $(document).on("click", ".closePopUp", function () {
                    $("#popUp").css("display", "none");
                });
                //fadeout popup
                fadeOutElement("#popUp", 15000);
                
                //navbar collapse on clicking element
                $(document).on("click", '.nav li a', function(){
                    $('.navbar-collapse').collapse('hide');
                });
                
                //navbar not collapsing on #loginDropdown
                $(document).on("click", "#loginDropdown", function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                });
                
                //navbar collapse on clicking outside navbar
                $(document).on("click", function(){
                    $('.navbar-collapse').collapse('hide');
                });
                
                //Dropdown menu hover
                $('ul.nav li.dropdown').hover(function() {
                  $(this).find('.dropdown-menu').stop(true, true).delay(100).fadeIn(400);
                }, function() {
                  $(this).find('.dropdown-menu').stop(true, true).delay(100).fadeOut(400);
                });
                
                //Schema img
                $(".flag-language").on('load', function() {        
                    
                });
            });

        </script>
        <!--========== END JAVASCRIPTS ==========-->
    </body>
</html>
