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
                            <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The number of received payments divided by total number of payments.') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Instalment Progress') ?></th>
                            <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Unpaid loan amount.') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Outstanding Principal') ?></th>
                            <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Next payment date.') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Next Payment Date') ?></th>
                            <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Indicates the number of days of payment delay.') ?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeInvestments[1] as $activeInvestment) { ?>
                            <tr>
                                <td><?php echo $activeInvestment['Investment']['investment_loanId'] ?></td>
                                <td><?php echo $activeInvestment['Investment']['investment_myInvestmentDate'] ?></td>
                                <td dataorder="<?php echo $activeInvestment['Investment']['investment_investment'] ?>"><?php echo number_format(round($activeInvestment['Investment']['investment_myInvestment'], 2), 2) . " &euro;"; ?></td>
                                <td dataorder="<?php echo $activeInvestment['Investment']['investment_nominalInterestRate'] ?>"><?php echo number_format(round($activeInvestment['Investment']['investment_nominalInterestRate']), 2) . "%" ?></td>
                                <td dataorder="<?php echo (int) explode("/", $activeInvestment['Investment']['investment_instalmentsProgress'])[0] / (int) explode("/", $activeInvestment['Investment']['investment_instalmentsProgress'])[1] ?>"><?php echo $activeInvestment['Investment']['investment_instalmentsProgress'] ?></td>
                                <td dataorder="<?php echo $activeInvestment['Investment']['investment_outstandingPrincipal'] ?>"><?php echo number_format(round($activeInvestment['Investment']['investment_outstandingPrincipal'], 2), 2) . " &euro;"; ?></td>
                                <td><?php echo $activeInvestment['Investment']['investment_nextPaymentDate']; ?></td>
                                <td> <?php
                             //   $activeInvestment['Investment']['investment_paymentStatus'] = -1;
                                    switch ($activeInvestment['Investment']['investment_paymentStatus']) {
                                        case 0:
                                            echo __("Current");
                                            break;                                          
                                        case ($activeInvestment['Investment']['investment_paymentStatus'] > 90):
                                            echo __("91+ DPD");
                                            break;
                                        case ($activeInvestment['Investment']['investment_paymentStatus'] > 60):
                                            echo __("61-90 DPD");
                                            break;
                                        case($activeInvestment['Investment']['investment_paymentStatus'] > 30):
                                            echo __("31-60 DPD");
                                            break;
                                        case($activeInvestment['Investment']['investment_paymentStatus'] > 7):
                                            echo __("8-30 DPD");
                                            break;
                                        case ($activeInvestment['Investment']['investment_paymentStatus'] > 0):
                                            echo __("1-7 DPD");
                                            break;
                                     
                                        default:
                                            echo __("N/A");
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
