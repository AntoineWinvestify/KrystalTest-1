<?php

/**
 *
 *
 * AJAX format for reading the personal data of the investor
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-12-26
 * @package



2016-12-26		version 0.1
multi-language support added




Pending:
Javascript,
Error messages and Error classes for each field must be properly named
Add all the funcionality in case the server generates an error while trying to save the data

button in tab linked account should be aligned at same level as "input fields"

*/


?>




<script src="/plugins/intlTelInput/js/intlTelInput.min.js"></script>
<script src="/plugins/intlTelInput/js/utils.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/intlTelInput/css/intlTelInput.css">
<link rel="stylesheet" type="text/css" href="/css/compare_styles.css">

<script type="text/javascript" src="/plugins/datepicker/bootstrap-datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/datepicker/datepicker3.css">


<script type="text/javascript">
// Google Analytics Data	
function ga_savePersonalData() {
	console.log("ga 'send' 'event' 'PersonalData'  'savePersonalDataClick'");
	ga('send', 'event', 'PersonalData', 'savePersonalDataClick');	
}
	
	
	
function successEditProfileData(data) {
	console.log("successEditProfileData function is called OK");
	$("#personalDataTab").replaceWith(data);
	console.log("New data was successfully saved");		
}
	
	
function errorEditProfileData(data) {
console.log("errorEditProfileData: ");
	$('.editDatosPersonales').replaceWith(data);	
}
	
	
	

$(document).ready(function() {

$(function() {
	

$("#editUserData").bind("click", function(event) {
console.log("saving personal data");

	var result,
		link = $(this).attr( "href" );
	 
	event.stopPropagation();
	event.preventDefault();

	if ((result = app.visual.checkFormUserDataModification()) == false) {
		event.stopPropagation();
		event.preventDefault();
		return false;
	}
	else {	
console.log("personal data has been checked locally using Javascript");
//		ga_savePersonalData();				// data locally checked and approved. Call Google Analytics
		var params = {	
			password: $("#ContentPlaceHolder_password_confirm").val(),
			investor_name: $("#ContentPlaceHolder_name").val(),
			investor_surname: $("#ContentPlaceHolder_surname").val(),
			investor_address1: $("#ContentPlaceHolder_address1").val(),
			investor_postCode: $("#ContentPlaceHolder_postCode").val(),
			investor_city: $("#ContentPlaceHolder_city").val(),
			investor_country: $("#ContentPlaceHolder_country").val(),
			identificationId: $("#ContentPlaceHolder_dni").val(),
			investor_telephone: $("#ContentPlaceHolder_telephone").val(),
			investor_dateOfBirth: $("#ContentPlaceHolder_dateOfBirth").val()
        };

		var data = jQuery.param( params );
		getServerData(link, data, successEditProfileData, errorEditProfileData);
	}
});	
	
	




});
});
</script>

					<div class="editDatosPersonales">
						<div class="box-body">
							<div class="row">
								<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="ContentPlaceHolder_password"><?php echo __('Password')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('password',$userValidationErrors)) {
		$errorClass = "redBorder";
	} 
	$class = "form-control password1". ' ' . $errorClass;
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
								<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
									<div class="form-group">
										<label for="ContentPlaceHolder_password_confirm"><?php echo __('Repeat Password')?></label>
<?php

	$errorClass = "";
	if (array_key_exists('password',$userValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control passwordConfirm". ' ' . $errorClass;
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
						<!-- /.box-body -->
						
						<div class="box-body">
							<div class="row">
								<div class="col-xs-12 col-sm-4 col-md-3"> <!-- Name -->
									<div class="form-group">
										<label for="ContentPlaceHolder_name"><?php echo __('Name')?></label>
<?php

	$errorClass = "";
	if (array_key_exists('investor_name',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control investorName". ' ' . $errorClass;
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
								<div class="col-xs-12 col-sm-8 col-md-6 col-lg-6"> <!-- surname -->
									<div class="form-group">
										<label for="ContentPlaceHolder_surname"><?php echo __('Surname(s)')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_surname',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control investorSurname". ' ' . $errorClass;
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
							<div class="row">
								<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3"> <!-- address -->
									<div class="form-group">
										<label for="ContentPlaceHolder_address1"><?php echo __('Address')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_address1',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control investorSurname". ' ' . $errorClass;
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
								</div>						 
								<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3"> <!-- post code -->
									<div class="form-group">
										<label for="ContentPlaceHolder_postCode"><?php echo __('PostCode')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_postCode',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control investorPostCode". ' ' . $errorClass;
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
								</div> <!-- /post code -->
								<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3"> <!-- city -->
									<div class="form-group">
										<label for="ContentPlaceHolder_city"><?php echo __('City')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_city',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control investorCity". ' ' . $errorClass;
										echo $this->Form->input('Investor.investor_city', array(
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


						<div class="box-body"> <!-- Box-body 4: country -->
							<div class="row"> <!-- row 5 -->
								<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3"> <!-- country -->
									<div class="form-group">
										<label for="ContentPlaceHolder_country"><?php echo __('Country')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_country',$investorValidationErrors)) {
		$errorClass = "redBorder";
}
	$class = "form-control investorCountry". ' ' . $errorClass;	
										echo $this->Form->input('Investor.investor_country', array(
											'name'			=> 'country',
											'id' 			=> 'ContentPlaceHolder_country',
											'label' 		=> false,
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
						<!-- /.box-body 4 -->
						
						<div class="box-body"> <!-- box-body 5: id + telephone + DoB -->
							<div class="row">
								<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3"> <!-- id -->
									<div class="form-group">
										<label for="ContentPlaceHolder_dni"><?php echo __('Id')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_DNI',$investorValidationErrors)) {
		$errorClass = "redBorder";
}
	$class = "form-control investorDni". ' ' . $errorClass;
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
								<!-- /id -->
								
								<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3"> <!-- telephone -->
									<div class="form-group">
										<label for="ContentPlaceHolder_telephone"><?php echo __('Telephone')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_telephone',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control investorTelephone". ' ' . $errorClass;
										echo $this->Form->input('Investor.investor_telephone', array(
											'name'			=> 'telephone',
											'id' 			=> 'ContentPlaceHolder_telephone',
											'label' 		=> false,
											'placeholder' 	=>  __('Telephone'),
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_telephone'],						
							));
?>
									</div>					
								</div>
<script>
	$('#ContentPlaceHolder_telephone').intlTelInput();
</script>
								<div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
									<div class="form-group">
										<label for="ContentPlaceHolder_dateOfBirth1"><?php echo __('Date of Birth')?></label>
<?php
	$errorClass = "";
	if (array_key_exists('investor_dateOfBirth',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control investorDateOfBirth". ' ' . $errorClass;
	
										echo $this->Form->input('Investor.investor_dateOfBirth', array(
											'name'			=> 'dateOfBirth',
											'id' 			=> 'ContentPlaceHolder_dateOfBirth1',
											'label' 		=> false,
											'placeholder' 	=>  __('Date of Birth'),
											'type'			=> 'text',
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_dateOfBirth'],						
										));
?>
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
<!--	
							<div class="col-xs-3">  <!-- Date of birth 
								<label for="ContentPlaceHolder_dateOfBirth"><?php echo __('Date of Birth')?></label>
							</div>
							<div class="input-group date" data-provide="datepicker">
-->
<?php
	$errorClass = "";
	if (array_key_exists('investor_dateOfBirth',$investorValidationErrors)) {
		$errorClass = "redBorder";
	}
	$class = "form-control investorDateOfBirth". ' ' . $errorClass;
/*
										echo $this->Form->input('Investor.investor_dateOfBirth', array(
											'name'			=> 'dateOfBirth',
											'id' 			=> 'ContentPlaceHolder_dateOfBirth',
											'label' 		=> false,
											'placeholder' 	=>  __('Date of Birth'),
											'type'			=> 'text',
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_dateOfBirth'],						
										));
*/
?>							
<!--								
	    						<div class="input-group-addon">
    	   							<span class="glyphicon glyphicon-th"></span>
    							</div>
    						</div>
-->
<script>
/*	
	$('.input-group .date').datepicker({
		format: "dd/mm/yyyy",
    	orientation: "bottom left",
    	autoclose: true,
    	toggleActive: true
	});
*/
</script>	
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
								<div class="col-xs-12 col-sm-4 col-md-5 col-lg-5">
									<div class="form-group">
										<button type="submit" href="/investors/editUserProfileData"
												id="editUserData" class="btn btn-primary btn-invest"><?php echo __('Save')?></button>
									</div>
								</div>	
							</div>
						</div>
					<div class="editDatosPersonales">
					<!-- /.editDatosPersonales" -->
					