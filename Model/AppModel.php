<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 *
 *
 *
 *
 *
 *
 *
 *
Version 0.1		2016-10-29																
function createReference															[OK, tested]

 
 
*/

 
class AppModel extends Model {


/**
 *
 *	Generates a unique investor reference
 *
 */
public function createInvestorReference() {

	$charid = strtoupper(md5(uniqid(rand(), true)));
	$hyphen = "-";
	$uuid = substr($charid, 0, 8).$hyphen
		.substr($charid, 8, 4).$hyphen
		.substr($charid,12, 4).$hyphen
		.substr($charid,16, 4).$hyphen
		.substr($charid,20,12);
	return $uuid;	
}



/**
 *
 *	Generates a unique code which can be used for registration confirmation
 *
 */
public function createReference() {
	$random = mt_rand (1000000, 9999999);
	return $random;
}





/**
 *
 *	Decrypt a string
 *	
*/
public function decryptDataAfterFind($string) {	
	return(Security::rijndael($string, Configure::read('Security.salt'), 'decrypt'));
}





/**
 *
 *	Encrypt a string
 *	
*/
public function encryptDataBeforeSave($string) {
	return(Security::rijndael($string, Configure::read('Security.salt'), 'encrypt'));
}





/**
 *
 * Formats the date into a "readable" format, DD/MM/YYYY. Date must be in format YYYY-MM-DD
 *
*/
public function formatDateAfterFind($string){
	if (empty($string)) {
		return;
	}	
	$tempDate = str_split($string,2);
	$tempDate = $this->multiexplode(array("/","-"), $string);		
	return $tempDate[2] . "/" .$tempDate[1] . "/" .$tempDate[0];
}





/**
 *
 * Converts the date for storage. Date input must be in format DD/MM/YYYY
 * and will be stored in format YYYY-MM-DD
 *
 *
*/
public function formatDateBeforeSave($string){

	$tempDate = $this->multiexplode(array("/","-"), $string);
	return $tempDate[2] . "-" . $tempDate[1] . "-" . $tempDate[0];
}





/**
*
*	php explode function but with multiple delimiter
*
*/
public function multiexplode ($delimiters,$string) {
	$ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}





/**
*
*	obtain the date of a datetime field
*
*/
public function getDate ($datetime) {
    $result = explode(" ", $datetime);
    return  $result[0];
}





public function calculateAge($birthday)  {  //date in yyyy-mm-dd format; or it can be in other formats as well
        $dob = date("Y-m-d",strtotime($birthday));

        $dobObject = new DateTime($dob);
        $nowObject = new DateTime();

        $diff = $dobObject->diff($nowObject);
		return $diff->y;
}




/**
* This function matches two fields - a useful extension for Cake Validation
*
* @param string|array $check Value to check
* @param string $compareField The field to compare
* @return boolean Success
*/
public function matchFields($check = array(), $compareField = null) {
	$value = array_shift($check);
	if (!empty($value) && !empty($this->data[$this->name][$compareField])) {
		if ($value !== $this->data[$this->name][$compareField]) {
			return false;
		}
	}
		return true;
}


}