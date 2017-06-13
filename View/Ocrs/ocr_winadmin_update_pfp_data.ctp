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
<div id="1CR_winadminUpdatePFP">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('WinAdmin - Update PFP data') ?></strong></h4>
                    <p class="category"><?php echo __('Modify any PFP data') ?></p>
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
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="table-responsive">
                                <table id="modifyPFPData" class="table table-striped display dataTable"  width="100%" cellspacing="0"
                                                       data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                    <tr>
                                        <th width="10%"><?php echo __('PFP')?></th>
                                        <th width="15%"><?php echo __('Terms of Service')?></th>
                                        <th width="15%"><?php echo __('Privacy Policy')?></th>
                                        <th with="10%"><?php echo __('Modality')?></th>
                                        <th><?php echo __('Country')?></th>
                                        <th with="5%"><?php echo __('Status')?></th>
                                        <th><?php echo __('Send')?></th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php 
                                                $class = "form-control blue_noborder winadminPFP";
                                                $filters = ["select PFP", "pfp1", "pfp2", "pfp3"];      
                                                echo $this->Form->input('Ocr.id', array(
                                                        'name'          => 'pfp',
                                                        'id'            => 'ContentPlaceHolder_pfp',
                                                        'label'         => false,
                                                        'options'       => $filters,
                                                        'class'         => $class,
                                                        'value'         => $resultUserData[0]['Ocr']['id'] /*this must be about PFP*/						
                                                ));
                                            ?>
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
                                                'id' => 'ContentPlaceHolder_number',
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
                                        <td align="left">
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('pfp_modality', $pfpValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder pfpModality" . ' ' . $errorClass;
                                            echo $this->Form->input('Company.company_type', array(
                                                'name' => 'modality',
                                                'id' => 'ContentPlaceHolder_modality',
                                                'label' => false,
                                                'placeholder' => __('Modality'),
                                                'class' => $class,
                                                'value' => $investor[0]['Company']['company_type'],
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
                                            $errorClass = "";
                                            if (array_key_exists('pfp_country', $pfpValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder pfpCountry" . ' ' . $errorClass;
                                            echo $this->Form->input('Company.company_countryName', array(
                                                'name' => 'country',
                                                'id' => 'ContentPlaceHolder_country',
                                                'label' => false,
                                                'placeholder' => __('Country'),
                                                'class' => $class,
                                                'value' => $investor[0]['Company']['company_countryName'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorModality";
                                            if (array_key_exists('pfp_country', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $pfpValidationErrors['pfp_country'][0] ?>
                                                </span>
                                            </div>
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
                                            <button type="button" class="btn btn-default btn-win1 form-control">
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