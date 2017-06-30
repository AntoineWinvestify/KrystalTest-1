<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>AZARUS</title>
		<meta name="generator" content="Winvestify" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href="/css/bootstrap.min.css" rel="stylesheet">
		<link href="/css/compare_styles.css" rel="stylesheet">
 <!--		<link href="/css/intl-tel-input/intlTelInput.css" rel="stylesheet"> -->
		
 <!--		<link href="/css/style.css" rel="stylesheet">		-->
		
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
<link type="text/css" rel="stylesheet" href="/js/widget/css/rcarousel.css" />
<script src="https://use.fontawesome.com/2d62372de2.js"></script>
	
</head>
<body>
		
		
<script src="/js/jquery.js"></script>
<script src="/js/jquery.countTo.js"></script

<script type="text/javascript" src="/js/widget/lib/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="/js/widget/lib/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="/js/widget/lib/jquery.ui.rcarousel.min.js"></script>
<script type="text/javascript" src="/js/accounting.min.js"></script>

<script type="text/javascript" src="/js/local.js"></script>

<?php echo $this->Html->script(array('local')); ?>


<script type="text/javascript">
	
function successPressLoginBtn(data){
	console.log("successPressLoginBtn function is called");
	$("#loginModal").empty();
	$('#loginModal').html(data).show();
}

function errorPressLoginBtn(data){
	console.log("errorPressLoginBtn function is called");
}

function successRegisterButton(data){
	console.log("successRegisterButton function is called");
	$('#loginModal').html(data).show();
}


function errorRegisterButton(data){
	console.log("errorRegisterButton function is called");
}

	
function ajaxSend(){
	console.log("AJAX call started");
	$(".cssload-squeeze").removeClass('hide');
}




function ajaxComplete(){
	console.log("AJAX call has finished");
	$('.cssload-squeeze').hide();
}




$(function() {


// Get the html for the logon modal
$("#loginPanel").on("click", function(event) {
	console.log("Login button pressed");	
	var link = $(this).attr( "href" );
	console.log("Login button pressed, link = " + link);	

  	if (!(app.visual.checkFormLogin()) === true) {
		event.stopPropagation();
		event.preventDefault();
		return false;
	}
	event.stopPropagation();
	event.preventDefault();	 

	var data = 0;
	getServerData(link, data, successPressLoginBtn, errorPressLoginBtn);
});



// Get the html for the register modal, page requesting username, password etc
$("#registerPanel").on("click", function(event) {
	var link = $(this).attr( "href" );
	console.log("Register button on landing page has been pressed pressed, link = " + link);	

//  	if (!(app.visual.checkFormRegistration()) === true) {
//		event.stopPropagation();
//		event.preventDefault();
//		return false;
//	}
	event.stopPropagation();
	event.preventDefault();	 

	var data = 0;
	getServerData(link, data, successRegisterButton, errorRegisterButton);
});

});

</script>


		
		
		
<!-- Navigation bar
================================================== -->		
<div class="navbar-wrapper">
	<div class="container">
		<div class="navbar navbar-inverse navbar-static-top opaque">
			<div class="navbar-header">
				<a class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="navbar-brand" href="#">Winvestify</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li><a href="#">Home</a></li>
					<li><a href="pages/AboutUs" target="ext">About Us</a></li>
					<li><a id="loginPanel" href="/users/loginPanel">Login</a></li>
					<li><a id="registerPanel" href="/users/registerPanel">Register</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo __("Language")?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="#">Castellano</a></li>
							<li><a href="#">Nederlands</a></li>
							<li><a href="#">Francais</a></li>
							<li><a href="#">Deutsch</a></li>
							<li><a href="#">Ruso?</a></li>				
							<li><a href="#">Italiano</a></li>
							<li><a href="#">Svenska</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div><!-- /container -->
</div><!-- /navbar wrapper -->

<style>
.spacer {
	margin-bottom:80px;
	
}
</style>

<!-- Login Modal
================================================== -->
<div id="loginModal" class=""></div>

<div class="spacer"></div>


<?php echo $this->fetch('content');?>

<?php echo $this->element('publicfooter')?>

<!-- Bootstrap Core JavaScript -->
<?php  echo $this->Html->script('bootstrap.min.js'); ?>

<?php echo $this->Js->writeBuffer(array('cache'  => TRUE)); 					// Write cached scripts   ?> 

	</body>
</html>