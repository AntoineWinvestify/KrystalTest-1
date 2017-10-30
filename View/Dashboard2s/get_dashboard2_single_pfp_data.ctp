<?php
/**
 * +--------------------------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                                              |
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
 * @author
 * @version 0.2
 * @date 2017-08-02
 * @package
 * 
 * DASHBOARD 2.0 - Dashboard overview individual data.
 * 
 * 2017-10-25 version 0.1
 * Return the view with individual info of the pfp
 * 																				[OK]



 */
?>
<?php
//P info

echo $companyInvestmentDetails[0];
/* echo print_r($activeInvestments) . HTML_ENDOFLINE;
  echo print_r($defaultedInvestments) . HTML_ENDOFLINE; */
?>
<script>
    $(function () {

        $("#defaultedInvestmentTable").DataTable();
        $("#activeInvestmentTable").DataTable();

<?php //Tooltip clicks   ?>
        $(".logo").hover(function () {
            id = $(this).attr("id");
            $("#showBtn").toggle();
        });

        $(document).on("click", ".chartIcon", function () {
            $("#chartInfo").css("display", "block");
        });
        //dismiss enlargeImg
        $(document).on("click", "#btnCloseChartInfo", function () {
            $("#chartInfo").css("display", "none");
        });


        $(document).on("click", "#backOverview", function () {
            $(".dashboarGlobaldOverview").fadeIn();
            $(".ajaxResponse").html("");
        });

        var birdsCanvas = document.getElementById("birdsChart");

        Chart.defaults.global.defaultFontFamily = "Lato";
        Chart.defaults.global.defaultFontSize = 18;

        var birdsData = {
            labels: ["Spring", "Summer", "Fall", "Winter"],
            datasets: [{
                    data: [20, 10, 40, 30],
                    backgroundColor: [
                        "rgba(255, 0, 0, 0.6)",
                        "rgba(0, 255,200, 0.6)",
                        "rgba(200, 0, 200, 0.6)",
                        "rgba(0, 255, 0, 0.6)"
                    ],
                    borderColor: "rgba(255, 255, 255, 0.8)"
                }]
        };

        var chartOptions = {
            startAngle: -Math.PI / 4,
            animation: {
                animateRotate: true
            },
            responsive: false
        };

        var polarAreaChart = new Chart(birdsCanvas, {
            type: 'polarArea',
            data: birdsData,
            options: chartOptions
        });
    });
</script>
<style>
    td {
        text-align: center;
    }
    #box1Table td, #box2Table td, #box3Table td {
        font-size: 12px;
        padding: 5px 0px !important;
    }
    th {
        text-align: center;
        font-weight: bold;
        padding-bottom: 4px !important;
    }
    td.right { 
        text-align: right; 
    }
    td.left {
        text-align: left;
    }
    .card-footer {
        border-top: 1px solid #00e64d !important;
    }
    .headerBox {
        font-size: large;
    }
    hr {
        margin: 8px !important;
        border-color: white !important;
    }
    .platformLogo {
        max-width: 150px !important;
    }
    .ion-ios-information-outline {
        color: lightslategray;
    }
    td.logo:hover {
        background-color: rgba(0,0,0,0.3);
    }
    #showBtn {
        position: absolute;
        margin-left: 80px;
    }
</style>
<div class="dashboardOverview">
    <div class="row" id="overview">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card card-nav-tabs">
                <div class="card-header" data-background-color="gray">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper">
                            <ul class="nav nav-tabs" data-tabs="tabs">
                                <li>
                                    <a id="backOverview" href="#">
                                        Global Overview
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="#<?php echo $companyInvestmentDetails[1]['name'] ?>Tab" id="globalOverviewTab" data-toggle="tab">
                                        <?php echo $companyInvestmentDetails[1]['name'] ?>
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="tab-content">
                        <div class="tab-pane active" id="<?php echo $companyInvestmentDetails[1]['name'] ?>Tab">
                            <div class="row">
                                <div class="col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
                                    <img src="<?php echo $companyInvestmentDetails[1]['logo'] ?>" class="img-responsive center-block"/>
                                </div>
                            </div>
                            <div class="row firstParagraph">
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <?php $total = round(bcadd(bcadd($companyInvestmentDetails[1][0]['Userinvestmentdata']['userinvestmentdata_activeInInvestments'], $companyInvestmentDetails[1][0]['Userinvestmentdata']['userinvestmentdata_reservedFunds'], 16), $companyInvestmentDetails[1][0]['Userinvestmentdata']['userinvestmentdata_myWallet'], 16), 2) ?>
                                            <p class="headerBox"><strong><?php echo __('Total Volume') ?></strong></p>
                                            <h3 class="title"><?php echo number_format((float) $total / 100, 2, ',', '') . " &euro;"; ?></h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box1Table" class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><?php echo __('Invested Assets') ?></td>
                                                        <td class="right"><?php echo $companyInvestmentDetails[1][0]['Userinvestmentdata']['userinvestmentdata_activeInInvestments'] . " &euro;"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Reserved Funds') ?></td>
                                                        <td class="right"><?php echo number_format((float) $companyInvestmentDetails[1][0]['Userinvestmentdata']['userinvestmentdata_reservedFunds'] / 100, 2, ',', '') . " &euro;"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash') ?></td>
                                                        <td class="right"><?php echo number_format((float) $companyInvestmentDetails[1][0]['Userinvestmentdata']['userinvestmentdata_myWallet'] / 100, 2, ',', '') . " &euro;"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash Drag') ?></td>
                                                        <td class="right"><?php echo __('25%') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Deposits') ?></td>
                                                        <td class="right"><?php echo __('13.000,00 €') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Number of Investments') ?></td>
                                                        <td class="right"><?php echo $companyInvestmentDetails[1][0]['Userinvestmentdata']['userinvestmentdata_investments'] ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><strong><?php echo __('Actual Yield') ?></strong></p>
                                            <h3 class="title">12,25%</h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box2Table" class="table" width="100%" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Past 12 Months') ?></td>
                                                        <td class="right"><?php echo __('12,15%') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Year to date') ?></td>
                                                        <td class="right"><?php echo __('11,33%') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Past Months') ?></td>
                                                        <td class="right"><?php echo __('9,22%') ?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                    <tr>
                                                        <td class="left"><a class="chartIcon" id="netReturn" href="#"><?php echo __('Net Return') ?> <i class="ion ion-arrow-graph-up-right" style="color:black"></i></a></td>
                                                        <td class="right"><?php echo __('995,00 €') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Return Past Months') ?></td>
                                                        <td class="right"><?php echo __('935,00 €') ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><strong><?php echo __('Defaulted') ?></strong></p>
                                            <h3 class="title">8,45%</h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box3Table" class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><?php echo __('Current') ?></td>
                                                        <td class="right"><?php echo __('91,55%') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('8-30 DPD') ?></td>
                                                        <td class="right"><?php echo __('2,99%') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('31-61 DPD') ?></td>
                                                        <td class="right"><?php echo __('2,25%') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('60-90 DPD') ?></td>
                                                        <td class="right"><?php echo __('1,99%') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('90 - DPD') ?></td>
                                                        <td class="right"><?php echo __('1,22%') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Written Off') ?></td>
                                                        <td class="right"><?php echo __('3.678,00 €') ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="investments">
    <div class="row" id="overview">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card card-nav-tabs">
                <div class="card-header" data-background-color="gray">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper">
                            <ul class="nav nav-tabs" data-tabs="tabs">
                                <li class="active">
                                    <a href="#defaultedInvestments" id="defaultedTab" data-toggle="tab">
                                        Defaulted
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#activeInvestments" id="activeTab" data-toggle="tab">
                                        Active
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="tab-content">
                        <div class="tab-pane active" id="defaultedTab">
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
                                                    <th><?php echo __('Installment Progress') ?></th>
                                                    <th><?php echo __('Outstadning Principal') ?></th>
                                                    <th><?php echo __('Term') ?></th>
                                                    <th><?php echo __('Status') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($defaultedInvestments as $defaultedInvestment) { ?>
                                                    <tr>
                                                        <td><?php echo $defaultedInvestment['Investment']['investment_loanId'] ?></td>
                                                        <td><?php echo $defaultedInvestment['Investment']['investment_investmentDate'] ?></td>
                                                        <td dataorder="<?php echo $defaultedInvestment['Investment']['investment_investment'] ?>"><?php echo number_format((float)$defaultedInvestment['Investment']['investment_investment'] / 100, 2, ',', '') . " &euro;"; ?></td>
                                                        <td dataorder="<?php echo $defaultedInvestment['Investment']['investment_nominalInterestRate'] ?>"><?php echo number_format((float) ($defaultedInvestment['Investment']['investment_nominalInterestRate'])/100, 2, ',', '') . " %" ?></td>
                                                        <td dataorder="<?php echo $defaultedInvestment['Investment']['investment_paymentsDone']/$defaultedInvestment['Investment']['investment_numberOfInstalments'] ?>"><?php echo $defaultedInvestment['Investment']['investment_paymentsDone'] . "/" . $defaultedInvestment['Investment']['investment_numberOfInstalments']?></td>
                                                        <td>Outstanding</td>
                                                        <td>Term</td>
                                                        <td><?php /*
                                                        switch ($defaultedInvestment['Investment']['']){
                                                            case 2:
                                                                echo "1-7 days delay";
                                                                break;
                                                            case 3:
                                                                echo "8-30 days delay";
                                                                break;
                                                            case 4:
                                                                echo "31-60 days delay";
                                                                break;
                                                            case 5:
                                                                echo "61-90 days delay";
                                                                break;
                                                            case 6:
                                                                echo "91+ days delay";
                                                                break;
                                                        }*/ ?>
                                                        </td>

                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="activeTab">
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
                                                    <th><?php echo __('Installment Progress') ?></th>
                                                    <th><?php echo __('Outstadning Principal') ?></th>
                                                    <th><?php echo __('Term') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($activeInvestments as $activeInvestment) { ?>
                                                    <tr>
                                                        <td><?php echo $activeInvestment['Investment']['investment_loanId'] ?></td>
                                                        <td><?php echo $activeInvestment['Investment']['investment_investmentDate'] ?></td>
                                                        <td dataorder="<?php echo $activeInvestment['Investment']['investment_investment'] ?>"><?php echo round($activeInvestment['Investment']['investment_investment'], 2) . " &euro;"; ?></td>
                                                        <td dataorder="<?php echo $activeInvestment['Investment']['investment_nominalInterestRate'] ?>"><?php echo round($activeInvestment['Investment']['investment_nominalInterestRate']) . " %" ?></td>
                                                        <td dataorder="<?php echo $activeInvestment['Investment']['investment_paymentsDone']/$activeInvestment['Investment']['investment_numberOfInstalments'] ?>"><?php echo $activeInvestment['Investment']['investment_paymentsDone'] . "/" . $activeInvestment['Investment']['investment_numberOfInstalments']?></td>
                                                        <td>Outstanding</td>
                                                        <td>Term</td>
                                                        <td>Status</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>