<?php
/* 
 * One Click Registration - PFD Admin Users Selection
 * Users registered by Winvestify or consulted by PFD Admin data panel
 * Tallyman Service.
 * 
 * [2017-05-23] Principal table done
 *              [pending] subdata table con every table row
 *              [pending] Update filters
 *              [pending] Add chart
 *              [pending] Update table view
 */
?>
<style>
    td.details-control {
        cursor: pointer;
    }
</style>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script>
    $(document).ready(function(){
        "data": [
    {
      "name": "Tiger Nixon",
      "position": "System Architect",
      "salary": "$320,800",
      "start_date": "2011/04/25",
      "office": "Edinburgh",
      "extn": "5421"
    }];
        function format ( d ) {
            // `d` is the original data object for the row
            return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                '<tr>'+
                    '<td>Full name:</td>'+
                    '<td>'+d.name+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>Extension number:</td>'+
                    '<td>'+d.extn+'</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>Extra info:</td>'+
                    '<td>And any further details here (images etc)...</td>'+
                '</tr>'+
            '</table>';
        }
        // Script to control datatable child rows.
        $('#usersTable tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );

            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
        });
    });
</script>
<div id="OCR_PFDPanelB">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div id="investorFilters" class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">
                            <label class= "invisible"></label>
                            <h4 class="header1CR"><?php echo __('Search:') ?></h4>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label><?php echo __('NIF')?></label>
                            <input type="text" class="form-control blue" placeholder="Enter NIF here">
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label><?php echo __('Email')?></label>
                            <input type="text" class="form-control blue" placeholder="Enter email here">
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label><?php echo __('Telephone')?></label>
                            <input type="text" class="form-control blue" placeholder="Insert telephone here">
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            <label class= "invisible"> </label>
                            <button type="button" class="btn btn-default btn-win1 center-block"><?php echo __('Search')?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10 col-lg-offset-1">
                    <div class="table-responsive">  
                        <table id="usersTable" class="display" width="100%" cellspacing="0"
                                                                        data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th><?php echo __('Date')?></th>
                                            <th><?php echo __('Name')?></th>
                                            <th><?php echo __('Surname')?></th>
                                            <th><?php echo __('Telephone')?></th>
                                            <th><?php echo __('Email')?></th>
                                            <th><?php echo __('Status')?></th>
                                            <th><?php echo __('Action')?></th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="details-control">prueba</td>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#990000"><i class="fa fa-times"></i> <?php echo __('Incorrect')?></span></td>
                                        <td><button class="btn btn-default btn-invest"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td class="details-control"></td>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning')?></span></td>
                                        <td><button class="btn btn-default btn-invest"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td class="details-control"></td>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct')?></span></td>
                                        <td><button class="btn btn-default btn-invest"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td class="details-control"></td>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#3399ff"><i class="fa fa-thumb-tack"></i> <?php echo __('Validating')?></span></td>
                                        <td><button class="btn btn-default btn-invest"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td class="details-control"></td>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet')?></span></td>
                                        <td><button class="btn btn-default btn-invest"><?php echo __('View')?></button></td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                    <div class="row">
                        <div class="col-md-3 col-md-offset-2">dibujo</div>
                        <div class="col-md-5">
                            <div class="progress" style="height:25px;">
                                <div class="progress-bar progress-bar-aqua" role="progress-bar" aria-value="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">
                                    <span>50%</span>
                                </div>
                            </div>
                            <div class="progress" style="height:25px;">
                                <div class="progress-bar progress-bar-red" role="progress-bar" aria-value="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                    <span>20%</span>
                                </div>
                            </div>
                            <div class="progress" style="height:25px;">
                                <div class="progress-bar progress-bar-yellow" role="progress-bar" aria-value="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    <span>80%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- /.col 9 -->
    </div> <!-- /.row general -->
</div>
<script>
    function format ( d ) {
        // `d` is the original data object for the row
        return '<table>'+
            '<tr>'+
                '<td colspan="7">Full name:</td>'+
            '</tr>'+
        '</table>';
    }
    $(document).ready(function(){
        // Add event listener for opening and closing details
        $(document).on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            alert("el valor de row es "+row);
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
            
        });
    });
</script>