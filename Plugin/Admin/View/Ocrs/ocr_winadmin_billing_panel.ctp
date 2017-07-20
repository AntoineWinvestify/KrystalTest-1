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
 * @date 2017-05-29
 * @package
 * 
 * 
 * Investor billing panel to generate & upload bills to PFP Admin.
 * 
 * [2017-05-29] Version 0.3
 * First view.
 * 
 * [2017-06-09] Version 0.2
 * Added top datatable to collect info about Bills to upload PDF & generate Email to PFP Admin.
 * Added bottom datatable to save history of sent bills.
 * Added datatables JS & CSS
 * 
 * [2017-06-13] Version 0.3
 * Added green boxes
 * Added style to overlay
 * 
 * [2017-06-15] Version 0.4
 * Added bills table
 *
 * [2017-06-19] Version 0.5
 * Added bills table db info
 * 
 * [2017-06-22] Version 0.6
 * Added javascript validation
 * Added file error div
 * Added file btn to style the form
 * Added Select PFP filter on History of Bills --> it's necessary? datatable can sort by columns.
 * 
 * [2017-06-28] Version 0.7
 * Deleted PFP Select filter (datatable has it)
 * Added datatable select filter
 * Added Currency + Tooltip
 * 
 * [2017-06-29] Version 0.8
 * Table refresh
 * Select company bug fix
 * 
 * [2017-06-30] Version 0.9
 * Added select to datatable on all columns to filter by column.
 * Added Accounting Plugin to format Money
 */
?>
<script type="text/javascript" src="/js/accounting.min.js"></script>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<style>
    .togetoverlay .overlay  {
        z-index: 50;
        background: rgba(255, 255, 255, 0);
        border-radius: 3px;
    }
    .togetoverlay .overlay > .fa {
        font-size: 50px;
    }
    input[type="file"] {
        display: none;
    }
</style>

<script>
    function formatMoney(id, amount) {
        amount = amount / 100;
        var optionsAccounting = {
            symbol: " &euro;",
            decimal: ",",
            thousand: ".",
            precision: 2,
            format: "%v%s"
        };

        var bill_value = accounting.formatMoney(amount, optionsAccounting);
        $("#" + id).html(bill_value);
    }
    $(function () {
        //Datatables
        $("#billsHistory").DataTable({
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                        );

                                column
                                        .search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                            });

                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
            }
        });

        //tooltip
        $(document).on("click", "#tooltip", function () {
            $("#amountTooltip").toggle();
        });
        $(document).on("click", "#sendBill", function () {
            console.log("validate Winadmin billing data");
            $(".alert-to-fade").hide();

<?php //Javascript validation                  ?>
            if ((result = app.visual.checkFormWinadminBilling()) === true) {
                var formdatas = new FormData($("#bill")[0]);
                link = '/admin/files/upload';
                $.ajax({
                    url: link,
                    dataType: 'json',
                    method: 'post',
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    traditional: true
                }).done(function (data) {
                    if (data[0]) {
                        $(".feedbackText").html(data[1]);
                        $(".alert-to-fade").show();
                        $(".alert-to-fade").addClass("alert-win-success");

                        $('#selected').html("");
                        $("#bill").trigger("reset");
                        refreshTable();
                    } else {
                        $(".feedbackText").html(data[1]);
                        $(".alert-to-fade").show();
                        $(".alert-to-fade").addClass("alert-win-warning");
                       
                        $('#selected').html("");
                        $("#bill").trigger("reset");
                    }
                });
            }
        });

        $(document).on("change", "#billUpload", function () {
            $("#selected").html("<?php echo __('Document selected') ?>");
        });
    });

    function refreshTable() {
        link = "/admin/Ocrs/billsTable";
        var data = null;
        getServerData(link, data, successRefesh, errorRefresh);
    }

    function successRefesh(result) {
        $("#billsTable").html(result);
    }

    function errorRefresh(result) {
        $("#billsTable").html(result);
    }

</script>
<div id="1CR_winAdmin_3_billingPanel">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('WinAdmin - Update Bill') ?></strong></h4>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <!-- <div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div> -->
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
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="alert bg-success alert-dismissible fade in alert-to-fade" role="alert" style="display:none;">
                                <strong class = "feedbackText"></strong>
                            </div>
                            <div class="table-responsive">
                                <table id="uploadBill" class="table table-striped display dataTable"  width="100%" cellspacing="0" data-page-length='25' rowspan='1' colspan='1'>
                                    <tr>
                                        <th><?php echo __('PFP') ?></th>
                                        <th><?php echo __('Bill Number') ?></th>
                                        <th><?php echo __('Concept') ?></th>
                                        <th><?php echo __('Amount') ?> <i class="fa fa-exclamation-circle" id="tooltip"></i></th>
                                        <th><?php echo __('Currency') ?></th>
                                        <th><?php echo __('Upload file') ?></th>
                                        <th><?php echo __('Send') ?></th>
                                    </tr>
                                    <?php echo $this->Form->create('bill', array('default' => false, 'id' => 'bill')); ?>
                                    <tr>
                                        <td>
                                            <?php
                                            //Make a array for the select
                                            $companiesSelectList = array();
                                            $companiesSelectList[0] = __('Choose One');
                                            foreach ($companies as $companyInfo) {
                                                $companiesSelectList += array($companyInfo["id"] => $companyInfo["company_name"]);
                                            }

                                            $class = "form-control blue_noborder billPFP";
                                            echo $this->Form->input('Companies.company_id', array(
                                                'name' => 'pfp',
                                                'id' => 'ContentPlaceHolder_pfp',
                                                'label' => false,
                                                'options' => $companiesSelectList,
                                                'class' => $class,
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorPFP";
                                            if (array_key_exists('bill_pfp', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['bill_pfp'][0] ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('bill_number', $billValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder billNumber" . ' ' . $errorClass;
                                            echo $this->Form->input('Bills.bill_number', array(
                                                'name' => 'number',
                                                'id' => 'ContentPlaceHolder_number',
                                                'label' => false,
                                                'placeholder' => __('Number'),
                                                'class' => $class,
                                                'value' => $investor[0]['Bill']['bill_number'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorNumber";
                                            if (array_key_exists('bill_number', $investorValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['bill_number'][0] ?>
                                                </span>
                                            </div>									
                                        </td>
                                        <td>
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('bill_concept', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder billConcept" . ' ' . $errorClass;
                                            echo $this->Form->input('Bills.bill_concept', array(
                                                'name' => 'concept',
                                                'id' => 'ContentPlaceHolder_concept',
                                                'label' => false,
                                                'placeholder' => __('Concept'),
                                                'class' => $class,
                                                'value' => $investor[0]['Bill']['bill_concept'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorConcept";
                                            if (array_key_exists('bill_concept', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['bill_number'][0] ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td align="left">
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('bill_amount', $billValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue_noborder billAmount" . ' ' . $errorClass;
                                            echo $this->Form->input('Bills.bill_amount', array(
                                                'name' => 'amount',
                                                'id' => 'ContentPlaceHolder_amount',
                                                'label' => false,
                                                'placeholder' => __('Amount'),
                                                'class' => $class,
                                                'value' => $investor[0]['Bill']['bill_amount'],
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorAmount";
                                            if (array_key_exists('bill_amount', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['bill_amount'][0] ?>
                                                </span>
                                            </div>
                                            <div id="amountTooltip" style="display:none;"><small><?php echo __('The Amount value must be formatted with 2 decimal digits. (Ex: 1111,10)') ?></small></div>
                                        </td>
                                        <td>
                                            <?php
                                            $class = "form-control blue_noborder billCurrency";
                                            echo $this->Form->input('Companies.company_id', array(
                                                'name' => 'currency',
                                                'id' => 'ContentPlaceHolder_currency',
                                                'label' => false,
                                                'options' => $currencyName,
                                                'class' => $class,
                                            ));
                                            $errorClassesText = "errorInputMessage ErrorCurrency";
                                            if (array_key_exists('bill_currency', $billValidationErrors)) {
                                                $errorClassesText .= " " . "actived";
                                            }
                                            ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span class="errorMessage">
                                                    <?php echo $billValidationErrors['bill_currency'][0] ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            echo "<label class='btn labelFile btnRounded btnUploadFile' for='billUpload'><i class='fa fa-upload'></i> Upload bill</label>";
                                            echo $this->Form->file("bill", array('class' => 'upload', 'id' => 'billUpload'));
                                            ?>
                                            <strong id = 'selected' ></strong>
                                        </td>
                                        <td>
                                            <button type="button" id="sendBill" class="btn btn-default btnWinAdmin btnRounded">
                                                <i class="fa fa-upload"></i> <?php echo __('Send') ?> 
                                            </button>                                        </td>

                                    </tr>
                                    <?php echo $this->Form->end(); ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title"><strong><?php echo __('Bills History') ?></strong></h4>
                </div>
                <div class="card-content table-responsive togetoverlay">
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
                    <div class="row" >
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="table-responsive" id="billsTable">
                                <table id="billsHistory" class="table table-striped display dataTable" width="100%" cellspacing="0"
                                       data-order='[[ 1, "desc" ]]' data-page-length='10' rowspan='1' colspan='1'>
                                    <thead>
                                        <tr>
                                            <th><?php echo __('PFP') ?></th>
                                            <th><?php echo __('Date') ?></th>
                                            <th><?php echo __('Bill Number') ?></th>
                                            <th><?php echo __('Concept') ?></th>
                                            <th><?php echo __('Amount') ?></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th><?php echo __('PFP') ?></th>
                                            <th><?php echo __('Date') ?></th>
                                            <th><?php echo __('Bill Number') ?></th>
                                            <th><?php echo __('Concept') ?></th>
                                            <th><?php echo __('Amount') ?></th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php foreach ($bills as $billsTable) {//Bills table creation    ?>                                      
                                            <tr>
                                                <td><?php echo __($billsTable['Pfpname']) ?></td>
                                                <td><?php echo __(substr($billsTable['info']['created'], 0, 10)) ?></td>
                                                <td><?php echo __($billsTable['info']['bill_number']) ?></td>
                                                <td><?php echo __($billsTable['info']['bill_concept']) ?></td>
                                                <td id="<?php echo __($billsTable['info']['id']) ?>" align="right"><script>formatMoney(<?php echo __($billsTable['info']['id']) ?>, <?php echo __($billsTable['info']['bill_amount']) ?>);</script></td>
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
