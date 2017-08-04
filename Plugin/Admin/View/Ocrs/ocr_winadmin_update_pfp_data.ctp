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
 * @version 0.2
 * @date 2017-06-12
 * @package
 * 
 * 
 * View to edit & update info about PFPs on DB (links of terms of service, privacy policy...)
 * 
 * [2017-06-12] Version 0.1
 * First view. Empty.
 * 
 * [2017-06-13] Version 0.2
 * Added plugin dataTable
 * Added green box
 * Added initial form
 * 
 * [2017-06-28] Version 0.3
 * Added selects to Modality, Country, Status
 * 
 * [2017-06-29] Version 0.4
 * Ajax function added
 * 
 * [2017-06-30] Version 0.5
 * Update completed
 * Added server feedback
 * Added javascript validation
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
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
        //update ajax
        $(document).on("click", "#updateBtn", function () {
            if ((result = app.visual.checkFormWinadminUpdatePFP()) === true) {
                params = {
                    pfp: $("#ContentPlaceHolder_pfp").val(),
                    temrs: $("#ContentPlaceHolder_terms").val(),
                    privacy: $("#ContentPlaceHolder_privacyPolicy").val(),
                    modality: $("#ContentPlaceHolder_modality").val(),
                    country: $("#ContentPlaceHolder_country").val(),
                    ocr: $("#ContentPlaceHolder_status").val()
                };
                link = $("#updateBtn").attr('href');
                var data = jQuery.param(params);
                getServerData(link, data, success, error);
            }
        });
    });
        function success(data) {
        $(".fbtext").html(data);
        $("#feedbackServer").addClass("alert-win-success");
        $("#feedbackServer").show();
    }

    function error(data) {
        $(".fbtext").html(data);
        $("#feedbackServer").addClass("alert-win-warning");
        $("#feedbackServer").show();
    }
</script>
<div id="1CR_winadmin_4_updatePFP">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('Update PFP data') ?></strong></h4>
                </div>
                <div class="card-content table-responsive togetoverlay">
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
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div style="display:none;" id="feedbackServer" class="alert bg-success alert-dismissible alert-win-success fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button>
                                <strong class="fbtext"></strong>	
                            </div>
                            <div class="table-responsive">
                                <table id="modifyPFPData" class="table table-striped display dataTable"  width="100%" cellspacing="0"
                                       data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                    <tr>
                                        <th width="10%"><?php echo __('PFP') ?></th>
                                        <th width="15%"><?php echo __('Terms of Service') ?></th>
                                        <th width="15%"><?php echo __('Privacy Policy') ?></th>
                                        <th with="10%"><?php echo __('Modality') ?></th>
                                        <th><?php echo __('Country') ?></th>
                                        <th with="5%"><?php echo __('Status') ?></th>
                                        <th><?php echo __('Send') ?></th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php
                                            //Make a array for the select
                                            $companiesSelectList = array();
                                            $companiesSelectList[0] = __('Choose One');
                                            foreach ($companies as $companyInfo) {
                                                $companiesSelectList += array($companyInfo["id"] => $companyInfo["company_name"]);
                                            }

                                            $class = "form-control blue_noborder winadminPFP";

                                            echo $this->Form->input('Ocr.id', array(
                                                'name' => 'pfp',
                                                'id' => 'ContentPlaceHolder_pfp',
                                                'label' => false,
                                                'options' => $companiesSelectList,
                                                'class' => $class,
                                                'value' => $resultUserData[0]['Ocr']['id'] /* this must be about PFP */
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorPFP";
                                            if (array_key_exists('company_pfp', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $pfpValidationErrors['company_pfp'][0] ?>
                                                </span>
                                            </div>	
                                        </td>
                                        <td>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('pfp_termsOfService', $pfpValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder pfpTermsOfService" . ' ' . $errorClass;
                                            echo $this->Form->input('Companies.company_termsUrl', array(
                                                'name' => 'termsOfService',
                                                'id' => 'ContentPlaceHolder_terms',
                                                'label' => false,
                                                'placeholder' => __('Terms Of Service URL'),
                                                'class' => $class,
                                                'value' => $investor[0]['Bill']['bill_number'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorTermsOfService";
                                            if (array_key_exists('company_termsUrl', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $pfpValidationErrors['company_termsUrl'][0] ?>
                                                </span>
                                            </div>									
                                        </td>
                                        <td>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('pfp_privacyPolicy', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder pfpPrivacyPolicy" . ' ' . $errorClass;
                                            echo $this->Form->input('Companies.company_privacityUrl', array(
                                                'name' => 'privacyPolicy',
                                                'id' => 'ContentPlaceHolder_privacyPolicy',
                                                'label' => false,
                                                'placeholder' => __('Privacy Policy URL'),
                                                'class' => $class,
                                                'value' => $investor[0]['Company']['company_privacityUrl'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorPrivacyPolicy";
                                            if (array_key_exists('pfp_privacyPolicy', $pfpValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['company_privacityUrl'][0] ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            echo $this->Form->input('Ocr.id', array(
                                                'name' => 'modality',
                                                'id' => 'ContentPlaceHolder_modality',
                                                'label' => false,
                                                'options' => $type,
                                                'class' => $class,
                                                'value' => $resultUserData[0]['Company']['????'] /* this must be about PFP */
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorModality";
                                            if (array_key_exists('pfp_modality', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $pfpValidationErrors['pfp_modality'][0] ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $class = "form-control blue_noborder pfpCountry" . ' ' . $errorClass;
                                            echo $this->Form->input('Company.company_countryName', array(
                                                'name' => 'country',
                                                'id' => 'ContentPlaceHolder_country',
                                                'label' => false,
                                                'options' => $countryData,
                                                'placeholder' => __('Country'),
                                                'class' => $class,
                                                'value' => $investor[0]['Company']['company_countryName'],
                                            ));
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('pfp_status', $pfpValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder pfpStatus" . ' ' . $errorClass;
                                            echo $this->Form->input('Company.company_OCRisActive', array(
                                                'name' => 'status',
                                                'id' => 'ContentPlaceHolder_status',
                                                'label' => false,
                                                'options' => $serviceStatus,
                                                'placeholder' => __('Status'),
                                                'class' => $class,
                                                'value' => $investor[0]['Company']['company_OCRisActive'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorStatus";
                                            if (array_key_exists('pfp_status', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $pfpValidationErrors['pfp_status'][0] ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <button id ="updateBtn" href="updateCompanyOcrData" type="button" class="btn btn-default btnWinAdmin form-control btnRounded">
                                                <i class="fa fa-upload"></i> <?php echo __('Update') ?> 
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>