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
 * @date 2017-07-31
 * @package
 * 
 * 
 * Winadmin Excel data to 100 percent Panel.
 * 
 * [2017-07-31] version 0.1
 * File creation
 * 
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
<script src="/plugins/daterangepicker/moment.min.js"></script>
<script src="/plugins/daterangepicker/daterangepicker.js"></script>
<style>
    .togetoverlay .overlay  {
        z-index: 50;
        background: rgba(255, 255, 255, 0);
        border-radius: 3px;
    }
    .togetoverlay .overlay > .fa {
        font-size: 50px;
    }
    .input-group-addon, #ContentPlaceholder_daterange {
        border: none;
    }
</style>

<script>
    $(document).ready(function(){
        //Date range picker default empty & updated value after applying
        $('#ContentPlaceholder_daterange').daterangepicker({
                locale: {
                cancelLabel: 'Clear'
            }
        });
        //Update initial value to empty
        $("#ContentPlaceholder_daterange").val(" ");
        //Update start & end hidden input
        $("#ContentPlaceholder_daterange").change(function(){
            $("#dateRangePickerStart").val($("#ContentPlaceholder_daterange").data('daterangepicker').startDate.format('DD/MM/YYYY'));
            $("#dateRangePickerEnd").val($("#ContentPlaceholder_daterange").data('daterangepicker').endDate.format('DD/MM/YYYY'));
         });
         //Update value to empty at clear btn to not to select date on input
         $(document).on("click", ".cancelBtn", function(){
             $("#dateRangePickerStart").val("");
             $("#dateRangePickerEnd").val("");
         });
         
        //Validation form
        $(document).on("click", "#generateBtn", function () {
            if ((result = app.visual.checkFormWinadminGenerateExcel()) === true) {
                
                
                //getserverdata!!!!
               params = {
                    daterange: $("#ContentPlaceHolder_daterange").val(),
                    state: $("#ContentPlaceHolder_state").val(),
                    country: $("#ContentPlaceHolder_country").val(),
                    pfp: $("#ContentPlaceHolder_pfp").val(),
                    freeSearch: $("#ContentPlaceHolder_freeSearch").val(),
                };
                link = $("#generateBtn").attr('href');
                var data = jQuery.param(params);
                getServerData(link, data, success, error);
            }
        });
    });
    
    
    function success(){}
    function error(){}
</script>
<div id="winAdmin_100PercentData">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><?php echo __('Excel Generator') ?></h4>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div>
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <p><?php
                                echo __('Text about 100% data on excel document')
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    
                    <div class="row">
                        
                        
                        <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                    <label><?php echo __('Date range:')?></label>
                                    <?php 
                                    $errorClass = "";
                                    if (array_key_exists('generateExcel_dates', $generateExcelErrors)) {
                                        $errorClass = "redBorder";
                                    }
                                    $classOne = "blue_noborder generateExcelState generateExcelGeneral" . ' ' . $errorClass;
                                    $errorClassesText = "errorInputMessage ErrorDates";
                                    if (array_key_exists('generateExcel_date', $generateExcelErrors)) {
                                        $errorClassesText .= " " . "actived";
                                    }
                                    ?>
                                    <div class="input-group <?php echo $classOne ?>">
                                      <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                      </div>
                                      <input type="text" class="form-control pull-right" id="ContentPlaceholder_daterange">
                                      <input type="hidden" id="dateRangePickerStart"/>
                                      <input type="hidden" id="dateRangePickerEnd"/>
                                    </div>
                                    <div class="<?php echo $errorClassesText ?>">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage">
                                            <?php echo $generateExcelErrors['generateExcel_dates'][0] ?>
                                        </span>
                                    </div>
                                    <!-- /.input group -->
                                    </div>
                                </div>
                                
                                
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <label><?php echo __('State')?></label>
                                    <?php
                                    $errorClass = "";
                                    if (array_key_exists('generateExcel_state', $generateExcelErrors)) {
                                        $errorClass = "redBorder";
                                    }
                                    $class = "form-control blue_noborder generateExcelState generateExcelGeneral" . ' ' . $errorClass;
                                    echo $this->Form->input('Marketplace.marketplace_status', array(
                                        'name' => 'status',
                                        'id' => 'ContentPlaceHolder_state',
                                        'label' => false,
                                        'options' => $status,
                                        'placeholder' => __('Status'),
                                        'class' => $class,
                                    ));
                                    $errorClassesText = "errorInputMessage ErrorState";
                                    if (array_key_exists('generateExcel_state', $generateExcelErrors)) {
                                        $errorClassesText .= " " . "actived";
                                    }
                                    ?>
                                    <div class="<?php echo $errorClassesText ?>">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage">
                                            <?php echo $generateExcelErrors['generateExcel_state'][0] ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <label><?php echo __('Country')?></label>
                                    <?php
                                    $errorClass = "";
                                    if (array_key_exists('generateExcel_country', $generateExcelErrors)) {
                                        $errorClass = "redBorder";
                                    }
                                    $class = "form-control blue_noborder generateExcelCountry generateExcelGeneral" . ' ' . $errorClass;
                                    echo $this->Form->input('Company.company_countryName', array(
                                        'name' => 'country',
                                        'id' => 'ContentPlaceHolder_country',
                                        'label' => false,
                                        'options' => $countryData,
                                        'placeholder' => __('Country'),
                                        'class' => $class,
                                    ));
                                    $errorClassesText = "errorInputMessage ErrorCountry";
                                    if (array_key_exists('generateExcel_country', $generateExcelErrors)) {
                                        $errorClassesText .= " " . "actived";
                                    }
                                    ?>
                                    <div class="<?php echo $errorClassesText ?>">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage">
                                            <?php echo $generateExcelErrors['generateExcel_country'][0] ?>
                                        </span>
                                    </div>
                                </div>
                                
                                
                                
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <label><?php echo __('PFP')?></label>
                                    <?php
                                    //Make a array for the select
                                    $companiesSelectList = array();
                                    $companiesSelectList[0] = __('Choose One');
                                    foreach ($companies as $companyInfo) {
                                        $companiesSelectList += array($companyInfo["id"] => $companyInfo["company_name"]);
                                    }
                                    $errorClass = "";
                                    if (array_key_exists('generateExcel_pfp', $generateExcelErrors)) {
                                        $errorClass = "redBorder";
                                    }
                                    $class = "form-control blue_noborder generateExcelPFP generateExcelGeneral"  . ' ' . $errorClass;

                                    echo $this->Form->input('Ocr.id', array(
                                        'name' => 'pfp',
                                        'id' => 'ContentPlaceHolder_pfp',
                                        'label' => false,
                                        'options' => $companiesSelectList,
                                        'class' => $class,
                                    ));
                                    $errorClassesText = "errorInputMessage ErrorPFP";
                                    if (array_key_exists('generateExcel_pfp', $generateExcelErrors)) {
                                        $errorClassesText .= " " . "actived";
                                    }
                                    ?>
                                    <div class="<?php echo $errorClassesText ?>">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <span class="errorMessage">
                                            <?php echo $generateExcelErrors['generateExcel_pfp'][0] ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <label><?php echo __('Free Search')?></label>
                                    <?php
                                    $errorClass = "";
                                    if (array_key_exists('generateExcel_freeSearch', $generateExcelErrors)) {
                                        $errorClass = "redBorder";
                                    }
                                    $class = "form-control blue_noborder generateExcelFreeSearch generateExcelGeneral" . ' ' . $errorClass;
                                    echo $this->Form->input('marketplace_.loanId', array(
                                        'name' => 'pfp',
                                        'id' => 'ContentPlaceHolder_freeSearch',
                                        'label' => false,
                                        'type' => 'text',
                                        'class' => $class,
                                    ));
                                    $errorClassesText = "errorInputMessage ErrorFreeSearch";
                                    if (array_key_exists('generateExcel_freeSearch', $generateExcelErrors)) {
                                        $errorClassesText .= " " . "actived";
                                    }
                                    ?>
                                </div>
                                <div class="<?php echo $errorClassesText ?>">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span class="errorMessage">
                                        <?php echo $generateExcelErrors['generateExcel_freeSearch'][0] ?>
                                    </span>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-10" style="margin-top: 10px;">
                                <?php 
                                $errorClassesText = "errorInputMessage ErrorExcelGeneral";
                                if (array_key_exists('generateExcel_general', $billValidationErrors)) {
                                    $errorClassesText .= " " . "actived";
                                }
                                ?>
                                <div class="<?php echo $errorClassesText ?>">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <span class="errorMessage" id="tallymanGeneral">
                                        <?php echo $billValidationErrors['generateExcel_general'][0] ?>
                                    </span>
                                </div>
                            </div>     
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            <button id ="generateBtn" href ="/admin/ocrs/importBackupExcel/" type="button" class="btn btn-default btnWinAdmin btnRounded">
                                <?php echo __('Generate Excel') ?> 
                            </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
