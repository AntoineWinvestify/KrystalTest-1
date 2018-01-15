<?php
/*
* +-----------------------------------------------------------------------+
* | Copyright (C) 2017, https://www.winvestify.com                        |
* +-----------------------------------------------------------------------+
* | This file is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by  |
* | the Free Software Foundation; either version 2 of the License, or     |
* | (at your option) any later version.                                   |
* | This file is distributed in the hope that it will be useful           |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
* | GNU General Public License for more details.                          |
* +-----------------------------------------------------------------------+
*
*
* @author
* @version 0.1
* @date 2017-10-10
* @package
*

2017-10-10 	  version 0.1





Pending:

*/
?>

<script src="/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">



<script type="text/javascript">
    function ga_expandClick(companyName) {
        console.log("ga ExpandCompanyInvestment  expandClick " + companyName);
        ga('send', 'event', 'ExpandCompanyInvestment', 'expandClick', companyName);
    }




    function successGetDashboardData(data) {
        console.log("successGetDashboard function is called");
        console.log("All data collected");
    }


    function errorGetDashboardData(data) {
        console.log("errorGetDashboard function is called");
        console.log("Informed user about error");
    }

    /*	
     var $this = $(this),
     loadurl = $this.attr('href');
     targ = $this.attr('data-target');
     */

    $(document).ready(function () {

        $(".close").on("click", function (event) {
            notificationType = $(this).attr('data-dismiss');

            var result = 'modal'.localeCompare(notificationType);
            if (result === 0) {
                ga_removeInformationBannerClick("Empty Dashboard");
            }
            result = 'alert'.localeCompare(notificationType);
            if (result === 0) {
                ga_removeInformationBannerClick("Outdated InvestmentInfo");
            }
        });

        $(".btn-box-tool").on("click", function (event) {
            newThis = $(this).closest(".box-header").find(".img-responsive");
            var companyName = newThis.attr("alt");
            ga_expandClick(companyName);
        });

<?php
//	Create Javascript on the fly for each "company" dataTable
$baseStringDataTable = '$("#XXXX").DataTable( {
        "processing": true,
        "serverSide": false,
                "searching": false,
                "paging": false,
        "ajax": {
            "url": "/dashboards/readInvestmentData/" + $("#XXXX").attr("data-company-name"),
            "type": "POST"
        },
        "columns": [
            { "data": "loanId" },
            { "data": "date" },
            { "data": "interest" },
            { "data": "invested" },
            { "data": "amortized" },
            { "data": "profitGained" },
            { "data": "commission" },
            { "data": "duration" },
            { "data": "status" }
        ]
    });';

$index = 0;
foreach ($dashboardGlobals['investments'] as $key => $companyGlobal) {
    $dataTableString = str_replace("XXXX", "Details" . $index, $baseStringDataTable);
    echo $dataTableString;
    $index = $index + 1;
}
?>


        var link = "/dashboards/getDashboardData";
        var params = {index: 0};
        var data = jQuery.param(params);
        getServerData(link, data, successGetDashboardData, errorGetDashboardData);



            Chart.defaults.global.legend.display = false;

// Doughnut Chart 1
            var canvas1 = document.getElementById("pieChart1");
            var labelsPieChart1 = <?php echo json_encode($labelsPieChart1); ?>;
            var dataPieChart1 = <?php echo json_encode($dataPieChart1); ?>;
            var data1 = {
                labels: labelsPieChart1,
                datasets: [
                    {
                        data: dataPieChart1,
                        backgroundColor: [
                            "#5acc5a",
                            "#24e0c5",
                            "#08c4b2",
                            "#0b8599",
                            "#706bdd",
                            "#55acee"
                        ],
                        hoverBackgroundColor: [
                            "#5acc5a",
                            "#24e0c5",
                            "#08c4b2",
                            "#0b8599",
                            "#706bdd",
                            "#55acee"
                        ]
                    }]
            };
            var myPieChart1 = new Chart(canvas1, {
                type: 'doughnut',
                data: data1,
                options: {
                    title: {
                        display: true,
                        text: '<?php echo __("My Balance") ?>',
                        fontSize: 16,
                        fontFamily: "Arial"
                    }
                }
            });




// Doughnut Chart 2
            var canvas2 = document.getElementById("pieChart2");
            var labelsPieChart2 = <?php echo json_encode($labelsPieChart2) ?>;
            var dataPieChart2 = <?php echo json_encode($dataPieChart2) ?>;
            var data2 = {
                labels: labelsPieChart2,
                datasets: [
                    {
                        data: dataPieChart2,
                        backgroundColor: [
                            "#5acc5a",
                            "#24e0c5",
                            "#08c4b2",
                            "#0b8599",
                            "#706bdd",
                            "#55acee"
                        ],
                        hoverBackgroundColor: [
                            "#5acc5a",
                            "#24e0c5",
                            "#08c4b2",
                            "#0b8599",
                            "#706bdd",
                            "#55acee"
                        ]
                    }]
            };
            var myPieChart2 = new Chart(canvas2, {
                type: 'doughnut',
                data: data2,
                options: {
                    title: {
                        display: true,
                        text: '<?php echo __("Outstanding Principal") ?>',
                        fontSize: 16,
                        fontFamily: "Arial"
                    }
                }
            });



// Doughnut Chart 3
            var canvas3 = document.getElementById("pieChart3");
            var labelsPieChart3 = <?php echo json_encode($labelsPieChart3); ?>;
            var dataPieChart3 = <?php echo json_encode($dataPieChart3); ?>;
            var data3 = {
                labels: labelsPieChart3,
                datasets: [
                    {
                        data: dataPieChart3,
                        backgroundColor: [
                            "#5acc5a",
                            "#24e0c5",
                            "#08c4b2",
                            "#0b8599",
                            "#706bdd",
                            "#55acee"
                        ],
                        hoverBackgroundColor: [
                            "#5acc5a",
                            "#24e0c5",
                            "#08c4b2",
                            "#0b8599",
                            "#706bdd",
                            "#55acee"
                        ]
                    }]
            };
            var myPieChart3 = new Chart(canvas3, {
                type: 'doughnut',
                data: data3,
                options: {
                    title: {
                        display: true,
                        text: '<?php echo __("Amount Invested") ?>',
                        fontSize: 16,
                        fontFamily: "Arial"
                    }
                }
            });

        });	// function
</script>



<?php
if ($noAccountsLinked == true) {
    ?>


    <script type="text/javascript">
        $(document).ready(function () {
            console.log("Load the error modal");
            $("#myModal1").modal('show');

        });
    </script>

    <div id="myModal1" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-center"><?php echo __('EMPTY DASHBOARD') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?php echo __("We don't have any Dashboard data for you as you don't have any of your crowdlending platform
								accounts linked to your Dashboard account") ?>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-invest center-block"
                            onclick="location.href = '/investors/userProfileDataPanel';"><?php echo __('Link your Accounts') ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="box box-warning fade in alert-win-success" style="padding: 10px;">
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times" style="margin-right:5px"></i>
        </button>
        <strong><?php
            echo __("The information presented in your dashboard dates from ") . $refreshDate;
            "."
            ?>

            <?php
            if ($investmentRefreshInProgress == true) {
                echo "<br>";
                echo __("We are currently collecting the data for your updated dashboard. ");
                echo __("You will receive a notification once the process has finished.");
            }
            ?>		
        </strong>
    </div>

    <div class="dashboard">
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-win1">
                    <div class="inner" data-toggle="tooltip" data-placement="auto" title="<?php echo __('Amount of money currently invested') ?>">
                        <h3><?php echo number_format((float) $dashboardGlobals['amountInvested'] / 100, WIN_SHOW_DECIMAL, ',', '') ?>&euro;</h3>
                        <p><?php echo __('Amount Invested') ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-area-chart"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-win3" data-toggle="tooltip" data-placement="auto" title="<?php echo __('Amount of money in all your Wallets') ?>">
                    <div class="inner">
                        <h3><?php echo number_format((float) $dashboardGlobals['wallet'] / 100, WIN_SHOW_DECIMAL, ',', '') ?>&euro;</h3>
                        <p><?php echo __('Available Funds') ?></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-win5" data-toggle="tooltip" data-placement="auto" title="<?php echo __('The average profitibility of all your active investments') ?>">
                    <div class="inner">
                        <h3><?php echo number_format((float) $dashboardGlobals['meanProfitibility'] / 100, WIN_SHOW_DECIMAL, ',', '') ?>&#37;</h3>
                        <p><?php echo __('Return') ?></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-line-chart"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-win7" data-toggle="tooltip" data-placement="auto" title="<?php echo __('The number of active investments, i.e investments not yet fully recovered') ?>">
                    <div class="inner">
                        <h3><?php
                            if (!$dashboardGlobals['activeInvestments']) {
                                $dashboardGlobals['activeInvestments'] = 0;
                            }
                            echo $dashboardGlobals['activeInvestments'];
                            ?></h3>
                        <p><?php echo __('Active Investments') ?></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <!-- ./row -->


        <!--	Doughnout Charts  -->
        <div class="box box-success">
            <div class="overlay">
                <div class="fa fa-spin fa-spinner" style="color:green">	
                </div>
            </div>
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo __('Global Accounts Overview') ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>

            <!-- CHARTS -->
            <div class="box-body">
                <div class="col-md-4">
                    <?php
                    if ($pieChart1Empty == true) {
                        ?>
                        <div id="error1" style="color:gray;">
                            <h4 align="center"><strong><?php echo __('My Balance') ?></strong></h4>
                            <p align="center"><?php echo __('No data in piechart') ?></p>
                        </div>
                        <?php
                    } else {
                        ?>							
                        <canvas id="pieChart1" style="height:450px"></canvas>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-md-4">
                    <?php
                    if ($pieChart2Empty == true) {
                        ?>
                        <div id="error2" style="color:gray;">
                            <h4 align="center"><strong><?php echo __('Amount not yet recovered') ?></strong></h4>
                            <p align="center"><?php echo __('No data in piechart') ?></p>
                        </div>
                        <?php
                    } else {
                        ?>
                        <canvas id="pieChart2" style="height:450px"></canvas>
                        <?php
                    }
                    ?>
                </div>		 
                <div class="col-md-4">
                    <?php
                    if ($pieChart3Empty == true) {
                        ?>
                        <div id="error3" style="color:gray;">
                            <h4 align="center"><strong><?php echo __('Amount Invested') ?></strong></h4>
                            <p align="center"><?php echo __('No data in piechart') ?></p>
                        </div>
                        <?php
                    } else {
                        ?>
                        <canvas id="pieChart3" style="height:450px"></canvas>
                        <?php
                    }
                    ?> 
                </div>
                <?php /*<!-- CHARTS LEGEND 

                                <div class="col-md-1"></div>
                                <div class="col-md-10" id="chartLegend" style="padding-top:20px;">
                                        <ul class="chart-legend">
                                                <li>
                                                        <i class="fa fa-circle" style="color: #ffcc66;"></i> Zank
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #000099;"></i> Grow.ly
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #3366cc;"></i> Loanbook
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #33ccff;"></i> Comunitae
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #0099ff;"></i> Arboribus
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #33cc33;"></i> Circulantis
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #000099;"></i> MyTripleA
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #00ccff;"></i> eCrowdInvest
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #53c653;"></i> Finanzarel
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #ff00ff;"></i> Socilen
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #ff3300;"></i> Excelend
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #000000;"></i> Colectual
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #002699;"></i> Lendix España
                                                </li>
                                                <li>
                                                        <i class="fa fa-circle" style="color: #cccccc;"></i> Receptum
                                                </li>
                                        </ul>
                                </div>
                -->*/?>
            </div>
            <!-- /.box-body -->
        </div>

        <?php
        $index = 0;
        foreach ($dashboardGlobals['investments'] as $key => $companyGlobal) {

            for ($i = 0; $i < $companyGlobal['global']['investments']; $i++) {

                if ($companyGlobal['investments'][$i]['status'] == -1) {
                    $companyGlobal['global']['inactive'] = $companyGlobal['global']['inactive'] + 1;
                }
            }
            ?>	
            <div class="row"> 
                <div class="col-md-12">
                    <div class="box box-success collapsed-box">
                        <div class="overlay">
                            <div class="fa fa-spin fa-spinner" style="color:green">	
                            </div>
                        </div>
                        <div class="box-header with-border">
                            <div class="box-tools pull-right"> <!-- minimize + delete -->
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <img src="/<?php echo IMAGES_URL . 'logo/' . $companyGlobal['companyData']['company_logoGUID'] ?>"
                                         alt="<?php echo $companyGlobal['companyData']['company_name'] ?>" class="img-responsive center-block"/>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <div class="description-block border-right">
                                        <span class="description-text"><?php echo __('AVAILABLE FUNDS') ?></span>
                                        <h4 class="description-header">
        <?php echo number_format((float) $companyGlobal['global']['myWallet'] / 100, WIN_SHOW_DECIMAL, ',', '') . " &euro;"; ?></h4>
                                    </div>
                                    <!-- /.description-block 1 -->
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-2 col-xs-6">
                                    <div class="description-block border-right">
                                        <span class="description-text"><?php echo __('RETURN') ?></span>
                                        <h4 class="description-header">
        <?php echo number_format((float) $companyGlobal['global']['profitibility'] / 100, WIN_SHOW_DECIMAL, ',', '') . "  &#37;"; ?></h4>
                                    </div>
                                    <!-- /.description-block 2 -->
                                </div> <!-- /col -->
                                <div class="col-sm-2 col-xs-6">
                                    <div class="description-block border-right">
                                        <span class="description-text"><?php echo __('INVESTED') ?></span>
                                        <h4 class="description-header">
        <?php echo number_format((float) $companyGlobal['global']['totalInvestment'] / 100, WIN_SHOW_DECIMAL, ',', '') . " &euro;"; ?></h4>
                                    </div><!-- /.description-block 3 -->
                                </div> <!-- /col -->
                                <div class="col-sm-2 col-xs-6">
                                    <div class="description-block">
                                        <span class="description-text"><?php echo __('OUTSTANDING PRINCIPAL') ?></span>
                                        <h4 class="description-header">
        <?php echo number_format((float) $companyGlobal['global']['activeInInvestments'] / 100, WIN_SHOW_DECIMAL, ',', '') . " &euro;"; ?></h4>
                                    </div> <!-- description-box 4 --> 
                                </div> <!-- /col -->
                                <div class="col-sm-2 col-xs-6">
                                    <div class="description-block">
                                        <span class="description-text"><?php echo __('ACTIVE INVESTMENTS') ?></span>

                                        <h4 class="description-header"><?php echo $companyGlobal['global']['investments'] - $companyGlobal['global']['inactive']; ?> </h4>
                                    </div> <!-- description-box 5 --> 
                                </div> <!-- /col -->
                            </div>
                            <!-- ./row -->

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>							
                        </div> <!-- /box-header -->

                        <div class="box-body">
                            <?php
                            if ($companyGlobal['global']['investments'] != 0) {
                                ?>
                                <div class="table-responsive">

                                    <table id="Details<?php echo $index ?>" data-company-name="<?php echo $key ?>" class="investmentDetails display" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th><?php echo __('Transaction Id') ?></th>
                                                <th><?php echo __('Due Date') ?></th>
                                                <th><?php echo __('Interest') ?></th>
                                                <th><?php echo __('Invested') ?></th>
                                                <th><?php echo __('Amortized') ?></th>
                                                <th><?php echo __('Gained profit') ?></th>
                                                <th><?php echo __('Comission') ?></th>
                                                <th><?php echo __('Period of<br>Investment') ?></th>
                                                <th><?php echo __('Status') ?></th>
                                            </tr>

                                        </thead>
                                    </table>

                                </div> <?php }; ?>
                            <!-- /box-body -->
                        </div> 
                        <!-- /box -->
                    </div>	
                </div> 		
            </div>
            <!-- /.row -->	
            <?php
            $index = $index + 1;
        }
        ?>	
    </div>
    <!-- /.dashboard -->	
    <?php
}
?>
