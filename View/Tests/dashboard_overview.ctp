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
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js"></script>
<script type="text/javascript" src="/js/accounting.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">

<script>
    $(function (){
        //Click on Account Linking btn
        $(document).on("click", "#btnAccountLinking", function(){
            window.location.replace('/investors/readLinkedAccounts');
        });
        
        $(document).on("click", "#btnAccountLinkingB", function(){
            $("#btnAccountLinkingB").hide();
            $("#keyIndividualPlatforms").show();
        });
        
        $(document).on("click", "[data-toggle='tab']", function(){
            $("#btnMyInvestments").hide();
            $("#btnAccountLinkingB").hide();
            $("#dashboardMyInvestments").show();
            $("#keyIndividualPlatforms").hide();
        });
        
        $(document).on("click", "#globalOverviewTab", function(){
            $("#btnMyInvestments").show();
            $("#btnAccountLinkingB").show();
            $("#dashboardMyInvestments").hide();
            $("#keyIndividualPlatforms").hide();
        });
        
        $(document).on("click", ".logo", function(){ 
            var params = {
                id : $(this).attr("id"),
                logo : $("#logo"+id).attr("src"),
                name : $("#logo"+id).attr("alt"),
            };
            var data = jQuery.param(params);
            link = '../Dashboard2s/getDashboard2SinglePfpData/';
            getServerData(link, data, successAjax, errorAjax);
               
        });
        
        <?php //Tooltip clicks ?>
        $(".logo").hover(function() {
            id = $(this).attr("id");
            $("#showBtn").toggle();
        });
        
        <?php //Chart invoke ?>
        $(document).on("click", ".chartIcon", function() {
            id = $(this).attr("id");
            $("#chart_" + id).slideToggle("slow");
            $(this).toggleClass("active");
        });
        <?php //Bootstrap tooltips ?>
        $('[data-toggle="tooltip"]').tooltip();
        
        var birdsCanvas = document.getElementById("birdsChart");

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
    
    function successAjax(result){
       // alert("ok " + result);
       $(".dashboarGlobaldOverview").fadeOut();
       $(".ajaxResponse").html(result);
       
    }
    
    function errorAjax(result){
         //alert("not ok " + result);
    }
    
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
    canvas {
        padding: 15px;
    }
    span.active {
        font-weight: bold;
        color: #87e14b;
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
</style>
<div class="dashboarGlobaldOverview">
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
                                            <p class="headerBox"><strong><?php echo __('Total Volume')?></strong> <small><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i></small></p>
                                            <h3 class="title"> <?php echo number_format((float) $global['totalVolume'] / 100, 2, ',', '') . " &euro;"; ?></h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box1Table" class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Invested Assets')?></td>
                                                        <td class="right"><?php echo number_format((float) $global['investedAssets'] / 100, 2, ',', '') . " &euro;"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Reserved Funds')?></td>
                                                        <td class="right"><?php echo number_format((float) $global['reservedFunds'] / 100, 2, ',', '') . " &euro;"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Cash')?></td>
                                                        <td class="right"><?php echo number_format((float) $global['cash'] / 100, 2, ',', '') . " &euro;"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Cash Drag')?></td>
                                                        <td class="right"><?php echo __('25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Net Deposits')?></td>
                                                        <td class="right"><?php echo number_format((float) $global['netDeposits'] / 100, 2, ',', '') . " &euro;";?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Number of Active Investments')?></td>
                                                        <td class="right"><?php echo $global['activeInvestment'] ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><strong><?php echo __('Actual Yield')?></strong> <small><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i></small></p>
                                            <h3 class="title">12,25%</h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box2Table" class="table" width="100%" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Net Annual Total Funds Return')?></td>
                                                        <td class="right"><?php echo __('12,15%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Net Annual Return Past 12 Months')?></td>
                                                        <td class="right"><?php echo __('11,33%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Net Annual Return Past Year')?></td>
                                                        <td class="right"><?php echo __('9,22%')?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                    <tr>
                                                        <td class="left">
                                                            <i data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> 
                                                            <span class="chartIcon" id="netReturn">
                                                                <?php echo __('Net Return year-to date')?> 
                                                                <i class="ion ion-arrow-graph-up-right" style="color:black"></i>
                                                            </span>
                                                        </td>
                                                        <td class="right"><?php echo __('995,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Net return, past year')?></td>
                                                        <td class="right"><?php echo __('935,00 €')?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><strong><?php echo __('Defaulted')?></strong> <small><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i></small></p>
                                            <h3 class="title">2,25%</h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box3Table" class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Current')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('8-30 DPD')?></td>
                                                        <td class="right"><?php echo __('2,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('31-61 DPD')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('60-90 DPD')?></td>
                                                        <td class="right"><?php echo __('1,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('90 - DPD')?></td>
                                                        <td class="right"><?php echo __('1,22%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Written Off')?></td>
                                                        <td class="right"><?php echo __('3.678,00 €')?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div align="right"><small><strong><?php echo __('Last Update:')?></strong> 13:23</small></div>
                                </div>
                            </div>
                            <div class="row" style="display:none;" id="chart_netReturn">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                                    <canvas id="birdsChart" class="center-block" width="400" align="center"></canvas>  
                                </div>
                            </div>
                        </div>
                       
                        <div class="tab-pane" id="zankTab">
                            <div class="row">
                                <div class="col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
                                    <img src="/img/logo/Zank.png" class="img-responsive center-block"/>
                                </div>
                            </div>
                            <div class="row firstParagraph">
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><strong><?php echo __('Total Volume')?></strong></p>
                                            <h3 class="title">76.125,11 €</h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box1Table" class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><?php echo __('Invested Assets')?></td>
                                                        <td class="right"><?php echo __('76.125,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Reserved Funds')?></td>
                                                        <td class="right"><?php echo __('32.000,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash')?></td>
                                                        <td class="right"><?php echo __('25.252,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Cash Drag')?></td>
                                                        <td class="right"><?php echo __('25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Deposits')?></td>
                                                        <td class="right"><?php echo __('13.000,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Number of Investments')?></td>
                                                        <td class="right"><?php echo __('1254')?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><strong><?php echo __('Actual Yield')?></strong></p>
                                            <h3 class="title">12,25%</h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box2Table" class="table" width="100%" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Past 12 Months')?></td>
                                                        <td class="right"><?php echo __('12,15%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Year to date')?></td>
                                                        <td class="right"><?php echo __('11,33%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Return Past Months')?></td>
                                                        <td class="right"><?php echo __('9,22%')?></td>
                                                    </tr>
                                                    <tr><td colspan="2"><hr width="90%" class="no-padding"/></td></tr>
                                                    <tr>
                                                        <td class="left"><a class="chartIcon" id="netReturn" href="#"><?php echo __('Net Return')?> <i class="ion ion-arrow-graph-up-right" style="color:black"></i></a></td>
                                                        <td class="right"><?php echo __('995,00 €')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Net Return Past Months')?></td>
                                                        <td class="right"><?php echo __('935,00 €')?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="card card-stats">
                                        <div class="card-content">
                                            <p class="headerBox"><strong><?php echo __('Defaulted')?></strong></p>
                                            <h3 class="title">2,25%</h3>
                                        </div>
                                        <div class="card-footer">
                                            <table id="box3Table" class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="left"><?php echo __('Current')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('8-30 DPD')?></td>
                                                        <td class="right"><?php echo __('2,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('31-61 DPD')?></td>
                                                        <td class="right"><?php echo __('2,25%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('60-90 DPD')?></td>
                                                        <td class="right"><?php echo __('1,99%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('90 - DPD')?></td>
                                                        <td class="right"><?php echo __('1,22%')?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="left"><?php echo __('Written Off')?></td>
                                                        <td class="right"><?php echo __('3.678,00 €')?></td>
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
    <?php if(count($individualInfoArray) == 0) {?>
    <div class="row" id="btnAL">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <input type='button' id="btnAccountLinkingB" class='btn btn-default btnDefault pull-right' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
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
                                <th><?php echo __('Total Volume')?></th>
                                <th><?php echo __('Cash')?></th>
                                <th><i data-toggle="tooltip" data-placement="top" title="Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de text" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Explosure to platform')?></th>
                                <th><i data-toggle="tooltip" data-placement="top" title="Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de text" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Actual Yield')?></th>
                                <th><i data-toggle="tooltip" data-placement="top" title="some text to tooltip" data-toggle="tooltip" data-placement="top" title="some text to tooltip" class="ion ion-ios-information-outline" ></i> <?php echo __('Current')?></th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php //Here go pfp data
                            foreach($individualInfoArray as $individualInfo){ 
                                $total = $individualInfo['Userinvestmentdata']['userinvestmentdata_myWallet'] + $individualInfo['Userinvestmentdata']['userinvestmentdata_activeInInvestments'] + $individualInfo['Userinvestmentdata']['userinvestmentdata_reservedFunds'];
                                ?>
                            <tr>
                                <td class="logo" id="<?php echo $individualInfo['Userinvestmentdata']['linkedaccount_id'] ?>">
                                    <img id="logo<?php echo $individualInfo['Userinvestmentdata']['linkedaccount_id'] ?>" src="/img/logo/<?php echo $individualInfo['Userinvestmentdata']['pfpLogo']?>" class="img-responsive center-block platformLogo" alt="<?php echo $individualInfo['Userinvestmentdata']['pfpName']?>"/>
                                </td>
                                
                                <td><?php echo number_format((float) $total / 100, 2, ',', '') . " &euro;"?></td>
                                <td><?php echo number_format((float) $individualInfo['Userinvestmentdata']['userinvestmentdata_myWallet'] / 100, 2, ',', '') . " &euro;"?></td>
                                <td><?php echo number_format((float) ($total/$global['totalVolume'])*100, 2, ',', '') . " %"?></td>
                                <td>12,11</td>
                                <td>63,22%</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <input type='button' id="btnAccountLinking" class='btn btn-default btnDefault pull-left' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
                    <input type='button' id="btnStart" class='btn btn-default btnDefault pull-right' name='accountLinking' value='<?php echo __('Go to One Click Registration')?>' />
                    <br/><br/>
                </div>
            </div>
        </div>
    </div> <?php  } ?>
</div>
<div class = "ajaxResponse"> 
</div>
