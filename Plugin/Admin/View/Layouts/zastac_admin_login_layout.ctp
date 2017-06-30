

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta http-equiv="X-UA-Compatible" content="IE=edge" /><meta name="viewport" content="width=device-width, initial-scale=1" />
<title>
	Zastac | Social Lending ADMIN
</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" /><link href="css/bootstrap-slider.css" rel="stylesheet" /><link href="css/bootstrap-select.min.css" rel="stylesheet" /><link href="css/bootstrap-datepicker.css" rel="stylesheet" /><link href="css/fileinput.min.css" rel="stylesheet" /><link rel="stylesheet" href="css/jquery-ui.css" /><link href="owl.carousel/owl-carousel/owl.carousel.css" rel="stylesheet" /><link href="owl.carousel/owl-carousel/owl.theme.css" rel="stylesheet" /><link href="owl.carousel/owl-carousel/owl.transitions.css" rel="stylesheet" /><link href="owl.carousel/assets/js/google-code-prettify/prettify.css" rel="stylesheet" /><link href="css/style.css?v=3" rel="stylesheet" /><link href="css/icons.css?v=3" rel="stylesheet" /><link href="//fonts.googleapis.com/css?family=Roboto:500,300,700,400&amp;subset=latin,latin-ext" rel="stylesheet" type="text/css" />
      <script src="//cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/TweenMax.min.js"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/fileinput.min.js"></script>
    <script src="js/fileinput_locale_es.js"></script>
    <script src="js/jquery.knob.min.js"></script>
    <script src="js/jquery.form.min.js"></script>
    <script src="js/css.js"></script>
    <script src="js/iban.js"></script>
    <script src="js/enscroll-0.6.1.min.js"></script>
    <script src="js/jquery.maskedinput.min.js" type="text/javascript"></script>

       <!--file upload IE9-->
        <script src="js/jquery.ui.widget.js"></script>
        <script src="js/jquery.iframe-transport.js"></script>
        <script src="js/jquery.fileupload.js"></script>

        <!--ajuste canvas-->
        <script type="text/javascript" src="js/fabric.min.js"></script>
        <script type="text/javascript" src="js/fastclick.js" ></script>
        <script type="text/javascript" src="js/binaryajax.js"></script>
        <script type="text/javascript" src="js/exif.js"></script>
        <script type="text/javascript" src="js/canvasResize.js"></script>

            <!-- Owl Carousel ./owl.carousel/assets -->

        <script src="owl.carousel/owl-carousel/owl.carousel.js"></script>
    <script src="owl.carousel/assets/js/bootstrap-collapse.js"></script>
    <script src="owl.carousel/assets/js/bootstrap-transition.js"></script>
    <script src="owl.carousel/assets/js/bootstrap-tab.js"></script>



    <script src="js/main.js?v=3"></script>
    <script src="js/jquery.cookies.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            app.main.init();
        });
    </script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!-- [if lt IE 9]>
          <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script> -->

    
</head>


<body>

<?php echo $this->fetch('content');   ?> 

  <!-- javascript at the bottom for fast page loading 
  <script type="text/javascript" src="js/jquery.js"></script>    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>  
-->
<!--
 <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-47311579-1', 'andalucialearningcentre.com');
  ga('send', 'pageview');
</script> 
-->

<script>
  
function getCookie(c_name){ 
	var c_value = document.cookie; 
	var c_start = c_value.indexOf(" " + c_name + "="); 
	if (c_start == -1){ 
		c_start = c_value.indexOf(c_name + "="); } 
	if (c_start == -1){ 
		c_value = null; 
	}
	else{ 
		c_start = c_value.indexOf("=", c_start) + 1; 
		var c_end = c_value.indexOf(";", c_start); 
		if (c_end == -1){ 
			c_end = c_value.length; 
		} 
	c_value = unescape(c_value.substring(c_start,c_end)); 
	} 
return c_value; 
} 

function comprobarCookie(){ 
	var acceptcookies=getCookie("acceptcookies"); 
	if(acceptcookies!="true"){ 



var aviso = '<div class="cookie_banner"> <div>\
		<a class="cookie_text"><b>Informaci&oacute;n que debes conocer</b></a><br /> \
		Las cookies recogen informaci&oacute;n en tu navegador web para ofrecerte una mejor experiencia online. \
		Si contin&uacute;as navegando, <br /> consideramos que aceptas su uso.\
		Puedes cambiar la configuraci&oacute;n u obtener m&aacute;s \
		informaci&oacute;n <a href="http://www.andalucialearningcentre.com/index.php/pages/cookies" target="_blank" \
		title="Cookies">aqu&iacute;.</a><br /><button onclick="ocultarAviso()" \
		href="#" class="cookie_button">Continuar</button> \
		</div></div> ';

	document.getElementById('avisoCookie').innerHTML = aviso; 
	}
} 



		
function ocultarAviso(){ 
	var exdays = 10000; 
	var exdate=new Date(); exdate.setDate(exdate.getDate() + exdays); 
	var c_value=escape("true") + ";expires="+exdate.toUTCString() + ";path=/"; 
	document.cookie="acceptcookies" + "=" + c_value; 
	document.getElementById('avisoCookie').style.display = 'none'; 
} 
</script>


<?php  echo $this->Html->script('js/jquery.js'); ?>

    <!-- Bootstrap Core JavaScript -->
<?php  echo $this->Html->script('js/bootstrap.min.js'); ?>


    <!-- Morris Charts JavaScript -->
<?php  echo $this->Html->script('js/plugins/morris/raphael.min.js'); ?>
<?php  echo $this->Html->script('js/plugins/morris/morris.min.js'); ?>
<?php  echo $this->Html->script('js/plugins/morris/morris-data.js'); ?>

	
 <?php echo $this->Js->writeBuffer(array('cache'  => TRUE)); 					// Write cached scripts   ?> 
</body>
</html>
