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
 * @version 0.5
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
 * 
 * [2017-06-11] Version 0.2
 * Added MODAL.
 * Added Error MODAL & if (php error)
 * Added JS to control closing btn on modal
 * 
 * [2017-06-12] Version 0.3
 * Deleted plugins JS & CSS (unnecessary)
 * Added disabled to all inputs
 * Added list of Documents & Selected PFPs
 * 
 * [2017-06-13] Version 0.4
 * Deleted modal & added green box
 * Added style to overlay
 * 
 * [2017-06-22] Version 0.5
 * Added Radio Buttons on Documents Checking
 * Added Save btn (without functionality)
 * 
 * [2017-06-23] Version 0.6
 * Data from db
 */
print_r($userData);
print_r($checking);
?>
<link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
<script type="text/javascript" src="/modals/assets/js/jquery.bootstrap.wizard.js"></script>
<script type="text/javascript" src="/modals/assets/js/paper-bootstrap-wizard.js"></script>
<style>
    .togetoverlay .overlay  {
        z-index: 50;
        background: rgba(255, 255, 255, 0);
        border-radius: 3px;
    }
    .togetoverlay .overlay > .fa {
        font-size: 50px;
    }
    .radio-inline {
        margin-left: 15px !important;
    }
</style>
<script>
    if (Cosa.is(":checked")) {
        $("#investmentVehicle").show();
    }
</script>
<div id="1CR_winAdmin_2_investorData">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('WinAdmin - Investor Data Checking') ?></strong></h4>
                    <p class="category"><?php echo __('Show all investor data') ?></p>
                </div>
                <div class="card-content togetoverlay">
                    <!--<div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div>-->
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <p><?php
                                echo __('One Click Registration Le permite registrarse con un solo click en cualquier plataforma'
                                        . ' que Winvestify tenga habilitada. Para ello, cumpliendo con la Ley 10/2012, del 28 de Abril, de prevención del'
                                        . ' blanqueo de capitales y de Financiación del Terrorismo deberá aportar la siguiente documentación para que las'
                                        . ' PFP puedan validar y autenticar su identidad.')
                                ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Investor complete data -->
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <h4 class="header1CR"><?php echo __('Investor Data') ?></h4>
                            <!-- User data -->
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4"> <!-- Name -->
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_name"><?php echo __('Name') ?></label> <input type="checkbox" id="checkName">
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_name', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder investorName" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_name', array(
                                            'name' => 'name',
                                            'id' => 'ContentPlaceHolder_name',
                                            'label' => false,
                                            'placeholder' => __('Name'),
                                            'class' => $class,
                                            'value' => $userData[0]['Investor']['investor_name'],
                                            'disabled' => 'disabled'
                                        ));
                                        ?>
                                    </div>					
                                </div>
                                <!-- /name -->

                                <!-- Surname(s) -->
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_surname"><?php echo __('Surname(s)') ?></label> <input type="checkbox" id="checkSurname">
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_surname', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder investorSurname" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_surname', array(
                                            'name' => 'surname',
                                            'id' => 'ContentPlaceHolder_surname',
                                            'label' => false,
                                            'placeholder' => __('Surname'),
                                            'class' => $class,
                                            'value' => $userData[0]['Investor']['investor_surname'],
                                            'disabled' => 'disabled'
                                        ));
                                        ?>
                                    </div>		
                                </div>
                                <!-- /Surname(s) -->

                                <!-- NIF -->
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_dni"><?php echo __('Id') ?></label> <input type="checkbox" id="checkId">
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_DNI', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder investorDni" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_DNI', array(
                                            'name' => 'dni',
                                            'id' => 'ContentPlaceHolder_dni',
                                            'label' => false,
                                            'placeholder' => __('Id'),
                                            'class' => $class,
                                            'value' => $userData[0]['Investor']['investor_DNI'],
                                            'disabled' => 'disabled'
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
                                        <label for="ContentPlaceHolder_dateOfBirth"><?php echo __('Date of Birth') ?></label> <input type="checkbox" id="checkDateOfBirth">
                                        <div class="input-group input-group-sm blue_noborder date">
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_dateOfBirth', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control pull-right investorDateOfBirth" . ' ' . $errorClass;
                                            ?>
                                            <div class="input-group-addon" style="border-radius:8px; border: none;">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" disabled="disabled" style="border-radius:8px; border:none;" class="<?php echo $class ?>" name="dateOfBirth" placeholder="<?php echo __('Date of Birth') ?>" id="ContentPlaceHolder_dateOfBirth" value="<?php $userData[0]['Investor']['investor_dateOfBirth'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <!-- /Date of Birth -->

                                <!-- email -->
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_email"><?php echo __('Email') ?></label> <input type="checkbox" id="checkEmail">
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_email', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder investorEmail" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_email', array(
                                            'name' => 'dni',
                                            'id' => 'ContentPlaceHolder_email',
                                            'label' => false,
                                            'placeholder' => __('Email'),
                                            'class' => $class,
                                            'value' => $userData[0]['Investor']['investor_email'],
                                            'disabled' => 'disabled'
                                        ));
                                        ?>
                                    </div>
                                </div>
                                <!-- /email -->

                                <!-- Telephone -->
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_telephone"><?php echo __('Telephone') ?></label> <input type="checkbox" id="checkTelephone">
                                        <div class="form-control blue_noborder">
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_telephone', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "telephoneNumber center-block" . ' ' . $errorClass;

                                            echo $this->Form->input('Investor.investor_telephone', array(
                                                'name' => 'telephone',
                                                'id' => 'ContentPlaceHolder_telephone',
                                                'label' => false,
                                                'placeholder' => __('Telephone'),
                                                'class' => $class,
                                                'type' => 'tel',
                                                'value' => $userData[0]['Investor']['investor_telephone'],
                                                'disabled' => 'disabled'
                                            ));
                                            $errorClassesForTexts = "errorInputMessage ErrorPhoneNumber col-xs-offset-1";
                                            if (array_key_exists('investor_telephone', $validationResult)) {
                                                $errorClassesForTexts .= " " . "actived";
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
                                        <label for="ContentPlaceHolder_postCode"><?php echo __('PostCode') ?></label> <input type="checkbox" id="checkPostCode">
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_postCode', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder investorPostCode" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_postCode', array(
                                            'name' => 'investor_postCode',
                                            'id' => 'ContentPlaceHolder_postCode',
                                            'label' => false,
                                            'placeholder' => __('PostCode'),
                                            'class' => $class,
                                            'value' => $userData[0]['Investor']['investor_postCode'],
                                            'disabled' => 'disabled'
                                        ));
                                        ?>
                                    </div>
                                </div>
                                <!-- /postal code -->
                                <!-- Address -->
                                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_address1"><?php echo __('Address') ?></label> <input type="checkbox" id="checkAdress">
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_address1', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder investorSurname" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_address1', array(
                                            'name' => 'address1',
                                            'id' => 'ContentPlaceHolder_address1',
                                            'label' => false,
                                            'placeholder' => __('Address'),
                                            'class' => $class,
                                            'value' => $userData[0]['Investor']['investor_address1'],
                                            'disabled' => 'disabled'
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
                                        <label for="exampleInputPassword1"><?php echo __('City') ?></label> <input type="checkbox" id="checkCity">
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_city', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder investorCity" . ' ' . $errorClass;
                                        echo $this->Form->input('ContentPlaceHolder_city', array(
                                            'name' => 'city',
                                            'id' => 'ContentPlaceHolder_city',
                                            'label' => false,
                                            'placeholder' => __('City'),
                                            'class' => $class,
                                            'value' => $userData[0]['Investor']['investor_city'],
                                            'disabled' => 'disabled'
                                        ));
                                        ?>
                                    </div>	
                                </div>
                                <!-- /city -->

                                <!-- Country -->
                                <div class="col-xs-12 col-sm-4 col-md-8 col-lg-8">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_country"><?php echo __('Country') ?></label> <input type="checkbox" id="checkCountry">
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_country', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder investorCountry" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_country', array(
                                            'name' => 'country',
                                            'id' => 'ContentPlaceHolder_country',
                                            'label' => false,
                                            'options' => $countryData,
                                            'placeholder' => __('Country'),
                                            'class' => $class,
                                            'value' => $userData[0]['Investor']['investor_country'],
                                            'disabled' => 'disabled'
                                        ));
                                        ?>
                                    </div>	
                                </div>
                                <!-- /country -->
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_iban"><?php echo __('IBAN') ?></label> <input type="checkbox" id="checkIBAN">
                                        <input type="text" disabled="disabled" class="form-control blue_noborder" value="<?php echo $userData[0]['Ocr']['investor_iban'] ?>">
                                    </div>
                                </div><!-- /Cif + Business Name -->
                            </div>
                            <!-- /User data -->
                        </div>

                        <!-- /Investor complete data -->
                        <?php if ($userData[0]['Ocr']['ocr_invesmentVehicle'] == CHECKED) { ?>
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="investmentVehicle">
                                <div class="row">
                                    <!-- CIF -->
                                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_cif"><?php echo __('CIF') ?></label> <input type="checkbox" id="checkCIF">
                                            <input disabled="disabled" type="text" class="form-control blue_noborder" value="<?php echo $userData[0]['Ocr']['investor_cif'] ?>" >
                                        </div>
                                    </div>
                                    <!-- /CIF -->

                                    <!-- Business Name -->
                                    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_businessName"><?php echo __('Business Name') ?></label> <input type="checkbox" id="checkBusinessName">
                                            <input disabled="disabled" type="text" class="form-control blue_noborder" value="<?php echo $userData[0]['Ocr']['investor_businessName'] ?>" >
                                        </div>
                                    </div>
                                    <!-- /Business Name -->
                                    <!-- /Business Data -->
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <!-- Investor complete data -->
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <h4 class="header1CR"><?php echo __('Investor Selected Platforms') ?></h4>
                            <ul>
                                <?php foreach ($userData[0]['Company'] as $company) { ?>
                                    <li val="<?php echo __($company['id']) ?>" ><?php echo __($company['company_name']) ?><input type="checkbox" id="checkPFP1"></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <h4 class="header1CR"><?php echo __('Investor Uploaded Documents') ?></h4>
                            <ul>
                                <?php foreach ($files as $file) {?>
                                    <li><a href="#" target="_blank"><?php echo __( $file['file']['FilesInvestor']['file_name'] . "(" . $file['type']['file_type'] . ")") ?></a> 
                                        <div>
                                            <label class="radio-inline"><input val = "1" type="radio" name="<?php echo $file['file']['FilesInvestor']['file_id'] ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input val = "2" type="radio" name="<?php echo $file['file']['FilesInvestor']['file_id'] ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input val = "0" type="radio" name="<?php echo $file['file']['FilesInvestor']['file_id'] ?>"><?php echo __('Pending') ?></label>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <button type="button" id="saveBtn" class="btn btn-default btn-lg btn-win1 btnRounded pull-left" style="padding: 10px 50px; margin-bottom: 25px"><?php echo __('Save') ?></button>
                            <button type="button" id="approveBtn" class="btn btn-default btn-lg btn-win1 btnRounded pull-right" style="padding: 10px 50px; margin-bottom: 25px"><?php echo __('Approve') ?></button>
                        </div>
                    </div>
                </div>
            </div> <!-- /card -->
        </div>
    </div>
</div>