<?php
/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               |
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
 * @author 
 * @version 0.1
 * @date 2017-09-04
 * @package
 * 
 * User Login redirect panel
 * 
 * [2017-09-04] version 0.1
 * Initial view
 * Added all form
 * Added navbar header
 * Added footer
 * Added error & inputs with redBorder
 * 
 */
?>
<?php
	echo $this->element('navbarinvestor');
?>
<div class="loginContainer">
    <div class="loginBox">
        <div class="row">    
            <div class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                <form method="post" action="/users/loginAction" id="ctl00">
                   <p id="profile-name" class="profile-name-card">
                       <img src ="/img/logo_winvestify/Logo.png" width="50"/><img src ="/img/logo_winvestify/Logo_texto.png" class="logoRedirect" height="50"/>
                       <?php echo __("ACCESS to Winvestify")?>
                   </p>
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
                        $errorClassesForTexts = "errorInputMessage ErrorUsernameLogin";
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
                    $class = "form-control blue_noborder center-block" . ' ' . $errorClass;
                    ?>
                   <input name="data[User][username]" type="text" id="inputEmail" class="<?php echo $class ?>" placeholder="<?php echo __("usuario");?>" required="" autofocus="" />	
                   <?php
                    $errorClassesText = "errorInputMessage ErrorInactiveaccount";
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
                    $class = "form-control blue_noborder center-block" . ' ' . $errorClass;
                    ?>
                   <input name="data[User][password]" type="password" id="inputPassword" class="<?php echo $class ?>" style="margin-top: 20px;" placeholder="Password" required="" />		              
                   <div class="errorInputMessage ErrorPassword">
                        <i class="fa fa-exclamation-circle"></i>
                        <span class="errorMessage">
                            <?php echo $error ?>
                        </span>
                    </div>									

                    <?php
                    if (!empty($authMsg)) {            // Authentication Error as detected by server
                        $errorClassesForTexts = "errorInputMessage ErrorPasswordLogin";
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
                   <input type="submit" name="btnLogin" value="Acceder" id="btnLogin" class="btn btn-default center-block" style="margin-top: 20px;" />
                   <span id="lbAviso" class="displayBlock" style="color:Red;"></span>
                </form>
            </div>   
        </div>  
    </div>
</div>
<?php
	echo $this->element('publicfooterinvestor');