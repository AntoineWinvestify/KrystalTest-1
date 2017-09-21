<?php

/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by       |
 * | the Free Software Foundation; either version 2 of the License, or  	|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the           	|
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2017-08-25
 * @package
 *
 * 
 * 
 * 2017-08-24
 * Created
 * link account
 */
class bondora extends p2pCompany {

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY Bondora DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */
        echo '1';
        //First we need get te token
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        echo '2';
        $inputs = $dom->getElementsByTagName('input');
        foreach ($inputs as $key => $input) {
            //echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . HTML_ENDOFLINE;
            if ($key == 0) {
                continue;
            }
            $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
        }
        echo '3';
        $credentials['Email'] = $user;
        $credentials['Password'] = $password;

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        echo '4';
        //echo $str;

        $confirm = false;

        $spans = $dom->getElementsByTagName('span');
        foreach ($spans as $span) {
            //echo $span->nodeValue . HTML_ENDOFLINE;
            if (trim($span->nodeValue) == 'Account value') {
                $confirm = true;
                break;
            }
        }

        if ($confirm) {
            return true;
        }
        return false;
    }

    /**
     *
     * 	Logout of user from to company portal.
     * 	
     * 	@returnboolean	true: user has logged out 
     * 	
     */
    function companyUserLogout($url = null) {
        //$this->doCompanyLogout();
        $this->getCompanyWebpage();
        return true;
    }

    /**
     * 
     * @param type $str
     */
    function generateReportParallel($str) {
        switch ($this->idForSwitch) {
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;
            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $inputs = $dom->getElementsByTagName('input');

                foreach ($inputs as $key => $input) {
                    echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . HTML_ENDOFLINE;
                    if ($key == 0) {
                        continue;
                    }
                    $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $credentials['Email'] = $this->user;
                $credentials['Password'] = $this->password;

                print_r($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials); //do login
                break;
            case 2:
                echo 'Doing loging' . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;


                $confirm = false;

                $spans = $dom->getElementsByTagName('span');
                foreach ($spans as $span) {
                    echo $span->nodeValue . HTML_ENDOFLINE;
                    if (trim($span->nodeValue) == 'Account value') {
                        echo 'Login ok' . HTML_ENDOFLINE;
                        $confirm = true;
                        break;
                    }
                }

                if (!$confirm) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Bondora login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }

                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 4:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $inputs = $dom->getElementsByTagName('input');
                foreach ($inputs as $key => $input) {
                    echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . HTML_ENDOFLINE;
                    if ($key == 0) {
                        continue;
                    }
                    $inputsValue[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $credentials = array(
                    '__RequestVerificationToken' => $inputsValue['__RequestVerificationToken'],
                    'NewReports[0].ReportType' => 'InvestmentsListV2',
                    "NewReports[0].DateFilterRequired" => False,
                    "NewReports[0].DateFilterShown" => True,
                    "NewReports[0].Selected" => true,
                    "NewReports[0].Selected" => false,
                    "NewReports[0].DateFilterSelected" => true,
                    "NewReports[0].DateFilterSelected" => false,
                    "NewReports[0].StartDate" => $date1, //22/08/2017
                    "NewReports[0].EndDate" => $date2, //20/09/2017
                    "NewReports[1].ReportType" => "Repayments",
                    "NewReports[1].DateFilterRequired" => False,
                    "NewReports[1].DateFilterShown" => True,
                    "NewReports[1].Selected" => false,
                    "NewReports[1].DateFilterSelected" => false,
                    "NewReports[2].ReportType" => 'PlannedFutureCashflows',
                    "NewReports[2].DateFilterRequired" => False,
                    "NewReports[2].DateFilterShown" => True,
                    "NewReports[2].Selected" => false,
                    "NewReports[2].DateFilterSelected" => false,
                    "NewReports[3].ReportType" => 'SecondMarketArchive',
                    "NewReports[3].DateFilterRequired" => False,
                    "NewReports[3].DateFilterShown" => True,
                    "NewReports[3].Selected" => false,
                    "NewReports[3].DateFilterSelected" => false,
                    "NewReports[4].ReportType" => 'MonthlyOverview',
                    "NewReports[4].DateFilterRequired" => False,
                    "NewReports[4].DateFilterShown" => True,
                    "NewReports[4].Selected" => false,
                    "NewReports[4].DateFilterSelected" => false,
                    "NewReports[5].ReportType" => 'AccountStatement',
                    "NewReports[5].DateFilterRequired" => False,
                    "NewReports[5].DateFilterShown" => True,
                    "NewReports[5].Selected" => true,
                    "NewReports[5].Selected" => false,
                    "NewReports[5].DateFilterSelected" => true,
                    "NewReports[5].DateFilterSelected" => false,
                    "NewReports[5].StartDate" => $date3, //14/09/2017
                    "NewReports[5].EndDate" => $date4, //21/09/2017
                    "NewReports[6].ReportType" => 'IncomeReport',
                    "NewReports[6].DateFilterRequired" => True,
                    "NewReports[6].DateFilterShown" => True,
                    "NewReports[6].DateFilterSelected" => True,
                    "NewReports[6].Selected" => false,
                    "NewReports[7].ReportType" => 'TaxReportPdf',
                    "NewReports[7].DateFilterRequired" => True,
                    "NewReports[7].DateFilterShown" => True,
                    "NewReports[7].DateFilterSelected" => True,
                    "NewReports[7].Selected" => false,
                    "NewReports[8].ReportType" => 'AccountValue',
                    "NewReports[8].DateFilterRequired" => False,
                    "NewReports[8].DateFilterShown" => True,
                    "NewReports[8].Selected" => false,
                    "NewReports[8].DateFilterSelected" => false,
                );
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl(null, $credentials, false);
                break;
            case 5:
                return $tempArray = 'Generando informe';
                break;
        }
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserGlobalFilesParallel($str) {
        switch ($this->idForSwitch) {
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;
            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $inputs = $dom->getElementsByTagName('input');

                foreach ($inputs as $key => $input) {
                    echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . HTML_ENDOFLINE;
                    if ($key == 0) {
                        continue;
                    }
                    $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $credentials['Email'] = $this->user;
                $credentials['Password'] = $this->password;

                print_r($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials); //do login
                break;
            case 2:
                echo 'Doing loging' . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;


                $confirm = false;

                $spans = $dom->getElementsByTagName('span');
                foreach ($spans as $span) {
                    echo $span->nodeValue . HTML_ENDOFLINE;
                    if (trim($span->nodeValue) == 'Account value') {
                        echo 'Login ok' . HTML_ENDOFLINE;
                        $confirm = true;
                        break;
                    }
                }

                if (!$confirm) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Bondora login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }

                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 4:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $trs = $dom->getElementsByTagName('tr');
                foreach ($trs as $tr) {
                    echo $tr->nodeValue . HTML_ENDOFLINE;
                    if (strpos($tr->nodeValue, "Investments list")) {
                        $urls = $tr->getElementsByTagName('a');
                        $this->tempUrl['downloadInvesment'] = $urls[0]->getAttribute('href');
                        $this->tempUrl['deleteInvesment'] = $urls[1]->getAttribute('href');
                    }
                    if (strpos($tr->nodeValue, "Account statement")) {
                        $urls = $tr->getElementsByTagName('a');
                        $this->tempUrl['downloadCashFlow'] = $urls[0]->getAttribute('href');
                        $this->tempUrl['deleteCashFlow'] = $urls[1]->getAttribute('href');
                    }
                }

                if (empty($this->downloadDeleteUrl)) {
                    $this->tempUrl['baseDownloadDelete'] = array_shift($this->urlSequence);
                }

                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['downloadInvesment'];
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, null, false, null, 'Bondora_Investment');
                break;
            case 5:
                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['downloadCashFlow'];
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, null, false, null, 'Bondora_CashFlow');
                break;
            case 6:
                return $tempArray = 'DEscargando fichero';
                break;
        }
    }

}
