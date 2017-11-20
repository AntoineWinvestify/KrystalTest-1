<!DOCTYPE html>
<html>
    <head>  
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="<?php echo __('Winvestify is a platform that integrates the management of all your investments in P2P Lending platforms in one global dashboard')?>">
        <meta name="keywords" content="<?php echo __('Investor, Peer to Peer Lending, P2P Lending, Yield, Invest, Interest, High return on investment, dashboard, high ROI, personal loan')?>">
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
                            <li id="liLogin" style="float:left; display:inline-block" class="dropdown"><a id="liLink" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Login <span class="caret"></span></a>
                                <div id="loginDropdown" class="dropdown-menu dropdown-menu-left" style="cursor:auto;z-index: 1">
                                    <div class="row">
                                        <div class="col-sm-offset-1 col-sm-10 col-xs-offset-1 col-xs-10" style="margin-top:10px;">
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
                                            $class = "form-control blue_noborder4 center-block userNameLogin" . ' ' . $errorClass;
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
                                            $class = "form-control blue_noborder4 center-block passwordLogin" . ' ' . $errorClass;
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
                                                <button type="submit" id="loginBtn" style="margin-top:10px; margin-bottom: 10px;z-index:10" class="text-uppercase btn"><?php echo __('Send') ?>
                                                </button><br/>
                                            </div>
                                        </div>
                                    <!--	</form> -->
                                    <?php
                                        $this->Form->end();
                                    ?>
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
                                <a class="dropdown-toggle"style="cursor: pointer;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo __('About Winvestify')?>
                                    <span class="caret"></span>
                                </a>
                                <ul style="margin-top:0px;" class="dropdown-menu">
                                    <?php /*<li><a href="/pages/aboutUs"><?php echo __('Our history') ?></a></li> */?>
                                    <li><a href="/pages/team"><?php echo __('Our team') ?></a></li>
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
        <div class="js__parallax-window" id="parallaxFeatures" style="background: url(/megaKit/img/1920x1080/03.jpg) 50% 0 no-repeat fixed;">
            <!-- Mockup -->
            <div class="container_features row">
                <div id="features_right" class="col-lg-offset-1 col-lg-6 col-md-10 col-sm-offset-1 col-sm-11 col-xs-offset-1 col-xs-12">
                    <div class="row featuresP" data-wow-duration="1s" data-wow-delay=".1s">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <br><br><br>
                            <span id="headerTitle"><?php echo __('We believe you have the right to get the most out of your investments') ?></span>
                            <span class="headerText"><?php echo __('Find out what is happening to all your investments in an organized and standardized way. Connect with your platforms from Winvestify and get total control of all your accounts.')?></span><br/>
                            <span class="headerText"><?php echo __('We are the leading tool in P2P Lending that helps you manage all your investments in a precise and effective way.')?></span>
                        </div>
                    </div>
                    <div class="row" id="featuresButton">
                        <div class="col-lg-offset-1 col-lg-6 col-md-10 col-sm-offset-1 col-sm-11 col-xs-offset-1 col-xs-12"><br/>
                            <a class="center-block" style="text-align: center;" href="/users/registerPanel">
                                <button class="btn btn-lg btnGeneral pull-left" type="button">
                                    <?php echo __('Open account') ?>
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="features_left" class="col-lg-1 col-md-12 hidden-sm hidden-xs">
                    <div class="s-mockup-v1" style="position:absolute;">
                        <div id="screen" data-wow-duration=".3" data-wow-delay=".1s">
                            <iframe width="480" style="position: absolute; top: 55px; left: 170px;" height="255" src="https://www.youtube.com/embed/rGlo2JITu2E?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Mockup -->
        <!-- Prizes -->
        <div id="prizes" class="row">
            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1">
                <div class="col-xs-12 col-sm-12 col-sm-5 col-sm-offset-1 col-lg-5 col-md-offset-1 col-lg-offset-1">
                    <div class="box box-widget1 widget-user-2 boxPrize">
                        <div class="widget-user-header">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <img src="/img/logo/BBVA.png" class="center-block imgResponsive" height="50px;"/>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer no-padding">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div style="font-size: 10px;text-align: center; font-weight: bold; margin-top: 10px;"><?php echo __('Award for Best Andalusian Fintech')?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-sm-5 col-lg-5">
                    <div class="box box-widget1 widget-user-2 boxPrize">
                        <div class="widget-user-header">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <img src="/img/logo/santander.jpg" class="center-block imgResponsive" height="50px;"/>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer no-padding">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div style="font-size: 10px;text-align: center; font-weight: bold; margin-top: 10px;"><?php echo __('Finalist Santander Open Challenge')?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Prizes -->
        <!-- Schema -->
        <div id="schema" class="row">
            <hr class="specialHr" width="90%">
            <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 hidden-xs hidden-sm" id="schemaImgDiv">
                    <img src="" class="imgResponsive schemaImg center-block"/>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" id="schemaText">
                    <h3 align="center"><?php echo __('What is P2P Lending?')?></h3>
                    <p><?php echo __('P2P Lending is a model of Alternative Financing, whereby private and professional investors, using Online Lending Platforms, lend their money in exchange for receiving an interest in accordance with the risk assumed.')?></p>
                    <p><?php echo __('The result is that the borrower gets financed at a more competitive price and the investor receives a higher return than other financial products.')?></p>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 50px;">
                    <p align="center" style="font-size: 26px;color:black;font-weight: 500"><?php echo __('The Benefits')?></p>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="margin-bottom: 15px;">
                        <strong><?php echo __('Risk-adjusted Returns')?></strong><br/>
                        <?php echo __('P2P investments offer average annual returns of between 5% and 12%. P2P can offer stable, predictable, risk-adjusted returns.')?>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"  style="margin-bottom: 25px;">
                        <strong><?php echo __('New Asset Class')?></strong><br/>
                        <?php echo __('P2P providers have innovated by offering an efficient, low-cost method of accessing credit investments.')?>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="margin-bottom: 25px;">
                        <strong><?php echo __('Alternative Financing Vs Stock Market')?></strong><br/>
                        <?php echo __('The performance of assets are not correlated to stock market volatility. P2P assets are expected to perform better during an economic downturn in comparison to equities or bonds.')?>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"  style="margin-bottom: 25px;">
                        <strong><?php echo __('Income or Growth')?></strong><br/>
                        <?php echo __('Investors often have the choice of taking interest payments as an income, or for these payments to be reinvested, enabling capital growth.')?>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="margin-bottom: 25px;">
                        <strong><?php echo __('Diversification')?></strong><br/>
                        <?php echo __('P2P Lending allows investors to diversify into an alternative asset class. Make wise fractional investments in several loans across different P2P Platforms.')?>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="margin-bottom: 25px;">
                        <strong><?php echo __('Management')?></strong><br/>
                        <?php echo __('Winvestify allows investors to connect their data to our dashboard for monitoring key metrics and taking of their investments.')?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /schema -->
        <!-- Info -->
        <div class="js__parallax-window" style="background: url(/megaKit/img/1920x1080/07.jpg) 50% 0 no-repeat fixed;">
            <div class="g-container--sm g-text-center--xs" style="padding: 80px 0px 0px 0px;">
                <div>
                    <p class="text-uppercase g-font-size-14--xs g-font-weight--700 g-color--white-opacity g-letter-spacing--2"><?php echo __('TECHNOLOGY SOLUTIONS FOR THE PROFESSIONAL INVESTOR')?></p>
                    <h2 class="g-font-size-32--xs g-font-size-36--md g-color--white"><?php echo __('All your portfolio and market data. Standardized.')?></h2>
                </div>
            </div>
            <div class="row" style="padding: 0px 0px 50px 0px;">
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding: 25px;">
                            <img src="/img/analytics_4.png" class="center-block imgResponsive" height="75px;"/>
                            <h4 class="g-color--white" align="center"><?php echo __('PORTFOLIO DATA')?></h4>
                            <p class="g-color--white" align="center"><?php echo __('Winvestify has direct, automated feeds from all major European online lenders, allowing us to display, in real-time, your loan portfolio.')?></p>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding: 25px;">
                            <img src="/img/man.png" class="center-block imgResponsive" height="75px;"/>
                            <h4 class="g-color--white" align="center"><?php echo __('NORMALIZATION')?></h4>
                            <p class="g-color--white" align="center"><?php echo __('Winvestify has established universal data formats across all credit verticals, allowing consistent views of loans purchased across all major lending platforms.')?></p>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding: 25px;">
                            <img src="/img/analytics_3.png" class="center-block imgResponsive" height="75px;"/>
                            <h4 class="g-color--white" align="center"><?php echo __('VALIDATION AND TRANSFORMATION')?></h4>
                            <p class="g-color--white" align="center"><?php echo __('We run platform-specific data cleaning rules to ensure that our clients always access the highest quality data possible.')?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2" style="padding: 50px 10px">
                <div class="g-text-center--xs">
                    <p class="text-uppercase g-font-size-32--xs g-font-weight--700 g-color--primary g-letter-spacing--2 g-margin-b-25--xs" style="padding: 0px 5px"><?php echo __("Join us. It's easy") ?></p>
                </div>
                <h4 align="center" style="padding: 0px 10px 20px 10px;"><?php echo __('Open an account and explore all the connected platforms.')?><br/><?php echo __('We make it easy for you to access the main Lending platforms with "One Click Registration"')?></h4>
                <a href="/users/registerPanel">
                    <button class="btn btn-lg btn1CR center-block" type="button"><?php echo __('Open account')?></button>
                </a>
            </div>
        </div>
        <!-- /Info -->
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
                var id = $(".flagvalue").attr("id");
                $(".schemaImg").attr("src", "/img/landingpage/schema_" + id + ".png");
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

                //disable clicking on #loginDropdown
                $("#loginDropdown").on("click", function(event) {
                    event.preventDefault;
                    event.stopPropagation();
                });
                
                //navbar collapse on clicking outside navbar
                $(document).on("click", function(){
                    $('.navbar-collapse').collapse('hide');
                    $('#loginDropdown').hide(); //hide loginDropdown
                });

                if ($(window).width() > 1023) {
                    //Dropdown menu click
                    $("ul.nav li.dropdown").on("click", function() {
                      $(this).find('.dropdown-menu').stop(true, true).fadeToggle(400);
                    });
                }
                
                //Initial schemaImg
                var id = $(".flagvalue").attr("id");
                $(".schemaImg").attr("src", "/img/landingpage/schema_" + id + ".png");
            });

        </script>
        <!--========== END JAVASCRIPTS ==========-->
    </body>
</html>
