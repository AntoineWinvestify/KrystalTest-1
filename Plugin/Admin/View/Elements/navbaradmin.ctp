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
 *  PFPADMIN - Landing Page NAVBAR
 * 
 * [2017-07-19] version 0.1
 * New Layout
 * 
 */ 
?>
<header class="navbar-fixed-top s-header js__header-sticky js__header-overlay">
    <!-- Navbar -->
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header navbar-right">
                <ul class="nav pull-left navbar-nav collapse-tablet">
                    <?php /*<li id="liLogin" style="float:left; display:inline-block" class="dropdown pull-left"><a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Login <span class="caret"></span></a>
                        <div id="loginDropdown" class="dropdown-menu">
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
                                        <?php /*<button type="submit" id="loginBtn" style="margin-top:10px; margin-bottom: 10px;" class="text-uppercase btn"><?php echo __('Send') ?>
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
                    <li class="dropdown pull-left" style="margin-top:-3px; display:inline-block">
                        <?php echo $this->element('languageWidget') ?>
                        <div class="visible-xs-block clearfix"></div>
                    </li>
                </ul>*/?>
                <button type="button" style="margin-top:12px; margin-left: 20px; float:right;" class="navbar-toggle" data-toggle="collapse" data-target="#principal_navbar" aria-expanded="false">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div> <!-- /navbar-header-->
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="visible-xs-block clearfix"></div>
            <div class="collapse navbar-collapse" id="principal_navbar">
                <ul class="nav navbar-nav navbar-right">
                    <li style="float:left"><a href="http://www.facebook.com/winvestify/"><small><i class="g-padding-r-5--xs ti-facebook"></i></small></a></li>
                    <li style="float:left"><a href="https://www.linkedin.com/company-beta/11000640/"><small><i class="g-padding-r-5--xs ti-linkedin"></i></small></a></li>
                    <li style="float:left"><a href="https://twitter.com/Winvestify"><small><i class="g-padding-r-5--xs ti-twitter"></i></small></a></li>
                </ul>
                <ul class="nav navbar-nav navbar-left">
                    <li><a href="#mark_features"><?php echo __('Features') ?></a></li>
                    <li><a href="#mark_ftb"><?php echo __('Investor Toolbox') ?></a></li>
                    <li><a href="#mark_platforms"><?php echo __('Platforms') ?></a></li>
                    <li><a href="#mark_statistics"><?php echo __('Statistics') ?></a></li>
                    <li><a href="/Contactforms/form"><?php echo __('Contact') ?></a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div> <!--/container-fluid-->
    </nav>
    <!-- End Navbar -->
</header>