<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo $activeInvestments[0];
?>
<script>
        $("#activeInvestmentTable").DataTable();
</script>
<div id="activeTab">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
            <div class="table-responsive">  
                <table id="activeInvestmentTable" class="investmentDetails table striped display" width="100%" cellspacing="0" data-page-length='25'>
                    <thead>
                        <tr>
                            <th><?php echo __('Loan Id') ?></th>
                            <th><?php echo __('Investment Date') ?></th>
                            <th><?php echo __('My Investment') ?></th>
                            <th><?php echo __('Interest Rate') ?></th>
                            <th><i data-toggle="tooltip" data-placement="top" title="<?php echo __('some text to tooltip 27') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Installment Progress') ?></th>
                            <th><i data-toggle="tooltip" data-placement="top" title="<?php echo __('some text to tooltip 28') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Outstanding Principal') ?></th>
                            <th><i data-toggle="tooltip" data-placement="top" title="<?php echo __('some text to tooltip 29') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Next Payment Date') ?></th>
                            <th><i data-toggle="tooltip" data-placement="top" title="<?php echo __('some text to tooltip 30') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeInvestments[1] as $activeInvestment) { ?>
                            <tr>
                                <td><?php echo $activeInvestment['Investment']['investment_loanId'] ?></td>
                                <td><?php echo $activeInvestment['Investment']['investment_my_InvestmentDate'] ?></td>
                                <td dataorder="<?php echo $activeInvestment['Investment']['investment_investment'] ?>"><?php echo round($activeInvestment['Investment']['investment_myInvestment'], 2) . " &euro;"; ?></td>
                                <td dataorder="<?php echo $activeInvestment['Investment']['investment_nominalInterestRate'] ?>"><?php echo round($activeInvestment['Investment']['investment_nominalInterestRate']) . "%" ?></td>
                                <td dataorder="<?php echo $activeInvestment['Investment']['investment_paymentsDone'] / $activeInvestment['Investment']['investment_numberOfInstalments'] ?>"><?php echo $activeInvestment['Investment']['investment_paymentsDone'] . "/" . $activeInvestment['Investment']['investment_numberOfInstalments'] ?></td>
                                <td dataorder="<?php echo $activeInvestment['Investment']['investment_outstandingPrincipal'] ?>"><?php echo round($activeInvestment['Investment']['investment_outstandingPrincipal'], 2) . " &euro;"; ?></td>
                                <td><?php echo $activeInvestment['Investment']['investment_nextPaymentDate']; ?></td>
                                <td> <?php
                            switch ($activeInvestment['Investment']['investment_defaultedDays']) {
                                case ($activeInvestment['Investment']['investment_defaultedDays'] > 90):
                                    echo __("91+ days delay");
                                    break;
                                case ($activeInvestment['Investment']['investment_defaultedDays'] > 60):
                                    echo __("61-90 days delay");
                                    break;
                                case($activeInvestment['Investment']['investment_defaultedDays'] > 30):
                                    echo __("31-60 days delay");
                                    break;
                                case($activeInvestment['Investment']['investment_defaultedDays'] > 7):
                                    echo __("8-30 days delay");
                                    break;
                                case ($activeInvestment['Investment']['investment_defaultedDays'] > 0):
                                    echo __("1-7 days delay");
                                    break;
                                default:
                                    echo __("Current");
                                    break;
                            }
                            ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>