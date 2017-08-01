<?php
/**
* +--------------------------------------------------------------------------------------------+
* | Copyright (C) 2016, http://www.winvestify.com                                              |
* +--------------------------------------------------------------------------------------------+
* | This file is free software; you can redistribute it and/or modify                          |
* | it under the terms of the GNU General Public License as published by                       |
* | the Free Software Foundation; either version 2 of the License, or                          |
* | (at your option) any later version.                                                        |
* | This file is distributed in the hope that it will be useful                                |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of                             |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                               |
* | GNU General Public License for more details.                                               |
* +--------------------------------------------------------------------------------------------+
*
*
* @author
* @version 0.1
* @date 2017-06-09
* @package
 * 
 * 
 * Login admin/pfpadmin VIEW
 * 
 * [2017-06-20] Version 0.1
*/

?>
<div class="loginContainer">
    <div class="paragraph">
        <img src="/img/logo_winvestify/Logo.png" style="max-width:140px;"/>
        <img src="/img/logo_winvestify/Logo_texto.png" style="max-width:450px;"/>
    </div>
    <div class="loginBox">
        <div class="row">
            <div class="col-sm-offset-1 col-sm-10 col-xs-12">
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
                $class = "form-control blue_noborder center-block userNameLogin" . ' ' . $errorClass;
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
                $class = "form-control blue_noborder center-block passwordLogin" . ' ' . $errorClass;
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
                    <button type="submit" id="loginBtn" style="margin-top:10px; margin-bottom: 10px;" class="text-uppercase btn btn-default btn-win1 btnRounded"><?php echo __('Send') ?>
                    </button><br/>
                </div>
            </div>
    </div>
</div>