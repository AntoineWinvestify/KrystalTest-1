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
 * Modal with data about 1CR investor to check by WinAdmin to approve that user & send data to 
 * PFP Admin to register into the investor's selected platforms.
 * 
 * [2017-06-09] Version 0.1
 * First view. Insert info about investor
 * [pending] Add MODAL.
*/

?>
<script src="/plugins/intlTelInput/js/intlTelInput.js"></script>
<script src="/plugins/intlTelInput/js/utils.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/intlTelInput/css/intlTelInput.css">
<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/datepicker/datepicker3.css">
<link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
<script type="text/javascript" src="/modals/assets/js/jquery.bootstrap.wizard.js"></script>
<script type="text/javascript" src="/modals/assets/js/paper-bootstrap-wizard.js"></script>
<script>
$(document).ready(function(){
  //iCheck plugin
  $('input').iCheck({
    checkboxClass: 'icheckbox_flat-blue'
  });
  //telephone
  $('#ContentPlaceHolder_telephone').intlTelInput();
  //Date picker
  $('#ContentPlaceHolder_dateOfBirth').datepicker({
      autoclose: true,
      format: 'dd/mm/yyyy'
  });
});
</script>
<style>
    .modal-dialog{
        overflow-y: initial !important
    }
    .modal-body{
        height: 450px;
        overflow-y: auto;
    }
    ul > li > a {
        cursor:default;
    }
    .modal { overflow-y:scroll; }
</style>
<div id="1CR_investorDataChecking" class="modal show" role="dialog">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="green" id="wizardProfile">
                        <div class="overlay">
                            <div class="fa fa-spin fa-spinner" style="color:green">	
                            </div>
                        </div>
                        <div class="wizard-header text-center">
                            <button type="button" class="close closeBtn" data-dismiss="modal" aria-hidden="true" style="margin-right: 15px;">&times;</button>
                            <img src="/img/logo_winvestify/Logo.png" style="float:center; max-width:75px;"/>
                            <img src="/img/logo_winvestify/Logo_texto.png" style="float:center; max-width:250px;"/>
                        </div>
                        <div class="tab-content" style="padding-top: 15px;">
							<?php 	echo $this->Form->create('User', array('url' => "registerPanelA",)); //ADD TO INVESTOR CHECKING CORRECT DATA	?>	
                            <form class="form">	
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="row">
                                            <h4 class="header1CR"><?php echo __('Investor Data')?></h4>
                                            <!-- Investor complete data -->
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <!-- User data -->
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4"> <!-- Name -->
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_name"><?php echo __('Name')?></label> <input type="checkbox" id="checkName">
                                                                <?php
                                                                        $errorClass = "";
                                                                        if (array_key_exists('investor_name',$investorValidationErrors)) {
                                                                                $errorClass = "redBorder";
                                                                        }
                                                                        $class = "form-control blue investorName". ' ' . $errorClass;
                                                                         echo $this->Form->input('Investor.investor_name', array(
                                                                                                                                'name'		=> 'name',
                                                                                                                                'id' 		=> 'ContentPlaceHolder_name',
                                                                                                                                'label' 		=> false,
                                                                                                                                'placeholder' 	=>  __('Name'),
                                                                                                                                'class' 		=> $class,
                                                                                                                                'value'			=> $resultUserData[0]['Investor']['investor_name'],						
                                                                                                                        ));
                                                                ?>
                                                        </div>					
                                                    </div>
                                                    <!-- /name -->

                                                    <!-- Surname(s) -->
                                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_surname"><?php echo __('Surname(s)')?></label> <input type="checkbox" id="checkSurname">
                                                            <?php
                                                                    $errorClass = "";
                                                                    if (array_key_exists('investor_surname',$investorValidationErrors)) {
                                                                            $errorClass = "redBorder";
                                                                    }
                                                                    $class = "form-control blue investorSurname". ' ' . $errorClass;
                                                                    echo $this->Form->input('Investor.investor_surname', array(
                                                                                                                               'name'		=> 'surname',
                                                                                                                               'id' 		=> 'ContentPlaceHolder_surname',
                                                                                                                               'label' 		=> false,
                                                                                                                               'placeholder' 	=>  __('Surname'),
                                                                                                                               'class' 		=> $class,
                                                                                                                               'value'		=> $resultUserData[0]['Investor']['investor_surname'],						
                                                                                                                    ));
                                                            ?>
                                                        </div>		
                                                    </div>
                                                    <!-- /Surname(s) -->

                                                    <!-- NIF -->
                                                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_dni"><?php echo __('Id')?></label> <input type="checkbox" id="checkId">
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
                                                    <!-- /NIF -->
                                                </div>
                                                <div class="row">
                                                    <!-- Date of Birth -->
                                                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_dateOfBirth"><?php echo __('Date of Birth')?></label> <input type="checkbox" id="checkDateOfBirth">
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
                                                                    <input type="text" style="border-radius:8px; border:none;" class="<?php echo $class ?>" name="dateOfBirth" placeholder="<?php echo __('Date of Birth')?>" id="ContentPlaceHolder_dateOfBirth" value="<?php $resultUserData[0]['Investor']['investor_dateOfBirth'] ?>">
                                                                </div>
                                                        </div>
                                                    </div>
                                                    <!-- /Date of Birth -->

                                                    <!-- email -->
                                                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_email"><?php echo __('Email')?></label> <input type="checkbox" id="checkEmail">
                                                                <?php
                                                                    $errorClass = "";
                                                                    if (array_key_exists('investor_email',$investorValidationErrors)) {
                                                                        $errorClass = "redBorder";
                                                                    }
                                                                    $class = "form-control blue investorEmail". ' ' . $errorClass;
                                                                    echo $this->Form->input('Investor.investor_email', array(
                                                                                                                           'name'			=> 'dni',
                                                                                                                           'id' 			=> 'ContentPlaceHolder_email',
                                                                                                                           'label' 		=> false,
                                                                                                                           'placeholder' 	=>  __('Email'),
                                                                                                                           'class' 		=> $class,
                                                                                                                           'value'			=> $resultUserData[0]['Investor']['investor_email'],						
                                                                                                                        ));
                                                                ?>
                                                        </div>
                                                    </div>
                                                    <!-- /email -->

                                                    <!-- Telephone -->
                                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_telephone"><?php echo __('Telephone')?></label> <input type="checkbox" id="checkTelephone">
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
                                                    <!-- /telephone -->
                                                </div>
                                                <div class="row">
                                                    <!-- Postal code -->
                                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_postCode"><?php echo __('PostCode')?></label> <input type="checkbox" id="checkPostCode">
                                                                <?php
                                                                    $errorClass = "";
                                                                    if (array_key_exists('investor_postCode',$investorValidationErrors)) {
                                                                        $errorClass = "redBorder";
                                                                    }
                                                                    $class = "form-control blue investorPostCode". ' ' . $errorClass;
                                                                    echo $this->Form->input('Investor.investor_postCode', array(
                                                                                                                                'name'		=> 'investor_postCode',
                                                                                                                                'id' 		=> 'ContentPlaceHolder_postCode',
                                                                                                                                'label' 		=> false,
                                                                                                                                'placeholder' 	=>  __('PostCode'),
                                                                                                                                'class' 		=> $class,
                                                                                                                                'value'		=> $resultUserData[0]['Investor']['investor_postCode'],						
                                                                                                                        ));
                                                                ?>
                                                        </div>
                                                    </div>
                                                    <!-- /postal code -->
                                                    <!-- Address -->
                                                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_address1"><?php echo __('Address')?></label> <input type="checkbox" id="checkAdress">
                                                                <?php
                                                                        $errorClass = "";
                                                                        if (array_key_exists('investor_address1',$investorValidationErrors)) {
                                                                                $errorClass = "redBorder";
                                                                        }
                                                                        $class = "form-control blue investorSurname". ' ' . $errorClass;
                                                                        echo $this->Form->input('Investor.investor_address1', array(
                                                                                                                                   'name'		=> 'address1',
                                                                                                                                   'id' 		=> 'ContentPlaceHolder_address1',
                                                                                                                                   'label' 		=> false,
                                                                                                                                   'placeholder' 	=>  __('Address'),
                                                                                                                                   'class' 		=> $class,
                                                                                                                                   'value'		=> $resultUserData[0]['Investor']['investor_address1'],						
                                                                                                                        ));
                                                                ?>
                                                        </div>
                                                    </div>
                                                    <!-- /Address -->
                                                </div>
                                                <div class="row">

                                                    <!-- city -->
                                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1"><?php echo __('City')?></label> <input type="checkbox" id="checkCity">
                                                                <?php
                                                                    $errorClass = "";
                                                                    if (array_key_exists('investor_city',$investorValidationErrors)) {
                                                                        $errorClass = "redBorder";
                                                                    }
                                                                    $class = "form-control blue investorCity". ' ' . $errorClass;
                                                                    echo $this->Form->input('ContentPlaceHolder_city', array(
                                                                                                                            'name'		=> 'city',
                                                                                                                            'id' 		=> 'ContentPlaceHolder_city',
                                                                                                                            'label' 	=> false,
                                                                                                                            'placeholder' 	=>  __('City'),
                                                                                                                            'class' 	=> $class,
                                                                                                                            'value'		=> $resultUserData[0]['Investor']['investor_city'],						
                                                                                                                        ));
                                                                        ?>
                                                        </div>	
                                                    </div>
                                                    <!-- /city -->

                                                    <!-- Country -->
                                                    <div class="col-xs-12 col-sm-4 col-md-8 col-lg-8">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_country"><?php echo __('Country')?></label> <input type="checkbox" id="checkCountry">
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
                                                                        'options'               => $countryData,
                                                                        'placeholder' 	=>  __('Country'),
                                                                        'class' 		=> $class,
                                                                        'value'			=> $resultUserData[0]['Investor']['investor_country'],						
                                                                ));
                                                            ?>
                                                        </div>	
                                                    </div>
                                                    <!-- /country -->
                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_iban"><?php echo __('IBAN')?></label> <input type="checkbox" id="checkIBAN">
                                                            <input type="text" class="form-control blue">
                                                        </div>
                                                    </div><!-- /Cif + Business Name -->
                                                </div>
                                            <!-- /User data -->
                                            </div>
                                            <!-- /Investor complete data -->
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                <div class="row">
                                                    <!-- CIF -->
                                                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_cif"><?php echo __('CIF')?></label> <input type="checkbox" id="checkCIF">
                                                            <input type="text" class="form-control blue">
                                                        </div>
                                                    </div>
                                                    <!-- /CIF -->

                                                    <!-- Business Name -->
                                                    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="ContentPlaceHolder_businessName"><?php echo __('Business Name')?></label> <input type="checkbox" id="checkBusinessName">
                                                            <input type="text" class="form-control blue">
                                                        </div>
                                                    </div>
                                                    <!-- /Business Name -->
                                                    <!-- /Business Data -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                                           <button type="button" class="btn btn-default btn-lg btn-win1 btnRounded center-block" style="padding: 10px 50px; margin-bottom: 25px"><?php echo __('Approve')?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> <!-- /tab-content -->
                </div>  <!-- /wizard-card -->
            </div> <!-- /wizard-container -->
        </div> <!-- /modal -->
    </div>
</div>