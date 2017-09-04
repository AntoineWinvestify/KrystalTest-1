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
    <div class="adminLogos" style="display:none;">
        <img src="/img/logo_winvestify/logo.png" class="logo1"/>
        <img src="/img/logo_winvestify/logo_texto.png" class="logo2"/>
        <p class="paragraph"><?php echo __('párrafo')?></p>
    </div>
    <div class="loginBox">
        <div class="row">    
            <div class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                <form method="post" action="/users/loginAction" id="ctl00">
                   <p id="profile-name" class="profile-name-card"><?php echo __("ACCESS to Winvestify")?></p>
                   <span id="reauth-email" class="reauth-email"></span>
                   <input name="data[User][username]" type="text" id="inputEmail" class="form-control blue_noborder redBorder" placeholder="<?php echo __("usuario");?>" required="" autofocus="" />	
                   <input name="data[User][password]" type="password" id="inputPassword" class="form-control blue_noborder redBorder" style="margin-top: 20px;" placeholder="Password" required="" />		
                   <p class="errorInputMessage" style="display: block"><i class="fa fa-exclamation-circle"></i>&nbsp;<?php echo __('Username or password is incorrect')?></p>
                   <input type="submit" name="btLogin" value="Acceder" id="btLogin" class="btn btn-default center-block" style="margin-top: 20px;" />
                   <span id="lbAviso" class="displayBlock" style="color:Red;"></span>
                </form>
            </div>   
        </div>  
    </div>
</div>
<?php
	echo $this->element('publicfooterinvestor');