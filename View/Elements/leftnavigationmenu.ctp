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
            <li class="header">
                <strong><?php echo __('Investor Menu')?></strong>
            </li>
            <?php
            
            //The names are on an array with a tree structure like the database
            //We do this to have the names on the PO file
            $sectorsName = [
                [__("Dashboard")],
                [__("Global Marketplace"), __("My Marketplace") ],
                [__("Personal Data")],
                [__("Link Account")],
                [__("One Click Registration")],
                [__("New Users")],
                [__("Bills")],
                [__("Tallyman")],
                [__("Bills")],
                [__("Investor Checking")],
                [__("Logout")]
            ];
            
            
            //echo $sectorsMenu[0]['sectors_name'];
            //This is the variable to get the sectors of the user
            //It depends on the role that the user has
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
                    $name_col = $sector["Sector"]["sectors_father"]-1;
                    $name_row = $sector["Sector"]["sectors_subSectorSequence"]-1;
                    echo "<a href='". __($sector["Sector"]["sectors_licontent"]) . "'>";
                    echo "<i class='". __($sector["Sector"]["sectors_class"])  . "'></i>";
                    echo "<span>". $sectorsName[$name_col][$name_row] ."</span>";
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
                    $name_col = $sector["Sector"]["sectors_father"]-1;
                    $name_row = $sector["Sector"]["sectors_subSectorSequence"]-1;
                    echo "<li><a href='". $sector["Sector"]["sectors_licontent"] . "'>";
                    echo "<i class='". $sector["Sector"]["sectors_class"]  . "'></i>";
                    echo $sectorsName[$name_col][$name_row];
                    echo "</a></li>";
                }
            }
        /*
        ?>
	  <li class="treeview">
      <a href="/dashboards/getDashboardData">
        <i class="fa fa-dashboard"></i> <span>
            <?php 
            $i = 0;
            
            echo __('Dashboard');
            ?>
        </span>
      </a>
    </li>
       <li class="treeview">
          <a href="#">
            <i class="fa fa-globe"></i>
            <span><?php echo __('Global Marketplace')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="/marketplaces/showMarketPlace"><i class="fa fa-circle-o"></i> <?php echo __('My Marketplace')?></a></li>
          </ul>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user-secret"></i> <span><?php echo __('Link Account')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="/investors/userProfileDataPanel"><i class="fa fa-circle-o"></i> <?php echo __('Personal Data')?></a></li>
            <li><a href="/investors/editUserProfileData"><i class="fa fa-circle-o"></i> <?php echo __('Personal Data_No ajax')?></a></li>
            <li><a href="/investors/readLinkedAccounts"><i class="fa fa-circle-o"></i> <?php echo __('Link accounts_No ajax')?></a></li>
          </ul>
        </li>
        <li class="treeview">
            <a href="/ocrs/ocrInvestorView">
              <i class="fa fa-dashboard"></i> <span><?php echo __('One Click Registration')?></span>
            </a>
        </li>
        <li class="treeview">
          <a href="/users/logout"><i class="fa fa-power-off"></i> <span><?php echo __('Logout')?></a></span>
        </li>
        <li class="treeview">*/?>
    </ul>