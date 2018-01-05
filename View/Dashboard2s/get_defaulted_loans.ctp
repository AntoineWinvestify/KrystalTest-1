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
                            <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The number of received payments divided by the total number of payments') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Instalment Progress') ?></th>
                            <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Unpaid loan amount.') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Outstanding Principal') ?></th>
                            <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Next repayment date.') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Next Payment Date') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($defaultedInvestments[1] as $defaultedInvestment) { ?>
                            <tr>
                                <td><?php echo $defaultedInvestment['Investment']['investment_loanId'] ?></td>
                                <td><?php echo $defaultedInvestment['Investment']['investment_myInvestmentDate'] ?></td>
                                <td dataorder="<?php echo $defaultedInvestment['Investment']['investment_investment'] ?>"><?php echo number_format(round($defaultedInvestment['Investment']['investment_myInvestment'], 2), 2) . " &euro;"; ?></td>
                                <td dataorder="<?php echo $defaultedInvestment['Investment']['investment_nominalInterestRate'] ?>"><?php echo number_format(round($defaultedInvestment['Investment']['investment_nominalInterestRate'], 2), 2) . "%" ?></td>
                                <td dataorder="<?php echo (int) explode("/", $defaultedInvestment['Investment']['investment_instalmentsProgress'])[0] / (int) explode("/", $defaultedInvestment['Investment']['investment_instalmentsProgress'])[1] ?>"><?php echo $defaultedInvestment['Investment']['investment_instalmentsProgress'] ?></td>
                                <td dataorder="<?php echo $defaultedInvestment['Investment']['investment_outstandingPrincipal'] ?>"><?php echo number_format(round($defaultedInvestment['Investment']['investment_outstandingPrincipal'], 2), 2) . " &euro;"; ?></td>
                                <td><?php echo $defaultedInvestment['Investment']['investment_nextPaymentDate']; ?></td>                                                       
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
