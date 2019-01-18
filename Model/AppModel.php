<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Winvestify Asset Management S.L.
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
 *
Version 0.1		2016-10-29																
function createReference															[OK, tested]


Version 0.2		2017-10-25
function getData        [Tested local, OK]
 
 
*/

 
class AppModel extends Model {
    
    /**
     * Configuration data for variable name translations
     * The following "endpoints" are incorporated into the configuration array:
     * investor.json
     * user.json
     * 
     *
     * @access private
     * @var array
     */
    private $apiVariableConfigArray = [ //'investor_DNI' => 'investor_D_N_I',
                                'investor_dateOfBirth' => 'investor_date_of_birth',
                                'investor_city' => 'investor_city',  // ONLY translation shall be allowed, this is really a configuration error
                                'linkedaccount_status' => 'linkedaccount_status',
                                'check_dateOfBirth' => 'check_date_of_birth',        // Not a realistic value, just for testing
                                'investor_postCode' => 'investor_postcode',
                                'linkedaccount_currencyCode' => 'linkedaccount_currency_code',
                                'company_countryName' => 'company_country_name',
                                'company_privacyUrl' => 'company_privacy_url',
                                'investor_approvalCode' => 'approval_code',
                                'investor_surname' => 'investor_surnames',
                                'investor_email' => 'email',
                                'accountDisplayName' => 'account_display_name',
                                'displayName' => 'display_name',
                                'usertoken_refreshToken' => 'refresh_token',
                                'linkedaccount_accountIdentity' => 'linkedaccount_identity',
                                'linkedaccount_accountDisplayName' => 'linkedaccount_platform_display_name',
                                'linkedaccount_visualStatus' => 'linkedaccount_visual_state',
                                'linkedaccount_apiStatus' => 'linkedaccount_status',
                                'accountowner_username' => 'linkedaccount_username',
                                'accountowner_password' => 'linkedaccount_password',
                                'accountCheck'  => 'linkedaccount_linkingstatus',
                                'requireNewAccessToken'=> 'require_new_access_token',
                                'investor_accredited' => 'accredited_investor',
 
                              ];

    /**
     * @brief wrapper for transactions
     *
     * Allow you to easily call transactions manually if you need to do saving
     * of lots of data, or just nested relations etc.
     *
     * @code
     *  // start a transaction
     *  $this->transaction();
     *
     *  // rollback if things are wrong (undo)
     *  $this->transaction(false);
     *
     *  // commit the sql if all is good
     *  $this->transaction(true);
     * @endcode
     *
     * @access public
     *
     * @param mixed $action what the command should do
     *
     * @return see the methods for transactions in cakephp dbo
     */
    public function transaction($action = null) {
        $this->__dataSource = $this->getDataSource();
        $return = false;
        if($action === null) {
            $return = $this->__dataSource->begin($this);
        } else if($action === true) {
            $return = $this->__dataSource->commit($this);
        } else if($action === false) {
            $return = $this->__dataSource->rollback($this);
        }
        return $return;
    }    
  
    
    /**
     * Generates a unique investor reference
     * 
     * @param -
     * @return UUID
     */
    public function createInvestorReference($telephone, $username) {

        $hashTelephone = hash("crc32", $telephone);
        $hashUsername = hash("crc32", $username);
        
        $uuid = $hashTelephone . $hashUsername;
        /*$charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = "-";
        $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);*/
        return $uuid;	
    }



    /**
     * Generates a unique code which can be used for registration confirmation
     * 
     * @param -
     * @return Random number
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


    /**
     * Generic function to get the data of a table
     * 
     * @param  array $filter filter of the table  ---> array("key" => value, ...),
     * @param  array $field  Fields you want get  ---> array("field", ...),
     * @param  array $order  table order          ---> array("field" DES or ASC),
     * @param  int   $limit  Limit table result   ---> 1,
     * @return array         data from the table
     */
    public function getData($filter = null, $field = null, $order = null, $limit = null, $type = "all"){

       
        $resultData = $this->find($type, array("recursive" => -1,
            "conditions" => $filter,
            "fields" => $field,
            "order" => $order,
            "limit" => $limit,
        ));

        return $resultData;
    }
    
    /**
     * Function to get the last query made by cakephp
     * 
     * @return string
     */
    public function getLastQuery() {
        $dbo = $this->getDatasource();
        $logs = $dbo->getLog();
        $lastLog = end($logs['log']);
        return $lastLog['query'];
    }

    
    /** 
     * Checks if the current model has a child model
     *  
     *  @param  bigInt      $currentInstance    Instance of the parent Model
     *  @param  string      $model              Name of child model
     *  @return boolean
     */   
    public function hasChildModel($currentInstance, $model) {  

        $this->Behaviors->load('Containable');
        $this->contain($model);            

        $result = $this->find("first", array('recursive' => 1,
                                                        'fields' => array('id'),
                                           'conditions' => array('id' => $currentInstance)
                                        ));       

        
        $count = count($result[$model]);
        if ($count <> 0) {            
            return true;
        }       
        return false;
    } 
 
    
    function print_r2($val) {
        echo '<pre>';
        print_r($val);
        echo '</pre>';
    }  
    
   

    /**  
     * Adapts the name of a fieldlist for a find operation to the internal name(s)
     * of the database variables
     * $inputArray elements can be of form 'Model.variableName' or 'variableName'
     * 
     * NOTE: The format of $inputArray: 
     *      ['Model.FieldName1 as Model.newFieldName1', 
     *       'FieldName2 as newFieldName2',
     *         ...
     *      ]
     * is not supported 
     * 
     *   id as NEWNAME
     *  @param  array $inputArray  
     *  @return boolean
     */   
    public function apiFieldListAdapter(&$inputArray) {

        foreach ($inputArray as $key => $item) {
            $explodeResult = explode(".", $item);
            $newItem = (!empty($explodeResult[1]) ? $explodeResult[1] : $explodeResult[0]); 
            $newKey = array_search($newItem, $this->apiVariableConfigArray);

            if (!empty($newKey)) {
                if (!empty($explodeResult[1])) {
                    $newKey = $explodeResult[0] . "." . $newKey;  
                }
               $inputArray[$key] = $newKey;
            }   
        }       
        return true;
    }
    
    /** 
     * Adapt the variable names of an array (= keys) to the internal name of the
     * database variable. 
     * Typically used as filtering condition of a find operation
     * Example  'investor_date_of_birth' ==> 'investor_dateOfBirth'
     * Note that it does NOT do recursion into arrays
     *  
     *  @param array $inputArray 
     *  @return boolean
     */   
    public function apiVariableNameInAdapter(&$inputArray)  {
        foreach ($inputArray as $key => $item) {
            $newKey = array_search($key, $this->apiVariableConfigArray);
            if ($newKey <> $key) {              // more resistent to errors in $this->apiVariableConfigArray
                if (!empty($newKey)) {
                    $inputArray[$newKey] = $item;
                    unset($inputArray[$key]);
                }
            
            }
        }       
        return true;
    }

    /** 
     * Adapt the internal variable names of an array (= keys) to the external name of the
     * variable according to the API specification. Typically this is used when returning
     * the result of a find operation and when returning validationErrors when a find
     * failed.
     * 
     * Example   'investor_dateOfBirth' ==> 'investor_date_of_birth'
     * Note that it does NOT do recursion into arrays
     *       
     *  @code 
     * 
     *  $this->Model->apiVariableNameOutAdapter($valErrors);
     * 
     *  @endcode 
     * 
     *  @param array $inputArray 
     *  @return boolean
     */   
    public function apiVariableNameOutAdapter(&$inputArray)  {

        foreach ($inputArray as $key => $item) {  
            if (array_key_exists($key, $this->apiVariableConfigArray)) {
                $newKey = $this->apiVariableConfigArray[$key];
                if ($key <> $newKey) {              // more resistent to errors in $this->apiVariableConfigArray
                    $inputArray[$newKey] = $item;
                    unset($inputArray[$key]);
                }
            }
        }    
        return true;
    }   
    
    /**
     *
     * 	Creates a new instance of class with name company, like zank, or comunitae....
     *
     * 	@param 		int 	$companyCodeFile		Name of "company"
     * 	@return 	object 	instance of class "company"
     *
     */
    function companyClass($companyCodeFile) {

        $dir = Configure::read('companySpecificPhpCodeBaseDir');
        $includeFile = $dir . $companyCodeFile . ".php";
        require_once($dir . 'p2pCompany.class' . '.php');   // include the base class IMPROVE WITH spl_autoload_register
        require_once($includeFile);
        $newClass = $companyCodeFile;
        $newComp = new $newClass;
        return $newComp;
    }  
  
    
   
    /**
     * Generate a random string, using a cryptographically secure 
     * pseudorandom number generator (random_int)
     * 
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     * 
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }   
    
       
    
}
