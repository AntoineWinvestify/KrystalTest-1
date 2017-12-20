<!DOCTYPE html>
<html>
    <head>  
        <title>Winvestify</title>
        <!-- Tell the browser to be responsive to screen width -->
        <?php 
            echo $this->element('meta');
            
            echo $this->element('csslandingpage');
        ?>
       

        <?php
        $file = APP . "Config" . DS . "googleCode.php";
        if (file_exists($file)) {
            include_once($file);
        }
        ?>
        <script type="text/javascript" src="/modals/assets/js/jquery-2.2.4.min.js"></script>
        <?php
            echo $this->element('favicon');
        ?>
    </head>
    <body>
        <?php 
        
        echo $this->Html->script(array('local'));
        /*========== HEADER ==========-->*/
         
        echo $this->element('headerPublicPage');

        /*<!--========== END HEADER ==========-->*/
        ?>
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
                    <div class="s-mockup-v1">
                        <div id="screen" data-wow-duration=".3" data-wow-delay=".1s">
                            <iframe width="480" style="position: absolute; top: 52px; left: 170px;" height="255" src="https://www.youtube.com/embed/rGlo2JITu2E?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
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
            <div class="row portfolioData" style="padding: 0px 0px 50px 0px;">
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1">
                    <div class="row portfolioData">
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
        
        <?php 
            echo $this->fetch('content');
            echo $this->element('publicfooter');
            echo $this->element('jsPublicLandingPage');
            echo $this->element('jsPublicFunctions');
        ?>
        <!--========== END JAVASCRIPTS ==========-->
    </body>
</html>
