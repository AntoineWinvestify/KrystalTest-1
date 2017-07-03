<?php
/**
 *
 *
 * Simple login screen
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-01-30
 * @package

 
 
2016-01-30		version 0.1
multi-language support added
 
 this is not actually being used. The login as defined in the main landing page is used
 
 
*/
	
?>


<?php
	if ($error) {
		echo "0";
	}
	else {
//		echo "1";		// TEMPORARILY DISABLED FOR BETATESTING LANDING PLACE
	}
?>


<script type="text/javascript">

$(function() {




$("#btnLoginUser").bind("click", function(event) {
  	if (!(app.visual.checkFormLogin()) === true) {
		event.stopPropagation();
		event.preventDefault();
		return false;
	}	
});

});
</script>



<?php
	if (!empty(	$credentialError = $this->Session->flash('auth'))) {
		echo $this->Form->input('', array(	'name'	=> 'credentialError',
											'value'	=> 'Credentialerror',
											'id'	=> 'credentialError',
											'type'	=> 'hidden'
										));
	}
?>





<?php
	echo $this->Form->create('User', array('url' => "loginAction",));	?>
				<form class="form col-md-12 center-block">
<?php
	if (!empty(	$credentialError = $this->Session->flash('auth'))) {
		echo $this->Form->input('', array(	'name'	=> 'credentialError',
											'value'	=> 'Credentialerror',
											'id'	=> 'credentialError',
											'type'	=> 'hidden'
										));
	}
?>
<!--
	<div class="col-lg-4 col-sm-4 g-hor-centered-row__col">
		<div class="wow fadeInUp" data-wow-duration=".3" data-wow-delay=".1s">
			<form class="center-block g-width-350--xs g-bg-color--white-opacity-lightest g-box-shadow__bluegreen-v1 g-padding-x-40--xs g-padding-y-60--xs g-radius--4">
				<div class="g-text-center--xs g-margin-b-40--xs">
					<h2 class="g-font-size-30--xs g-color--white">
						<?php echo __('Login | Register') ?>
					</h2>
				</div>
				<div class="g-margin-b-30--xs">
					<input type="email" class="form-control s-form-v3__input" placeholder="<?php echo __('iuiEmail')?>" name="data[User][username]">
				</div>
				<div class="g-margin-b-30--xs">
					<input type="password" class="form-control s-form-v3__input" placeholder="<?php echo __('Password')?>" name="data[User][password]">
				</div>
				<div class="g-text-center--xs">
					<button type="submit" class="text-uppercase btn-block s-btn s-btn--md s-btn--white-bg g-radius--50 g-padding-x-50--xs g-margin-b-20--xs">
						<?php echo __('LOGIN') ?>

<?php
	echo $this->Form->button(__('Login'), $options = array('name' 	=> 'btnLoginUser',
														   'value' 	=> 'Login',
														   'id' 	=> 'btnLoginUser',
														   'class' 	=> 'btn btn-blue btn-primary btn-rounded-corner center-block btnLoginUser btn-green'));
	echo $this->Form->end();
?>
					</button>
					<a class="g-color--white g-font-size-13--xs" href="#"><?php echo __('Forgot your password?') ?></a>
					<a class="g-color--white g-font-size-13--xs" href="/users/registerPanel">&nbsp;&nbsp;&nbsp;<?php echo __('Register') ?></a>
				</div>
			</form>
		</div>
	</div>

-->






