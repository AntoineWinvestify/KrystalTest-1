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
 * @version 0.5
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
 */
?>

<link rel="stylesheet" type="text/css" href="/plugins/intlTelInput/css/intlTelInput.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="/plugins/datepicker/datepicker3.css">
<script src="/plugins/intlTelInput/js/utils.js"></script>
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
</style>

<script>
    $(function () {
        addExistingDocuments();
        validationerrors = false;
<?php //telephone   ?>
        $('#ContentPlaceHolder_telephone').intlTelInput();

<?php //Date picker   ?>
        $('#ContentPlaceHolder_dateOfBirth').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        });
<?php //Show div with CIF & IBAN if its checked.   ?>
        $(document).on("change", "#investmentVehicle", function () {
            if ($(this).is(":checked")) {
                $("#investmentVehicleContent").show();
            } else {
                $("#investmentVehicleContent").hide();
            }
        });
<?php //Data successfully saved feedback to user FADEOUT.   ?>
        $(document).bind('DOMSubtreeModified', function () {
            fadeOutElement(".alert-to-fade", 5000);
        });



        $(document).on("click", "#activateOCR", function () {
            console.log("saving investor 1CR data");
            var result, link = $(this).attr("href");
            event.stopPropagation();
            event.preventDefault();
<?php //Javascript validation   ?>
            if ((result = app.visual.checkForm1CRInvestorData()) === false) {
<?php //Validation error   ?>
                event.stopPropagation();
                event.preventDefault();
                $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Your data is incorrect.") ?></strong></div>');
                return false;
            } else { //Validation ok

                $('#notification').load("/ocrs/ocrInvestorConfirmModal");
<?php /* var params = {


  investor_name: $("#ContentPlaceHolder_name").val(),
  investor_surname: $("#ContentPlaceHolder_surname").val(),
  investor_DNI: $("#dni").val(),
  investor_dateOfBirth: $("#ContentPlaceHolder_dateOfBirth").val(),
  investor_telephone: $("#ContentPlaceHolder_telephone").intlTelInput("getNumber"),
  investor_address1: $("#ContentPlaceHolder_address1").val(),
  investor_postCode: $("#ContentPlaceHolder_postCode").val(),
  investor_city: $("#ContentPlaceHolder_city").val(),
  investor_country: $("#ContentPlaceHolder_country").val(),
  investor_email: $("#ContentPlaceHolder_email").val()
  };

  if ($("#investmentVehicle").prop("checked")) {
  params.investmentVehicle = 1;
  params.cif = $("#ContentPlaceHolder_cif").val();
  params.businessName = $("#ContentPlaceHolder_businessName").val();
  params.iban = $("#ContentPlaceHolder_iban").val();

  } else {
  params.investmentVehicle = 0;
  params.iban = $("#ContentPlaceHolder_iban").val();
  }
  link = $("#activateOCR").attr('href');
  var data = jQuery.param(params);
  getServerData(link, data, success, error); */ ?>
            }
        });

        $(document).on("change", ".upload", function () {
<?php // Upload  file   ?>
            id = $(this).attr("value");
            var formdatas = new FormData($("#FileForm" + id)[0]);
            $.ajax({
                url: '../Files/upload',
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

        $(document).on("click", ".delete", function () {
<?php //Delete File   ?>
            id = $(this).val();
            url = $(".url" + id).attr("value");
            name = $("#file" + id).attr("value");
            params = {
                url: url,
                name: name,
                id: id
            };
            var data = jQuery.param(params);
            $.ajax({
                url: '../Files/delete',
                method: 'post',
                data: data,
                success: successDelete(id)
            });
        });

        $(document).on("click", "#backOCR", function () {
<?php //Go back   ?>
            link = "../Ocrs/ocrInvestorPlatformSelection";
            var data = null;
            getServerData(link, data, successBack, errorBack);
        });


<?php if ($ocr[0]['Ocr']['ocr_investmentVehicle']) { //Invesment vehicle comprobation ?> 
            if (<?php echo $ocr[0]['Ocr']['ocr_investmentVehicle'] ?> === 1) {
                $("#investmentVehicle").prop('checked', true);
                $("#investmentVehicleContent").show();
            }
<?php } ?>


    });


    function error(result) { <?php //Server validation Error ?>
        result = JSON.parse(result);
        if (result[0][0]["investor_name"]) {
            $(".investorName").addClass("redBorder");
            $(".ErrorName").find(".errorMessage").html(TEXTOS.T01); // "empty field" warning
            $(".ErrorName").fadeIn();
            validationerrors = true;
        } else {
            validationerrors = false;
        }

        if (validationerrors === true || app.visual.checkForm1CRInvestorData() === false) {
            $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Your data is incorrect.") ?></strong></div>');
        } else {
            $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Your data has been successfully modified") ?></strong></div>');
        }

    }

    function success() { <?php //Server validation Ok ?>
        validationerrors = false;
        if (validationerrors === true || app.visual.checkForm1CRInvestorData() === false) {
            $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Your data is incorrect.") ?></strong></div>');
        } else {
            $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Your data has been successfully modified") ?></strong></div>');
        }

    }

    function successUpload(data, id) {
        if (data != 0) { <?php //Upload ok ?>
            $("#file" + id).html(data[0] + " <?php echo __('upload ok') ?>");
            $("#file" + id).attr("value", data[0]);
            $("#file" + id).append('<input type="hidden" name="data[Files][info]" class="typeFile" value="' + id + '" id="FilesInfo">');
            $("#file" + id).append('<input type="hidden" name="data[Files][info]" class="url' + id + '" value="' + data[1] + '" id="FilesInfo">');
            $("#file" + id).append('<input type="hidden" name="data[Files][upload]" id="uploaded' + id + '" class="uploaded" value="1">');
            $("#del" + id).prop("disabled", false);
            $("#status" + id).html('<span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct') ?></span>');
        } else { //upload fail, incorrect file type or too big
            $("#notification" + id).html('<td colspan="4"><div class="alert bg-success alert-dismissible alert-win-warning fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Upload failed. Incorrect type or file too big.") ?></strong></div></td>');
            $("#status" + id).html('<span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning') ?></span>');
        }
    }

    function successDelete(id) { <?php // Delete ok ?>
        $("#del" + id).prop("disabled", true);
        $("#file" + id).html('<input type="file" name="data[Files][fileId' + id + ']" id="fileId' + id + '"> <input type="hidden" name="data[Files][info]" class="typeFile" value="' + id + '" id="FilesInfo">');
        $("#file" + id).append('<input type="hidden" name="data[Files][info]" class="typeFile" value="' + id + '" id="FilesInfo">');
        $("#file" + id).append('<input type="hidden" name="data[Files][upload]" id="uploaded' + id + '" class="uploaded" value="0">');
        $("#status" + id).html('<span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet') ?></span>')
    }

    function successBack(result) { <?php // Go back ok ?> 
        $(document).off('click');
        $(document).off('change');
        $("#content").html(result);
    }
    function errorBack(result) { <?php //Go back error ?>
        $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Cant go back") ?></strong></div>');
    }



    function addExistingDocuments() { <?php //Show alreadey upladed files in the table ?>
<?php
foreach ($existingFiles as $existingFiles) {
    ?>
            id = <?php echo $existingFiles["files_investors"]["file_id"] ?>;
            url = "<?php echo $existingFiles["files_investors"]["file_url"] ?>";
            $(".documentRow").each(function () {
                if ($(this).attr("id") == id) {
                    $("#file" + id).html('<?php echo $existingFiles["files_investors"]["file_name"] . __(" already exist") ?>');
                    $("#file" + id).attr("value", "<?php echo $existingFiles["files_investors"]["file_name"] ?>");
                    $("#file" + id).append('<input type="hidden" name="data[Files][info]" class="typeFile" value="' + id + '" id="FilesInfo">');
                    $("#file" + id).append('<input type="hidden" name="data[Files][info]" class="url' + id + '" value="' + url + '" id="FilesInfo">');
                    $("#file" + id).append('<input type="hidden" name="data[Files][upload]" id="uploaded' + id + '" class="uploaded" value="1">');
                    $("#status" + id).html('<span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct') ?></span>');
                    $("#del" + id).prop("disabled", false);
                }
            });


    <?php
}
?>
    }

</script>
<div id = "notification"></div>
<div id="1CR_investor_2_investorDataPanel">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title"><strong><?php echo __('One Click Registration') ?></strong></h4>
                    <p class="category"><?php echo __('Investor One Click Registration Data') ?></p>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div>
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
                                        $class = "form-control blue_noborder2 investorName" . ' ' . $errorClass;
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
                                        $class = "form-control blue_noborder2 investorSurname" . ' ' . $errorClass;
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
                                        $class = "form-control blue_noborder2 investorDni" . ' ' . $errorClass;
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
                                        <div class="input-group input-group-sm blue_noborder2 date investorDateOfBirth">
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_dateOfBirth', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control pull-right" . ' ' . $errorClass;
                                            ?>
                                            <div class="input-group-addon" style="border-radius:8px; border: none;">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" style="border-radius:8px; border:none;" class="<?php echo $class ?>" name="dateOfBirth" placeholder="<?php echo __('Date of Birth') ?>" id="ContentPlaceHolder_dateOfBirth" value="<?php echo $investor[0]['Investor']['investor_dateOfBirth']; ?>">
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
                                        $class = "form-control blue_noborder2 investorEmail" . ' ' . $errorClass;
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
                                        <div class="form-control blue_noborder2 telephoneNumber">
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_telephone', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "center-block" . ' ' . $errorClass;

                                            echo $this->Form->input('Investor.investor_telephone', array(
                                                'name' => 'telephone',
                                                'id' => 'ContentPlaceHolder_telephone',
                                                'label' => false,
                                                'placeholder' => __('Telephone'),
                                                'class' => $class,
                                                'type' => 'tel',
                                                'value' => $investor[0]['Investor']['investor_telephone']
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
                                        $class = "form-control blue_noborder2 investorPostCode" . ' ' . $errorClass;
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
                                        $class = "form-control blue_noborder2 investorAddress" . ' ' . $errorClass;
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
                                        $class = "form-control blue_noborder2 investorCity" . ' ' . $errorClass;
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
                                <div class="col-xs-12 col-sm-4 col-md-8 col-lg-8">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_country"><?php echo __('Country') ?></label>
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_country', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder2 investorCountry" . ' ' . $errorClass;
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
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_iban"><?php echo __('IBAN') ?></label>
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_iban', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder2 investorIban" . ' ' . $errorClass;
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
                                        $class = "form-control blue_noborder2 investorCif" . ' ' . $errorClass;
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
                                        $class = "form-control blue_noborder2 investorBusinessName" . ' ' . $errorClass;
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
</div>

<div id="OCR_InvestorPanelB">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title"><strong><?php echo __('One Click Registration') ?></strong></h4>
                    <p class="category"><?php echo __('Document Uploading') ?></p>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div>
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <p><?php echo __('Maximun File Size: 10MB'); ?></p>
                            <p><?php echo __('Permitted Formats: .png, .pdf, .png'); ?></p>
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
                                    foreach ($requiredFiles as $requiredFiles) { //Genearete the required files table
                                        $file = "file" . $requiredFiles[0]['File']['id'];
                                        ?>
                                        <tr id = "notification<?php echo $requiredFiles[0]['File']['id'] ?>">

                                        </tr>
                                        <tr id="<?php echo $requiredFiles[0]['File']['id'] ?>" class="documentRow">
                                            <td><?php echo __($requiredFiles[0]['File']['file_type']) ?></td>
                                            <td id="status<?php echo $requiredFiles[0]['File']['id'] ?>"><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet') ?></span></td>
                                            <td>
                                                <?php
                                                $uploaded = "uploaded" . $requiredFiles[0]['File']['id'];
                                                echo $this->Form->create('Files', array('action' => '../Files/upload', 'type' => 'file', 'class' => 'Files', 'id' => 'FileForm' . $requiredFiles[0]['File']['id'], 'class' => 'upload', 'value' => $requiredFiles[0]['File']['id']));
                                                echo "<span id='" . $file . "' >";
                                                echo $this->Form->file("fileId" . $requiredFiles[0]['File']['id']);
                                                echo $this->Form->hidden('info', array('class' => 'typeFile','value' => $requiredFiles[0]['File']['id']));
                                                echo $this->Form->hidden('upload', array('id' => $uploaded, 'class' => 'uploaded', 'value' => 0));
                                                echo "</span>";
                                                echo $this->Form->end();
                                                ?>
                                            </td>
                                            <td>
                                                <button type="button" id="del<?php echo $requiredFiles[0]['File']['id'] ?>" value="<?php echo $requiredFiles[0]['File']['id'] ?>" class="delete btn btn-default" style="background-color:#990000; color:white;" disabled=""><i class="fa fa-times"></i> <?php echo __('Delete') ?> </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    /* <tr>
                                      <td>01-01-2017</td>
                                      <td>NIF_Front</td>
                                      <td><span style="color:#990000"><i class="fa fa-times"></i> <?php echo __('Incorrect') ?></span></td>
                                      <td>
                                      <?php
                                      echo $this->Form->create('Files', array('action' => '../Files/upload', 'type' => 'file', 'class' => 'Files'));
                                      echo $this->Form->file('nifF');
                                      echo $this->Form->end();
                                      ?>
                                      </td>
                                      <td>
                                      <button type="button" class="delete btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                      </td>
                                      </tr>
                                      <tr>
                                      <td>01-01-2017</td>
                                      <td>NIF_Back</td>
                                      <td><span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning') ?></span></td>
                                      <td>
                                      <?php
                                      echo $this->Form->create('Files', array('action' => '../Files/upload', 'type' => 'file', 'class' => 'Files'));
                                      echo $this->Form->file('nifB');
                                      echo $this->Form->end();
                                      ?>
                                      </td>
                                      <td>
                                      <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                      </td>
                                      </tr>
                                      <tr>
                                      <td>01-01-2017</td>
                                      <td>IBAN</td>
                                      <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct') ?></span></td>
                                      <td>
                                      <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                      </td>
                                      <td>
                                      <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                      </td>
                                      </tr>
                                      <tr>
                                      <td>01-01-2017</td>
                                      <td>Another one</td>
                                      <td><span style="color:#3399ff"><i class="fa fa-thumb-tack"></i> <?php echo __('Validating') ?></span></td>
                                      <td>
                                      <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                      </td>
                                      <td>
                                      <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                      </td>
                                      </tr>
                                      <tr>
                                      <td>01-01-2017</td>
                                      <td>Another one</td>
                                      <td><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet') ?></span></td>
                                      <td>
                                      <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                      </td>
                                      <td>
                                      <button type="button" class="btn btn-default" style="background-color:#990000; color:white;" disabled><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                      </td>
                                      </tr> */
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="btn1CR" class="col-xs-12 col-sm-12 col-md-12 col-lg-9">
            <div class="form-group">
                <button type="submit" href="/ocrs/oneClickInvestorI" id="backOCR" class="btn btn-lg btn1CR btnRounded pull-left"><?php echo __('Back') ?></button>
                <button type="submit" href="/ocrs/oneClickInvestorII" id="activateOCR" class="btn btn-lg btn1CR btnRounded pull-right"><?php echo __('Activate 1CR') ?></button>
            </div>
        </div>	
    </div>
</div>
