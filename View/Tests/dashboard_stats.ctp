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
 * @version 0.1
 * @date 2017-08-02
 * @package
 * 
 * DASHBOARD 2.0 - Statistics view (Similar to Tallyman Service)
 *  * 
 * [2017-08-02] version 0.1
 * Initial view
 */
?>
<link type="text/css" rel="stylesheet" href="/modals/assets/css/paper-bootstrap-wizard.css"/>
<script src="/plugins/chartjs/Chart.min.js"></script>
<script>
    $(function (){
        
        //polarChart1
        var birdsCanvas = document.getElementById("birdsChart");

        Chart.defaults.global.defaultFontFamily = "Lato";
        Chart.defaults.global.defaultFontSize = 18;

        var birdsData = {
          labels: ["Spring", "Summer", "Fall", "Winter"],
          datasets: [{
            data: [1200, 1700, 800, 200],
            backgroundColor: [
              "rgba(255, 0, 0, 0.6)",
              "rgba(0, 255,200, 0.6)",
              "rgba(200, 0, 200, 0.6)",
              "rgba(0, 255, 0, 0.6)"
            ],
            borderColor: "rgba(0, 0, 0, 0.8)"
          }]
        };

        var chartOptions = {
          startAngle: -Math.PI / 4,
          legend: {
            position: 'left'
          },
          animation: {
            animateRotate: false
          }
        };

        var polarAreaChart = new Chart(birdsCanvas, {
          type: 'polarArea',
          data: birdsData,
          options: chartOptions
        });
    });
</script>
<style>
    
</style>
<div class="dashboardStats">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="gray">
                    <h4 class="title"><strong><?php echo __('Stats') ?></strong></h4>
                </div>
                <div class="card-content">
                    <div class='row'>
                        <div class='col-xs-12 col-sm-12 col-md-3 col-lg-3'>
                            <div class="card card-stats">
                                <div class="card-content">			
                                    <canvas id="birdsChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class='col-xs-12 col-sm-12 col-md-3 col-lg-3'>
                            <div class="card card-stats">
                                <div class="card-content">			
                                    <canvas id="polarChart2" style="height: 100px;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class='col-xs-12 col-sm-12 col-md-6 col-lg-6'>
                            <div class="card">
                                <div class="card-content">
                                    <h4 class="title"></h4>
                                    <canvas id="lineChart1" style="height: 100px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-xs-12 col-sm-12 col-md-3 col-lg-3'>
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="gray">
                                        <i class="fa fa-signal"></i>
                                </div>
                                <div class="card-content" style="text-align: center;">
                                    <p>
                                        <?php echo __('% Total cartera')?>
                                        <strong>
                                            <?php echo __('invertida')?>
                                        </strong>
                                        <i class="fa fa-exclamation-circle tooltipIcon" id="totalPortfolioTendency"></i>
                                    </p>
                                    <h1><?php echo $resultTallyman[0]['totalPortfolio_Norm'] ?>%</h1>
                                    <p align="left" class="statusError" style="display:none">
                                        <i class="<?php echo $arrowClass[$resultTallyman[0]['totalPortfolioTendency']] ?>"></i>
                                        <i class="<?php echo $arrowClass[$resultTallyman[0]['totalPortfolioTendency']] ?>"></i>
                                        <?php echo __('Text')?>
                                    </p>
                                </div>
                                <div class="card-footer" id="tooltip_totalPortfolioTendency" style="display:none">
                                    <div class="stats" style="text-align: center;">
                                        <div class="stats" style="text-align: left;">
                                                <?php echo __('The amount invested in the platform of AdminPFP/ total invested amount[ in %]')?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-xs-12 col-sm-12 col-md-3 col-lg-3'>
                            <div class="card card-stats">
                                <div class="card-header" data-background-color="gray">
                                        <i class="fa fa-signal"></i>
                                </div>
                                <div class="card-content" style="text-align: center;">
                                    <p>
                                        <?php echo __('% Total cartera')?>
                                        <strong>
                                            <?php echo __('invertida')?>
                                        </strong>
                                        <i class="fa fa-exclamation-circle tooltipIcon" id="totalPortfolioTendency"></i>
                                    </p>
                                    <h1><?php echo $resultTallyman[0]['totalPortfolio_Norm'] ?>%</h1>
                                    <p align="left" class="statusError" style="display:none">
                                        <i class="<?php echo $arrowClass[$resultTallyman[0]['totalPortfolioTendency']] ?>"></i>
                                        <i class="<?php echo $arrowClass[$resultTallyman[0]['totalPortfolioTendency']] ?>"></i>
                                        <?php echo __('Text')?>
                                    </p>
                                </div>
                                <div class="card-footer" id="tooltip_totalPortfolioTendency" style="display:none">
                                    <div class="stats" style="text-align: center;">
                                        <div class="stats" style="text-align: left;">
                                                <?php echo __('The amount invested in the platform of AdminPFP/ total invested amount[ in %]')?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-xs-12 col-sm-12 col-md-6 col-lg-6'>
                            <div class="card card-stats">
                                <div class="card-content">			
                                    <canvas id="pieChart1" style="height: 100px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>