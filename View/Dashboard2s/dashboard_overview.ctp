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
 * DASHBOARD 2.0 - Dashboard overview
 * 
 * [2017-08-01] version 0.1
 * Initial view
 * 
 * [2017-08-02] version 0.2
 * Added plugins
 * Added style
 * Added Js
 * Added all content
 * 
 * [2017-10-24] version 0.3
 * Added db data
 * Added ajax function for individual pfp data
 * 
 * [2017-10-27] version 0.4
 * Moved from test to dasboard2s
 * 
 * [2017-11-09] version 0.5
 * New db adaptation
 * 
 * [2017-11-13] version 0.6
 * Added Google Analytics
 * 
 * [2017-11-16] version 0.7
 * Defaulted percent fix
 * Undefined logo and name in single pfp data javascript fixed.
 * 
 * [2017-11-16] version 0.8
 * Ajax moved to js file.
 * 
 * 
 */
?>

<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
<script type="text/javascript" src="/js/accounting.min.js"></script>
<script type="text/javascript" src="/js/view/dashboard.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">

<script>
    <?php /* variable declaration */ ?>
        labelOvervieW = <?php echo $graphLabel ?>;
        dataLabelOverview = <?php echo $graph ?>;
    <?php /* Google Analytics for Dashboard 2.0 - Overview */?>
    function ga_company(idCompany, nameCompany) {
        if (typeof ga === 'function') { 
            console.log("ga 'send' 'event' 'Dashboard2'  'company' " + idCompany + nameCompany);
            ga('send', 'event', 'Dashboard2', 'company', idCompany + nameCompany);
        }
    }
    
    function ga_1CR(counter1Click) {
        if (typeof ga === 'function') { 
            console.log("ga 'send' 'event' 'Dashboard2'  '1CR' " + counter1Click);
            ga('send', 'event', 'Dashboard2', '1CR', counter1Click);
        }
    }
    
    function ga_linkAccount(counterLA) {
        if (typeof ga === 'function') { 
            console.log("ga 'send' 'event' 'Dashboard2'  'linkAccount' " + counterLA);
            ga('send', 'event', 'Dashboard2', 'linkAccount', counterLA);
        }
    }
    
    function ga_chart(idChart) {
        if (typeof ga === 'function') { 
            console.log("ga 'send' 'event' 'Dashboard2'  'chart' " + idChart);
            ga('send', 'event', 'Dashboard2', 'chart', idChart);
        }
    }
    
    $(function (){
        overviewDataJS();
        
        //Click on Account Linking btn
        $(document).on("click", "#btnAccountLinking", function(){
            counterLinkAccount = 0;
            ga_linkAccount(counterLinkAccount);
            window.location.replace('/investors/readLinkedAccounts');
        });
        
        $(document).on("click", "#btnAccountLinkingB", function(){
            counterLinkAccount = <?php echo count($individualInfoArray); ?>;
            ga_linkAccount(counterLinkAccount);
            window.location.replace('/investors/readLinkedAccounts');
        });
        
        //Click on 1CR btn
        $(document).on("click", "#btn1CR", function(){
            counter1CR = 0;
            ga_1CR(counter1CR);
            window.location.replace('/ocrs/ocrInvestorView');
        });
        
        $(document).on("click", "#btn1CRB", function(){
            counter1CR = 1;
            ga_1CR(counter1CR);
            window.location.replace('/ocrs/ocrInvestorView');
        });
        
        
        
        <?php //Tooltip clicks ?>
        $(".logo").hover(function() {
            id = $(this).attr("id");
            $("#showBtn").toggle();
        });
        
        <?php //Chart invoke ?>    
            

        polarAreaChart = graphOverview(labelOvervieW, dataLabelOverview);//Create the graph
        
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
        font-weight: 500 !important;
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
        cursor: pointer;
    }
    #showBtn {
        position: absolute;
        margin-left: 80px;
    }
    canvas {
        padding: 15px;
    }
    span.active {
        font-weight: bold;
        color: rgb(0, 230, 77);
    }
    .tooltip.top .tooltip-inner {
        border: 1px solid #87e14b;
        border-radius: 0px;
        background-color: rgba(255,255,255,1);
        color: black;
        text-align: center;
        padding: 8px 12px;
        -webkit-box-shadow: 5px 5px 0px rgba(85, 85, 85, 0.15);
        -moz-box-shadow: 5px 5px 0px rgba(85, 85, 85, 0.15);
        box-shadow: 5px 5px 0px rgba(85, 85, 85, 0.15); 
    }
    .tooltip.top .tooltip-arrow {
        background: white;
        border: 1px solid #87e14b;
        content: '';
        width: 10px;
        height: 10px;
        border-right: none;
        border-bottom: none;
        position: absolute;
        transform: rotate3d(0, 0, 1, -135deg);
    }
    
    .clickable:hover {
        cursor: pointer;
    }
</style>
<div class="dashboardGlobalOverview">
    <div class="row" id="overview">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card card-nav-tabs">
                <div class="card-header" data-background-color="gray">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper">
                            <ul class="nav nav-tabs" data-tabs="tabs">
                                <li class="active">
                                    <a href="#globalOverviewTab" id="globalOverviewTab" data-toggle="tab">
                                        Global Overview
                                        <div class="ripple-container"></div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="tab-content">
                        <div class="tab-pane active" id="globalOverviewTab">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><small><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The sum of Invested Assets and Cash. Note that due to rounding the visual result might be off with 1 cent. Internally however the system uses many decimals in order to avoid rounding errors.')?>" class="ion ion-ios-information-outline" ></i></small> <strong><?php echo __('Total Volume')?></strong></p>
                                            <h3 class="title"> <?php echo number_format( round($global['totalVolume'], 2, PHP_ROUND_HALF_UP), 2) . " &euro;"; ?></h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box1Table" class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Total nominal value of all assets held in your linked accounts.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Invested Assets')?></td>
                                                        <td class="right"><?php echo number_format( round($global['investedAssets'], 2, PHP_ROUND_HALF_UP), 2) . " &euro;"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The part of Invested Assets, which are dedicated to specific loans that are not yet issued.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Reserved Funds')?></td>
                                                        <td class="right"><?php echo number_format( round($global['reservedFunds'], 2, PHP_ROUND_HALF_UP), 2)  . " &euro;"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The total cash balance on all your linked accounts. You should use this balance to invest in assets to reduce Cash Drag.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Cash')?></td>
                                                        <td class="right"><?php echo number_format( round($global['cash'], 2, PHP_ROUND_HALF_UP), 2)  . " &euro;"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The percentage of your Total Volume, which is not invested in assets and therefore does not yield any interest currently.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Cash Drag')?></td>
                                                        <td class="right"><?php echo number_format( round( $global['cashDrag'] , 2, PHP_ROUND_HALF_UP), 2)  . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('All transfers from your bank account to all linked platforms minus the withdrawals from these platforms.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Net Deposits')?></td>
                                                        <td class="right"><?php echo number_format( round($global['netDeposits'], 2), 2)  . " &euro;";?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Number of individual loans or assets that you currently own. The higher the sum, the better diversified your portfolio is.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Active Investments')?></td>
                                                        <td class="right"><?php echo $global['activeInvestment'] ?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><small><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Actual Yield.')?>" class="ion ion-ios-information-outline" ></i></small> <strong><?php echo __('Actual Yield')?></strong></p>
                                            <h3 class="title">12,25%</h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box2Table" class="table" width="100%" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Net Annual Return - Total Funds.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('NAR Total Funds')?></td>
                                                        <td class="right"><?php echo __('12,15%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Net Annual Return.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('NAR')?></td>
                                                        <td class="right"><?php echo __('11,33%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Net Annual Return past year.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('NAR past year')?></td>
                                                        <td class="right"><?php echo __('9,22%')?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                    <tr>
                                                        <td class="left">
                                                            <i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Net Return.')?>" class="ion ion-ios-information-outline" ></i> 
                                                            <span class="chartIcon clickable" id="netReturn">
                                                                <?php echo __('Net Return')?> 
                                                                <i class="ion ion-arrow-graph-up-right" style="color:black"></i>
                                                            </span>
                                                        </td>
                                                        <td class="right"><?php echo __('3.743,82 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Your total interest and other income on all linked platforms minus fees, tax and write-offs.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Net Return, past year')?></td>
                                                        <td class="right"><?php echo __('935,00 €')?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><small><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Percentage of your total invested assets that are in status Default, i.e. more than 90 days overdue.')?>" class="ion ion-ios-information-outline" ></i></small> <strong><?php echo __('Defaulted')?></strong></p>
                                            <h3 class="title"><?php echo number_format($defaultedRange['>90'], 2) . "%"?></h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box3Table" class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The percentage of your Invested Assets that have no payment delays at all.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Current')?></td>
                                                        <td class="right"><?php echo number_format($defaultedRange['current'], 2) . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The percentage of your Invested Assets that have between 1 and 7 days of payment delay.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('1-7 DPD')?></td>
                                                        <td class="right"><?php echo number_format($defaultedRange['1-7'], 2) . "%"?></td>                                                                                                     
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The percentage of your Invested Assets that have between 8 and 30 days of payment delay.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('8-30 DPD')?></td>
                                                        <td class="right"><?php echo number_format($defaultedRange['8-30'], 2) . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The percentage of your Invested Assets that have between 31 and 60 days of payment delay.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('31-60 DPD')?></td>
                                                        <td class="right"><?php echo number_format($defaultedRange['31-60'], 2) . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The percentage of your Invested Assets that have between 61 and 90 days of payment delay.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('61-90 DPD')?></td>
                                                        <td class="right"><?php echo number_format($defaultedRange['61-90'], 2) . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The percentage of your Invested Assets that have more than 90 days of payment delay.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('91 - DPD')?></td>
                                                        <td class="right"><?php echo number_format($defaultedRange['>90'], 2) . "%"?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The total amount, which your linked platforms have so far deducted from your Invested Assets balance because of long-term non-payment by clients.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Written Off')?></td>
                                                        <td class="right"><?php echo __('81.56 €')?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display:none;" id="chart_netReturn">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                                    <canvas id="birdsChart" class="center-block" width="400" align="center"></canvas>  
                                </div>
                            </div>
                            <div class="row" style="display:none;" id="chart_netReturn">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                                    <div align="right"><small><strong><?php echo __('Last Update:')?></strong> 13:23</small></div>
                                </div>
                            </div>
                        </div>                    
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if(count($individualInfoArray) == 0) {?>
    <div class="row" id="btnAL">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <input type='button' id="btnAccountLinking" class='btn btn-default btnDefault pull-right' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
            <input type='button' id="btn1CR" class='btn btn-default btnDefault pull-left' name='1CR' value='<?php echo __('Go to One Click Registration')?>' />
        </div>
    </div> <?php } else {?>
    <div class="row" id="keyIndividualPlatforms">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="gray">
                    <h4 class="title"><?php echo __('Key Performance statistics of Individual Platforms') ?></h4>
                </div>
                <div class="card-content table-responsive">
                    <table id="keyPerformanceStatistics" class="table">
                        <thead>
                            <tr>
                                <th><?php echo __('Lending Company')?></th>
                                <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The sum of Invested Assets and Cash. Note that due to rounding the visual result might be off with 1 cent. Internally however the system uses many decimals in order to avoid rounding errors.')?>" class="ion ion-ios-information-outline"></i> <?php echo __('Total Volume')?></th>
                                <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The total cash balance on all your linked accounts. You should use this balance to invest in assets to reduce Cash Drag.')?>" class="ion ion-ios-information-outline"></i> <?php echo __('Cash')?></th>
                                <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The percentage of the total volume invested in this platform in relationship to your total invested volume.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Explosure to Platform')?></th>
                                <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('Actual Yield.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Actual Yield')?></th>
                                <th><i data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo __('The percentage of your Invested Assets that have no payment delays at all.')?>" class="ion ion-ios-information-outline" ></i> <?php echo __('Current')?></th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php //Here go pfp data
                            foreach($individualInfoArray as $individualInfo){ 
                                $total = round(bcadd(bcadd($individualInfo['Userinvestmentdata']['userinvestmentdata_outstandingPrincipal'], $individualInfo['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], 16), $individualInfo['Userinvestmentdata']['userinvestmentdata_reservedAssets'], 16) , 2, PHP_ROUND_HALF_UP);
                                ?>
                            <tr>
                                <td class="logo" href='getDashboard2SinglePfpData' id="<?php echo $individualInfo['Userinvestmentdata']['linkedaccount_id']  .  " " . $individualInfo['Userinvestmentdata']["id"] ?>" >
                                    <img id="logo<?php echo $individualInfo['Userinvestmentdata']['linkedaccount_id'] ?>" src="/img/logo/<?php echo $individualInfo['Userinvestmentdata']['pfpLogo'] ?>" class="img-responsive center-block platformLogo" alt="<?php echo $individualInfo['Userinvestmentdata']['pfpName']?>"/>
                                </td>
                                
                                <td><?php echo number_format($total, 2) . " &euro;"?></td>
                                <td><?php echo number_format(round($individualInfo['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], 2, PHP_ROUND_HALF_UP), 2) . " &euro;"?></td>
                                <td><?php echo number_format(round(bcmul(bcdiv($total, $global['totalVolume'],16), 100, 16), 2, PHP_ROUND_HALF_UP), 2) . "%"?></td>
                                <td>12,11</td>
                                <td><?php echo number_format(round($individualInfo['Userinvestmentdata']['current'], 2, PHP_ROUND_HALF_UP), 2) . "%"?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <input type='button' id="btnAccountLinkingB" class='btn btn-default btnDefault pull-left' name='accountLinkingB' value='<?php echo __('Go to Account Linking')?>' />
                    <input type='button' id="btn1CRB" class='btn btn-default btnDefault pull-right' name='1CRB' value='<?php echo __('Go to One Click Registration')?>' />
                    <br/><br/>
                </div>
            </div>
        </div>
    </div> <?php  } ?>
</div>
<div class = "ajaxResponse"> 
</div>
