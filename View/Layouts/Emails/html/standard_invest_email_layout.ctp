<html>
<head>
<title>Zastac Equity</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<style type="text/css">
	* {-webkit-text-size-adjust: none}
	p {margin-bottom:0 ; margin:0}
</style>

</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%"><tr align="center"><td>
<table id="Tabla_01" width="800" bgcolor="#e6e9ea" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">

	<tr border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
		<td colspan="4">
			<table width="800" border="0" cellpadding="0" cellspacing="0">
				<tr width="800" border="0" cellpadding="0" cellspacing="0">
					
					
					<td width="634" height="85">
<?php
					echo $this->Html->image(Router::fullbaseUrl() . DS . APP_DIR . DS . WEBROOT_DIR . DS . "img" .DS . "mailImages" . DS . "mailing_01.jpg", array('alt' => 'Zastac Logo', 
																					'width' => '634',
																					'height'=> '85'));
?>				
					</td>
				
					
					<td width="55" height="85">
<?php					
					echo '<a href="https://twitter.com/zastac" target="_blank">';
					echo $this->Html->image(Router::fullbaseUrl() . DS . APP_DIR . DS . WEBROOT_DIR . DS . "img" .DS . "mailImages" . DS . "mailing_01_c.jpg", array('alt' => 'Twitter Logo', 
																					'width' => '55',
																					'height'=> '85',
																					'style' => 'display:block; border:0;'));
					echo '</a>';
?>					
					</td>
										
					
					<td width="55" height="85">
<?php
					echo '<a href="https://www.facebook.com/ZASTAC" target="_blank">';
					echo $this->Html->image(Router::fullbaseUrl() . DS . APP_DIR . DS . WEBROOT_DIR . DS . "img" .DS . "mailImages" . DS . "mailing_01_b.jpg", array('alt' => 'FaceBook Logo', 
																					'width' => '55',
																					'height'=> '85',
																					'style' => 'display:block; border:0;'));
					echo '</a>';
?>					
					</td>
					

					<td width="54" height="85">
<?php
					echo '<a href="https://www.linkedin.com/company/zastac" target="_blank">';
					echo $this->Html->image("mailImages" . DS . "mailing_01_a.jpg", array(
																					'alt' 		=> 'LinkedIn Logo',
																					'fullBase' 	=> true,
																					'width' 	=> '54',
																					'height'	=> '85',
																					'style' 	=> 'display:block; border:0;'));
					echo '</a>';
?>
					</td>
					
					
				</tr>
			</table>
		</td>
	</tr>

	<tr border="0" cellpadding="0" cellspacing="0" height="152">
		<td colspan="4" height="152" bgcolor="#ffffff">
			<?php echo $this->Html->image(Router::fullbaseUrl() . DS . APP_DIR . DS . WEBROOT_DIR . DS . "img" .DS . "mailImages" . DS . "people-green.jpg", array('alt' => 'Logo', 
												'width' => '800',
												'height'=> '152',
												'style' => 'display:block; min-width:800px;'));
			?>
		</td>
	</tr>

	<tr border="0" cellpadding="0" cellspacing="0">

		<?php echo $this->fetch('content'); ?>
		
	</tr>

	<tr border="0" cellpadding="0" cellspacing="0">
		<td height="95" bgcolor="#e6e9ea"  width="35"></td>
		<td colspan="2" bgcolor="#ffffff" height="95" width="730"  style="padding-left:45px; padding-right:45px; font-weight:bold; color: #2e3e44; text-align: left;">
			Gracias por confíar en nosotros, <br> el equipo de Zastac Equity
		</td>
		<td height="95"  width="35" bgcolor="#e6e9ea"></td>
	</tr>

	<tr border="0" cellpadding="0" cellspacing="0">
		<td colspan="4" bgcolor="#e6e9ea" width="800" height="51"></td>
	</tr>

	<tr border="0" cellpadding="0" cellspacing="0">
		<td colspan="4" bgcolor="#2f3f45" width="800" height="152" align="center" style="color:#d0d2d3; font-size:12px;">
			<p>¿Dudas, sugerencias, problemas? Escríbenos a <a href="mailto:info@zastac.com" style="color:#d0d2d3;">info@zastac.com</a></p>
			<br />
			<p>Si deseas dejar de recibir correos de Zastac Equity debe indicarlo en sus preferencias de usuario.</p>
		</td>
	</tr>

</table>
</td></tr></table>

</body>
</html>
