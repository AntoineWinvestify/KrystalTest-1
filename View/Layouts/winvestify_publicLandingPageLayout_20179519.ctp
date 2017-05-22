<!DOCTYPE html>
<html>
    <head>  
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Winvestify</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

        <!-- Web Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,400i/Montserrat:400,700" rel="stylesheet">

        <script type="text/javascript" src="/js/accounting.min.js"></script>
        <script src="/plugins/jQuery/jquery-2.2.3.min.js"></script>
        <script type="text/javascript" src="/js/local.js"></script>

        <!-- Vendor Styles -->
        <link href="/megaKit/css/animate.css" rel="stylesheet" type="text/css"/>
        <link href="/megaKit/vendor/themify/themify.css" rel="stylesheet" type="text/css"/>
        <link href="/megaKit/vendor/scrollbar/scrollbar.min.css" rel="stylesheet" type="text/css"/>
        <link href="/megaKit/vendor/swiper/swiper.min.css" rel="stylesheet" type="text/css"/>

        <!-- Theme Styles -->
        <link href="/megaKit/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/megaKit/css/global.css" rel="stylesheet" type="text/css"/>
        <link type="text/css" rel="stylesheet" href="/css/compare_styles.css">


        <!-- Modals css -->
        <link href="/modals/assets/css/paper-bootstrap-wizard.css" rel="stylesheet" />
        <link href="http://netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
        <link href="/modals/assets/css/themify-icons.css" rel="stylesheet">
        <!-- /modals css -->

        <?php
        $file = APP . "Config" . DS . "googleCode.php";
        if (file_exists($file)) {
            include_once($file);
        }
        ?>
        
        <link rel="icon" href="/img/logo_winvestify/Logo_favicon.png">
    </head>
    <body>
        <!-- Modals js -->
        <script src="/modals/assets/js/jquery-2.2.4.min.js" type="text/javascript"></script>
        <script src="/modals/assets/js/jquery.bootstrap.wizard.js" type="text/javascript"></script>
        <!--  Plugin for the Wizard -->
        <script src="/modals/assets/js/paper-bootstrap-wizard.js" type="text/javascript"></script>
        <!--  More information about jquery.validate here: http://jqueryvalidation.org/	 -->
        <script src="/modals/assets/js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- /modals js -->

        <script type="text/javascript" src="/js/local.js"></script>
        <?php echo $this->Html->script(array('local')); ?>

        <script>
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
            //navbar collapse on clicking outside navbar
            $(document).on("click", function(){
                $('.navbar-collapse').collapse('hide');
            });
        </script>

        <!--========== HEADER ==========-->
        <header class="navbar-fixed-top s-header js__header-sticky js__header-overlay">
            <!-- Navbar -->
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header navbar-right">
                        <ul class="nav pull-left navbar-nav">
                            <li class="pull-left"><a href="#mark_login"><strong>Login</strong></a></li>
                            <li class="dropdown pull-left" style="margin-top:-3px">
                                <?php echo $this->element('languageWidget') ?>
                            </li>
                        </ul>
                        <button type="button" style="margin-top:12px; margin-left: 20px;" class="navbar-toggle" data-toggle="collapse" data-target="#principal_navbar" aria-expanded="false">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div> <!-- /navbar-header-->
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="visible-xs-block clearfix"></div>
                    <div class="collapse navbar-collapse" id="principal_navbar">
                        <ul class="nav navbar-nav navbar-left">
                            <li style="float:left"><a href="http://www.facebook.com/winvestify/"><i class="g-padding-r-5--xs ti-facebook"></i></a></li>
                            <li style="float:left"><a href="https://www.linkedin.com/company-beta/11000640/"><i class="g-padding-r-5--xs ti-linkedin"></i></a></li>
                            <li style="float:left"><a href="https://twitter.com/Winvestify"><i class="g-padding-r-5--xs ti-twitter"></i></a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="#mark_features"><?php echo __('Features') ?></a></li>
                            <li><a href="#mark_ftb"><?php echo __('Investor Toolbox') ?></a></li>
                            <li><a href="#mark_platforms"><?php echo __('Platforms') ?></a></li>
                            <li><a href="#mark_statistics"><?php echo __('Statistics') ?></a></li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div> <!--/container-fluid-->
            </nav>
            <!-- End Navbar -->
        </header>

        <!--========== END HEADER ==========-->

        <!--========== PROMO BLOCK ==========-->
        <a name="mark_login"></a>
        <div class="s-promo-block-v1 g-bg-color--primary-to-blueviolet-ltr g-fullheight--md" style="background-image:url(/img/resize/photo5_resize.png);">
            <div class="container g-ver-center--md g-padding-y-100--xs">
                <div class="row g-hor-centered-row--md g-margin-t-30--xs g-margin-t-20--sm">
                    <div class="col-lg-8 col-sm-8 g-hor-centered-row__col g-text-center--xs g-text-left--md g-margin-b-60--xs g-margin-b-0--md">
                        <h1 class="g-font-size-32--xs g-font-size-45--sm g-font-size-50--lg g-color--white">
                            <img src="/img/logo_winvestify/Logo_texto.png" alt="winvestify logo" class="responsiveImg center-block" style="float:center; max-width:400px;"/>
                        </h1>
                        <p style="text-align:center" class="g-font-size-32--xs g-font-size-42--md g-color--white g-margin-b-0--xs">
                            <strong><?php echo __('Connect to the major crowdlending platforms from our portal') ?>
                            </strong></p>
                        <p style="text-align:center">
                            <a href="/users/registerPanel" style="margin-top:10px;" class="text-uppercase s-btn s-btn--md s-btn--white-bg g-radius--50 g-padding-x-50--xs g-margin-b-20--xs btnRegister">
                                <?php echo __('Register') ?>
                            </a>
                        </p>
                    </div>
                    <div class="col-lg-2"></div>
                    <div class="col-lg-3 col-sm-4 g-hor-centered-row__col">
                        <div class="wow fadeInUp" data-wow-duration=".3" data-wow-delay=".1s">
                            <?php echo $this->Form->create('User', array('url' => "/users/loginAction",
                                'class' => "center-block g-width-340--xs g-bg-color--white-opacity-lightest g-box-shadow__bluegreen-v1 g-padding-x-40--xs g-padding-y-30--xs g-radius--4",));
                            ?>

                            <div class="g-text-center--xs">
                                <h2 class="g-font-size-30--xs g-color--white"><?php echo __('Login') ?></h2>
                            </div>
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
                            $class = "form-control1 s-form-v3__input userNameLogin" . ' ' . $errorClass;
                            ?>
                            <div>
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
                            $class = "form-control1 s-form-v3__input passwordLogin" . ' ' . $errorClass;
                            ?>								
                            <div style="margin-top:30px">
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

                            <div class="g-text-center--xs"> 
                                <button type="submit" id="loginBtn" style="margin-top:30px" class="text-uppercase s-btn s-btn--md s-btn--white-bg g-radius--50 g-padding-x-50--xs g-margin-b-20--xs"><?php echo __('Send') ?>
                                </button><br/>
                                <a class="g-color--white g-font-size-13--xs" href="#"><?php //echo __('Forgot your password?')  ?></a>
                            </div>
                            <!--	</form> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--========== END PROMO BLOCK ==========-->

        <!--========== PAGE CONTENT ==========-->
        <!-- Features -->
        <a name="mark_features"></a>
        <div id="js__scroll-to-section" class="container g-padding-y-80--xs g-padding-y-125--sm">
            <div class="g-text-center--xs g-margin-b-100--xs">
                <p class="text-uppercase g-font-size-18--xs g-font-weight--700 g-color--primary g-letter-spacing--2 g-margin-b-25--xs">
                    <img src="/img/logo_winvestify/Logo_texto.png" alt="winvestify logo" class="responsiveImg center-block" style="float:center; max-width:400px;"/>
                </p>
                <p style="text-align:center" class="g-font-size-18--xs">
                    <strong>
                        <?php echo __('Centralize all your investment accounts and connect to the major Crowdlending and Invoice Trading platforms
							  from a single portal. Our tool lets you organize and manage all your investments more efficiently. 
						At the same time you can access all investment oportunities in real time and have a truly global view of the Crowdlending market') ?>
                    </strong>
                </p>
            </div>
            <div class="row g-margin-b-60--xs g-margin-b-70--md">
                <div class="col-sm-4 g-margin-b-60--xs g-margin-b-0--md">
                    <div class="clearfix">
                        <div class="g-media__body g-padding-x-20--xs">
                            <p style="text-align:center"><i class="g-font-size-80--xs g-color--primary ion-person-stalker"></i></p>
                            <h3 class="g-font-size-18--xs" style="text-align:center"><?php echo __('Register') ?></h3>
                            <p class="g-margin-b-0--xs" style="text-align:center"><?php echo __('Use our platform for free and enjoy all the benefits') ?></p>
                        </div>

                    </div>
                </div>
                <div class="col-sm-4 g-margin-b-60--xs g-margin-b-0--md">
                    <div class="clearfix">
                        <div class="g-media__body g-padding-x-20--xs">
                            <p style="text-align:center"><i class="g-font-size-80--xs g-color--primary ion-link"></i></p>
                            <h3 class="g-font-size-18--xs" style="text-align:center"><?php echo __('Link your accounts') ?></h3>
                            <p class="g-margin-b-0--xs" style="text-align:center"><?php echo __('Connect to the principal alternative financing plataforms') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="clearfix">
                        <div class="g-media__body g-padding-x-20--xs">
                            <p style="text-align:center"><i class="g-font-size-80--xs g-color--primary ion-ios-settings-strong"></i></p>
                            <h3 class="g-font-size-18--xs" style="text-align:center"><?php echo __('Manage your investments') ?></h3>
                            <p class="g-margin-b-0--xs" style="text-align:center"><?php echo __('Your investments are completely organized, all automatically') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- // end row  -->
        </div>
        <!-- End Features -->

        <!-- Toolbox -->
        <a name="mark_ftb"></a>
        <div class="js__parallax-window" style="background: url(/megaKit/img/1920x1080/03.jpg) 50% 0 no-repeat fixed;">
            <div class="container g-text-center--xs g-padding-y-80--xs g-padding-y-125--sm">
                <div class="g-margin-b-80--xs">
                    <h2 class="g-font-size-40--xs g-font-size-50--sm g-font-size-60--md g-color--white"><?php echo __('Investor ToolBox Manager') ?></h2>
                </div>
                <br/><br/>
            </div>
        </div>
        <!-- End ftp -->
        <!-- Mockup -->
        <div class="container g-margin-t-o-100--xs g-margin-t-o-230--md">
            <div class="center-block s-mockup-v1">
                <div class="wow fadeInUp" data-wow-duration=".3" data-wow-delay=".1s">
                    <img class="img-responsive" alt="mockup" src="/megaKit/img/mockups/devices-01.png" alt="Mockup Image">
                </div>
            </div>
        </div>
        <!-- End Mockup -->

        <!-- Popup -->
        <div id="popUp" class="g-box-shadow__bluegreen-v1 wow fadeInLeft" data-wow-duration="5" data-wow-delay=".1s" style="position:fixed;">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <a class="closePopUp pull-right"><i class="ion ion-close-circled"></i></a>
                <p align="justify">
                    <?php echo __('Register for FREE and get ALL the advantages of a unified marketplace and single dashboard') ?>
                    <br/>
                    <a class="center-block" style="text-align: center; margin-top:5px;" href="/users/registerPanel">
                        <button class="btn btn-win3" style="margin-top:5px;">
                            <?php echo __('Register NOW') ?>
                        </button>
                    </a>
                </p>
            </div>
        </div>
        <!-- /popUp -->

        <!-- Platforms -->
        <a name="mark_platforms"></a>
        <div class="g-bg-color--sky-lighttt">
            <div class="g-container--md g-padding-y-40--xs g-padding-y-125--sm">
                <h3 class="g-color--primary" style="text-align:center"><?php echo __('A SMALL STEP FOR YOU, BUT A BIG STEP FOR YOUR INVESTMENTS') ?></h3>
                <p style="text-align:center" style="margin: 20px 0px;"><?php echo __('Winvestify connects to the principal <strong>Invoice
				Trading</strong> and <strong>Crowdlending</strong> platforms of Spain in a simple and secure way');?>
                </p>
                <!-- Swiper Clients -->
                <div class="s-swiper js__swiper-clients">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="wow fadeIn" data-wow-duration=".3" data-wow-delay=".1s">
                                <img class="s-clients-v1" src="/img/logo/Zank.png" alt="Zank">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="wow fadeIn" data-wow-duration=".3" data-wow-delay=".2s">
                                <img class="s-clients-v1" src="/img/logo/Comunitae.png" alt="Comunitae">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="wow fadeIn" data-wow-duration=".3" data-wow-delay=".3s">
                                <img class="s-clients-v1" src="/img/logo/Arboribus.png" alt="Arboribus">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="wow fadeIn" data-wow-duration=".3" data-wow-delay=".4s">
                                <img class="s-clients-v1" src="/img/logo/LoanBook.png" alt="Loanbook">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="wow fadeIn" data-wow-duration=".3" data-wow-delay=".5s">
                                <img class="s-clients-v1" src="/img/logo/Circulantis.png" alt="Circulantis">
                            </div>
                        </div>
                    </div><br/><br/>
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="wow fadeIn" data-wow-duration=".3" data-wow-delay=".1s">
                                <img class="s-clients-v1" src="/img/logo/Colectual.png" alt="Colectual">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="wow fadeIn" data-wow-duration=".3" data-wow-delay=".2s">
                                <img class="s-clients-v1" src="/img/logo/MyTripleA.png" alt="MyTripleA">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="wow fadeIn" data-wow-duration=".3" data-wow-delay=".3s">
                                <img class="s-clients-v1" src="/img/logo/Ecrowd.png" alt="EcrowdInvest">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="wow fadeIn" data-wow-duration=".3" data-wow-delay=".4s">
                                <img class="s-clients-v1" src="/img/logo/Lendix.png" alt="Lendix">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="wow fadeIn" data-wow-duration=".3" data-wow-delay=".5s">
                                <img class="s-clients-v1" src="/img/logo/Growly.png" alt="Growly">
                            </div>
                        </div>
                    </div><br/><br/>
                </div>
                <!-- End Swiper Clients -->
            </div>
        </div>
        <!-- End Platforms -->

        <!-- Statistics -->
        <?php
        foreach ($globalResults as $result) {
            $global['totalAmount'] = $global['totalAmount'] + $result['TotalInvestmentAmountAvailableInCompany'];
            $global['totalOptions'] = $global['totalOptions'] + $result['TotalInvestmentOptionsAvailableInCompany'];
            $global['totalPreInvested'] = $global['totalPreInvested'] + $result['TotalAmountPreInvestedInCompany'];
        }
        $tempvalue1 = (int) ($global['totalPreInvested'] / 100 );
        $tempvalue2 = (int) ($global['totalAmount'] / 100 );
        ?>

        <script type="text/javascript">
            var optionsAccounting = {
                symbol: " &euro;",
                decimal: ",",
                thousand: ".",
                precision: 0,
                format: "%v%s"
            };

            temp1 = accounting.formatMoney(<?php echo $tempvalue1 ?>, optionsAccounting);
            $(".value1").append(temp1);
            temp2 = accounting.formatMoney(<?php echo $tempvalue2 ?>, optionsAccounting);
            $(".value2").append(temp1);
        </script>

        <a name="mark_statistics"></a>
        <div class="js__parallax-window" style="background: url(/megaKit/img/1920x1080/06.jpg) 50% 0 no-repeat fixed;">
            <div class="container g-padding-y-80--xs g-padding-y-125--sm">
                <div class="row">
                    <div class="col-md-4 col-xs-6 g-full-width--xs g-margin-b-70--xs g-margin-b-0--sm">
                        <div class="g-text-center--xs">
                            <span class="g-display-block--xs g-font-size-60--xs g-color--white g-margin-b-10--xs js__counter value1">
                                <script>document.writeln(temp1);</script>
                            </span>
                            <div class="center-block g-hor-divider__solid--white g-width-40--xs g-margin-b-25--xs"></div>
                            <h4 class="g-font-size-18--xs g-color--white">
                                <?php echo __('Amount Invested') ?>
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-offset-1 col-md-3 col-xs-6 g-full-width--xs g-margin-b-70--xs g-margin-b-0--lg">
                        <div class="g-text-center--xs">
                            <span class="g-display-block--xs g-font-size-60--xs g-color--white g-margin-b-10--xs js__counter">
                                <?php echo $global['totalOptions'] ?>
                            </span>
                            <div class="center-block g-hor-divider__solid--white g-width-40--xs g-margin-b-25--xs"></div>
                            <h4 class="g-font-size-18--xs g-color--white">
                                <?php echo __('Open investments') ?>
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-6 g-full-width--xs">
                        <div class="g-text-center--xs">
                            <div class="g-margin-b-10--xs">
                                <span class="g-display-inline-block--xs g-font-size-60--xs g-color--white js__counter">
                                    <script>document.writeln(temp2);</script>
                                </span>
                            </div>
                            <div class="center-block g-hor-divider__solid--white g-width-40--xs g-margin-b-25--xs"></div>
                            <h4 class="g-font-size-18--xs g-color--white">
                                <?php echo __('Total Investment') ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Statistics-->
        <!--========== END PAGE CONTENT ==========-->

        <?php echo $this->fetch('content'); ?>
        <?php echo $this->element('publicfooter') ?>

        <!-- Back To Top -->
        <a href="javascript:void(0);" class="s-back-to-top js__back-to-top"></a>

        <!--========== JAVASCRIPTS (Load javascripts at bottom, this will reduce page load time) ==========-->
        <!-- Vendor -->
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/megaKit/vendor/jquery.back-to-top.min.js"></script>
        <script type="text/javascript" src="/megaKit/vendor/swiper/swiper.jquery.min.js"></script>
        <script type="text/javascript" src="/megaKit/vendor/jquery.wow.min.js"></script>

        <!-- General Components and Settings -->
        <script type="text/javascript" src="/megaKit/js/global.min.js"></script>
        <script type="text/javascript" src="/megaKit/js/swiper.min.js"></script>
        <script type="text/javascript" src="/megaKit/js/wow.min.js"></script>


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
        
        <script type="text/javascript" src="/js/publicLandingPage.js">
            
        </script>
        <!--========== END JAVASCRIPTS ==========-->
    </body>
</html>
