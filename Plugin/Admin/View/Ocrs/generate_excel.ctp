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
    .input-group-addon, #excelDates {
        border: none;
    }
    .vcenter {
        float:none;
        display:inline-block;
        vertical-align:middle;
    }
    .aClass {
        display: inline-block
    }
</style>

<script>
    $(document).ready(function(){
        //Date range picker
        $('#excelDates').daterangepicker();
    });
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
                        <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 aClass">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                    <label><?php echo __('Date range:')?></label>
                                    <div class="input-group blue_noborder">
                                      <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                      </div>
                                      <input type="text" class="form-control pull-right" id="excelDates">
                                    </div>
                                    <!-- /.input group -->
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <label><?php echo __('Staus')?></label>
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
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    <label><?php echo __('Country')?></label>
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

                                        $class = "form-control blue_noborder winadminPFP";

                                        echo $this->Form->input('Ocr.id', array(
                                            'name' => 'pfp',
                                            'id' => 'ContentPlaceHolder_pfp',
                                            'label' => false,
                                            'options' => $companiesSelectList,
                                            'class' => $class,
                                            'value' => $resultUserData[0]['Ocr']['id'] /* this must be about PFP */
                                        ));
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <label><?php echo __('Free Search')?></label>
                                    <?php
                                        $class = "form-control blue_noborder winadminPFP";
                                        echo $this->Form->input('Ocr.id', array(
                                            'name' => 'pfp',
                                            'id' => 'ContentPlaceHolder_pfp',
                                            'label' => false,
                                            'type' => 'text',
                                            'class' => $class,
                                            'value' => $resultUserData[0]['Ocr']['id'] /* this must be about PFP */
                                        ));
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 aClass">
                            <button id ="generateBtn" type="button" class="btn btn-default btnWinAdmin btnRounded vcenter">
                                <?php echo __('Generate Excel') ?> 
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
