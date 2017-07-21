<?php
/**
 *
 *
 * Left navigation menu of internal user portal
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date
 * @package

 
 
2016-12-02		version 0.1




*/

?>
<script src="/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script type="text/javascript">	

function successGetErrorModal(data) {
	$('#errorModalMain').replaceWith(data);
}



function errorGetErrorModal(data) {
	$('document').replaceWith(data);	
}



$(document).ready(function() {
	$(".errorBtn").on("click", function(event) {
	var link = $(this).attr( "href" );
	var params = {	
		datum: 0
	};	
	var data = jQuery.param( params );
	getServerData(link, data, successGetErrorModal, errorGetErrorModal);			
	});	
});		
</script>

    <ul class="sidebar-menu">
        <li class="header"><?php echo __('WINADMIN NAVIGATION')?></li>
        <?php 
            //This is the variable to get the sectors of the user
            //It depends on the role that the user has
            /*echo $sectorsMenu[0]['sectors_name']; 
            $sectorActual = 0;
            $sectorHasChildren = false;
            foreach ($sectorsMenu as $sector) {
                if ($sectorActual != $sector["Sector"]["sectors_father"]) {
                    if ($sectorHasChildren) {
                        echo "</ul>";
                        $sectorHasChildren = false;
                    }
                    if ($sectorActual != 0) {
                        echo "</li>";
                    }
                    echo "<li class='treeview'>";
                    $sectorActual = $sector["Sector"]["sectors_father"];
                }
                if ($sector["Sector"]["sectors_subSectorSequence"] == 1) {
                    echo "<a href='". __($sector["Sector"]["sectors_licontent"]) . "'>";
                    echo "<i class='". __($sector["Sector"]["sectors_class"])  . "'></i>";
                    echo "<span>". __($sector["Sector"]["sectors_name"]) ."</span>";
                    if ($sector["Sector"]["sectors_licontent"] == "#") {
                        $sectorHasChildren = true;
                        ?>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                            </a>
                            <ul class="treeview-menu">
                        <?php
                    }
                    else {
                        echo "</a></li>";
                    }
                }
                else {
                    echo "<li><a href='". $sector["Sector"]["sectors_licontent"] . "'>";
                    echo "<i class='". $sector["Sector"]["sectors_class"]  . "'></i>";
                    echo __($sector["Sector"]["sectors_name"]);
                    echo "</a></li>";
                }
            }*/
        ?>
    
        <li class="treeview">
            <a href="/admin/ocrs/ocrWinadminBillingPanel">
              <i class="fa fa-dashboard"></i> <span><?php echo __('Bills')?></span>
            </a>
        </li>
        <li class="treeview">
            <a href="/admin/ocrs/ocrWinadminInvestorChecking">
              <i class="fa fa-dashboard"></i> <span><?php echo __('Investor Checking')?></span>
            </a>
        </li>
        <li class="treeview">
            <a href="/admin/ocrs/ocrWinadminUpdatePfpData">
              <i class="fa fa-dashboard"></i> <span><?php echo __('Update PFP data')?></span>
            </a>
        </li>
        <?php /*<li class="treeview">
            <a href="/ocrs/ocrWinadminSoldUsers">
              <i class="fa fa-dashboard"></i> <span><?php echo __('WinAdmin - Sold Users')?></span>
              <i class="fa fa-dashboard"></i> <span><?php echo __('Sold Users')?></span>
            </a>
        </li>
        <li class="treeview">
            <a href="/ocrs/ocrWinadminTallyman">
              <i class="fa fa-dashboard"></i> <span><?php echo __('WinAdmin - Tallyman')?></span>
              <i class="fa fa-dashboard"></i> <span><?php echo __('Tallyman')?></span>
            </a>
        </li>*/ ?>
        <li class="treeview">
          <a href="/admin/users/logout"><i class="fa fa-power-off"></i> <span><?php echo __('Logout')?></a></span>
        </li>
    </ul>
