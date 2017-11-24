<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo $defaultedInvestments[0];
?>
<script>
    $("#defaultedInvestmentTable").DataTable();
</script>
<div id="defaultedTab">
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
                            <th><i data-toggle="tooltip" data-placement="top" title="<?php echo __('some text to tooltip 27') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Installment Progress') ?></th>
                            <th><i data-toggle="tooltip" data-placement="top" title="<?php echo __('some text to tooltip 28') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Outstanding Principal') ?></th>
                            <th><i data-toggle="tooltip" data-placement="top" title="<?php echo __('some text to tooltip 29') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Next Payment Date') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($defaultedInvestments[1] as $defaultedInvestment) { ?>
                            <tr>
                                <td><?php echo $defaultedInvestment['Investment']['investment_loanId'] ?></td>
                                <td><?php echo $defaultedInvestment['Investment']['investment_myInvestmentDate'] ?></td>
                                <td dataorder="<?php echo $defaultedInvestment['Investment']['investment_investment'] ?>"><?php echo round($defaultedInvestment['Investment']['investment_myInvestment'], 2) . " &euro;"; ?></td>
                                <td dataorder="<?php echo $defaultedInvestment['Investment']['investment_nominalInterestRate'] ?>"><?php echo round($defaultedInvestment['Investment']['investment_nominalInterestRate'], 2) . "%" ?></td>
                                <td dataorder="<?php echo $defaultedInvestment['Investment']['investment_paidInstalments'] / $defaultedInvestment['Investment']['investment_numberOfInstalments'] ?>"><?php echo $defaultedInvestment['Investment']['investment_paidInstalments'] . "/" . $defaultedInvestment['Investment']['investment_numberOfInstalments'] ?></td>
                                <td dataorder="<?php echo $defaultedInvestment['Investment']['investment_outstandingPrincipal'] ?>"><?php echo round($defaultedInvestment['Investment']['investment_outstandingPrincipal'], 2) . " &euro;"; ?></td>
                                <td><?php echo $defaultedInvestment['Investment']['investment_nextPaymentDate']; ?></td>                                                       
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>