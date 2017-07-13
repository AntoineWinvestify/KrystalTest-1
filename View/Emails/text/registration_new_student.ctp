<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Emails.text
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>

Estimada Sra:
Gracias por inscribirse en la escuela municipal de Fiñana.
<?php 
	if ($sex == "M") {
		echo "<br />Estimado&nbsp;$name $surname,<br /><br />";
	}
	else {
		echo "Estimada&nbsp;$name $surname,<br /><br /> ";
	}

echo "Gracias por inscribirse en la $schoolName.";

echo "<br /><br />";
echo "Un saludo cordial,<br /><br />";
echo "&nbsp;&nbsp;Andalucia Learning Centre<br";

?>
<br />
<br />
