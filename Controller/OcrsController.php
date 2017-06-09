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
 * | GNU General Public License for more details.        			              	|
 * +---------------------------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2016-10-25
 * @package
 *

  2016/29/2017 version 0.1
  function OneClickInvestorI, Save personal data in db                    [OK]
  function OneClickInvestorII Save selected companies                     [OK]
  function companyFilter      Company filter for platform selection panel [OK]
  function OneClickAdmin                                     [Not implemented]
  function OneClickCompany                                   [Not implemented]

  2017/6/01  version 0.2
 * upload                                                            [OK]
  2017/6/05  version 0.3
  deleteCompanyOcr                                                     [OK]
 *                                       
  2017/6/06  version 0.4
  upload deleted
  id problem fixed
 *                                                  [OK]


  Pending:
  OneClickInvestorI upload the needed document for register in the selected companies


 */
App::uses('CakeEvent', 'Event');

class ocrsController extends AppController {

    var $name = 'Ocrs';
    var $helpers = array('Session');
    var $uses = array('Ocr', 'Company', 'Investor', 'File');
    var $error;

    function beforeFilter() {
        parent::beforeFilter(); // only call if the generic code for all the classes is required.
        $this->Security->requireAuth();
        $this->Auth->allow(); //allow these actions without login
    }

    /*
     * 
     * Ciclo Principal
     * 
     */

//Envia datos personales a la bd.
    function oneClickInvestorII() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {

            $this->layout = 'ajax';
            $this->disableCache();

            $investor_name = strip_tags(htmlspecialchars($_REQUEST['investor_name']));
            $investor_surname = strip_tags(htmlspecialchars($_REQUEST['investor_surname']));
            $investor_DNI = strip_tags(htmlspecialchars($_REQUEST['investor_DNI']));
            $investor_dateOfBirth = $_REQUEST['investor_dateOfBirth'];
            $investor_telephone = $_REQUEST['investor_telephone'];
            $investor_address1 = strip_tags(htmlspecialchars($_REQUEST['investor_address1']));
            $investor_postCode = strip_tags(htmlspecialchars($_REQUEST['investor_postCode']));
            $investor_city = strip_tags(htmlspecialchars($_REQUEST['investor_city']));
            $investor_country = $_REQUEST['investor_country'];
            $investor_email = $_REQUEST['investor_email'];
            
            $datosInvestor = array(
                'id' => $this->Session->read('Auth.User.id'),
                'investor_name' => $investor_name,
                'investor_surname' => $investor_surname,
                'investor_DNI' => $investor_DNI,
                'investor_dateOfBirth' => $investor_dateOfBirth,
                'investor_telephone' => $investor_telephone,
                'investor_address1' => $investor_address1,
                'investor_postCode' => $investor_postCode,
                'investor_city' => $investor_city,
                'investor_country' => $investor_country,
                'investor_email' => $investor_email,
            );

            $result1 = $this->Investor->investorDataSave($datosInvestor);
            $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));
            $datosOcr = array(
                'investor_id' => $id,
                'ocr_investmentVehicle' => $_REQUEST['investmentVehicle'],
                'investor_cif' => $_REQUEST['cif'],
                'investor_businessName' => $_REQUEST['businessName'],
                'investor_iban' => $_REQUEST['iban']
            );


            $result2 = $this->Ocr->ocrDataSave($datosOcr);
            //$this->Orc->saveDocuments($datos);
            $this->set('result1', $result1);
            $this->set('result2', $result2);
        }
    }

//Envia solicitud de las compañias seleccionadas al admin. Ademas te actualizaria la seccion de compañias seleccionadas
    function oneClickInvestorI() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));
            $this->layout = 'ajax';
            $this->disableCache();

            $companyNumber = $_REQUEST['numberCompanies'];


            $companies = array(
                'investorId' => $id,
                'number' => $companyNumber,
                'idCompanies' => $_REQUEST['idCompany']
            );

            $result = $this->Ocr->saveCompaniesOcr($companies);
            $this->set('result', $result);
        }
    }

    function deleteCompanyOcr() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));
            $this->layout = 'ajax';
            $this->disableCache();

            $companyId = $_REQUEST['id_company'];

            $delComp = array(
                'investorId' => $id,
                'companyId' => $companyId,
            );

            $result = $this->Ocr->deleteCompanyOcr($delComp);
            $this->set('result', $result);
        }
    }

    //Filtro de las compañias a elegir
    function companyFilter() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {
            $this->layout = 'ajax';
            $this->disableCache();
            $filter = ['country_filter' => $_REQUEST['country_filter'], 'type_filter' => $_REQUEST['type_filter'],];

            $result = $this->Company->companiesDataOCR($filter);
            $this->set('company', $result);
        }
    }

//Para el admin- Revisa y envia los datos del inversor a la compañia.
    function oneClickAdmin() {
        $this->Orc->investorDataToCompany($datos);
    }

//Para la compañia- Revisa y termina el registro del inversor
    function oneClickCompany() {
        $this->Orc->ocrEnd($datos);
    }

    //Tabs panel
    function ocrTabsPanel() {
        $this->layout = 'azarus_private_layout';
    }

    //One Click Registration - Investor Views
    function ocrInvestorDataPanel() {
        echo "1";

        $data = $this->Investor->investorGetInfo($this->Session->read('Auth.User.id'));


        $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));
        $data2 = $this->Ocr->ocrGetData($id);

        $companies = $this->Ocr->getSelectedCompanies($id);
        $requiredFiles = $this->File->readRequiredFiles($companies);  
        
        $existingFiles = $this->File->readExistingFiles($id);
        
        $this->set('existingFiles',$existingFiles);
        $this->set('investor', $data);
        $this->set('ocr', $data2);
        $this->set('requiredFiles', $this->File->getFilesData($requiredFiles));
        echo " ";
        return 1;
    }

    function ocrInvestorPlatformSelection() {
        $this->layout = 'azarus_private_layout';
        
        $this->set('company', $this->Company->companiesDataOCR());
        
        $id = $this->Investor->getInvestorId($this->Session->read('Auth.User.id'));       
     
        $this->set('selected', $this->Ocr->getSelectedCompanies($id));
        $this->set('registered', $this->Ocr->getRegisteredCompanies($id));
        
        echo " ";
        return 1;
    }

    //One Click Registration - Admin PFP Views
    function ocrPfpBillingPanel() {
        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    function ocrPfpUsersPanel() {
        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    //One Click Registration - Winvestify Admin Views
    function ocrWinadminInvestorChecking() {
        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    function ocrWinadminBillingPanel() {
        $this->layout = 'azarus_private_layout';
        echo " ";
    }
    
    function ocrWinadminInvestorModal() {
        $this->layout = 'azarus_private_layout';
        echo " ";
    }

    /*
     * 
     * Tallyman
     * 
     */
}
