<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <!-- <title>Winvestify | Dashboard</title> -->
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
		<link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css">
		
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/adminLTE/dist/css/AdminLTE.min.css">
	
  <!-- Winvestify Styles -->
<link type="text/css" rel="stylesheet" href="/css/compare_styles.css">	
<link type="text/css" rel="stylesheet" href="/megaKit/css/style.css"/>
<link type="text/css" rel="stylesheet" href="/megaKit/css/global.css"/>

<title><?php echo __('Winvestify')?></title>
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/adminLTE//dist/css/skins/skin-blue.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->


  
<?php
	$file = APP . "Config" . DS . "googleCode.php";
	if (file_exists($file)) {
		include_once($file);
	}
?>	
  <link rel="icon" href="/img/logo_winvestify/Logo_favicon.png">
</head>
<body class="hold-transition skin-blue sidebar-mini">


<div id="errorModalMain"></div>
<div id="notificationContentModalMain"></div>
<div class="wrapper">

  <header class="main-header">

    <!-- Logo -->
    <a class="logo" style="background-color: #222e2e;">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="/img/logo_winvestify/Logo.png" style="max-width: 45px;"></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="/img/logo_winvestify/Logo_texto.png" style="max-height: 45px; margin-top: 5px;"></span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top newHeader">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

<?php
	$sessionData = $this->Session->read();
	echo $this->element('headerlinepfpadmin', array('sessionData' => $sessionData));
?>
    </nav>
  </header>
	<!-- Left side column. contains the logo and sidebar -->
	<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
		<section class="sidebar">
		<!-- Sidebar user panel -->

		<!-- search form -->
			<form action="#" method="get" class="sidebar-form">
				<div class="input-group">
					<input type="text" name="q" class="form-control" placeholder="<?php echo __('Search...')?>">
					<span class="input-group-btn">
						<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
						</button>
					</span>
				</div>
			</form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
			
<?php 
    $sectorsMenu = $this->Session->read('sectorsMenu');
    echo $this->element('leftnavigationmenupfpadmin', array('sectorsMenu' => $sectorsMenu));
?>					
			

    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
	   
      
      <?php echo $this->fetch('content');  ?> 
		

      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-8">
          <div class="row">
           </div>
          <!-- /.row -->
      </div>
      <!-- /.row -->
	  
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php
	echo $this->Element('privatefooter');
?>

  <!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
	<!-- Create the tabs -->
	<ul class="nav nav-tabs nav-justified control-sidebar-tabs">
		<li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
		<li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
	</ul>
    <!-- Tab panes -->
	<div class="tab-content">
      <!-- Home tab content -->
		<div class="tab-pane" id="control-sidebar-home-tab">
			<h3 class="control-sidebar-heading"><?php echo __('Recent Activity')?></h3>
			<ul class="control-sidebar-menu">
				<li>
					<a href="javascript:void(0)">
						<i class="menu-icon fa fa-birthday-cake bg-red"></i>

						<div class="menu-info">
							<h4 class="control-sidebar-subheading">Langdon's Birthday</h4>
	
							<p>Will be 23 on April 24th</p>
						</div>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<i class="menu-icon fa fa-user bg-yellow"></i>

						<div class="menu-info">
							<h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>
							<p>New phone +1(800)555-1234</p>
						</div>
					</a>
				</li>

				<li>
					<a href="javascript:void(0)">
						<i class="menu-icon fa fa-file-code-o bg-green"></i>

						<div class="menu-info">
							<h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>
							<p>Execution time 5 seconds</p>
						</div>
					</a>
				</li>
			</ul>
			<!-- /.control-sidebar-menu -->


			<h3 class="control-sidebar-heading"><?php echo __('Tasks Progress')?></h3>
			<ul class="control-sidebar-menu">
				<li>
					<a href="javascript:void(0)">
						<h4 class="control-sidebar-subheading">
							Custom Template Design
							<span class="label label-danger pull-right">70%</span>
						</h4>

						<div class="progress progress-xxs">
							<div class="progress-bar progress-bar-danger" style="width: 70%"></div>
						</div>
					</a>
				</li>
				<li>
				
					<a href="javascript:void(0)">
						<h4 class="control-sidebar-subheading">
							Update Resume
							<span class="label label-success pull-right">95%</span>
						</h4>

						<div class="progress progress-xxs">
							<div class="progress-bar progress-bar-success" style="width: 95%"></div>
						</div>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<h4 class="control-sidebar-subheading">
							Laravel Integration
							<span class="label label-warning pull-right">50%</span>
						</h4>

						<div class="progress progress-xxs">
							<div class="progress-bar progress-bar-warning" style="width: 50%"></div>
						</div>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)">
						<h4 class="control-sidebar-subheading">
							Back End Framework
							<span class="label label-primary pull-right">68%</span>
						</h4>

						<div class="progress progress-xxs">
							<div class="progress-bar progress-bar-primary" style="width: 68%"></div>
						</div>
					</a>
				</li>
			</ul>
			<!-- /.control-sidebar-menu -->
		
		</div>
		<!-- /.tab-pane -->

		<!-- Settings tab content -->
		<div class="tab-pane" id="control-sidebar-settings-tab">
			<form method="post">
				<h3 class="control-sidebar-heading">General Settings</h3>

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Report panel usage
						<input type="checkbox" class="pull-right" checked>
					</label>
					<p>
						Some information about this general settings option
					</p>
				<div>
				<!-- /.form-group -->

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Allow mail redirect
						<input type="checkbox" class="pull-right" checked>
					</label>
					<p>
						Other sets of options are available
					</p>
				</div>
				<!-- /.form-group -->

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Expose author name in posts
						<input type="checkbox" class="pull-right" checked>
					</label>
					<p>
						Allow the user to show his name in blog posts
					</p>
				</div>
				<!-- /.form-group -->

				<h3 class="control-sidebar-heading">Chat Settings</h3>
				<div class="form-group">
					<label class="control-sidebar-subheading">
						Show me as online
						<input type="checkbox" class="pull-right" checked>
					</label>
				</div>
				<!-- /.form-group -->

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Turn off notifications
						<input type="checkbox" class="pull-right">
					</label>
				</div>
				<!-- /.form-group -->

				<div class="form-group">
					<label class="control-sidebar-subheading">
						Delete chat history
						<a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
					</label>
				</div>
				<!-- /.form-group -->
			</form>
		</div>
		<!-- /.tab-pane -->
	</div>
</aside>
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->







    


<!-- jQuery 2.2.3 -->
<script src="/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script type="text/javascript" src="/megaKit/js/global.min.js"></script>

<!-- Bootstrap 3.3.6 -->
<script src="/js/bootstrap.min.js"></script>

<script type="text/javascript" src="/js/local.js"></script>

<!-- AdminLTE App -->
<script src="/adminLTE/dist/js/app.min.js"></script>

<!-- ChartJS 1.0.1 -->
<!-- <script src="/plugins/chartjs/Chart.min.js"></script> -->

<!-- FastClick -->
<script src="/plugins/fastclick/fastclick.js"></script>


<!-- Number Counters and Formatting -->
<script type="text/javascript" src="/js/accounting.min.js"></script>
<script type="text/javascript" src="/js/jquery.countTo.js"></script>

<!-- Sparkline -->
<script src="/plugins/sparkline/jquery.sparkline.min.js"></script>

<!-- jvectormap -->
<script src="/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

<!-- SlimScroll 1.3.0 -->
<script src="/plugins/slimScroll/jquery.slimscroll.min.js"></script>

<!-- Datepicker -->
<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>


<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="/adminLTE/dist/js/pages/dashboard2.js"></script> -->
<!-- AdminLTE for demo purposes -->
<script src="/adminLTE/dist/js/demo.js"></script>


<script>
//Google Analytics, global events
function ga_userLogout() {

        if (typeof ga === 'function') { 
            console.log("ga 'send' 'event' 'UserSession'  'logoutClick'");            
            ga('send', 'event', 'UserSession', 'logoutDataClick');
        }
}
function ga_errorReporting() {

        if (typeof ga === 'function') {   
            console.log("ga 'send' 'event' 'ErrorReports'  'errorClick'");            
            ga('send', 'event', 'ErrorReports', 'errorClick');
        }
}
function ga_removeInformationBannerClick(bannerOrigin){

        if (typeof ga === 'function') {        
            console.log("ga 'send' 'event' 'RemoveInformationBanner' 'closeBannerClick' " + bannerOrigin );            
            ga('send', 'event', 'RemoveInformationBanner', 'closeBannerClick', bannerOrigin);
        }
	return true;
}

// Global Google Analytics functions for tables
function ga_tableSearchstring(string, tableIdentifier) {

        if (typeof ga === 'function') { 
            console.log("ga 'send' 'event' 'TableSearch'  'SearchClick' " + string + " " + tableIdentifier );          
            ga('send', 'event', 'TableSearch', 'searchClick', string, tableIdentifier);
        }
}
function ga_tableChangePageLength(newLength, tableIdentifier) {

        if (typeof ga === 'function') { 
            console.log("ga 'send' 'event' 'TablePageLength'  'ChangePageLength' " + newLength + " " + tableIdentifier);            
            ga('send', 'event', 'TablePageLength', 'ChangePageLength', newLength, tableIdentifier);
        }
}
function ga_tableSortedColumnClick(column, tableIdentifier) {

        if (typeof ga === 'function') {   
            console.log("ga 'send' 'event' 'TableColumnSort'  'sortColumnClick' " + column + " " + tableIdentifier);           
            ga('send', 'event', 'TableColumnSort', 'sortColumnClick', column, tableIdentifier);
        }
}
function ga_tablePageClick(page, tableIdentifier) {

        if (typeof ga === 'function') {       
            console.log("ga 'send' 'event' 'TablePage'  'forwardBackwardClick' " + page + " " + tableIdentifier);          
            ga('send', 'event', 'TablePage', 'forwardBackwardClick', page, tableIdentifier);
        }
}

</script>


 <?php echo $this->Js->writeBuffer(array('cache'  => TRUE)); 					// Write cached scripts   ?>
      
      
</body>
</html>
