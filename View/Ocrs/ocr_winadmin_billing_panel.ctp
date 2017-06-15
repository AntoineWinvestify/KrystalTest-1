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
 * 
 * 
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
</style>
<div id="1CR_winAdmin_1_billingPanel">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('WinAdmin - Update Bill') ?></strong></h4>
                    <p class="category"><?php echo __('Update bill to selected PFP') ?></p>
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
                                <table id="uploadedBills" class="table table-striped display dataTable"  width="100%" cellspacing="0"
                                                       data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                    <tr>
                                        <th width="15%"><?php echo __('PFP')?></th>
                                        <th width="10%"><?php echo __('Number')?></th>
                                        <th width="25%"><?php echo __('Concept')?></th>
                                        <th with="10%"><?php echo __('Amount')?></th>
                                        <th><?php echo __('Upload file')?></th>
                                        <th><?php echo __('Send')?></th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php 
                                                $class = "form-control blue_noborder winadminPFP";
                                                $filters = ["select PFP", "pfp1", "pfp2", "pfp3"];      
                                                echo $this->Form->input('Investor.investor_country', array(
                                                        'name'          => 'pfp',
                                                        'id'            => 'ContentPlaceHolder_pfp',
                                                        'label'         => false,
                                                        'options'       => $filters,
                                                        'class'         => $class,
                                                        'value'         => $resultUserData[0]['Investor']['investor_country'] /*this must be about PFP*/						
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
                                                'name' => 'concept',
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
                                            <button type="button" class="btn btn-default btnRounded" style="background-color:#3399ff; color:white;">
                                                <?php echo __('Upload file') ?> 
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-default btnWinAdmin btnRounded">
                                                <i class="fa fa-upload"></i> <?php echo __('Send') ?> 
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
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('WinAdmin - History of Bills') ?></strong></h4>
                    <p class="category"><?php echo __('All uploaded bills') ?></p>
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
                                <table id="uploadedBills" class="table table-striped display dataTable"  width="100%" cellspacing="0"
                                                       data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                    <tr>
                                        <th width="15%"><?php echo __('PFP')?></th>
                                        <th width="10%"><?php echo __('Number')?></th>
                                        <th width="25%"><?php echo __('Concept')?></th>
                                        <th with="10%"><?php echo __('Amount')?></th>
                                        <th><?php echo __('Status')?></th>
                                    </tr>
                                    <tr>
                                        <?php foreach(){?>
                                        <td><?php echo __('PFP name')?></td>
                                        <td><?php echo __('000000')?></td>
                                        <td><?php echo __('concept')?></td>
                                        <td align="left"><?php echo __('0.00000 €')?></td>
                                        <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Paid')?></span></td>
                                        <?php } ?>
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
