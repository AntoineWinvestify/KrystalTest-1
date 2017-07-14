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
    <li class="header">MAIN NAVIGATION</li>
        <?php  
        /*<li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i>
            <span class="disabled" style="opacity:0.5"><?php echo __('Investor Comunity')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="disabled" style="opacity:0.5"><a href="pages/layout/top-nav.html"><i class="fa fa-circle-o"></i> Mi perfil</a></li>
            <li class="disabled" style="opacity:0.5"><a href="pages/layout/boxed.html"><i class="fa fa-circle-o"></i> Mi muro</a></li>
            <li class="disabled" style="opacity:0.5"><a href="pages/layout/fixed.html"><i class="fa fa-circle-o"></i> Mis Notificaciones</a></li>
          </ul>
        </li>*/
        ?>
       <li class="treeview">

            <?php  
            /*<li class="disabled" style="opacity:0.5"><a href="pages/charts/morris.html"><i class="fa fa-circle-o"></i> <?php echo __('Spain')?></a></li>
            <li class="disabled" style="opacity:0.5"><a href="pages/charts/flot.html"><i class="fa fa-circle-o"></i> <?php echo __('France')?></a></li>
            <li class="disabled" style="opacity:0.5"><a href="pages/charts/inline.html"><i class="fa fa-circle-o"></i> <?php echo __('Germany')?></a></li>-->
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-pie-chart"></i>
            <span class="disabled" style="opacity:0.5"><?php echo __('Loan Control')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
      </a>
          <ul class="treeview-menu">
            <li class="disabled" style="opacity:0.5"><a href="pages/charts/chartjs.html"><i class="fa fa-circle-o"></i> Mis Incidencias</a></li>
            <li class="disabled" style="opacity:0.5"><a href="pages/charts/morris.html"><i class="fa fa-circle-o"></i> Buscador de incidencias</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-bank"></i>
            <span class="disabled" style="opacity:0.5"><?php echo __('Companies')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="disabled" style="opacity:0.5"><a href="pages/UI/general.html"><i class="fa fa-circle-o"></i> General</a></li>
            <li class="disabled" style="opacity:0.5"><a href="pages/UI/icons.html"><i class="fa fa-circle-o"></i> Icons</a></li>
            <li class="disabled" style="opacity:0.5"><a href="pages/UI/buttons.html"><i class="fa fa-circle-o"></i> Buttons</a></li>
            <li class="disabled" style="opacity:0.5"><a href="pages/UI/sliders.html"><i class="fa fa-circle-o"></i> Sliders</a></li>
            <li class="disabled" style="opacity:0.5"><a href="pages/UI/timeline.html"><i class="fa fa-circle-o"></i> Timeline</a></li>
            <li class="disabled" style="opacity:0.5"><a href="pages/UI/modals.html"><i class="fa fa-circle-o"></i> Modals</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-edit"></i> <span><?php echo __('Suggestion Box')?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="pages/forms/general.html"><i class="fa fa-circle-o"></i> General Elements</a></li>
            <li><a href="pages/forms/advanced.html"><i class="fa fa-circle-o"></i> Advanced Elements</a></li>
            <li><a href="pages/forms/editors.html"><i class="fa fa-circle-o"></i> Editors</a></li>
          </ul>
        </li>*/?>

        
     <li class="header">
            <strong>PFP Admin Menu</strong>
        </li>
        <li class="treeview">
            <a href="/adminpfp/ocrs/ocrPfpUsersPanel">
              <i class="fa fa-dashboard"></i> <span><?php echo __('New Users')?></span>
            </a>
        </li>
        <li class="treeview">
            <a href="/adminpfp/ocrs/ocrPfpBillingPanel">
              <i class="fa fa-dashboard"></i> <span><?php echo __('Bills')?></span>
            </a>
        </li>
        <li class="treeview">
            <a href="/adminpfp/ocrs/showTallymanPanel">
              <i class="fa fa-dashboard"></i> <span><?php echo __('Tallyman')?></span>
            </a>
        </li>       

        <li class="treeview">
          <a href="/adminpfp/users/logout"><i class="fa fa-power-off"></i> <span><?php echo __('Logout')?></a></span>
        </li>
 
	</ul>
