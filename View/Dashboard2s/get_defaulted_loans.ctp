<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo 1;
?>
<script>
    
    $("#defaultedInvestmentTable").DataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "ajaxDataTableDefaultedInvestments/"+ id,
        "aoColumns": [
            {"mData": 'Investment.investment_loanId'},
            {"mData": 'Investment.investment_nextPaymentDate'},
            {"mData": 'Investment.MyInvestmentFloat', "sType": "numeric", "mRender": function (data, type, row) {
                    return parseFloat(+(Math.round(data + "e+2") + "e-2")).toFixed(2) + ' €';
                },
            },
            {"mData": 'Investment.InterestFloat', "sType": "numeric", "mRender": function (data, type, row) {
                    return parseFloat(+(Math.round(data + "e+2") + "e-2")).toFixed(2) + ' %';
                }},
            {"mData": 'Investment.investment_instalmentsProgress'},
            {"mData": 'Investment.OutstandingFloat', "sType": "numeric", "mRender": function (data, type, row) {
                    return parseFloat(+(Math.round(data + "e+2") + "e-2")).toFixed(2) + ' €';
                }},
            {"mData": 'Investment.investment_myInvestmentDate'}
        ],
    });
</script>
<div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
            <div class="table-responsive">  
                <table id="defaultedInvestmentTable" class="investmentDetails table striped display" width="100%" cellspacing="0" data-page-length='25'>
                    <thead>
                        <tr>
                            <th><?php echo __('Loan Id') ?></th>
                            <th><?php echo __('Investment Date') ?></th>
                            <th><?php echo __('My Investment') ?></th>
                            <th><?php echo __('Interest Rate') ?></th>
                            <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The number of received payments divided by the total number of payments') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Instalment Progress') ?></th>
                            <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Unpaid loan amount.') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Outstanding Principal') ?></th>
                            <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Next repayment date.') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Next Payment Date') ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
