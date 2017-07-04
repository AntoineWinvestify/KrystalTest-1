<?php
/*
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
* @version 0.4
* @date 2017-06-13
* @package
 * 
 * One Click Registration - PFD Billing Panel
 * Panel with all billings related to PFD
 * 
 * [2017-05-23] Completed view
 *              [pending] Update filters
 * 
 * [2017-06-13] Version 0.2
 * Added green box
 * Added style to overlay
 * Updated .blue_noborder to .blue_noborder3 (green to orange focus)
 * 
 * 
 *[2017-06-26] Version 0.3
 * Table from db
 * 
 * [2017-06-28] version 0.4
 * Added datatable javascript
 * Deleted searchfor filter (unnecessary) -> included on DataTable
 * 
 * [2017-06-30] Version 0.5
 * Added Accounting Plugin to format Money
 */
?>
<script type="text/javascript" src="/js/accounting.min.js"></script>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script>
    function formatMoney(id, amount) {
        amount = amount/100;
        var optionsAccounting = {
            symbol : " &euro;",
            decimal : ",",
            thousand: ".",
            precision : 0,
            format: "%v%s"
            };

            var bill_value = accounting.formatMoney(amount, optionsAccounting);
            $("#"+id).html(bill_value);
    }
    $(function() {
        $("#billingTable").DataTable();
    });
</script>
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
<div id="1CR_pfpadmin_2_billingPanel">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="orange">
                    <h4 class="title"><strong><?php echo __('PFPAdmin - Billing Panel') ?></strong></h4>
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
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                                    <div class="table-responsive">  
                                        <table id="billingTable" class="table table-striped dataTable display " width="100%" cellspacing="0"
                                                                                        data-order='[[ 0, "asc" ]]' data-page-length='25'>
                                                <thead>
                                                        <tr>
                                                                <th width="10%"><?php echo __('Date')?></th>
                                                                <th width="20%"><?php echo __('Bill Number')?></th>
                                                                <th><?php echo __('Concept')?></th>
                                                                <th width="10%"><?php echo __('Amount')?></th>
                                                                <th width="10%"><?php echo __('Action')?></th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($bills as $bill){?>
                                                    <tr>
                                                        <td><?php echo __(substr($bill['CompaniesFile']['created'],0,10)); ?></td>
                                                        <td><?php echo __($bill['CompaniesFile']['bill_number']); ?></td>
                                                        <td><?php echo __($bill['CompaniesFile']['bill_concept']); ?></td>
                                                        <td id="<?php echo __($bill['CompaniesFile']['id']) ?>" align="right"><script>formatMoney(<?php echo __($bill['CompaniesFile']['id']) ?>, <?php echo __($bill['CompaniesFile']['bill_amount']) ?>);</script></td>
                                                        <td>
                                                            <form action = "../files/downloadDocument/bill/<?php echo __($bill['CompaniesFile']['id']) ?>">
                                                            <button type="submit"  class="btn btn-default btnPFPAdmin btnRounded"><?php echo __('Download')?></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>                                        
                                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /.col 9 -->
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /.row general -->
</div>
