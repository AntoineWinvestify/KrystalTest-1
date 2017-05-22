<?php
/**
 *
 *
 * layout for a PDF form

// +-----------------------------------------------------------------------+
// | Copyright (C) 2014, http://beyond-language-skills.com                 |
// +-----------------------------------------------------------------------+
// | This file is free software; you can redistribute it and/or modify     |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation; either version 2 of the License, or     |
// | (at your option) any later version.                                   |
// | This file is distributed in the hope that it will be useful           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
// | GNU General Public License for more details.                          |
// +-----------------------------------------------------------------------+
// | Author: Antoine de Poorter                                            |
// +-----------------------------------------------------------------------+
//

2014-09-29    version 2014_0.1
Simply layout file with some basic css formatting



Pending:
Images are not yet displayed
Spanish chars are not properly displayed

*/


require_once(APP . 'Vendor' . DS . 'dompdf' . DS . 'dompdf_config.inc.php');
spl_autoload_register('DOMPDF_autoload');
$dompdf = new DOMPDF();
header("Content-type: application/pdf");
 
echo $content_for_layout;	
/*
	$fullContentsHeader = '
	<!DOCTYPE html><head>
	</head><body>
	<br/>'
	;
*/
	$fullContentsHeader = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><body>
<img src="http://andalucialearningcentre.com/img/logo_6.png"/>

	<br/>';



	$fullContentsFooter = '	
	';

//	$contents = $this->fetch('content'); 
	$fullContents = $fullContentsHeader . $content_for_layout . $fullContentsFooter;
//	$dompdf->set_base_path = WWW_ROOT;

	$dompdf->load_html(utf8_decode($fullContents), Configure::read('App.encoding'));
	$dompdf->render();

	$temp = $dompdf->output(); 
file_put_contents(WWW_ROOT . "TESTINGadpo3.TXT", $fullContents);
?>
