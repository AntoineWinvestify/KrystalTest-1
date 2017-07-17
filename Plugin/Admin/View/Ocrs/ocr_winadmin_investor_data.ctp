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
 * [2017-06-26] Version 0.6
 * Added Radio buttons on ALL fields. (Initial Value: PENDING)
 * (telephone & email are on YES because they are necessary to register/access Winvestify)
 * [pending] Timestamp on YES clicking.
 * 
 * [2017-06-28] Version 0.7
 * Added Arrays to checking
 * 
 * [2017-07-03] Version 0.8
 * Save ajax
 * Approve Ajax
 * Check values defined in appController
 * Time stamp implemented
 * Added feedback box to user.
 * Checked from db
 */
?>

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
<script src="/js/dateFormat.js"></script>
<script src="/js/jquery-dateFormat.js"></script>
<script>
    $(function () {

        $(document).on('click', '#saveBtn', function () {

<?php //Create files info array            ?>
            fileArray = [];
            i = 0
            $(".file :checked").each(function () {
                id = $(this).attr('id');
                status = $(this).val();
                fileArray[i] = {id, status};
                i++;
            });

<?php //save all radio button status (yes/no/pending) & charge them on next visit at this investor data           ?>
            var params = {

                //Investor Info
                id: <?php echo $checking[0]['Check']['Id'] ?>,
                investorId: <?php echo $checking[0]['Check']['investor_id'] ?>,

                //Checks Info
                name: $("input[name=checkName]:checked").val(),
                nameCheck: $("#checkName").text(),
                surname: $("input[name=checkSurname]:checked").val(),
                surnameCheck: $("#checkSurname").text(),
                dni: $("input[name=checkId]:checked").val(),
                dniCheck: $("#checkId").text(),
                dateOfBirth: $("input[name=checkDateOfBirth]:checked").val(),
                dateOfBirthCheck: $("#checkDateOfBirth").text(),
                email: $("input[name=checkEmail]:checked").val(),
                emailCheck: $("#checkEmail").text(),
                telephone: $("input[name=checkTelephone]:checked").val(),
                telephoneCheck: $("#checkTelephone").text(),
                postCode: $("input[name=checkPostCode]:checked").val(),
                postCodeCheck: $("#checkPostCode").text(),
                address: $("input[name=checkAddress]:checked").val(),
                addressCheck: $("#checkAddress").text(),
                city: $("input[name=checkCity]:checked").val(),
                cityCheck: $("#checkCity").text(),
                country: $("input[name=checkCountry]:checked").val(),
                countryCheck: $("#checkCountry").text(),
                iban: $("input[name=checkIban]:checked").val(),
                ibanCheck: $("#checkIban").text(),
                cif: $("input[name=checkCIF]:checked").val(),
                cifCheck: $("#checkCIF").text(),
                businessName: $("input[name=checkBusinessName]:checked").val(),
                businessNameCheck: $("#checkBusinessName").text(),
                type: 'save',

                //File data
                file: fileArray

            };
            link = '/admin/ocrs/updateChecks';
            var data = jQuery.param(params);
            getServerData(link, data, success, error);
        });


        $(document).on('click', '#approveBtn', function () {
            if ((app.visual.checkFormWinadminInvestorData()) === true) {

<?php //Create companies info array            ?>
                companyArray = [];
                i = 0;
                $(".company :checked").each(function () {
                    id = $(this).attr('name');
                    status = $(this).val();
                    companyArray[i] = {id, status};
                    i++;
                });


<?php //Create files info array            ?>
                fileArray = [];
                i = 0;
                $(".file :checked").each(function () {
                    id = $(this).attr('id');
                    status = $(this).val();
                    fileArray[i] = {id, status};
                    i++;
                });


                var params = {

                    //Investor data   
                    id: <?php echo $checking[0]['Check']['Id'] ?>,
                    investorId: <?php echo $checking[0]['Check']['investor_id'] ?>,

                    //Checking data
                    name: $("input[name=checkName]:checked").val(),
                    nameCheck: $("#checkName").text(),
                    surname: $("input[name=checkSurname]:checked").val(),
                    surnameCheck: $("#checkSurname").text(),
                    dni: $("input[name=checkId]:checked").val(),
                    dniCheck: $("#checkId").text(),
                    dateOfBirth: $("input[name=checkDateOfBirth]:checked").val(),
                    dateOfBirthCheck: $("#checkDateOfBirth").text(),
                    email: $("input[name=checkEmail]:checked").val(),
                    emailCheck: $("#checkEmail").text(),
                    telephone: $("input[name=checkTelephone]:checked").val(),
                    telephoneCheck: $("#checkTelephone").text(),
                    postCode: $("input[name=checkPostCode]:checked").val(),
                    postCodeCheck: $("#checkPostCode").text(),
                    address: $("input[name=checkAddress]:checked").val(),
                    addressCheck: $("#checkAddress").text(),
                    city: $("input[name=checkCity]:checked").val(),
                    cityCheck: $("#checkCity").text(),
                    country: $("input[name=checkCountry]:checked").val(),
                    countryCheck: $("#checkCountry").text(),
                    iban: $("input[name=checkIban]:checked").val(),
                    ibanCheck: $("#checkIban").text(),
                    cif: $("input[name=checkCIF]:checked").val(),
                    cifCheck: $("#checkCIF").text(),
                    businessName: $("input[name=checkBusinessName]:checked").val(),
                    businessNameCheck: $("#checkBusinessName").text(),
                    type: 'approve',

                    //File data
                    file: fileArray,

                    //Companies data
                    company: companyArray
                };
                link = '/admin/ocrs/updateChecks';
                var data = jQuery.param(params);
                getServerData(link, data, successApprove, error);
            } else {
                $(".feedbackText").html('<?php echo __('You must select on "Yes" all radio buttons') ?>');
                $(".alert-to-fade").show();
                $(".alert-to-fade").addClass("alert-win-warning");
            }
        });


        $(document).on('change', "input", function () {
            id = $(this).attr('name');
            timeStamp = new Date($.now());
             $("#" + id).html(DateFormat.format.date(timeStamp, "yyyy-MM-dd HH:mm:ss"));
        });
    });
    function success(data) {
        $(".feedbackText").html(data);
        $(".alert-to-fade").show();
        $(".alert-to-fade").addClass("alert-win-success");
    }
    function successApprove() {
        window.location.replace('/admin/ocrs/ocrWinadminInvestorChecking');
    }
    function error(data) {
        $(".feedbackText").html(data);
        $(".alert-to-fade").show();
        $(".alert-to-fade").addClass("alert-win-warning");
    }
</script>
<div id="1CR_winAdmin_2_investorData">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('Investor Data Checking') ?></strong></h4>
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
                            <div class="box box-warning fade in alert-to-fade" style="display:none;">
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                <strong class="feedbackText"></strong>
                            </div>
                            <h4 class="header1CR"><?php echo __('Investor Data') ?></h4>
                            <!-- User data -->
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4"> <!-- Name -->
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_name"><?php echo __('Name') ?></label>
                                        <div>
                                            <div id="checkName"><?php echo $checking[0]['Check']['check_nameTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" class="checkCorrect" name="checkName" <?php
                                                if ($checking[0]['Check']['check_name'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkName" <?php
                                                if ($checking[0]['Check']['check_name'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkName" <?php
                                                if ($checking[0]['Check']['check_name'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
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
                                        <label for="ContentPlaceHolder_surname"><?php echo __('Surname(s)') ?></label>
                                        <div>
                                            <div id="checkSurname"><?php echo $checking[0]['Check']['check_surnameTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" name="checkSurname" <?php
                                                if ($checking[0]['Check']['check_surname'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkSurname" <?php
                                                if ($checking[0]['Check']['check_surname'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkSurname" <?php
                                                if ($checking[0]['Check']['check_surname'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
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
                                        <label for="ContentPlaceHolder_dni"><?php echo __('Id') ?></label>
                                        <div>
                                            <div id="checkId"><?php echo $checking[0]['Check']['check_dniTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" name="checkId" <?php
                                                if ($checking[0]['Check']['check_dni'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkId" <?php
                                                if ($checking[0]['Check']['check_dni'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkId" <?php
                                                if ($checking[0]['Check']['check_dni'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
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
                                        <label for="ContentPlaceHolder_dateOfBirth"><?php echo __('Date of Birth') ?></label>
                                        <div>
                                            <div id="checkDateOfBirth"><?php echo $checking[0]['Check']['check_dateOfBirthTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" name="checkDateOfBirth" <?php
                                                if ($checking[0]['Check']['check_dateOfBirth'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkDateOfBirth" <?php
                                                if ($checking[0]['Check']['check_dateOfBirth'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkDateOfBirth" <?php
                                                if ($checking[0]['Check']['check_dateOfBirth'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
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
                                            <input type="text" disabled="disabled" style="border-radius:8px; border:none;" class="<?php echo $class ?>" name="dateOfBirth" placeholder="<?php echo __('Date of Birth') ?>" id="ContentPlaceHolder_dateOfBirth" value="<?php echo $userData[0]['Investor']['investor_dateOfBirth'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <!-- /Date of Birth -->

                                <!-- email -->
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_email"><?php echo __('Email') ?></label>
                                        <div>
                                            <div id="checkEmail"><?php echo $checking[0]['Check']['check_emailTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" name="checkEmail" <?php
                                                if ($checking[0]['Check']['check_email'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkEmail" <?php
                                                if ($checking[0]['Check']['check_email'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkEmail" <?php
                                                if ($checking[0]['Check']['check_email'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
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
                                        <label for="ContentPlaceHolder_telephone"><?php echo __('Telephone') ?></label>
                                        <div>
                                            <div id="checkTelephone"><?php echo $checking[0]['Check']['check_telephoneTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" name="checkTelephone" <?php
                                                if ($checking[0]['Check']['check_telephone'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?>  value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkTelephone" <?php
                                                if ($checking[0]['Check']['check_telephone'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkTelephone" <?php
                                                if ($checking[0]['Check']['check_telephone'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
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
                                        <label for="ContentPlaceHolder_postCode"><?php echo __('PostCode') ?></label>
                                        <div>
                                            <div id="checkPostCode"><?php echo $checking[0]['Check']['check_postCodeTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" name="checkPostCode" <?php
                                                if ($checking[0]['Check']['check_postCode'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkPostCode" <?php
                                                if ($checking[0]['Check']['check_postCode'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkPostCode" <?php
                                                if ($checking[0]['Check']['check_postCode'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
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
                                        <label for="ContentPlaceHolder_address1"><?php echo __('Address') ?></label>
                                        <div>
                                            <div id="checkAddress"><?php echo $checking[0]['Check']['check_addressTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" name="checkAddress" <?php
                                                if ($checking[0]['Check']['check_address'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkAddress" <?php
                                                if ($checking[0]['Check']['check_address'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkAddress" <?php
                                                if ($checking[0]['Check']['check_address'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
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
                                        <label for="exampleInputPassword1"><?php echo __('City') ?></label>
                                        <div>
                                            <div id="checkCity"><?php echo $checking[0]['Check']['check_cityTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" name="checkCity" <?php
                                                if ($checking[0]['Check']['check_city'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkCity" <?php
                                                if ($checking[0]['Check']['check_city'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkCity" <?php
                                                if ($checking[0]['Check']['check_city'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
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
                                        <label for="ContentPlaceHolder_country"><?php echo __('Country') ?></label>
                                        <div>
                                            <div id="checkCountry"><?php echo $checking[0]['Check']['check_countryTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" name="checkCountry" <?php
                                                if ($checking[0]['Check']['check_country'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkCountry" <?php
                                                if ($checking[0]['Check']['check_country'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkCountry" <?php
                                                if ($checking[0]['Check']['check_country'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
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
                                        <label for="ContentPlaceHolder_iban"><?php echo __('IBAN') ?></label>
                                        <div>
                                            <div id="checkIban"><?php echo $checking[0]['Check']['check_ibanTime'] ?></div>
                                            <label class="radio-inline"><input type="radio" name="checkIban" <?php
                                                if ($checking[0]['Check']['check_iban'] == YES) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkIban" <?php
                                                if ($checking[0]['Check']['check_iban'] == NO) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                            <label class="radio-inline"><input type="radio" name="checkIban" <?php
                                                if ($checking[0]['Check']['check_iban'] == PENDING) {
                                                    echo 'checked="checked"';
                                                }
                                                ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                        </div>
                                        <input type="text" disabled="disabled" class="form-control blue_noborder" value ="<?php echo $userData[0]['Ocr']['investor_iban'] ?>">
                                    </div>
                                </div><!-- /Cif + Business Name -->
                            </div>
                            <!-- /User data -->
                        </div>
                        <!-- /Investor complete data -->
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="investmentVehicle">

                            <?php if ($userData[0]['Ocr']['ocr_invesmentVehicle'] == CHECKED) { ?>
                                <div id="cifOptional" class="row">
                                    <!-- CIF -->
                                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_cif"><?php echo __('CIF') ?></label>
                                            <div>
                                                <div id="checkCIF"><?php echo $checking[0]['Check']['check_cifTime'] ?></div>
                                                <label class="radio-inline"><input type="radio" name="checkCIF" <?php
                                                    if ($checking[0]['Check']['check_cif'] == YES) {
                                                        echo 'checked="checked"';
                                                    }
                                                    ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                                <label class="radio-inline"><input type="radio" name="checkCIF" <?php
                                                    if ($checking[0]['Check']['check_cif'] == NO) {
                                                        echo 'checked="checked"';
                                                    }
                                                    ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                                <label class="radio-inline"><input type="radio" name="checkCIF" <?php
                                                    if ($checking[0]['Check']['check_cif'] == PENDING) {
                                                        echo 'checked="checked"';
                                                    }
                                                    ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                            </div>
                                            <input disabled="disabled" type="text" class="form-control blue_noborder"value ="<?php echo $userData[0]['Ocr']['investor_cif'] ?>" >
                                        </div>
                                    </div>
                                    <!-- /CIF -->

                                    <!-- Business Name -->
                                    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_businessName"><?php echo __('Business Name') ?></label>
                                            <div>
                                                <div id="checkBusinessName"><?php echo $checking[0]['Check']['check_businessNameTime'] ?></div>
                                                <label class="radio-inline"><input type="radio" name="checkBusinessName" <?php
                                                    if ($checking[0]['Check']['check_businessName'] == YES) {
                                                        echo 'checked="checked"';
                                                    }
                                                    ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                                <label class="radio-inline"><input type="radio" name="checkBusinessName" <?php
                                                    if ($checking[0]['Check']['check_businessName'] == NO) {
                                                        echo 'checked="checked"';
                                                    }
                                                    ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                                <label class="radio-inline"><input type="radio" name="checkBusinessName" <?php
                                                    if ($checking[0]['Check']['check_businessName'] == PENDING) {
                                                        echo 'checked="checked"';
                                                    }
                                                    ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                            </div>
                                            <input disabled="disabled" type="text" class="form-control blue_noborder" value ="<?php echo $userData[0]['Ocr']['investor_businessName'] ?>">
                                        </div>
                                        <!-- /CIF -->

                                    </div>
                                </div>
                            <?php } ?>

                            <div class="row">
                                <!-- Investor complete data -->
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <h4 class="header1CR"><?php echo __('Investor Selected Platforms') ?></h4>
                                    <ul>
                                        <?php
                                        foreach ($userData[0]['Company'] as $company) {//Company list 
                                            if ($company['CompaniesOcr']['company_status'] == SENT) {
                                                ?>
                                                <li>
                                                    <?php
                                                    echo $company['company_name']
                                                    ?>
                                                    <div>
                                                        <label class="radio-inlinev company"><input type="radio" name="<?php echo $company['id'] ?>"  value="<?php echo ACCEPTED ?>"><?php echo __('Yes') ?></label>
                                                        <label class="radio-inline company"><input type="radio" name="<?php echo $company['id'] ?>"  value="<?php echo DENIED ?>"><?php echo __('No') ?></label>
                                                        <label class="radio-inline company"><input type="radio" name="<?php echo $company['id'] ?>"  value="<?php echo SENT ?>" checked="checked"><?php echo __('Pending') ?></label>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <h4 class="header1CR"><?php echo __('Investor Uploaded Documents') ?></h4>
                                    <ul>
                                        <?php
                                        $fileConfig = Configure::read('files');
                                        foreach ($files as $file) {//Company list 
                                            ?>               <div class="time"> <?php echo $file['file']['FilesInvestor']['modified'] ?></div>
                                            <li>

                                                <form action="/files/downloadDocument/ocrfile/<?php echo $file['file']['FilesInvestor']['id'] ?>">
                                                    <button  type="submit" class="download" target="_blank"><?php echo $file['file']['FilesInvestor']['file_name'] . "(" . $file['type']['file_type'] . ")" ?></button> 
                                                </form>

                                                <div>
                                                    <label class="radio-inline file"><input type="radio" id ="<?php echo $file['file']['FilesInvestor']['id'] ?>" name="File<?php echo $file['file']['FilesInvestor']['id'] ?>" <?php
                                                        if ($file['file']['FilesInvestor']['file_status'] == YES) {
                                                            echo 'checked="checked"';
                                                        }
                                                        ?> value="<?php echo YES ?>"><?php echo __('Yes') ?></label>
                                                    <label class="radio-inline file"><input type="radio" id ="<?php echo $file['file']['FilesInvestor']['id'] ?>" name="File<?php echo $file['file']['FilesInvestor']['id'] ?>" <?php
                                                        if ($file['file']['FilesInvestor']['file_status'] == NO) {
                                                            echo 'checked="checked"';
                                                        }
                                                        ?> value="<?php echo NO ?>"><?php echo __('No') ?></label>
                                                    <label class="radio-inline file"><input type="radio" id ="<?php echo $file['file']['FilesInvestor']['id'] ?>" name="File<?php echo $file['file']['FilesInvestor']['id'] ?>" <?php
                                                        if ($file['file']['FilesInvestor']['file_status'] == PENDING) {
                                                            echo 'checked="checked"';
                                                        }
                                                        ?> value="<?php echo PENDING ?>"><?php echo __('Pending') ?></label>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <button type="button" id="saveBtn" href = 'admin/ocrs/updateChecks' class="btn btn-default btn-lg btn-win1 btnRounded pull-left" style="padding: 10px 50px; margin-bottom: 25px"><?php echo __('Save') ?></button>
                                    <button type="button" id="approveBtn" class="btn btn-default btn-lg btn-win1 btnRounded pull-right" style="padding: 10px 50px; margin-bottom: 25px"><?php echo __('Approve') ?></button>
                                </div>
                            </div>
                        </div>
                    </div> <!-- /card -->
                </div>
            </div>
        </div>
