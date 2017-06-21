<?php
/**
 *
 *
 * Simple login screen for administrators
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-02-13
 * @package

 
 
// imported variables are
tried to use the bootstrap them, but it does not work. STILL TO DO
 
*/
	
?>
<form method="post" action="login" id="ctl00">

    <div class="container">
        <div class="card card-container">

            <img id="profile-img" class="profile-img-card" src="../img/logo-text-right.png" />
            <p id="profile-name" class="profile-name-card"><?php echo __("This is the profile")?></p>
     <!--       <form class="form-signin">         -->
                <span id="reauth-email" class="reauth-email"></span>
                <input name="inputEmail" type="text" id="inputEmail" class="form-control" placeholder="<?php echo __("usuario");?>" required="" autofocus="" />
                <input name="inputPassword" type="password" id="inputPassword" class="form-control" placeholder="Password" required="" />
                <input type="submit" name="btLogin" value="Acceder" id="btLogin" class="btn btn-lg btn-green" />
                <span id="lbAviso" class="displayBlock" style="color:Red;"></span>
            </form><!-- /form -->

        </div><!-- /card-container -->
    </div><!-- /container -->
</form>