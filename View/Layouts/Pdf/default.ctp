<?php
// NOT ACTUALLY USED
require_once(APP . 'Vendor' . DS . 'dompdf' . DS . 'dompdf_config.inc.php');
spl_autoload_register('DOMPDF_autoload');
$dompdf = new DOMPDF();
$dompdf->load_html(utf8_decode($content_for_layout), Configure::read('App.encoding'));
$dompdf->render();
//$dompdf->stream();
$temp = $dompdf->output();
file_put_contents(WWW_ROOT . "TESTING_CONTENTS165.TXT", $content_for_layout);
file_put_contents(WWW_ROOT . "TESTING66.PDF", $temp);
echo $dompdf->output();

	
?>
