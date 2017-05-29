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
* @version 0.1
* @date 2017-05-29
* @package
 * 
 * 
 * 
 * 
 * [2017-05-29] Version 0.1
 * Added all tabs & views to 1CR.
*/

?>

<script type="text/javascript">
$(document).ready(function() {

    // Read the data for the first tab (platformselection) when page is loaded	
    var targ = $("#ocrTabs").find("a:first").attr('data-target');
    var loadurl = $("#ocrTabs").find("a:first").attr('href');
    $.get(loadurl, function(data) {
        $(targ).html(data);
    });

    $('[data-toggle="tooltip"]').tooltip();
    
    $('[data-toggle="tabajax"]').click(function(e) {
        var $this = $(this),
        loadurl = $this.attr('href');
        targ = $this.attr('data-target');
        console.log("data-toggle: loadurl = " + loadurl);
        console.log("data-toggle: targ = " + targ);

        $.get(loadurl, function(data) {
            $(targ).html(data);
        });

        $this.tab('show');
        return false;
    });
});
</script>
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <div class="box box-success">
            <div class="overlay" style="display:none">
                <div class="fa fa-spinner fa-spin" style="color:green;">	
                </div>
            </div>
            <div class="box-header with-border">
                <div class="box-title">
                    <ul id="ocrTabs" class="nav nav-tabs">
                        <li class="active">
                                <a href = "/ocrs/ocrInvestorPlatformSelection" id="ocr1"  rel="tooltip" data-target="#OCR1Tab" data-toggle="tabajax"><h4><?php echo __('Investor I')?></h4></a>
                        </li>
                        <li>
                                <a href = "/ocrs/ocrInvestorDataPanel" id="ocr2"  rel="tooltip" data-target="#OCR2Tab" data-toggle="tabajax"><h4><?php echo __('Investor II')?></h4></a>
                        </li>
                        <li>
                                <a href = "/ocrs/ocrPfpBillingPanel" id="ocr3"  rel="tooltip" data-target="#OCR3Tab" data-toggle="tabajax"><h4><?php echo __('PFP Admin I')?></h4></a>
                        </li>
                        <li>
                                <a href = "/ocrs/ocrPfpUsersPanel" id="ocr4"  rel="tooltip" data-target="#OCR4Tab" data-toggle="tabajax"><h4><?php echo __('PFP Admin II')?></h4></a>
                        </li>
                        <li>
                                <a href = "/ocrs/ocrWinadminInvestorChecking" id="ocr5"  rel="tooltip" data-target="#OCR5Tab" data-toggle="tabajax"><h4><?php echo __('WinAdmin I')?></h4></a>
                        </li>
                        <li>
                                <a href = "/ocrs/ocrWinadminBillingPanel" id="ocr6"  rel="tooltip" data-target="#OCR6Tab" data-toggle="tabajax"><h4><?php echo __('WinAdmin II')?></h4></a>
                        </li>
                    </ul>
                </div>
                <div id = "myTabContent" class = "tab-content">
<!-- ------------------------------------------------------------------------------------------------------------- -->
                    <div class = "tab-pane fade" id="OCR1Tab">
                    <!-- here goes the content of the "Investor Panel I" tab   -->

                    </div>	
                    <!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                    <div class = "tab-pane fade" id="OCR2Tab">
                    <!-- here goes the content of the "Investor Panel II" tab   -->

                    </div>	
                    <!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                    <div class = "tab-pane fade" id="OCR3Tab">
                    <!-- here goes the content of the "PFD Admin I" tab   -->

                    </div>	
                    <!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                    <div class = "tab-pane fade" id="OCR4Tab">
                    <!-- here goes the content of the "PFD Admin II" tab   -->

                    </div>	
                    <!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                    <div class = "tab-pane fade" id="OCR5Tab">
                    <!-- here goes the content of the "WinAdmin I" tab   -->

                    </div>	
                    <!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                    <div class = "tab-pane fade" id="OCR6Tab">
                    <!-- here goes the content of the "WinAdmin II" tab   -->

                    </div>	
                    <!-- /.tab-pane -->
<!-- ------------------------------------------------------------------------------------------------------------- -->
                </div>
            </div>	
        </div>		
    </div>
</div>
