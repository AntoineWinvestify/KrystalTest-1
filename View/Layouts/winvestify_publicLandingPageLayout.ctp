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
        <div id="enlargeImg" style="display:none;">
            <img id="largeImg" src="" alt="">
                <button id="btnCloseLargeImg" type="button" class="close" data-dismiss="modal" aria-hidden="true"><?php echo __('CLOSE')?> &times;</button>
            </img>
        </div>
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
                            <li><a href="/pages/investors"><?php echo __('Inversores') ?></a></li>
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
        
        
        
        
        
        
        
        
        
        
        <!--========== PROMO BLOCK ==========-->
        <a name="mark_login"></a>
        <div class="s-promo-block-v1 g-bg-color--primary-to-blueviolet-ltr g-fullheight--md" style="background-image:url(/img/admin/login.png); background-size:cover ;">
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
                    <div class="col-lg-3 col-sm-4 g-hor-centered-row__col" style="display:none;">
                        <div class="wow fadeInUp" data-wow-duration=".3" data-wow-delay=".1s" style="border: 2px solid white; padding: 20px;">
                            <?php echo $this->Form->create('User', array('url' => "/users/loginAction",
                                'class' => "center-block g-width-340--xs g-bg-color--white-opacity-lightest g-padding-x-40--xs g-padding-y-30--xs g-radius--4",));
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
                            <div class="col-xs-offset-1">
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
                            <div style="margin-top:30px" class="col-xs-offset-1">
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
                    <div class="col-lg-3 col-sm-4 g-hor-centered-row__col">
                        <div class="wow fadeInUp" data-wow-duration=".3" data-wow-delay=".1s" style="border: 2px solid #87e14b; padding: 20px; background-color: rgba(255,255,255,0.2);">
                            <?php echo $this->Form->create('User', array('url' => "/users/loginAction",
                                'class' => "center-block g-width-340--xs g-bg-color--white-opacity-lightest g-padding-x-40--xs g-padding-y-30--xs g-radius--4",));
                            ?>

                            <div class="g-text-center--xs">
                                <h2 class="g-font-size-30--xs"><?php echo __('Login') ?></h2>
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
                            <div class="col-xs-offset-1">
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
                            <div style="margin-top:30px" class="col-xs-offset-1">
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
        <!-- Toolbox -->
        <a name="mark_ftb"></a>
        <div class="js__parallax-window" id="parallaxFeatures" style="background: url(/megaKit/img/1920x1080/03.jpg) 50% 0 no-repeat fixed; display:none;">
            <div class="container g-text-center--xs g-padding-y-80--xs g-padding-y-125--sm">
                <div class="g-margin-b-80--xs" style="margin-top: -50px !important;">
                    <h2 style="padding-bottom: 10px;" class="g-font-size-30--xs g-font-size-40--sm g-font-size-50--md g-color--white"><?php echo __('Investor ToolBox Manager') ?></h2>      
                </div>
                <br/><br/>
            </div>
        </div>
        <!-- End ftp -->
        <!-- Mockup -->
        <div class="container_features row" style="display:none;">
            <div id="features_right" class="col-lg-offset-1 col-lg-6 col-md-offset-2 col-md-10 col-sm-offset-1 col-sm-11 col-xs-offset-1 col-xs-12">
                <p class="featuresP" data-wow-duration="1s" data-wow-delay=".1s"><?php echo __('DASHBOARD<br/>All your investments are shown on one single page') ?></p>
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
            <div id="features_left" class="col-lg-1 col-md-12 col-sm-12 hidden-xs">
                <div class="s-mockup-v1" style="position:absolute;">
                    <div id="screen" data-wow-duration=".3" data-wow-delay=".1s">
                        <div class="screen_mark">
                            <ul class="nopad">
                                <li><img id="imgDashboard" src="/megaKit/img/mockups/ss_dashboard.png" style="cursor:pointer;" class="liElement"/></li>
                                <li><img id="imgMarketplace" src="/megaKit/img/mockups/ss_marketplace.png" style="cursor:pointer;" class="liElement"/></li>
                                <li><img id="imgLinkedAccounts" src="/megaKit/img/mockups/ss_linkaccounts.png" style="cursor:pointer;" class="liElement"/></li>
                                <li><img id="imgUserData" src="/megaKit/img/mockups/ss_userdata.png" style="cursor:pointer;" class="liElement"/></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Mockup -->
        <!-- Features -->
        <div class="reestructure"></div>
        
        <a name="mark_features"></a>
        <div id="js__scroll-to-section" class="container" style="margin-top: 75px;">
            <div id="featuresPadding" class="row g-margin-b-60--xs g-margin-b-70--md">
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
            <div class="g-text-center--xs g-margin-b-100--xs">
                <p id="pDescription" style="text-align:center" class="g-font-size-18--xs">
                    <strong>
                        <?php echo __('Centralize all your investment accounts and connect to the major Crowdlending and Invoice Trading platforms
							  from a single portal. Our tool lets you organize and manage all your investments more efficiently. 
						At the same time you can access all investment oportunities in real time and have a truly global view of the Crowdlending market') ?>
                    </strong>
                </p>
            </div>
            <!-- // end row  -->
        </div>
        <!-- End Features -->

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
                //dismiss enlargeImg
                $(document).on("click", "#btnCloseLargeImg", function () {
                    $("#enlargeImg").css("display", "none");
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
                
                //Screenshots Slider + Text Slider
                <?php 
                      /*
                       *  El slider de imágenes funciona desplazando los elementos la cantidad de pxs que ocupa cada una de las imágenes.
                       *  En caso de que se quiera cambiar la configuración, hay que modificar el width inferior para que  se ajuste al
                       *      ancho de la imagen correspondiente.
                       * 
                       *  El slider de textos funciona insertando el texto que se corresponde con la imagen del slider, quedando así asociados
                       *      los elementos entre sí.
                       *
                       */ 
                ?>
		var self = this;
		var slider = $("#screen");
		var holder = slider.find(".screen_mark ul");
		var current = 0;
		var limit = slider.find("li").length;
		var width = 356;
		var t;

		$("img.liElement").css({"cursor": "pointer"}).click(function(){
			self.move(current+1);
		});

		this.move = function(to) {
			current = to >= limit ? 0 : to;
			to = current * -width;
			holder.animate({left: to+"px"});
			
			clearTimeout(t);
			t = setTimeout(function(){
				self.move(current+1);
                                $(".featuresP").hide();
                                $(".featuresP").html(sliderText[current]);
                                $(".featuresP").fadeIn("slow");
			}, 7000);
		};
		self.move(0);
                var sliderText = ["<?php echo __('DASHBOARD<br/>All your investments are shown on one single page') ?>", 
                                  "<?php echo __('MARKETPLACE<br/>Our global marketplace contains all the investment opportunities in real time') ?>", 
                                  "<?php echo __('LINKED ACCOUNTS PANEL<br/>Incorporate all your Investment Accounts in our linked accounts panel') ?>", 
                                  "<?php echo __('PERSONAL DATA PANEL<br/>Access to your personal data is simple, secure and fast') ?>"
                                 ];
                
                //enlarge img
                $(document).on("click", ".liElement", function() {
                    imgID = $(this).attr("id");
                    switch(imgID){
                        case 'imgDashboard':
                            $("#largeImg").attr("src", "/megaKit/img/mockups/large_dashboard.png");
                            $("#largeImg").attr("alt", "Dashboard Screenshot");
                            break;
                        case 'imgMarketplace':
                            $("#largeImg").attr("src", "/megaKit/img/mockups/large_marketplace.png");
                            $("#largeImg").attr("alt", "Marketplace Screenshot");
                            break;
                        case 'imgLinkAccounts':
                            $("#largeImg").attr("src", "/megaKit/img/mockups/large_linkaccounts.png");
                            $("#largeImg").attr("alt", "Link Accounts Screenshot");
                            break;
                        case 'imgUserData':
                            $("#largeImg").attr("src", "/megaKit/img/mockups/large_userdata.png");
                            $("#largeImg").attr("alt", "User Data Screenshot");
                            break;
                        }
                    $("#enlargeImg").css("display", "block");
                });
            });
            
        </script>
        <!--========== END JAVASCRIPTS ==========-->
    </body>
</html>
