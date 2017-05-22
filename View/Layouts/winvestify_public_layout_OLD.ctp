<!DOCTYPE html>
<html lang="es-ES">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Winvestify</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style-admin.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link href="http://fonts.googleapis.com/css?family=Roboto:500,300,700,400&amp;subset=latin,latin-ext" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<?php
	$file = APP . "Config" . DS . "googleCode.php";
	if (file_exists($file)) {
		include_once($file);
	}
?>
</head>


<body>
<?php echo $this->fetch('content');    ?> 

<div id="footer">
  
	<script type="text/javascript" src="<?php echo $this->webroot; ?>js/rot13.js"></script>  
  
  <?php //echo $this->element('footer'); ?>  
</div>


  <!-- javascript at the bottom for fast page loading 
  <script type="text/javascript" src="js/jquery.js"></script>    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>  
-->

 
<?php
	echo $this->Html->script("js/jquery.js");

// Bootstrap Core JavaScript 
	echo $this->Html->script("js/bootstrap.min.js");
	echo $this->Js->writeBuffer(array('cache'  => TRUE)); 					// Write cached scripts
?> 
</body>
</html>
