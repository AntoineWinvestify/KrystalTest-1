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
        <strong>Investor Menu</strong>
    </li>
	  <li class="treeview">
      <a href="/dashboards/getDashboardData">
        <i class="fa fa-dashboard"></i> <span><?php echo __('Dashboard')?></span>
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
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user-secret"></i> <span><?php echo __('Dashboard')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              <li><a href="/tests/showInitialPanel"><i class="fa fa-circle-o"></i> <?php echo __('Initial Panel')?></a></li>
            <li><a href="/tests/dashboardOverview"><i class="fa fa-circle-o"></i> <?php echo __('Overview')?></a></li>
            <li><a href="/tests/dashboardMyInvestments"><i class="fa fa-circle-o"></i> <?php echo __('My Investments')?></a></li>
            <li><a href="/tests/dashboardStats"><i class="fa fa-circle-o"></i> <?php echo __('Stats')?></a></li>
            <li><a href="/tests/modal"><i class="fa fa-circle-o"></i> <?php echo __('NEW MODAL')?></a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="/users/logout"><i class="fa fa-power-off"></i> <span><?php echo __('Logout')?></a></span>
        </li>
        <li class="treeview">
        </li>
	</ul>
