<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2017, https://www.winvestify.com                        |
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
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2017-10-18
* @package
*


2017-10-18		version 0.1
initial version





Pending:







*/

class Amortizationtable extends AppModel
{
	var $name= 'Amortizationtable';


    /**
    *	Apparently can contain any type field which is used in a field. It does NOT necessarily
    *	have to map to a existing field in the database. Very useful for automatic checks
    *	provided by framework
    */
    var $validate = array(

    );



    /**
     * creates a new 'investment' table and also links the 'paymenttotal' database table
     * The $amortizationdata is an array of the following structure (not all elements are mandatory)
     * array   ['cuoteNo']['amortizationtable_scheduledDate' => xx         (format yyyy-mm-dd)
     *         ['cuoteNo']['amortizationtable_interest'      => yy
     *         ['cuoteNo']['amortizationtable_capitalRepayment' => zz
     *        
     * 	@param 		array 	$investmentId    	Link to the corresponding Investment table
     * 	@param 		array 	$amortizationdata 	All the data to be saved
     * 	@return 	boolean   
     * 			
     */
    public function createNewAmortizationTable($investmentId ,$amortizationdata) {

        $instalmentNumber = 1;
        foreach ($amortizationdata as $item) {
            $this->create();
            $item['amortizationtable_quoteNumber'] = $instalmentNumber;
            $item['investment_id'] = $investmentId;
            if ($this->save($item, $validate = true)) {
                $ids[] = $this->id;
            }
            else {  // error occured, so delete already created tables and return false
                foreach ($ids as $id) {
                    $this->delete($id);
                }
                return false;
            }  
            $instalmentNumber = $instalmentNumber + 1;
        } 
        return true;         
    }
    
    /**
     * Function to save amortization tables per pfp
     * @param array $amortizationData   It contains the amortization data of an investment(slice)
     * @return boolean
     */
    public function saveAmortizationtable($amortizationData) {
        $amortizationtable = [];

        foreach ($amortizationData as $loanId => $loanData) {
            foreach ($loanData as $value) {
                $loanIdInformation = explode("_", $loanId);
                $value['investmentslice_id'] = $loanIdInformation[0];
                $amortizationtable[] = $value;
            }
        }

        $this->saveMany($amortizationtable, array('validate' => true));
        return true;
    }



}