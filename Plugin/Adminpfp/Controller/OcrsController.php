<?php

/**
 * +--------------------------------------------------------------------------------------------+
 * | Copyright (C) 2016, http://www.winvestify.com                   	  	|
 * +--------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or 	|
 * | (at your option) any later version.                                      		|
 * | This file is distributed in the hope that it will be useful   		    	|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the      	|
 * | GNU General Public License for more details.        		        |
 * +---------------------------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.15
 * @date 2017-07-10
 * @package
 *
 *
 * 2016/29/2017 version 0.1
 * function OneClickInvestorI, Save personal data in db                    [OK]
 * function OneClickInvestorII Save selected companies                     [OK]
 * function companyFilter      Company filter for platform selection panel [OK]
 *
 * 2017/6/01  version 0.2
 * upload                                                            [OK]
 * 2017/6/05  version 0.3
 * deleteCompanyOcr                                                     [OK]
 *                                       
 * 2017/6/06  version 0.4
 * upload deleted
 * id problem fixed
 *     
 * 2017/6/13  version 0.5
 * Ocr status added
 * 
 * 2017/6/16  version 0.6
 * oneClickInvestorI error 500 fixed
 * 
  2017/6/19 version 0.7
  ocrWinadminBillingPanel-> bill table added

 * 2017/6/23 version 0.8
 * checking data table
 * user checking data
 * 
 * 2017/6/26 version 0.9
 * pfp admin tables ok
 * 
 * [2017-06-28] Version 0.10
 * Added countryCodes to VinAdmin View #4  (Update PFP Data, to select country)
 * Added currencyName to WinAdmin View #3 (Billing Panel)
 * Added 
 * server validation
 * 
 * 
 * [2017-06-28] Version 0.11
 * Set status name
 * Bills table refesh
 * 
 * [2017-06-29] Version 0.12
 *   Update pfp added
 * 
 * [2017-06-30] Version 0.13
 * Update pfp completed
 * Upload cif
 * 
 * [2017-07-03] Version 0.14
 * Update Checks
 * 
 * 
 * 2017-07-10   Version 0.15
 * Copied only the relevant part of the adminpfp original Controlller to plugin "adminpfp" directory
 * Added the truth table in readtallymandata
 * 
 * 
 * 
 * Pending:
 * Fix uploadStatusInvestorPfp id request.
 */
App::uses('CakeEvent', 'Event');

class ocrsController extends AppController {

    var $name = 'Ocrs';
    var $helpers = array('Session');
    var $uses = array('Ocr', 'Investor');
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
        //       $this->Security->requireAuth();
        $this->Auth->allow();       //allow these actions without login
    }

    //One Click Registration - PFPAdmin Views
    //PFPAdmin View #2
    function ocrPfpBillingPanel() {
        
        $this->Ocrfile = ClassRegistry::init('Ocrfile');
        
        //Read all company bills
        $bills = $this->Ocrfile->billCompanyFilter($this->Session->read('Auth.User.Adminpfp.company_id'));
        $this->set('bills', $bills); //Set the bills

        $this->layout = 'Adminpfp.azarus_private_layout';
    }

    /*     * PFPAdmin View #1
     * New accepted user
     */

    function ocrPfpUsersPanel() {

        $this->Company = ClassRegistry::init('Company'); 
        
        $status = $this->Company->checkOcrServiceStatus($this->Session->read('Auth.User.Adminpfp.company_id'));//Check ocr servecie status, block the view if isnt active.

        if ($status[0]) {
            //Read accepted relations
            $ocrList = $this->Ocr->getAllOcrRelations($this->Session->read('Auth.User.Adminpfp.company_id'));
            $this->set('ocrList', $ocrList);

            //Set PFP  status
            $this->set('pfpStatus', $status[1]['Serviceocr']['serviceocr_status']);

            //Set status names
            $this->set('statusName', $this->pfpStatus);
        } else {
            //You can't access to this page
        }
        $this->layout = 'Adminpfp.azarus_private_layout';
    }

    /**
     * 
     * Shows the Tallyman data of a user in a graphical manner
     * 
     */
    public function readtallymandata() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
        $this->layout = 'ajax';
        $this->disableCache();

        $platformId = $this->Session->read('Auth.User.Adminpfp.company_id');
        $error = null;

        $inputId = $_REQUEST['inputId'];
        $userEmail = $_REQUEST['userEmail'];
        $userTelephone = $_REQUEST['userTelephone'];
        $chargingConfirmed = $_REQUEST['chargingConfirmed'];
        
        
// Get the unique investor identification
        $inputParmCount = 0;
        if (!empty($inputId)) {
            $key[] = "Investor.investor_DNI";
            $value[] = $inputId;
            ++$inputParmCount;
        }
        if (!empty($userEmail)) {
            $key[] = 'Investor.investor_email';
            $value[] = $userEmail;
            ++$inputParmCount;
        }
        if (!empty($userTelephone)) {
            $key[] = 'Investor.investor_telephone';
            $value[] = $userTelephone;
            ++$inputParmCount;
        }

        if ($inputParmCount < 2) {
            $error = NOT_ENOUGH_PARAMETERS;
        } else {
            $filterConditions = array_combine($key, $value);
            $searchData = json_encode($filterConditions);
            
            // Save all the searches done by the adminpfp's in order to harvest email addresses
            $this->Search = ClassRegistry::init('Adminpfp.Search');
            $result = $this->Search->writeSearchData($searchData, $platformId, null, null, TALLYMAN_APP);

            $this->Investor = ClassRegistry::init('Investor');
            $resultInvestor = $this->Investor->getInvestorData($filterConditions);
            $userIdentification = $resultInvestor[0]['Investor']['investor_identity'];

            if (!$userIdentification) {
                $error = USER_DOES_NOT_EXIST;
            } else {
                $this->Investorglobaldata = ClassRegistry::init('Adminpfp.Investorglobaldata');
                $resultTallymanData = $this->Investorglobaldata->readinvestorData($userIdentification, $platformId);

//print_r($resulyTallyManData);
                // CHECK IF structure can be improved
                if (empty($resultTallymanData)) {
                    $error = NO_DATA_AVAILABLE;
                } 
                else {      // All parameters were provided and correct
                    $this->set('resultTallyman', $resultTallymanData);
                    $this->Billingparm = ClassRegistry::init('Adminpfp.Billingparm');
                    $chargeThisEvent = $this->isChargeableEvent($userIdentification, null, $platformId, null, "tallyman");                
/*
 * Truth-Table:
 * $chargingConfirmed $chargeThisEvent  Result
 *          0               0           Continue without Charging
 *          0               1           Send Confirmation Modal
 *          1               0           Continue without Charging
 *          1               1           Store the Charging Data
 * 
 */       
                    if ($chargeThisEvent) {
                        if ($chargingConfirmed == false) {
                            $parameters = array($inputId, $userEmail, $userTelephone);
                            $this->set('parameters', $parameters);
                            $this->render('chargingconfirmationmodal');
                            return;
                        }
                    }

                    // provide data for billing purposes
                    if ($chargeThisEvent && $chargingConfirmed) {
                        $data = array();
                        $data['reference'] = $userIdentification;                           // investor unique identification
                        $data['parm1'] = $this->Session->read('Auth.User.Adminpfp.adminpfp_identity');       // adminpfp unique identification
                        $data['parm2'] = $platformId;                                      // platformId of the adminfp user
                        $data['parm3'] = null;
                        $this->Billingparm->writeChargingData($data, "tallyman");  // CHECK RESULT CODE
                    }
                }
            }
        }

        if (!$error) {                              // No error encountered, use default view
            return;
        }

        $this->set("error", $error);
        $this->render('tallymanErrorPage');
    }

    /**
     *
     *  Checks if Tallyman event is to be charged, i.e. if charging data must be stored in database
     * 
     *  @param 	    $reference      parameter to be checked
     *  @param      string      transparent parameter 2 to be checked
     *  @param      string      transparent parameter 3 to be checked
     *  @param      string      transparent parameter 4 to be checked
     *  @param      string      name of application
     *
     *  @return 	boolean	true	All OK, data has been saved
     * 				false	Error occured
     * 						
     */
    public function isChargeableEvent($reference, $parameter1, $parameter2, $parameter3, $application) {
        return true;

//  Calculate cutoff date for billing purposes
        Configure::load('p2pGestor.php', 'default');
        $validBeforeExpiration = Configure::read('CollectNewInvestmentData');
        $cutoffTime = date("Y-m-d H:i:s", time() - $validBeforeExpiration * 3600 * 7 * 24);

        $result = $this->Billingparm->find('first', array(
            "fields" => array("created"),
            "order" => "id DESC",
            "recursive" => -1,
            "conditions" => array("billingparm_reference" => $reference,
                "billingparm_parm2" => $parameter2,
                "billingparm_serviceName" => $application),
        ));

        if (empty($result)) {  // No information found, so not a chargeable event
            return false;
        }

        if ($result['Billingparm']['created'] > $cutoffTime) {          // This request should NOT be counted as a new chargeable request
            return true;
        }
        return false;
    }

    //Update companies_ocr status when a company download a zip
    public function uploadStatusInvestorPfp() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $this->layout = 'ajax';
            $id = $this->request->data['id'];// Investor id
            $userId = $this->Investor->getInvestorUserId($id); //User id
            $status = DOWNLOADED;
            $companyId = $this->Session->read('Auth.User.Adminpfp.company_id'); //Company id
            $result = $this->Ocr->updateInvestorStatus($id, $status, $companyId);//Update status
            $this->set('result', [$result, $id, $userId]); //Ajax response, $result is true or false.
        }
    }

    /**
     * 
     * Shows the initial, basic screen of the Tallyman service with the three input fields
     * 
     */
    public function showTallymanPanel() {
        $this->layout = 'Adminpfp.azarus_private_layout';
    }

    /**
     * 
     * Shows the initial, basic screen of the Tallyman service
     * 
     */
    public function startTallyman($investorEmail, $investorTelephone) {

        $this->layout = 'Adminpfp.azarus_private_layout';

        $this->set("investorEmail", $investorEmail);
        $this->set("investorDNI", $investorDNI);
        $this->set("investorTelephone", $investorTelephone);


        $filterconditions = array('investor_identity', $investorIdentification);
        $this->set('result', $result);
    }

}
