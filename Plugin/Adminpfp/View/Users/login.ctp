<?php
/**
 *
 *
 * Simple login screen for PFP administrators
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2017-07-30
 * @package

 
 

 
 
*/
	
	
	
?>
<?php
	echo $this->element('navbaradminpfp');
?>
<div class="loginContainer">
    <div class="adminLogos" style="display:none">
        <img src="/img/logo_winvestify/logo.png" class="logo1"/>
        <img src="/img/logo_winvestify/logo_texto.png" class="logo2"/>
        <p class="paragraph"><?php echo __('párrafo')?></p>
    </div>
    <div class="loginBox">
        <div class="row">    
            <div class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                <form method="post" action="/adminpfp/users/loginAction" id="ctl00">
                   <p id="profile-name" class="profile-name-card"><?php echo __("Access to Crowdlending platform Administrators")?></p>
                   <span id="reauth-email" class="reauth-email"></span>
                   <input name="data[User][username]" type="text" id="inputEmail" class="form-control blue_noborder" placeholder="<?php echo __("usuario");?>" required="" autofocus="" />
                   <input name="data[User][password]" type="password" id="inputPassword" class="form-control blue_noborder" style="margin-top: 20px;" placeholder="Password" required="" />
                   <input type="submit" name="btLogin" value="Acceder" id="btLogin" class="btn btn-default center-block" style="margin-top: 20px;" />
                   <span id="lbAviso" class="displayBlock" style="color:Red;"></span>
                </form>
            </div>   
        </div>  
    </div>
</div>
<?php
	echo $this->element('publicfooteradminpfp');
