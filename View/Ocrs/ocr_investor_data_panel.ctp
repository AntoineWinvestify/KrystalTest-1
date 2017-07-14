<?php
/**
 * +---------------------------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                                               |
 * +---------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 				 |
 * | it under the terms of the GNU General Public License as published by  			 |
 * | the Free Software Foundation; either version 2 of the License, or                           |
 * | (at your option) any later version.                                      			 |
 * | This file is distributed in the hope that it will be useful   				 |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of                          	 |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                                |
 * | GNU General Public License for more details.        			              	 |
 * +---------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.8
 * @date 2017-05-23
 * @package

 * One Click Registration - Investor Data Panel
 * Investor data panel to collect all data to register on platforms
 * 
 * [2017-05-23]  Version 0.1
 * Completed view 
 * 
 * [2017-06-05] Version 0.2
 * Added all error divs & classes to check form 
 * 
 * [2017-06-07] Version 0.3
 * Added javascript form validation [pending: country, telephone, dateOfBirth, IBAN].
 * 
 * [2017-06-08] Version 0.4
 * Added spinner (not working at all, need fix z-index of card-header)
 * Added div with user feedback after saving data on DB.
 * 
 * [2017-06-12] Version 0.5
 * Added input hidden to count correct uploaded files
 * Added paragraph explaining permitted formats & sizes to upload files
 * Added Error div on files
 * 
 * [2017-06-12] Version 0.6
 * Added file upload status for validating
 * 
 * [2017-06-12] Version 0.7
 * Modal and user feedback
 * 
 * [2017-06-22] Version 0.8
 * Added upload file btn & file tooltip
 * 
 * [2017-06-22] Version 0.9
 * Bug fixing
 * 
 * [2017-06-23] Version 0.9
 * Disabled checked data
 * 
 * [2017-06-27] Version 0.9
 * Upload fixed
 * Upload after deleted not fixed
 * 
 * [2017-06-27] Version 0.10
 * Upload cif
 * 
  [2017-07-10] Version 0.11
 * Upload failed fix
 */
echo $result; //for ajax
if ($result) {
    ?>

    <link rel="stylesheet" type="text/css" href="/plugins/intlTelInput/css/intlTelInput.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="/plugins/datepicker/datepicker3.css">
    <script src="/plugins/intlTelInput/js/utils.js"></script>
    <script src="/js/iban.js"></script>
    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
    <script src="/plugins/intlTelInput/js/intlTelInput.js"></script>
    <style>
        .togetoverlay .overlay  {
            z-index: 50;
            background: rgba(255, 255, 255, 0);
            border-radius: 3px;
        }
        .togetoverlay .overlay > .fa {
            font-size: 50px;
        }
        .disabledPointer{
            cursor: not-allowed !important;
        }
        input[type="file"] {
            display: none;
        }
    </style>
    <script src="/js/jquery-dateFormat.js"/>
    <script>
        $(function () {
            addExistingDocuments();
            disbleCheckedData();
            validationerrors = false;
    <?php //telephone                            ?>
            $('#ContentPlaceHolder_telephone').intlTelInput();

    <?php //Date picker                            ?>
            $('#ContentPlaceHolder_dateOfBirth').datepicker({
                autoclose: true,
                format: 'dd/mm/yyyy'
            });
    <?php //Tooltip clicks   ?>
            $(document).on("click", ".tooltipIcon", function () {
                id = $(this).attr("id");
                $("#tooltip" + id).toggle();
            });
    <?php //Show div with CIF & IBAN if its checked.                            ?>
            $(document).on("change", "#investmentVehicle", function () {
                if ($(this).is(":checked")) {
                    $("#investmentVehicleContent").show();
                    //4 is cif id
                    $("#4").show();
                    $("#notification4").show();
                    $("#uploaded4").addClass('uploaded');

                } else {
                    $("#investmentVehicleContent").hide();
                    //4 is cif id
                    $("#4").hide();
                    $("#notification4").hide();
                    $("#uploaded4").removeClass('uploaded');
                }
            });


            $(document).on("click", "#activateOCR", function () {
                console.log("validate 1CR data");
                var result; //link = $(this).attr("href");

    <?php //Javascript validation                            ?>
                if ((result = app.visual.checkForm1CRInvestorData()) === false) {
    <?php //Validation error                            ?>
                    event.stopPropagation();
                    event.preventDefault();
                    $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-warning fade in alert-to-fade" role="alert"><strong><?php echo __("Your data is incorrect.") ?></strong></div>');
                } else { //Validation ok
                    $('#notification').load("/ocrs/ocrInvestorConfirmModal");
                }
            });

            $(document).on("change", ".upload", function () {
    <?php // Upload  file                            ?>
                id = $(this).attr("value");
                var formdatas = new FormData($("#FileForm" + id)[0]);
                link = '/files/upload';
                $.ajax({
                    url: link,
                    dataType: 'json',
                    method: 'post',
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        successUpload(data, id);
                    }
                });
            });

            $(document).on("click", ".upload", function () {
                $(".alert-win-warning").hide();
            });

            $(document).on("click", ".delete", function () {
    <?php //Delete File                            ?>
                id = $(this).val();
                url = $(".url" + id).attr("value");
                params = {
                    url: url,
                    id: id
                };
                var data = jQuery.param(params);
                link = '/files/delete';
                $.ajax({
                    url: link,
                    method: 'post',
                    data: data,
                    success: successDelete(id)
                });
            });

            $(document).on("click", "#backOCR", function () {
    <?php //Go back                            ?>
                link = "../Ocrs/ocrInvestorPlatformSelection";
                var data = null;
                getServerData(link, data, successBack, errorBack);
            });


    <?php if ($ocr[0]['Ocr']['ocr_investmentVehicle'] == CHECKED) {   //Investment vehicle check                     ?>
                $("#investmentVehicle").prop('checked', true);
                $("#investmentVehicleContent").show();
                $("#4").show();
                $("#notification4").show();
                $("#uploaded4").addClass('uploaded');


    <?php } else { ?>

                $("#uploaded4").removeClass('uploaded');

    <?php } ?>


        });


        function successUpload(data, id) {
            data = JSON.parse(data);
            if (data[0]) {
                $("#file" + id).html(data[2][0]);
                $("#file" + id).attr("value", data[2][0]);
                $("#file" + id).append('<input type="hidden" name="data[Files][info]" class="typeFile" value="' + id + '" id="FilesInfo">');
                $("#file" + id).append('<input type="hidden" name="data[Files][info]" class="url' + id + '" value="' + data[2][1] + '" id="FilesInfo">');
                $("#file" + id).append('<input type="hidden" name="data[Files][upload]" id="uploaded' + id + '" class="uploaded" value="1">');
                $("#del" + id).prop("disabled", false);
                $("#status" + id).html('<img src="/img/feedback_true.png" class="feedbackIcon center-block" />');
                $(".label" + id).removeClass("btn");
                $(".label" + id).removeClass("btnRounded");
                $(".label" + id).removeClass("btnUploadFile");
                $(".label" + id).html("");
            } else { //upload fail, incorrect file type or too big
                $("#notification" + id).html('<td colspan="4"><div class="alert bg-success alert-dismissible alert-win-warning fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong>' + data[1] + '</strong></div></td>');
                $("#status" + id).html('<img src="/img/feedback_false.png" class="feedbackIcon center-block" />');
            }

        }

        function successDelete(id) {
    <?php // Delete ok                          ?>
            $("#del" + id).prop("disabled", true);
            $("#file" + id).html('<label class="btn labelFile btnRounded btnUploadFile label' + id + '" for="fileId' + id + '"><i class="fa fa-upload"></i> Upload file</label>');
            $("#file" + id).append('<input type="file" name="data[Files][fileId' + id + ']" id="fileId' + id + '">');
            $("#file" + id).append('<input type="hidden" name="data[Files][info]" class="typeFile" value="' + id + '" id="FilesInfo">');
            $("#file" + id).append('<input type="hidden" name="data[Files][upload]" id="uploaded' + id + '" class="uploaded" value="0">');
            $("#status" + id).html('<span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet') ?></span>')
        }

        function successBack(result) {
    <?php // Go back ok                          ?>
            $(document).off('click');
            $(document).off('change');
            $("#content").html(result);
        }
        function errorBack(result) {
    <?php //Go back error                          ?>
            $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-warning fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Cant go back") ?></strong></div>');
        }



        function addExistingDocuments() {
    <?php //Show alreadey uploaded files in the table                          ?>
    <?php
    foreach ($existingFiles as $file) {
        ?>
                id = <?php echo $file["file"]["FilesInvestor"]["file_id"] ?>;
                url = "<?php echo $file["file"]["FilesInvestor"]["file_url"] ?>";
                $(".documentRow").each(function () {
                    if ($(this).attr("id") == id) {
                        $("#file" + id).html('<?php echo $file["file"]["FilesInvestor"]["file_name"] ?>');
                        $("#file" + id).attr("value", "<?php echo $file["file"]["FilesInvestor"]["file_name"] ?>");
                        $("#file" + id).append('<input type="hidden" name="data[Files][info]" class="typeFile" value="' + id + '" id="FilesInfo">');
                        $("#file" + id).append('<input type="hidden" name="data[Files][info]" class="url' + id + '" value="' + url + '" id="FilesInfo">');
                        $("#file" + id).append('<input type="hidden" name="data[Files][upload]" id="uploaded' + id + '" class="uploaded" value="1">');
                        $("#status" + id).html('<img src="/img/feedback_true.png" class="feedbackIcon center-block" />');
                        $("#del" + id).prop("disabled", false);
                    }
                });

                // DISABLED FIELDS
                //Telephone
                if ($("#ContentPlaceHolder_telephone").is(':disabled')) {
                    $("#ContentPlaceHolder_telephone").addClass("disabledPointer");
                    $("#ContentPlaceHolder_telephone .selected-flag").addClass("disabledPointer");
                }
                //dateOfBirth
                if ($("#ContentPlaceHolder_dateOfBirth").is(':disabled')) {
                    $('#ContentPlaceHolder_dateOfBirth').datepicker({
                        showOn: "off"
                    });
                    $("#ContentPlaceHolder_dateOfBirth").addClass("disabledPointer");
                }
        <?php
    }
    ?>
        }

        function disbleCheckedData() {

        //$inputName must be a class of the input
    <?php foreach ($checkData[0]['Check'] as $inputName => $check) {
        if ($check == CHECKED) { //Data checking  ?> 
                    $('.<?php echo $inputName ?>').prop('disabled', true); // If is CHECKED, block hte input
        <?php } else if ($check == ERROR) { ?>
                    $('.<?php echo $inputName ?>').addClass('redBorder'); // If is ERROR, mark the input
        <?php }} ?>
        }

    </script>
    <div id = "notification"></div>
    <div id="1CR_investor_2_investorDataPanel">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header" data-background-color="blue">
                        <h4 class="title"><strong><?php echo __('One Click Registration') ?></strong></h4>
                    </div>
                    <div class="card-content table-responsive togetoverlay">
                        <div class="overlay">
                            <div class="fa fa-spin fa-spinner" style="color:green">	
                            </div>
                        </div>
                        <div class="row firstParagraph">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <p><?php
    echo __('Para continuar con su proceso de registro debe rellenar las siguientes casillas:')
    ?>
                                    <small><?php echo __('(Todos los campos son obligatorios)') ?></small>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                            echo $this->Form->create('OCR', array('default' => false));
                            ?>
                            <!-- Investor complete data -->
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                <!-- User data -->
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4"> <!-- Name -->
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_name"><?php echo __('Name') ?></label>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_name', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorName check_name" . ' ' . $errorClass;
                                            echo $this->Form->input('Investor.investor_name', array(
                                                'name' => 'name',
                                                'id' => 'ContentPlaceHolder_name',
                                                'label' => false,
                                                'placeholder' => __('Name'),
                                                'class' => $class,
                                                'value' => $investor[0]['Investor']['investor_name'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorName";
                                            if (array_key_exists('investor_name', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_name'][0] ?>
                                                </span>
                                            </div>									
                                        </div>					
                                    </div>
                                    <!-- /name -->

                                    <!-- Surname(s) -->
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_surname"><?php echo __('Surname(s)') ?></label>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_surname', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorSurname check_surname" . ' ' . $errorClass;
                                            echo $this->Form->input('Investor.investor_surname', array(
                                                'name' => 'surname',
                                                'id' => 'ContentPlaceHolder_surname',
                                                'label' => false,
                                                'placeholder' => __('Surname'),
                                                'class' => $class,
                                                'value' => $investor[0]['Investor']['investor_surname'],
                                            ));

                                            $errorClassesText = "errorInputMessage ErrorSurname";
                                            if (array_key_exists('investor_surname', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_surname'][0] ?>
                                                </span>
                                            </div>	
                                        </div>		
                                    </div>
                                    <!-- /Surname(s) -->

                                    <!-- NIF -->
                                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="dni"><?php echo __('Id') ?></label>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_DNI', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorDni check_dni" . ' ' . $errorClass;
                                            echo $this->Form->input('Investor.investor_DNI', array(
                                                'name' => 'dni',
                                                'id' => 'dni',
                                                'label' => false,
                                                'placeholder' => __('Id'),
                                                'class' => $class,
                                                'value' => $investor[0]['Investor']['investor_DNI'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorId";
                                            if (array_key_exists('investor_DNI', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_DNI'][0] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /NIF -->
                                </div>
                                <div class="row">
                                    <!-- Date of Birth -->
                                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_dateOfBirth"><?php echo __('Date of Birth') ?></label>
                                            <div class="input-group input-group-sm blue_noborder2 date investorDateOfBirth disabledPointer">
                                                <?php
                                                $errorClass = "";
                                                if (array_key_exists('investor_dateOfBirth', $investorValidationErrors)) {
                                                    $errorClass = "redBorder";
                                                }
                                                $class = "form-control pull-right check_dateOfBirth" . ' ' . $errorClass;
                                                ?>
                                                <div class="input-group-addon" style="border-radius:8px; border: none;">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" style="border-radius:8px; border:none;" disabled="disabled" class="<?php echo $class ?>" name="dateOfBirth" placeholder="<?php echo __('Date of Birth') ?>" id="ContentPlaceHolder_dateOfBirth" value="<?php echo $investor[0]['Investor']['investor_dateOfBirth']; ?>">
                                                <?php
                                                $errorClassesText = "errorInputMessage ErrorDateOfBirth";
                                                if (array_key_exists('investor_dateOfBirth', $investorValidationErrors)) {
                                                    $errorClassesText .= " " . "actived";
                                                }
                                                ?>
                                                <div class="<?php echo $errorClassesText ?>">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                    <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_dateOfBirth'][0] ?>
                                                    </span>
                                                </div>	
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Date of Birth -->

                                    <!-- email -->
                                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_email"><?php echo __('Email') ?></label>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_email', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorEmail check_email" . ' ' . $errorClass;
                                            echo $this->Form->input('Investor.investor_email', array(
                                                'name' => 'dni',
                                                'id' => 'ContentPlaceHolder_email',
                                                'label' => false,
                                                'placeholder' => __('Email'),
                                                'class' => $class,
                                                'value' => $investor[0]['Investor']['investor_email'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorEmail";
                                            if (array_key_exists('investor_email', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_email'][0] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /email -->

                                    <!-- Telephone -->
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_telephone"><?php echo __('Telephone') ?></label>
                                            <div class="form-control blue_noborder2 telephoneNumber disabledPointer">
                                                <?php
                                                $errorClass = "";
                                                if (array_key_exists('investor_telephone', $investorValidationErrors)) {
                                                    $errorClass = "redBorder";
                                                }
                                                $class = "center-block check_telephone" . ' ' . $errorClass;

                                                echo $this->Form->input('Investor.investor_telephone', array(
                                                    'name' => 'telephone',
                                                    'id' => 'ContentPlaceHolder_telephone',
                                                    'label' => false,
                                                    'placeholder' => __('Telephone'),
                                                    'class' => $class,
                                                    'type' => 'tel',
                                                    'value' => $investor[0]['Investor']['investor_telephone'],
                                                    'disabled' => 'disabled'
                                                ));
                                                $errorClassesText = "errorInputMessage ErrorTelephone";
                                                if (array_key_exists('investor_telephone', $investorValidationErrors)) {
                                                    $errorClassesText .= " " . "actived";
                                                }
                                                ?>
                                                <div class="<?php echo $errorClassesText ?>">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                    <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_telephone'][0] ?>
                                                    </span>
                                                </div>	
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
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_postCode', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorPostCode check_postCode" . ' ' . $errorClass;
                                            echo $this->Form->input('Investor.investor_postCode', array(
                                                'name' => 'investor_postCode',
                                                'id' => 'ContentPlaceHolder_postCode',
                                                'label' => false,
                                                'placeholder' => __('PostCode'),
                                                'class' => $class,
                                                'value' => $investor[0]['Investor']['investor_postCode'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorPostCode";
                                            if (array_key_exists('investor_postCode', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_postCode'][0] ?>
                                                </span>
                                            </div>		
                                        </div>
                                    </div>
                                    <!-- /postal code -->
                                    <!-- Address -->
                                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_address1"><?php echo __('Address') ?></label>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_address1', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorAddress check_address" . ' ' . $errorClass;
                                            echo $this->Form->input('Investor.investor_address1', array(
                                                'name' => 'address1',
                                                'id' => 'ContentPlaceHolder_address1',
                                                'label' => false,
                                                'placeholder' => __('Address'),
                                                'class' => $class,
                                                'value' => $investor[0]['Investor']['investor_address1'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorAddress";
                                            if (array_key_exists('investor_address1', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_address1'][0] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Address -->
                                </div>
                                <div class="row">

                                    <!-- city -->
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="exampleInputCity"><?php echo __('City') ?></label>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_city', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorCity check_city" . ' ' . $errorClass;
                                            echo $this->Form->input('ContentPlaceHolder_city', array(
                                                'name' => 'city',
                                                'id' => 'ContentPlaceHolder_city',
                                                'label' => false,
                                                'placeholder' => __('City'),
                                                'class' => $class,
                                                'value' => $investor[0]['Investor']['investor_city'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorCity";
                                            if (array_key_exists('investor_city', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_city'][0] ?>
                                                </span>
                                            </div>						
                                        </div>	
                                    </div>
                                    <!-- /city -->

                                    <!-- Country -->
                                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_country"><?php echo __('Country') ?></label>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_country', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorCountry check_country" . ' ' . $errorClass;
                                            echo $this->Form->input('Investor.investor_country', array(
                                                'name' => 'country',
                                                'id' => 'ContentPlaceHolder_country',
                                                'label' => false,
                                                'options' => $countryData,
                                                'placeholder' => __('Country'),
                                                'class' => $class,
                                                'value' => $investor[0]['Investor']['investor_country'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorCountry";
                                            if (array_key_exists('investor_country', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_country'][0] ?>
                                                </span>
                                            </div>
                                        </div>	
                                    </div>
                                    <!-- /country -->
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_iban"><?php echo __('IBAN') ?></label>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_iban', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorIban check_iban" . ' ' . $errorClass;
                                            echo $this->Form->input('Ocr.investor_iban', array(
                                                'name' => 'iban',
                                                'id' => 'ContentPlaceHolder_iban',
                                                'label' => false,
                                                'placeholder' => __('IBAN'),
                                                'class' => $class,
                                                'value' => $ocr[0]['Ocr']['investor_iban'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorIban";
                                            if (array_key_exists('investor_iban', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_iban'][0] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div><!-- /Cif + Business Name -->
                                </div>
                                <!-- /User data -->
                            </div>
                            <!-- /Investor complete data -->
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <!-- Checkbox -->
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="checkbox">
                                            <label>
                                                <input id="investmentVehicle" type="checkbox">  <?php echo __('I use my company as investment vehicle') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- /checkbox -->
                                <div class="row" id="investmentVehicleContent">
                                    <!-- CIF -->
                                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_cif"><?php echo __('CIF') ?></label>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_cif', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorCif check_cif" . ' ' . $errorClass;
                                            echo $this->Form->input('Ocr.investor_cif', array(
                                                'name' => 'cif',
                                                'id' => 'ContentPlaceHolder_cif',
                                                'label' => false,
                                                'placeholder' => __('Your company CIF'),
                                                'class' => $class,
                                                'value' => $ocr[0]['Ocr']['investor_cif'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorCif";
                                            if (array_key_exists('investor_cif', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_cif'][0] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /CIF -->

                                    <!-- Business Name -->
                                    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="ContentPlaceHolder_businessName"><?php echo __('Business Name') ?></label>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_businessName', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder2 investorBusinessName check_businessName" . ' ' . $errorClass;
                                            echo $this->Form->input('Ocr.investor_businessName', array(
                                                'name' => 'iban',
                                                'id' => 'ContentPlaceHolder_businessName',
                                                'label' => false,
                                                'placeholder' => __('Your company name'),
                                                'class' => $class,
                                                'value' => $ocr[0]['Ocr']['investor_businessName'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorBusinessName";
                                            if (array_key_exists('investor_businessName', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_businessName'][0] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Business Name -->
                                    <!-- /Business Data -->
                                </div>
                            </div>
                            <?php
                            echo $this->Form->end();
                            ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <div id="OCR_InvestorPanelB">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header" data-background-color="blue">
                            <h4 class="title"><strong><?php echo __('Documentation Uploading') ?></strong></h4>
                        </div>
                        <div class="card-content table-responsive togetoverlay">
                            <div class="overlay">
                                <div class="fa fa-spin fa-spinner" style="color:green">	
                                </div>
                            </div>
                            <div class="row firstParagraph">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p><?php
                                        echo __('Cumpliendo con la Ley 10/2012, del 28 de Abril, de prevención del'
                                                . ' blanqueo de capitales y de Financiación del Terrorismo deberá aportar la siguiente documentación para que las'
                                                . ' PFP puedan validar y autenticar su identidad.')
                                        ?></p>
                                    <p><?php
                                    echo __('Para ello, deberá de aportar copia de su DNI/NIE en vigor y justificante de titularidad bancaria.')
                                    ?></p>
                                </div>
                            </div>
                            <div class="row firstParagraph">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p><?php echo __('Maximun File Size: 10MB'); ?></p>
                                    <p><?php echo __('Permitted Formats:' . $filesType); ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Investor complete data -->
                                <?php
                                $errorClassesText = "errorInputMessage ErrorFiles";
                                if (array_key_exists('investor_files', $investorValidationErrors)) {
                                    $errorClassesText .= " " . "actived";
                                }
                                ?>
                                <div class="<?php echo $errorClassesText ?> col-xs-offset-1 col-sm-offset-1 col-md-offset-1 col-lg-offset-1">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span class="errorMessage">
    <?php echo $investorValidationErrors['investor_files'][0] ?>
                                    </span>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">	
                                    <div class="table-responsive">  
                                        <table id="documentsTable" class="table table-striped display dataTable" width="100%" cellspacing="0"
                                               data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                            <thead>
                                                <tr>
                                                    <th><?php echo __('Name') ?></th>
                                                    <th><?php echo __('Status') ?></th>
                                                    <th><?php echo __('Upload') ?></th>
                                                    <th><?php echo __('Delete') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody id="body">
                                            <input type="hidden" name="countFiles" value="<?php echo count($requiredFiles) ?>">
                                            <?php
                                            foreach ($requiredFiles as $filesTable) {  //Generate the required files table
                                                $file = "file" . $filesTable[0]['Ocrfile']['id'];
                                                ?>
                                                <tr <?php
                                                if ($filesTable[0]['Ocrfile']['file_optional'] == OPTIONAL) {
                                                    echo "style='display: none'";
                                                }
                                                ?> id = "notification<?php echo $filesTable[0]['Ocrfile']['id'] ?>">
                                                </tr>
                                                <tr <?php
                                                if ($filesTable[0]['Ocrfile']['file_optional'] == OPTIONAL) {
                                                    echo "style='display: none'";
                                                }
                                                ?> id="<?php echo $filesTable[0]['Ocrfile']['id'] ?>" class="documentRow">
                                                    <td>
        <?php echo __($filesTable[0]['Ocrfile']['file_type']) ?> <i class="fa fa-exclamation-circle tooltipIcon" id="<?php echo $filesTable[0]['Ocrfile']['id'] ?>"></i>
                                                        <span id="tooltip<?php echo $filesTable[0]['Ocrfile']['id'] ?>" style="display:none"><br/><?php echo $filesTable[0]['Ocrfile']['file_tooltip'] ?></span>
                                                    </td>
                                                    <td id="status<?php echo $filesTable[0]['Ocrfile']['id'] ?>"><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet') ?></span></td>
                                                    <td>
                                                        <?php
                                                        $uploaded = "uploaded" . $filesTable[0]['Ocrfile']['id'];

                                                        echo $this->Form->create('Files', array('action' => 'upload', 'type' => 'file', 'class' => 'Files', 'id' => 'FileForm' . $filesTable[0]['Ocrfile']['id'], 'class' => 'upload', 'value' => $filesTable[0]['Ocrfile']['id']));
                                                        echo "<span id='" . $file . "' >";
                                                        echo "<label class='btn labelFile btnRounded btnUploadFile label" . $filesTable[0]['Ocrfile']['id'] . "' for='fileId" . $filesTable[0]['Ocrfile']['id'] . "'><i class='fa fa-upload'></i> Upload file</label>";
                                                        echo "<input type='file' name='data[Files][fileId" . $filesTable[0]['Ocrfile']['id'] . "]' id='fileId" . $filesTable[0]['Ocrfile']['id'] . "' >";
                                                        //echo $this->Form->file("fileId" . $filesTable[0]['Ocrfile']['id']);
                                                        echo $this->Form->hidden('info', array('class' => 'typeFile', 'value' => $filesTable[0]['Ocrfile']['id']));
                                                        echo $this->Form->hidden('upload', array('id' => $uploaded, 'class' => 'uploaded', 'value' => 0));
                                                        echo "</span>";
                                                        echo $this->Form->end();
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" id="del<?php echo $filesTable[0]['Ocrfile']['id'] ?>" value="<?php echo $filesTable[0]['Ocrfile']['id'] ?>" class="delete btn btn-default btnDeleteFile btnRounded" disabled=""><i class="fa fa-times"></i> <?php echo __('Delete') ?> </button>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="btn1CR" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group">
                        <?php if ($ocr[0]['Ocr']['ocr_status'] != ERROR) { ?>
                            <button type="submit" href="/ocrs/oneClickInvestorI" id="backOCR" class="btn btn-lg btn1CR btnRounded pull-left"><?php echo __('Back') ?></button>
    <?php } ?>
                        <button type="submit" href="/ocrs/oneClickInvestorII" id="activateOCR" class="btn btn-lg btn1CR btnRounded pull-right"><?php echo __('Activate 1CR') ?></button>
                    </div>
                </div>	
            </div>
        </div>
        <?php
    }    
