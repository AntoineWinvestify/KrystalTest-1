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

 
 

 
 
*/
	
	
	
?>

<div class="loginContainer">
    <div class="loginBox">
        <div class="row">    
    <div class="col-sm-offset-1 col-sm-10 col-xs-12">
        <form method="post" action="/adminpfp/users/loginAction" id="ctl00">
        <div class="container">
            <div class="card card-container">
                <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                    <p id="profile-name" class="profile-name-card"><?php echo __("ACCESS a PFP Admin")?></p>
     <!--       <form class="form-signin"> 
     -->
                <span id="reauth-email" class="reauth-email"></span>
                <input name="data[User][username]" type="text" id="inputEmail" class="form-control" placeholder="<?php echo __("usuario");?>" required="" autofocus="" />
                <input name="data[User][password]" type="password" id="inputPassword" class="form-control" placeholder="Password" required="" />
                <input type="submit" name="btLogin" value="Acceder" id="btLogin" class="btn btn-lg btn-green" />
                <span id="lbAviso" class="displayBlock" style="color:Red;"></span>
            </div>
        </div><!-- /card-container -->
       </div><!-- /container -->
        </form>
        </div>   
    </div>  
    </div
</div>