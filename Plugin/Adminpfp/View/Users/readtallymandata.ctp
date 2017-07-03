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
* @date 2017-06-13
* @package
 * 
 * One Click Registration - PFD Admin Tallyman
 * PFP admin Tallyman service and investor profiles.
 * 
 * [2017-06-13] version 0.1
 * First view.
 * 
 * [2017-06-19] version 0.2
 * Added new table to insert Tallyman info about searched user.
 * Added plugins CSS & JS
 * 

 
 
 
 PENDING:
 * class="fa fa-long-arrow-down" for the % of invested money
 * colors of the graphs (line chart and bar chart)
 * vertical axis of line chart (multiply by 100 and name the axis)
 * vertical axis of bar chart (name of the axis)

 */
?>
<?php 
echo "1";                                     // positive result
$arrowClass[UPWARDS] = "fa fa-long-arrow-up";
$arrowClass[DOWNWARDS] = "fa fa-long-arrow-down";

?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<script src="/plugins/chartjs/Chartist.min.js"></script>
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
<script>
    $(function () {
        //chart bar
        var ctx = document.getElementById("barChart1").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_values($crowdlendingTypesShort))?>,
                datasets: [{
                    label: 'Number of Investments',
                    data: <?php echo json_encode($resultTallyman[0]['userplatformglobaldata_PFPPerTypeNorm'])?>,
                    backgroundColor: [
                            "#5acc5a",
                            "#24e0c5",
                            "#08c4b2",
                            "#0b8599",
                            "#55acee"
                        ],
                    hoverBackgroundColor: [
                            "#5acc5a",
                            "#24e0c5",
                            "#08c4b2",
                            "#0b8599",
                            "#55acee"
                    ]
                },
                {
                    label: 'Investment Amount',
                    data: <?php echo json_encode($resultTallyman[0]['userplatformglobaldata_PFPPerAmountNorm'])?>,
                    backgroundColor: [
                            "#5acc5a",
                            "#24e0c5",
                            "#08c4b2",
                            "#0b8599",
                            "#55acee"
                    ],
                    hoverBackgroundColor: [
                            "#5acc5a",
                            "#24e0c5",
                            "#08c4b2",
                            "#0b8599",
                            "#55acee"
                    ]
                }]  
         }
        });
        



var ctx = document.getElementById('lineChart1').getContext('2d');
var myChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?php echo json_encode($resultTallyman[0]['totalPortfolioHistoricalDate'])?>,
    datasets: [{
      label: 'Portfolio Invested',
      data:<?php echo json_encode($resultTallyman[0]['totalPortfolioHistorical'])?>,
 //     backgroundColor: "#2acc5a", "#9acc5a", "#5acc5a"
    }]
  }
});

     
    //chart doughnut
        var ctx = document.getElementById("pieChart1").getContext('2d');
        var myChart = new Chart(ctx, {
         type: 'doughnut',
          data: {
     labels: <?php echo json_encode($resultTallyman[0]['labelsPieChart1'])?>,
  datasets: [{
      label: 'Volume',
        backgroundColor: [
                            "#5acc5a",
                            "#55acee"
                        ],
        hoverBackgroundColor: [
                            "#1acc5a",
                            "#15acee"
                        ],
      data: <?php echo json_encode($resultTallyman[0]['dataPieChart1'])?>
    }]      
 
          }
        });


});

</script>




<div id="1CR_pfpAdmin_3_searchResult">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="orange">
                    <h4 class="title"><strong><?php echo __('Tallyman') ?></strong></h4>
                    <p class="category"><?php echo __('Tallyman service about @search_fields') ?></p>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <!--<div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div>-->
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
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="card card-stats">
                                                <div class="card-content" style="text-align: center;">
                                                    <h1><?php //echo round($resultTallyman[0]['totalPortfolio'], 1)?></h1>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="stats" style="text-align: center;">
                                                        <?php echo __('% Total cartera')?>
                                                        <strong><?php echo __('invertida')?></strong>
                                                        <div class="card-content"> 
                                                            <p class="category"><span class="text-success"><i 
                                                                        class="<?php //echo $arrowClass[$resultTallyman[0]['AtotalPortfolioTendency']] ?>"></i></span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="card">
                                                <div class="card-header card-chart" data-background-color="green">
                                                        <div class="ct-chart" id="dailySalesChart"></div>
                                                </div>
                                                <div class="card-content">
                                                    <h4 class="title"></h4>
                                                    <canvas id="lineChart1" style="height: 100px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="card card-stats">
                                                <div class="card-content">
                                                    <h1 style="text-align: center;"><?php //echo round($resultTallyman[0]['totalMyModality'],1) ?></h1>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="stats" style="text-align: center;">
                                                        <?php echo __('% Total cartera')?>
                                                        <strong><?php echo __('por modalidad')?></strong>
                                                    </div>
                                                    <div class="card-content">
                                                            <p class="category"><span class="text-success"><i 
                                                                        class="<?php //echo $arrowClass[$resultTallyman[0]['AtotalMyModalityTendency']] ?>"><?php echo $arrowClass[$resultTallyman[0]['AtotalMyModalityTendency']] ?></i></span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="card">
                                                <div class="card-header card-chart" data-background-color="blue">
                                                    <div class="ct-chart" id="emailsSubscriptionChart"></div>
                                                </div>
                                                <div class="card-content">
                                                    <h4 class="title" style="text-align: center;">
                                                        <?php echo __('A) Investments Volume')?>
                                                        <?php echo __('B) nº of investments')?>
                                                    </h4>
                                                    <canvas id="barChart1" style="height: 100px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card card-stats">
                                                <div class="card-content">			
                                                    <canvas id="pieChart1" style="height: 100px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="card card-stats">
                                                        <div class="card-content">
                                                            <!-- Number of linked accounts -->
                                                            <h1 style="text-align: center;"><?php //echo count($resultTallyman[0]['Userplatformglobaldata'])?></h1>
                                                        </div>
                                                        <div class="card-footer">
                                                            <div class="stats" style="text-align: center;">
                                                                <?php echo __('Account Linking')?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card card-stats">
                                                        <div class="card-content">
                                                            <!-- total number of platforms -->
                                                            <h1 style="text-align: center;"><?php //echo $resultTallyman[0]['investorglobaldata_totalPFPs']?></h1>
                                                        </div>
                                                        <div class="card-footer">
                                                            <div class="stats" style="text-align: center;">
                                                                <?php echo __('Total PFP')?>
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
                </div>
            </div>
        </div>
    </div> <!-- /.row general -->
</div>