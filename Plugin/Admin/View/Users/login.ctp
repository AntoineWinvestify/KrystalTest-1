<?php
/**
 *
 *
 * Simple login screen for winvestify administrators
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2017-07-30
 * @package
 * 
 * Winadmin Login Panel
 * 
 * [2017-08-16] version 0.2
 * Added header navbar & footer.
 * 
*/
	

?>
<?php
    echo $this->element('navbaradmin');
?>
<div class="loginContainer">
    <div class="loginBox">
        <div class="row">    
            <div class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                <form method="post" action="/admin/users/loginAction" id="ctl00">
                   <p id="profile-name" class="profile-name-card"><?php echo __("Access to Winvestify Administration")?></p>
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
	echo $this->element('publicfooteradmin');
