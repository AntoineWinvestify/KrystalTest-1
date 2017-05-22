<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
 	<title>Winvestify</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.6 -->
	<link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css">
	
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

    <!-- Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,400i|Montserrat:400,700" rel="stylesheet">

    <!-- Vendor Styles -->
    <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/megaKit/css/animate.css" rel="stylesheet" type="text/css"/>
    <link href="/megaKit/vendor/themify/themify.css" rel="stylesheet" type="text/css"/>
    <link href="/megaKit/vendor/scrollbar/scrollbar.min.css" rel="stylesheet" type="text/css"/>
    <link href="/megaKit/vendor/swiper/swiper.min.css" rel="stylesheet" type="text/css"/>

    <!-- Theme Styles -->
    <link href="/megaKit/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/megaKit/css/global.css" rel="stylesheet" type="text/css"/>
    <link href="/megaKit/css/custom.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<!--========== HEADER ==========-->
        <header class="navbar-fixed-top s-header js__header-sticky js__header-overlay">
            <!-- Navbar -->
            <div class="s-header__navbar">
                <div class="s-header__container">
                    <div class="s-header__navbar-row">
                        <div class="s-header__navbar-row-col">
                            <!-- Logo -->
                            <div class="s-header__logo">
                                <a href="index.html" class="s-header__logo-link">
                                    <img class="s-header__logo-img s-header__logo-img-default" src="/img/logo_winvestify/Logo.png" height="50px" alt="Winvestify Logo">
                                    <img class="s-header__logo-img s-header__logo-img-shrink" src="/img/logo_winvestify/Logo.png" height="50px" alt="Winvestify Logo">
                                </a>
                            </div>
                            <!-- End Logo -->
                        </div>
                        <div class="s-header__navbar-row-col">
				            <ul class="nav navbar-nav" style="float:right;">
					           	<li style="float: left; margin-left: 10px;"><a href="#mark_features">Features</a></li>
					           	<li style="float: left; margin-left: 10px;"><a href="#mark_parallax">Parallax</a></li>
					           	<li style="float: left; margin-left: 10px;"><a href="#mark_logos">Logos</a></li>
					           	<li style="float: left; margin-left: 10px;"><a href="#mark_counter">Counter</a></li>
					           	<li style="float: left; margin-left: 10px;"><a href="#mark_contact">Contact</a></li>
					            <li class="dropdown" style="float: left; margin-left: 10px;">
                                  <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="/img/es.png"/> <span class="caret"></span></a>
                                  <ul style="min-width: 50px !important;" class="dropdown-menu">
                                    <li><a href="#"><img src="/img/gb.png"/></a></li>
                                    <li><a href="#"><img src="/img/fr.png"/></a></li>
                                    <li><a href="#"><img src="/img/de.png"/></a></li>
                                    <li><a href="#"><img src="/img/jp.png"/></a></li>
                                    <li><a href="#"><img src="/img/kr.png"/></a></li>
                                  </ul>
                                </li>
							</ul>
						</div>
                    </div>
                </div>
            </div>
            <!-- End Navbar -->
        </header>
        <!--========== END HEADER ==========-->

        <!--========== PAGE CONTENT ==========-->
        <!-- Parallax -->
        <div class="js__parallax-window" style="background: url(/megaKit/img/1920x1080/03.jpg) 50% 0 no-repeat fixed;">
            <div class="container g-text-center--xs g-padding-y-80--xs g-padding-y-125--sm">
                <div class="g-margin-b-80--xs" style="padding-top: 100px;">
                    <img src="/img/logo_winvestify/Logo_texto.png" style="float:center; max-width:600px;"/>
                    <h2 class="g-font-size-40--xs g-font-size-45--sm g-font-size-40--md g-color--white"><?php echo __('Contacto') ?></h2>
                </div>
            </div>
        </div>
        <!-- Feedback Form -->
        <div class="gradient">
            <div class="container g-padding-y-80--xs g-padding-y-125--sm">
                <div class="g-text-center--xs g-margin-b-80--xs">
                    <p class="text-uppercase g-font-size-32--xs g-font-weight--700 g-color--primary g-letter-spacing--2 g-margin-b-25--xs">Contacto</p>
                </div>
                <form>
                    <div class="row g-margin-b-40--xs">
                        <div class="col-sm-6 g-margin-b-20--xs g-margin-b-0--md">
                            <br/>
                            <div class="g-margin-b-20--xs">
                                <input type="text" class="form-control s-form-v2__input g-radius--50" placeholder="* Name">
                            </div>
                            <br/>
                            <div class="g-margin-b-20--xs">
                                <input type="email" class="form-control s-form-v2__input g-radius--50" placeholder="* Email">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <textarea name="feedback_msg" class="form-control s-form-v2__input g-radius--10 g-padding-y-20--xs" rows="8" placeholder="* Your message"></textarea>
                        </div>
                    </div>
                    <div class="g-text-center--xs">
                        <button type="submit" class="text-uppercase s-btn s-btn--md s-btn--primary-bg g-radius--50 g-padding-x-80--xs">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Feedback Form -->
        </div>
        <!-- End Terms & Conditions -->
        <!--========== END PAGE CONTENT ==========-->

        <!--========== FOOTER ==========-->
        <footer class="g-bg-color--dark">
            <!-- Links -->
            <div class="g-hor-divider__dashed--white-opacity-lightest">
                <div class="container g-padding-y-80--xs">
                    <div class="row">
                        <div class="col-sm-2 g-margin-b-20--xs g-margin-b-0--md">
                            <ul class="list-unstyled g-ul-li-tb-5--xs g-margin-b-0--xs">
                                <li><a class="g-font-size-15--xs g-color--white-opacity" href="http://www.winvestify.com">Home</a></li>
                                <li><a class="g-font-size-15--xs g-color--white-opacity" href="http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes">About</a></li>
                                <li><a class="g-font-size-15--xs g-color--white-opacity" href="http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes">Services</a></li>
                                <li><a class="g-font-size-15--xs g-color--white-opacity" href="http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes">Contact</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-2 g-margin-b-20--xs g-margin-b-0--md">
                            <ul class="list-unstyled g-ul-li-tb-5--xs g-margin-b-0--xs">
                                <li><a class="g-font-size-15--xs g-color--white-opacity" href="https://www.facebook.com/winvestify/">Facebook</a></li>
                                <li><a class="g-font-size-15--xs g-color--white-opacity" href="https://twitter.com/Winvestify">Twitter</a></li>
                                <li><a class="g-font-size-15--xs g-color--white-opacity" href="https://www.linkedin.com/company-beta/11000640/">LinkedIn</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-2 g-margin-b-40--xs g-margin-b-0--md">
                            <ul class="list-unstyled g-ul-li-tb-5--xs g-margin-b-0--xs">
                                <li><a class="g-font-size-15--xs g-color--white-opacity" href="http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes">Newsletter</a></li>
                                <li><a class="g-font-size-15--xs g-color--white-opacity" href="http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes">Privacy Policy</a></li>
                                <li><a class="g-font-size-15--xs g-color--white-opacity" href="http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes">Terms &amp; Conditions</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4 col-md-offset-2 col-sm-5 col-sm-offset-1 s-footer__logo g-padding-y-50--xs g-padding-y-0--md">
                            <h3 class="g-font-size-18--xs g-color--white">
                                <img src="/img/logo_winvestify/Logo_texto.png" style="float:center; max-width:200px;"/>
                            </h3>
                            <p class="g-color--white-opacity">We are a creative studio focusing on culture, luxury, editorial &amp; art. Somewhere between sophistication and simplicity.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Links -->

            <!-- Sponsors -->
            <div class="container g-padding-y-50--xs">
                <div class="row">
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"></div>
                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                        <img style="margin-left: 30px;" src="/img/logo/AOF_100_bn.png"/>
                        <img src="/img/logo/t_100_b.png" style="margin-top: 20px"/>
                        <img src="/img/logo/JDA_100_b.png" style="margin-left: 30px; margin-top: 20px;"/>
                        <img src="/img/logo/ayto_100_bn.png" style="margin-left: 40px; margin-top: 20px;"/>
                    </div>
                </div>
            </div>
            <!-- End Sponsors -->
        </footer>
        <!--========== END FOOTER ==========-->
        <!-- Back To Top -->
        <a href="javascript:void(0);" class="s-back-to-top js__back-to-top"></a>

        <!--========== JAVASCRIPTS (Load javascripts at bottom, this will reduce page load time) ==========-->
        <!-- Vendor -->
        <script type="text/javascript" src="/megaKit/vendor/jquery.min.js"></script>
        <script type="text/javascript" src="/megaKit/vendor/jquery.migrate.min.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/megaKit/vendor/jquery.smooth-scroll.min.js"></script>
        <script type="text/javascript" src="/megaKit/vendor/jquery.back-to-top.min.js"></script>
        <script type="text/javascript" src="/megaKit/vendor/scrollbar/jquery.scrollbar.min.js"></script>
        <script type="text/javascript" src="/megaKit/vendor/swiper/swiper.jquery.min.js"></script>
        <script type="text/javascript" src="/megaKit/vendor/jquery.wow.min.js"></script>

        <!-- General Components and Settings -->
        <script type="text/javascript" src="/megaKit/js/global.min.js"></script>
        <script type="text/javascript" src="/megaKit/js/header-sticky.min.js"></script>
        <script type="text/javascript" src="/megaKit/js/scrollbar.min.js"></script>
        <script type="text/javascript" src="/megaKit/js/swiper.min.js"></script>
        <script type="text/javascript" src="/megaKit/js/wow.min.js"></script>
        <!--========== END JAVASCRIPTS ==========-->
</body>