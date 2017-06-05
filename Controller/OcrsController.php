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

2017/6/1  version 0.2
 * upload                                                            [OK]
2017/6/5  version 0.3
 deleteCompanyOcr                                                     [OK]
 *                                       



  Pending:
  OneClickInvestorI upload the needed document for register in the selected companies


 */
App::uses('CakeEvent', 'Event');

class ocrsController extends AppController {

    var $name = 'Ocrs';
    var $helpers = array('Session');
    var $uses = array('Ocr', 'Company', 'Investor');
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

            $investor_name = $_REQUEST['investor_name'];
            $investor_surname = $_REQUEST['investor_surname'];
            $investor_DNI = $_REQUEST['investor_DNI'];
            $investor_dateOfBirth = $_REQUEST['investor_dateOfBirth'];
            $investor_telephone = $_REQUEST['investor_telephone'];
            $investor_address1 = $_REQUEST['investor_address1'];
            $investor_postCode = $_REQUEST['investor_postCode'];
            $investor_city = $_REQUEST['investor_city'];
            $investor_country = $_REQUEST['investor_country'];

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
            );


            $datosOcr = array(
                'Investor_id' => $this->Session->read('Auth.User.id'),
                'Ocr_vehicle' => $_REQUEST['vehicle'],
                'Investor_cif' => $_REQUEST['cif'],
                'Investor_businessName' => $_REQUEST['businessName'],
                'Investor_iban' => $_REQUEST['iban']
            );

            $result1 = $this->Investor->investorDataSave($datosInvestor);
            $result2 = $this->Ocr->ocrDataSave($datosOcr);
            //$this->Orc->saveDocuments($datos);
            $this->set('result1', $result1);
            $this->set('result2', $result2);
        }
    }

    function upload() {
        $this->autoRender = false;
        $data = $this->params['data']['Files'];
        $id = $this->Session->read('Auth.User.id');
        echo "<h1>ID " . $id . "</h1>";
        $this->Ocr->ocrFileSave($data, $id);
    }

//Envia solicitud de las compañias seleccionadas al admin. Ademas te actualizaria la seccion de compañias seleccionadas
    function oneClickInvestorI() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {

            $this->layout = 'ajax';
            $this->disableCache();

            $companyNumber = $_REQUEST['numberCompanies'];

            $companies = array(
                'investorId' => $this->Session->read('Auth.User.id'),
                'number' => $companyNumber,
            );

            for ($i = 0; $i < $companyNumber; $i++) {
                $companies[$i] = $_REQUEST[$i];
            }
            $result = $this->Ocr->saveCompaniesOcr($companies);
            $this->set('result', $result);
        }
    }

    function deleteCompanyOcr() {
        if (!$this->request->is('ajax')) {
            $result = false;
        } else {

            $this->layout = 'ajax';
            $this->disableCache();

            $companyId = $_REQUEST['id_company'];

            $delComp = array(
                'investorId' => $this->Session->read('Auth.User.id'),
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
        $data2 = $this->Ocr->ocrGetData($this->Session->read('Auth.User.id'));
        $this->set('investor', $data);
        $this->set('ocr', $data2);
        echo " ";
        return 1;
    }

    function ocrInvestorPlatformSelection() {
        $this->layout = 'azarus_private_layout';
        $this->set('company', $this->Company->companiesDataOCR());
        $this->set('selected', $this->Ocr->getSelectedCompanies($this->Session->read('Auth.User.id')));
        echo " ";
        return 1;
    }

    function ocrInvestorDocuments() {
        echo " ";
    }

    //One Click Registration - Admin PFP Views
    function ocrPfpBillingPanel() {
        echo " ";
    }

    function ocrPfpUsersPanel() {
        echo " ";
    }

    //One Click Registration - Winvestify Admin Views
    function ocrWinadminInvestorChecking() {
        echo " ";
    }

    function ocrWinadminBillingPanel() {
        echo " ";
    }

    /*
     * 
     * Tallyman
     * 
     */
}
