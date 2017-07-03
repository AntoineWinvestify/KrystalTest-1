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
 * @date 2017-05-29
 * @package
 * 
 * 
 * Investor billing panel to generate & upload bills to PFP Admin.
 * 
 * [2017-05-29] Version 0.3
 * First view.
 * 
 * [2017-06-09] Version 0.2
 * Added top datatable to collect info about Bills to upload PDF & generate Email to PFP Admin.
 * Added bottom datatable to save history of sent bills.
 * Added datatables JS & CSS
 * 
 * [2017-06-13] Version 0.3
 * Added green boxes
 * Added style to overlay
 * 
 * [2017-06-15] Version 0.4
 * Added bills table
 *
 * [2017-06-19] Version 0.5
 * Added bills table db info
 * 
 * [2017-06-22] Version 0.6
 * Added javascript validation
 * Added file error div
 * Added file btn to style the form
 * Added Select PFP filter on History of Bills --> it's necessary? datatable can sort by columns.
 * 
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
    input[type="file"] {
        display: none;
    }
</style>

<?php
//Make a array for the select
$companiesSelectList = array();
foreach ($companies as $companyInfo) {
    $companiesSelectList += array($companyInfo["id"] => $companyInfo["company_name"]);
}
?>
<script>
    $(function () {
        $(document).on("click", "#sendBill", function () {
            console.log("validate Winadmin billing data");
            <?php //Javascript validation   ?>
            if ((result = app.visual.checkFormWinadminBilling()) === true) {
                var formdatas = new FormData($("#bill")[0]);
                params = {
                    pfp: $("#ContentPlaceHolder_pfp").val(),
                    number: $("#ContentPlaceHolder_number").val(),
                    concept: $("#ContentPlaceHolder_concept").val(),
                    amount: $("#ContentPlaceHolder_amount").val(),
                    bill: formdatas
                };
                link = '../Files/upload';
                var data = jQuery.param(params);
                    $.ajax({
                        url: link,
                        dataType: 'json',
                        method: 'post',
                        data: data,
                        contentType: false,
                        processData: false,
                }).done(function (data) {

                });
            }
        });
    });
</script>
<div id="1CR_winAdmin_1_billingPanel">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('WinAdmin - Update Bill') ?></strong></h4>
                    <p class="category"><?php echo __('Update bill to selected PFP') ?></p>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <!-- <div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div> -->
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
                            <div class="alert bg-success alert-dismissible alert-win-warning fade in alert-to-fade" role="alert">
                                <strong><?php echo __("The file is too big or incorrect format.") ?></strong>
                            </div>
                            <div class="table-responsive">
                                <table id="uploadedBills" class="table table-striped display dataTable"  width="100%" cellspacing="0" data-page-length='25' rowspan='1' colspan='1'>
                                    <tr>
                                        <th><?php echo __('PFP') ?></th>
                                        <th><?php echo __('Number') ?></th>
                                        <th><?php echo __('Concept') ?></th>
                                        <th><?php echo __('Amount') ?></th>
                                        <th><?php echo __('Upload file') ?></th>
                                        <th><?php echo __('Send') ?></th>
                                    </tr>
                                    <?php echo $this->Form->create('bill', array('default' => false, 'id' => 'bill')); ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $class = "form-control blue_noborder winadminPFP";
                                            echo $this->Form->input('Companies.company_id', array(
                                                'name' => 'pfp',
                                                'id' => 'ContentPlaceHolder_pfp',
                                                'label' => false,
                                                'options' => $companiesSelectList,
                                                'class' => $class,
                                            ));
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('bill_number', $billValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder billNumber" . ' ' . $errorClass;
                                            echo $this->Form->input('Bills.bill_number', array(
                                                'name' => 'number',
                                                'id' => 'ContentPlaceHolder_number',
                                                'label' => false,
                                                'placeholder' => __('Number'),
                                                'class' => $class,
                                                'value' => $investor[0]['Bill']['bill_number'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorNumber";
                                            if (array_key_exists('bill_number', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['bill_number'][0] ?>
                                                </span>
                                            </div>									
                                        </td>
                                        <td>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('bill_concept', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder billConcept" . ' ' . $errorClass;
                                            echo $this->Form->input('Bills.bill_concept', array(
                                                'name' => 'concept',
                                                'id' => 'ContentPlaceHolder_concept',
                                                'label' => false,
                                                'placeholder' => __('Concept'),
                                                'class' => $class,
                                                'value' => $investor[0]['Bill']['bill_concept'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorConcept";
                                            if (array_key_exists('bill_concept', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['bill_number'][0] ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td align="left">
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('bill_amount', $billValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder billAmount" . ' ' . $errorClass;
                                            echo $this->Form->input('Bills.bill_amount', array(
                                                'name' => 'amount',
                                                'id' => 'ContentPlaceHolder_amount',
                                                'label' => false,
                                                'rule' => 'numeric',
                                                'placeholder' => __('Amount'),
                                                'class' => $class,
                                                'value' => $investor[0]['Bill']['bill_amount'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorAmount";
                                            if (array_key_exists('bill_amount', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['bill_amount'][0] ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<label class='btn labelFile btnRounded btnUploadFile' for='billUpload'><i class='fa fa-upload'></i> Upload bill</label>";
                                            echo $this->Form->file("bill", array('class' => 'upload', 'id' => 'billUpload'));
                                            ?>
                                        </td>
                                        <td>
                                            <button type="button" id="sendBill" class="btn btn-default btnWinAdmin btnRounded">
                                                <i class="fa fa-upload"></i> <?php echo __('Send') ?> 
                                            </button>
                                        </td>
                                    </tr>
                                    <?php echo $this->Form->end(); ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('WinAdmin - History of Bills') ?></strong></h4>
                    <p class="category"><?php echo __('All uploaded bills') ?></p>
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
                        <div id="investorFilters" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <?php 
                            $class = "form-control blue_noborder investorCountry". ' ' . $errorClass;
                            $countries = ["select PFP", "pfp1", "pfp2", "pfp3"];      
                                                                                echo $this->Form->input('Investor.investor_country', array(
                                                                                        'name'			=> 'country',
                                                                                        'id' 			=> 'ContentPlaceHolder_country',
                                                                                        'label' 		=> false,
                                                                                        'options'               => $countries,
                                                                                        'placeholder' 	=>  __('Country'),
                                                                                        'class' 		=> $class,
                                                                                        'value'			=> $resultUserData[0]['Investor']['investor_country'],						
                                                        ));
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="table-responsive">
                                <table id="uploadedBills" class="table table-striped display dataTable"  width="100%" cellspacing="0"
                                       data-order='[[ 3, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                    <tr>
                                        <th width="15%"><?php echo __('PFP') ?></th>
                                        <th><?php echo __('Number') ?></th>
                                        <th><?php echo __('Concept') ?></th>
                                        <th><?php echo __('Amount') ?></th>
                                    </tr>

                                    <?php foreach ($bills as $billsTable) { //Bills table creation ?>
                                        <tr>
                                            <td><?php echo __($billsTable['name']) ?></td>
                                            <td><?php echo __($billsTable['info']['bill_number']) ?></td>
                                            <td><?php echo __($billsTable['info']['bill_concept']) ?></td>
                                            <td align="left"><?php echo __($billsTable['info']['bill_amount']) ?></td>
                                        </tr>
                                    <?php } ?>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
