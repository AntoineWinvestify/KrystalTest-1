<?php

/**
 *
 *
 * AJAX format for adding a payment reminder
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-01-28
 * @package



2016-01-28		version 0.1
multi-language support added




Pending:
Javascript,
Error messages and Error classes for each field must be properly named
Add all the funcionality in case the server generates an error while trying to save the data

button in tab linked account should be aligned at same level as "input fields"

*/


?>

<?php
if (!$initialLoad) {		// page is NOT loaded for the first time, but as a result of a change of data
	if ($error) {
//		echo "0";
	}
	else {
		echo "1";
	}
}
?>
<script src="/plugins/intlTelInput/js/intlTelInput.js"></script>
<script src="/plugins/intlTelInput/js/utils.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/intlTelInput/css/intlTelInput.css">
<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/datepicker/datepicker3.css">
<script type="text/javascript">


function successAdd(data){
	console.log("successAdd function is called");
	$(".linkedAccountsList").replaceWith(data);	
	console.log("New data was successfully saved");	
}


function errorAdd(data){
	console.log("errorAdd function is called");	
	
}
	

function successModified(data) {
//	ga_removeInformationBannerClick("Personal Data Saved");
	console.log("edit_user_profile_data: LINE 70");
	$('.editDatosPersonales').replaceWith(data);	
}
	
function errorModified(data) {
	console.log("edit_user_profile_data: LINE 80");
	$('.editDatosPersonales').replaceWith(data);

}
	
	
	

$(document).ready(function() {
$(".close").on("click", function(event) {
    notificationType = $(this).attr('data-dismiss');

	var result = 'alert'.localeCompare(notificationType);
	if (result === 0) {
		ga_removeInformationBannerClick("Personal Data saved");
	}	
});	




//tooltip
$(document).on("click", "#tooltip", function() {
	$("#passwordTooltip").toggle();
});

//telephone
$('#ContentPlaceHolder_telephone').intlTelInput();

//Date picker
$('#ContentPlaceHolder_dateOfBirth').datepicker({
    autoclose: true,
    format: 'dd/mm/yyyy'
});

$(document).bind('DOMSubtreeModified',function(){
    fadeOutElement(".alert-to-fade", 5000);
});


$(function() {
	

$("#editUserData").bind("click", function(event) {
console.log("saving personal data");

	var result,
		link = $(this).attr( "href" );
	 
	event.stopPropagation();
	event.preventDefault();

	if ((result = app.visual.checkFormUserDataModification()) === false) {
		event.stopPropagation();
		event.preventDefault();
		return false;
	}
	else {	
console.log("second edit of personal data has been checked locally using Javascript");	
		var params = {	
			password: $("#ContentPlaceHolder_password_confirm").val(),
			investor_name: $("#ContentPlaceHolder_name").val(),
			investor_surname: $("#ContentPlaceHolder_surname").val(),
			investor_address1: $("#ContentPlaceHolder_address1").val(),
			investor_postCode: $("#ContentPlaceHolder_postCode").val(),
			investor_city: $("#ContentPlaceHolder_city").val(),
			investor_country: $("#ContentPlaceHolder_country").val(),
			investor_DNI: $("#ContentPlaceHolder_dni").val(),
			investor_telephone: $("#ContentPlaceHolder_telephone").intlTelInput("getNumber"),
			investor_dateOfBirth: $("#ContentPlaceHolder_dateOfBirth").val()
        };
//		ga_savePersonalData();
		var data = jQuery.param( params );
		getServerData(link, data, successModified, errorModified);
	}
});	
	


});
});
</script>




<?php


if (empty($userValidationErrors) AND empty($investorValidationErrors)) {
	if (!$initialLoad) {		// page is NOT loaded for the first time, but as a result of a change of data
?>	
		<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button>
			<strong><?php echo __("You're data has been successfully modified")?></strong>	
		</div>
<?php
		}
	}
?>

<div class="editDatosPersonales">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> <!-- Password -->
                <div class="form-group">
                    <label for="ContentPlaceHolder_password1" data-placement="auto"><?php echo __('Password')?></label>
                    <i class="fa fa-exclamation-circle" id="tooltip"></i>
<?php
	$errorClass = "";
	if (array_key_exists('password',$userValidationErrors)) {
		$errorClass = "redBorder";
	} 
	$class = "form-control blue password1". ' ' . $errorClass;
	echo $this->Form->input('User.password1', array('name'	=> 'password',
											'id' 			=> 'ContentPlaceHolder_password1',
											'label' 		=> false,
											'placeholder' 	=>  __('New Password'),
											'type'			=> 'password',
											'class' 		=> $class,					
							));
?>
		</div>					
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> <!-- Password -->
                <div class="form-group">
                    <label for="ContentPlaceHolder_password_confirm"><?php echo __('Repeat Password')?></label>
<?php

	$errorClass = "";
	if (array_key_exists('password',$userValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control blue passwordConfirm". ' ' . $errorClass;
	echo $this->Form->input('User.passwordConfirm', array('name'	=> 'surnames',
											'id' 			=> 'ContentPlaceHolder_password_confirm',
											'label' 		=> false,
											'placeholder' 	=>  __('Repeat new Password'),
											'type'			=> 'password',
											'class' 		=> $class,						
							));
	?>										
		</div>					
            </div>
	</div>
	<div class="row" id="passwordTooltip" style="display:none">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <small><?php echo __('Your password should be at least 8 characters long and contain uppercase and lowercase characters, a number and another symbol');?></small>
            </div>
	</div>
<?php
	$errorClassesText = "errorInputMessage ErrorPassword";
	if (array_key_exists('password',$userValidationErrors)) {
		$errorClassesText .= " ". "actived";
	}
?>
        <div class="<?php echo $errorClassesText?>">
            <i class="fa fa-exclamation-circle"></i>
            <span class="errorMessage">
                <?php echo $userValidationErrors['password'][0] ?>
            </span>
	</div>		
    </div>
    <!-- ./box-body -->
									
    <div class="box-body">
	<div class="row">
            <div class="col-xs-12 col-sm-6 col-md-3"> <!-- Name -->
                <div class="form-group">
                    <label for="ContentPlaceHolder_name"><?php echo __('Name')?></label>
<?php

	$errorClass = "";
	if (array_key_exists('investor_name',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control blue investorName". ' ' . $errorClass;
										echo $this->Form->input('Investor.investor_name', array(
											'name'			=> 'name',
											'id' 			=> 'ContentPlaceHolder_name',
											'label' 		=> false,
											'placeholder' 	=>  __('Name'),
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_name'],						
							));
?>									
		</div>					
            </div>
            <!-- /name -->
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6"> <!-- surname -->
                <div class="form-group">
                    <label for="ContentPlaceHolder_surname"><?php echo __('Surname(s)')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_surname',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control blue investorSurname". ' ' . $errorClass;
										echo $this->Form->input('Investor.investor_surname', array(
											'name'			=> 'surname',
											'id' 			=> 'ContentPlaceHolder_surname',
											'label' 		=> false,
											'placeholder' 	=>  __('Surname'),
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_surname'],						
							));
?>
		</div>					
            </div>
	</div>


<?php
	$errorClassesText = "errorInputMessage ErrorName";
	if (array_key_exists('investor_name',$investorValidationErrors)) {
		$errorClassesText .= " ". "actived";
	}
?>
        <div class="<?php echo $errorClassesText?>">
            <i class="fa fa-exclamation-circle"></i>
            <span class="errorMessage">
                <?php echo $investorValidationErrors['investor_name'][0] ?>
            </span>
	</div>

<?php
	$errorClassesText = "errorInputMessage ErrorSurname";
	if (array_key_exists('investor_surname',$investorValidationErrors)) {
		$errorClassesText .= " ". "actived";
	}
?>
        <div class="<?php echo $errorClassesText?>">
            <i class="fa fa-exclamation-circle"></i>
            <span class="errorMessage">
                <?php echo $investorValidationErrors['investor_surname'][0] ?>
            </span>
	</div>						
    </div>
						<!-- /.box-body -->
	
								
    <div class="box-body">
	<div class="row"><!-- row 3 -->
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> <!-- address -->
		<div class="form-group">
                    <label for="ContentPlaceHolder_address1"><?php echo __('Address')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_address1',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control blue investorSurname". ' ' . $errorClass;
										echo $this->Form->input('Investor.investor_address1', array(
											'name'			=> 'address1',
											'id' 			=> 'ContentPlaceHolder_address1',
											'label' 		=> false,
											'placeholder' 	=>  __('Address'),
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_address1'],						
							));
?>

		</div>
            </div>	<!-- /address -->				 
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> <!-- post code -->
		<div class="form-group">
                    <label for="ContentPlaceHolder_postCode"><?php echo __('PostCode')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_postCode',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control blue investorPostCode". ' ' . $errorClass;
										echo $this->Form->input('Investor.investor_postCode', array(
											'name'			=> 'investor_postCode',
											'id' 			=> 'ContentPlaceHolder_postCode',
											'label' 		=> false,
											'placeholder' 	=>  __('PostCode'),
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_postCode'],						
							));
?>
		</div>					
            </div><!-- /post code -->
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> <!-- city -->
		<div class="form-group">
                    <label for="exampleInputPassword1"><?php echo __('City')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_city',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control blue investorCity". ' ' . $errorClass;
										echo $this->Form->input('ContentPlaceHolder_city', array(
											'name'			=> 'city',
											'id' 			=> 'ContentPlaceHolder_city',
											'label' 		=> false,
											'placeholder' 	=>  __('City'),
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_city'],						
							));
	?>
		</div>					
            </div>
	</div>	
<?php
	$errorClassesText = "errorInputMessage ErrorAddress";
	if (array_key_exists('investor_address1',$investorValidationErrors)) {
		$errorClassesText .= " ". "actived";
	}
?>
        <div class="<?php echo $errorClassesText?>">
            <i class="fa fa-exclamation-circle"></i>
            <span class="errorMessage">
		<?php echo $investorValidationErrors['investor_address1'][0] ?>
            </span>
	</div>

<?php
	$errorClassesText = "errorInputMessage ErrorPostCode";
	if (array_key_exists('investor_postCode',$investorValidationErrors)) {
		$errorClassesText .= " ". "actived";
	}
?>
        <div class="<?php echo $errorClassesText?>">
            <i class="fa fa-exclamation-circle"></i>
            <span class="errorMessage">
		<?php echo $investorValidationErrors['investor_postCode'][0] ?>
            </span>
	</div>		
<?php
	$errorClassesText = "errorInputMessage ErrorCity";
	if (array_key_exists('investor_city',$investorValidationErrors)) {
		$errorClassesText .= " ". "actived";
	}
?>
        <div class="<?php echo $errorClassesText?>">
            <i class="fa fa-exclamation-circle"></i>
            <span class="errorMessage">
		<?php echo $investorValidationErrors['investor_city'][0] ?>
            </span>
	</div>						
    </div>
    <!-- /.box-body -->


    <div class="box-body">
	<div class="row">
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> <!-- country -->
		<div class="form-group">
                    <label for="ContentPlaceHolder_country"><?php echo __('Country')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_country',$investorValidationErrors)) {
		$errorClass = "redBorder";
}
	$class = "form-control blue investorCountry". ' ' . $errorClass;	
										echo $this->Form->input('Investor.investor_country', array(
											'name'			=> 'country',
											'id' 			=> 'ContentPlaceHolder_country',
											'label' 		=> false,
                                            'options'       => $countryData,
											'placeholder' 	=>  __('Country'),
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_country'],						
							));
	?>
		</div>					
            </div>
	</div>							
<?php
	$errorClassesText = "errorInputMessage ErrorCountry";
	if (array_key_exists('investor_country',$investorValidationErrors)) {
		$errorClassesText .= " ". "actived";
	}
?>
        <div class="<?php echo $errorClassesText?>">
            <i class="fa fa-exclamation-circle"></i>
            <span class="errorMessage">
                <?php echo $investorValidationErrors['investor_country'][0] ?>
            </span>
	</div>
    </div>
    <!-- ./box-body -->
				
    <div class="box-body">
	<div class="row">
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> <!-- id -->
		<div class="form-group">
                    <label for="ContentPlaceHolder_dni"><?php echo __('Id')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_DNI',$investorValidationErrors)) {
		$errorClass = "redBorder";
}
	$class = "form-control blue investorDni". ' ' . $errorClass;
										echo $this->Form->input('Investor.investor_DNI', array(
											'name'			=> 'dni',
											'id' 			=> 'ContentPlaceHolder_dni',
											'label' 		=> false,
											'placeholder' 	=>  __('Id'),
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_DNI'],						
							));
?>
		</div>					
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> <!-- telephone -->
                <div class="form-group">
                    <label for="ContentPlaceHolder_telephone"><?php echo __('Telephone')?></label>
                    <div class="form-control blue">
											<?php
												$errorClass = "";
												if (array_key_exists('investor_telephone', $investorValidationErrors)) {
													$errorClass = "redBorder";
												}
												$class = "telephoneNumber center-block". ' ' . $errorClass;
							
												echo $this->Form->input('Investor.investor_telephone', array(
																		'name'			=> 'telephone',
																		'id' 			=> 'ContentPlaceHolder_telephone',
																		'label' 		=> false,
																		'placeholder' 	=>  __('Telephone'),
																		'class' 		=> $class,
																		'type'			=> 'tel',
																		'value'			=> $resultUserData[0]['Investor']['investor_telephone']
																		));
												$errorClassesForTexts = "errorInputMessage ErrorPhoneNumber col-xs-offset-1";
												if (array_key_exists('investor_telephone',$validationResult)) {
													$errorClassesForTexts .= " ". "actived";
												}
											?>
                    </div>
                </div>					
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3"> <!-- date picker -->
		<div class="form-group">
                    <label for="ContentPlaceHolder_dateOfBirth"><?php echo __('Date of Birth')?></label>
			<div class="input-group input-group-sm blue date">
											<?php
												$errorClass = "";
												if (array_key_exists('investor_dateOfBirth',$investorValidationErrors)) {
													$errorClass = "redBorder";
												}
												$class = "form-control pull-right investorDateOfBirth". ' ' . $errorClass;
											?>
                            <div class="input-group-addon" style="border-radius:8px; border: none;">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" style="border-radius:8px; border:none;" class="<?php echo $class ?>" name="dateOfBirth" placeholder="<?php echo __('Date of Birth')?>" id="ContentPlaceHolder_dateOfBirth" value="<?php echo $resultUserData[0]['Investor']['investor_dateOfBirth'] ?>">
			</div>
		</div>
            </div>
	</div>
<?php
	$errorClassesText = "errorInputMessage ErrorId";
	if (array_key_exists('investor_DNI',$investorValidationErrors)) {
		$errorClassesText .= " ". "actived";
	}
?>
        <div class="<?php echo $errorClassesText?>">
            <i class="fa fa-exclamation-circle"></i>
            <span class="errorMessage">
                    <?php echo $investorValidationErrors['investor_DNI'][0] ?>
            </span>
	</div>

<?php
	$errorClassesText = "errorInputMessage ErrorTelephone";
	if (array_key_exists('investor_telephone',$investorValidationErrors)) {
		$errorClassesText .= " ". "actived";
	}
?>
        <div class="<?php echo $errorClassesText?>">
            <i class="fa fa-exclamation-circle"></i>
            <span class="errorMessage">
                <?php echo $investorValidationErrors['investor_telephone'][0] ?>
            </span>
	</div>		
<?php
	$errorClassesText = "errorInputMessage ErrorDateOfBirth";
	if (array_key_exists('investor_dateOfBirth',$investorValidationErrors)) {
		$errorClassesText .= " ". "actived";
	}
?>
        <div class="<?php echo $errorClassesText?>">
            <i class="fa fa-exclamation-circle"></i>
            <span class="errorMessage">
                <?php echo $investorValidationErrors['investor_dateOfBirth'][0] ?>
            </span>
	</div>		
    </div>
    <!-- /.box-body -->
 
    <div class="box-body">
	<div class="row">
            <div class="col-xs-12 col-sm-6 col-md-5 col-lg-5">
		<div class="form-group">
                    <button type="submit" href="/investors/editUserProfileData" id="editUserData" class="btn btn-primary btn-invest"><?php echo __('Save')?></button>
		</div>
            </div>	
	</div>
    </div>
    <!-- /.box-body -->
</div>	
<!-- /.editDatosPersonales" -->
