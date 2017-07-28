<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>Winvestify</title>
		<meta name="generator" content="Winvestify" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="/css/compare_styles.css">
		<link type="text/css" rel="stylesheet" href="/css/normalize.min.css" />		

		<link href="/css/intl-tel-input/intlTelInput.css" rel="stylesheet"> 
		<link href="/css/intl-tel-input/demo.css  rel="stylesheet">	
 <!--		<link href="/css/style.css" rel="stylesheet">		-->
		
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<link type="text/css" rel="stylesheet" href="/js/widget/css/rcarousel.css" />
		<link type="text/css" rel="stylesheet" href="/css/digitalclock.css" />
		
		<script src="https://use.fontawesome.com/2d62372de2.js"></script>
	
	
</head>
<body>
		
		
<script src="/js/jquery.js"></script>
<script src="/js/jquery.countTo.js"></script>

<script type="text/javascript" src="/js/widget/lib/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="/js/widget/lib/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="/js/widget/lib/jquery.ui.rcarousel.min.js"></script>
<script type="text/javascript" src="/js/accounting.min.js"></script>
<script type="text/javascript" src="/js/intl-tel-input/intlTelInput.js"></script>

<script type="text/javascript" src="/js/local.js"></script>

<?php echo $this->Html->script(array('local')); ?>


<script type="text/javascript">

$(document).ready(function() {
	
	
	
	console.log("Login2 button pressed");	
	var link = "/users/login";
	console.log("Login2 button pressed, link = " + link);	

  	if (!(app.visual.checkFormLogin()) === true) {
//		event.stopPropagation();
//		event.preventDefault();
		return false;
	}
//	event.stopPropagation();
//	event.preventDefault();	 

	var data = 0;
	getServerData(link, data, successPressLoginBtn, errorPressLoginBtn);	

	
});





	
function successPressLoginBtn(data){
	console.log("successPressLoginBtn function is called");
	$('#loginModalMain').html(data);
	$('#loginModal').modal('show');
}



function errorPressLoginBtn(data){
	console.log("errorPressLoginBtn function is called");  // authentication failure

	$('#loginModalMain').replaceWith(data);
	console.log("add the actived4 class");
	error = $("#credentialError").val();
	console.log("value of credentialError = " + error);

	if (error ==="Credentialerror") {
		console.log("Authentication Error");
		app.utils.sacarMensajeError(true, ".errorCredentials", TEXTOS.T02);
		$('.errorCredentials').addClass('actived4');		
		app.utils.trace("added");
	}
	return true;	

}



function successRegisterBtnPressed(data){
console.log("successRegisterButton function is called");


}


function errorRegisterBtnPressed(data){
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
	getServerData(link, data, successRegisterBtnPressed, errorRegisterBtnPressed);
});

});

</script>


		
		


<!-- Modals
================================================== -->
<div id="loginModalMain"></div>
<div id="registerModalMain"></div>
<div id="subscribeModalMain"></div>
<div id="errorModalMain"></div>

<style>	
.main-carousel{
	margin:0px;
	padding:0px;
}


</style>
<!-- Carousel
================================================== -->
<div id="myCarousel" class="carousel slide main-carousel">
<!-- Indicators -->
	<ol class="carousel-indicators">
	<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
	<li data-target="#myCarousel" data-slide-to="1"></li>
	<li data-target="#myCarousel" data-slide-to="2"></li>

	</ol>
	<div class="carousel-inner">
		<div class="item active">
			<img src="/img/photo-1470500822217-f941d86f16a1.jpeg" style="width:100%" class="img-responsive">
			<div class="container">
				<div class="carousel-caption">
					<h1><?php echo __("WINVESTIFY PROVIDES YOU WITH A SINGLE POINT OF ACCESS FOR MANAGING ALL YOUR CROWDLENDING INVESTMENTS");
						echo "<br>";
						?>
					</h1>
				</div>
			</div>
		</div>
	
		<div class="item">
			<img src="/img/photo-1467348733814-f93fc480bec6.jpeg" style="width:100%" class="img-responsive">
			<div class="container">
				<div class="carousel-caption">
					<h1><?php echo __("SEE ALL YOUR INVESTMENTS JUST ON ONE WEBPAGE");
						echo "<br><br><br>";?>
					</h1>          
				</div>
			</div>
		</div>
	
		<div class="item">
			<img src="/img/photo-1438401171849-74ac270044ee.jpeg" style="width:100%" class="img-responsive">
			<div class="container">
				<div class="carousel-caption">
					<h1><?php echo __("WINVESTIFY ALSO GIVES YOU INFORMATION ABOUT INVESTMENTS WITH REPAYMENT ISSUES");
						echo "<br>";
						?>
					</h1>
					<p></p>         
				</div>
			</div>
		</div>
	</div>
  
<!-- Controls -->
	<a class="left carousel-control" href="#myCarousel" data-slide="prev">
		<span class="icon-prev"></span>
	</a>
	<a class="right carousel-control" href="#myCarousel" data-slide="next">
		<span class="icon-next"></span>
	</a>  
</div>
<!-- /.carousel -->



<div class="container-fluid nopadding nomargin">

<?php echo $this->fetch('content');?>

<?php echo $this->element('publicfooter')?>
 </div>


<!-- Bootstrap Core JavaScript -->
<?php  echo $this->Html->script('bootstrap.min.js'); ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-88074631-1', 'auto');
  ga('send', 'pageview');

</script>
<?php echo $this->Js->writeBuffer(array('cache'  => TRUE)); 					// Write cached scripts   ?> 

	</body>
</html>