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

    
 
    protected $valuesAmortizationTable = [  // NOT FINISHED
            "A" =>  [
                "name" => "transaction_id"
             ],
        ];    

    protected $transactionConfigParms = array ('OffsetStart' => 1,
                                'offsetEnd'     => 0,
                        //        'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );
 
    protected $investmentConfigParms = array ('OffsetStart' => 1,
                                'offsetEnd'     => 0,
                         //       'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );
/*   NOT YET READY
    protected $amortizationConfigParms = array ('OffsetStart' => 1,
                                'offsetEnd'     => 0,
                         //       'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
                                 );
*/    
    
    function __construct() {
        parent::__construct();
        $this->i = 0;
        //$this->loanIdArray = array("6b3649c5-9a6b-4cee-ac05-a55500ef480a");
        //$this->maxLoans = count($this->loanIds);
// Do whatever is needed for this subsclass
    }

    public function getParserConfigTransactionFile() {
        return $this->$valuesBondoraTransaction;
    }

    public function getParserConfigInvestmentFile() {
        return $this->$valuesBondoraInvestment;
    }

    public function getParserConfigAmortizationTableFile() {
        return $this->$valuesBondoraAmortization;
    }

    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY Bondora DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */

        //First we need get te token
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;


        $inputs = $dom->getElementsByTagName('input');
        foreach ($inputs as $key => $input) {
            //echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . HTML_ENDOFLINE;
            if ($key == 0) {
                continue;
            }
            $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
        }

        $credentials['Email'] = $user;
        $credentials['Password'] = $password;

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
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
     *  Generate report to download.
     * @param type $str
     */
    function generateReportParallel($str = null) {
        switch ($this->idForSwitch) {
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to page of the company
                break;
            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $inputs = $dom->getElementsByTagName('input');

                foreach ($inputs as $key => $input) {
                    echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . SHELL_ENDOFLINE;
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
                echo 'Doing loging' . SHELL_ENDOFLINE;
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
                    echo $span->nodeValue . SHELL_ENDOFLINE;
                    if (trim($span->nodeValue) == 'Account value') {
                        echo 'Login ok' . SHELL_ENDOFLINE;
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
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
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
                    $inputsValue[$input->getAttribute('name')] = $input->getAttribute('value');
                }
                echo "INPUTS VALUE" . SHELL_ENDOFLINE;
                $this->print_r2($inputsValue);
                echo "ENDS INPUTS VALUE" . SHELL_ENDOFLINE;
                $date1 = "14/09/2017";
                $date2 = "20/09/2017";
                $credentials = array(
                    '__RequestVerificationToken' => $inputsValue['__RequestVerificationToken'],
                    'NewReports[0].ReportType' => 'InvestmentsListV2',
                    "NewReports[0].DateFilterRequired" => 'False',
                    "NewReports[0].DateFilterShown" => 'True',
                    "NewReports[0].Selected" => 'true',
                    //"NewReports[0].Selected" => false,
                    "NewReports[0].DateFilterSelected" => 'true',
                    //"NewReports[0].DateFilterSelected" => false,
                    "NewReports[0].StartDate" => $date1, //22/08/2017
                    "NewReports[0].EndDate" => $date2, //20/09/2017
                    "NewReports[1].ReportType" => "Repayments",
                    "NewReports[1].DateFilterRequired" => 'False',
                    "NewReports[1].DateFilterShown" => 'True',
                    "NewReports[1].Selected" => 'false',
                    "NewReports[1].DateFilterSelected" => 'false',
                    "NewReports[2].ReportType" => 'PlannedFutureCashflows',
                    "NewReports[2].DateFilterRequired" => 'False',
                    "NewReports[2].DateFilterShown" => 'True',
                    "NewReports[2].Selected" => 'false',
                    "NewReports[2].DateFilterSelected" => 'false',
                    "NewReports[3].ReportType" => 'SecondMarketArchive',
                    "NewReports[3].DateFilterRequired" => 'False',
                    "NewReports[3].DateFilterShown" => 'True',
                    "NewReports[3].Selected" => 'false',
                    "NewReports[3].DateFilterSelected" => 'false',
                    "NewReports[4].ReportType" => 'MonthlyOverview',
                    "NewReports[4].DateFilterRequired" => 'False',
                    "NewReports[4].DateFilterShown" => 'True',
                    "NewReports[4].Selected" => 'false',
                    "NewReports[4].DateFilterSelected" => 'false',
                    "NewReports[5].ReportType" => 'AccountStatement',
                    "NewReports[5].DateFilterRequired" => 'False',
                    "NewReports[5].DateFilterShown" => 'True',
                    "NewReports[5].Selected" => 'true',
                    //"NewReports[5].Selected" => false,
                    "NewReports[5].DateFilterSelected" => 'true',
                    //"NewReports[5].DateFilterSelected" => false,
                    "NewReports[5].StartDate" => $date1, //14/09/2017
                    "NewReports[5].EndDate" => $date2, //21/09/2017
                    "NewReports[6].ReportType" => 'IncomeReport',
                    "NewReports[6].DateFilterRequired" => 'True',
                    "NewReports[6].DateFilterShown" => 'True',
                    "NewReports[6].DateFilterSelected" => 'True',
                    "NewReports[6].Selected" => 'false',
                    "NewReports[7].ReportType" => 'TaxReportPdf',
                    "NewReports[7].DateFilterRequired" => 'True',
                    "NewReports[7].DateFilterShown" => 'True',
                    "NewReports[7].DateFilterSelected" => 'True',
                    "NewReports[7].Selected" => 'false',
                    "NewReports[8].ReportType" => 'AccountValue',
                    "NewReports[8].DateFilterRequired" => 'False',
                    "NewReports[8].DateFilterShown" => 'True',
                    "NewReports[8].Selected" => 'false',
                    "NewReports[8].DateFilterSelected" => 'false',
                );
                echo "CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->print_r2($credentials);
                echo "END CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl(null, $credentials);
                break;
            case 5:
                echo $str . SHELL_ENDOFLINE;
                return $tempArray = 'Generando informe';
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
                    echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . SHELL_ENDOFLINE;
                    if ($key == 0) {
                        continue;
                    }
                    $this->credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $this->credentials['Email'] = $this->user;
                $this->credentials['Password'] = $this->password;

                print_r($this->credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials); //do login
                break;

            case 2:
                echo 'Doing loging' . SHELL_ENDOFLINE;
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
                    echo $span->nodeValue . SHELL_ENDOFLINE;
                    if (trim($span->nodeValue) == 'Account value') {
                        echo 'Login ok' . SHELL_ENDOFLINE;
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
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }

                //Get global data
                $this->tempArray['global'] = "";
                $spans = $dom->getElementsByTagName("span");
                echo "GLOBAL DATA: ";
                foreach ($spans as $globalDataKey => $span) {
                    echo $globalDataKey . " IS " . $span->getAttribute('data-original-title');
                }

                $this->idForSwitch++;
                $this->tempUrl['reportUrl'] = array_shift($this->urlSequence);
                $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                break;

            case 4:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $trs = $dom->getElementsByTagName('tr');
                $date1 = "14/09/2017";
                $date2 = "20/09/2017";
                if (empty($this->tempUrl['generateReport'])) {
                    $this->tempUrl['generateReport'] = array_shift($this->urlSequence);
                }
                foreach ($trs as $tr) {
                    echo $tr->nodeValue . SHELL_ENDOFLINESHELL_ENDOFLINE;
                    if (strpos($tr->nodeValue, "Investments list") && strpos($tr->nodeValue, $date1) && strpos($tr->nodeValue, $date2)) {
                        $urls = $tr->getElementsByTagName('a');
                        $this->tempUrl['downloadInvesment'] = $urls[0]->getAttribute('href');
                        $this->tempUrl['deleteInvesment'] = $urls[1]->getAttribute('href');
                        break;
                    } else {
                        $inputs = $dom->getElementsByTagName('input');
                        foreach ($inputs as $key => $input) {
                            $inputsValue[$input->getAttribute('name')] = $input->getAttribute('value');
                        }
                        echo "INPUTS VALUE" . SHELL_ENDOFLINE;
                        $this->print_r2($inputsValue);
                        echo "ENDS INPUTS VALUE" . SHELL_ENDOFLINE;
                        $date1 = "14/09/2017";
                        $date2 = "20/09/2017";
                        $credentials = array(
                            '__RequestVerificationToken' => $inputsValue['__RequestVerificationToken'],
                            'NewReports[0].ReportType' => 'InvestmentsListV2',
                            "NewReports[0].DateFilterRequired" => 'False',
                            "NewReports[0].DateFilterShown" => 'True',
                            "NewReports[0].Selected" => 'true',
                            //"NewReports[0].Selected" => false,
                            "NewReports[0].DateFilterSelected" => 'true',
                            //"NewReports[0].DateFilterSelected" => false,
                            "NewReports[0].StartDate" => $date1, //22/08/2017
                            "NewReports[0].EndDate" => $date2, //20/09/2017
                            "NewReports[1].ReportType" => "Repayments",
                            "NewReports[1].DateFilterRequired" => 'False',
                            "NewReports[1].DateFilterShown" => 'True',
                            "NewReports[1].Selected" => 'false',
                            "NewReports[1].DateFilterSelected" => 'false',
                            "NewReports[2].ReportType" => 'PlannedFutureCashflows',
                            "NewReports[2].DateFilterRequired" => 'False',
                            "NewReports[2].DateFilterShown" => 'True',
                            "NewReports[2].Selected" => 'false',
                            "NewReports[2].DateFilterSelected" => 'false',
                            "NewReports[3].ReportType" => 'SecondMarketArchive',
                            "NewReports[3].DateFilterRequired" => 'False',
                            "NewReports[3].DateFilterShown" => 'True',
                            "NewReports[3].Selected" => 'false',
                            "NewReports[3].DateFilterSelected" => 'false',
                            "NewReports[4].ReportType" => 'MonthlyOverview',
                            "NewReports[4].DateFilterRequired" => 'False',
                            "NewReports[4].DateFilterShown" => 'True',
                            "NewReports[4].Selected" => 'false',
                            "NewReports[4].DateFilterSelected" => 'false',
                            "NewReports[5].ReportType" => 'AccountStatement',
                            "NewReports[5].DateFilterRequired" => 'False',
                            "NewReports[5].DateFilterShown" => 'True',
                            "NewReports[5].Selected" => 'false',
                            "NewReports[5].DateFilterSelected" => 'false',
                            "NewReports[6].ReportType" => 'IncomeReport',
                            "NewReports[6].DateFilterRequired" => 'False',
                            "NewReports[6].DateFilterShown" => 'True',
                            "NewReports[6].DateFilterSelected" => 'True',
                            "NewReports[6].Selected" => 'false',
                            "NewReports[7].ReportType" => 'TaxReportPdf',
                            "NewReports[7].DateFilterRequired" => 'True',
                            "NewReports[7].DateFilterShown" => 'True',
                            "NewReports[7].DateFilterSelected" => 'True',
                            "NewReports[7].Selected" => 'false',
                            "NewReports[8].ReportType" => 'AccountValue',
                            "NewReports[8].DateFilterRequired" => 'False',
                            "NewReports[8].DateFilterShown" => 'True',
                            "NewReports[8].Selected" => 'false',
                            "NewReports[8].DateFilterSelected" => 'false',
                        );
                        echo "CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                        $this->print_r2($credentials);
                        echo "END CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                        $this->idForSwitch = 10;
                        $this->getCompanyWebpageMultiCurl($this->tempUrl['generateReport'], $credentials);
                        break;
                    }
                }
                foreach ($trs as $tr) {
                    echo $tr->nodeValue . SHELL_ENDOFLINE;
                    if (strpos($tr->nodeValue, "Account statement") && strpos($tr->nodeValue, $date1) && strpos($tr->nodeValue, $date2)) {
                        $urls = $tr->getElementsByTagName('a');
                        $this->tempUrl['downloadCashFlow'] = $urls[0]->getAttribute('href');
                        $this->tempUrl['deleteCashFlow'] = $urls[1]->getAttribute('href');
                        break;
                    } else {
                        $inputs = $dom->getElementsByTagName('input');
                        foreach ($inputs as $key => $input) {
                            $inputsValue[$input->getAttribute('name')] = $input->getAttribute('value');
                        }
                        echo "INPUTS VALUE" . SHELL_ENDOFLINE;
                        $this->print_r2($inputsValue);
                        echo "ENDS INPUTS VALUE" . SHELL_ENDOFLINE;
                        $date1 = "14/09/2017";
                        $date2 = "20/09/2017";
                        $credentials = array(
                            '__RequestVerificationToken' => $inputsValue['__RequestVerificationToken'],
                            'NewReports[0].ReportType' => 'InvestmentsListV2',
                            "NewReports[0].DateFilterRequired" => 'False',
                            "NewReports[0].DateFilterShown" => 'True',
                            "NewReports[0].Selected" => 'false',
                            "NewReports[0].DateFilterSelected" => 'false',
                            "NewReports[1].ReportType" => "Repayments",
                            "NewReports[1].DateFilterRequired" => 'False',
                            "NewReports[1].DateFilterShown" => 'True',
                            "NewReports[1].Selected" => 'false',
                            "NewReports[1].DateFilterSelected" => 'false',
                            "NewReports[2].ReportType" => 'PlannedFutureCashflows',
                            "NewReports[2].DateFilterRequired" => 'False',
                            "NewReports[2].DateFilterShown" => 'True',
                            "NewReports[2].Selected" => 'false',
                            "NewReports[2].DateFilterSelected" => 'false',
                            "NewReports[3].ReportType" => 'SecondMarketArchive',
                            "NewReports[3].DateFilterRequired" => 'False',
                            "NewReports[3].DateFilterShown" => 'True',
                            "NewReports[3].Selected" => 'false',
                            "NewReports[3].DateFilterSelected" => 'false',
                            "NewReports[4].ReportType" => 'MonthlyOverview',
                            "NewReports[4].DateFilterRequired" => 'False',
                            "NewReports[4].DateFilterShown" => 'True',
                            "NewReports[4].Selected" => 'false',
                            "NewReports[4].DateFilterSelected" => 'false',
                            "NewReports[5].ReportType" => 'AccountStatement',
                            "NewReports[5].DateFilterRequired" => 'False',
                            "NewReports[5].DateFilterShown" => 'True',
                            "NewReports[5].Selected" => 'true',
                            //"NewReports[5].Selected" => false,
                            "NewReports[5].DateFilterSelected" => 'true',
                            //"NewReports[5].DateFilterSelected" => false,
                            "NewReports[5].StartDate" => $date1, //14/09/2017
                            "NewReports[5].EndDate" => $date2, //21/09/2017
                            "NewReports[6].ReportType" => 'IncomeReport',
                            "NewReports[6].DateFilterRequired" => 'True',
                            "NewReports[6].DateFilterShown" => 'True',
                            "NewReports[6].DateFilterSelected" => 'True',
                            "NewReports[6].Selected" => 'false',
                            "NewReports[7].ReportType" => 'TaxReportPdf',
                            "NewReports[7].DateFilterRequired" => 'True',
                            "NewReports[7].DateFilterShown" => 'True',
                            "NewReports[7].DateFilterSelected" => 'True',
                            "NewReports[7].Selected" => 'false',
                            "NewReports[8].ReportType" => 'AccountValue',
                            "NewReports[8].DateFilterRequired" => 'False',
                            "NewReports[8].DateFilterShown" => 'True',
                            "NewReports[8].Selected" => 'false',
                            "NewReports[8].DateFilterSelected" => 'false',
                        );
                        echo "CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                        $this->print_r2($credentials);
                        echo "END CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                        $this->idForSwitch = 10;
                        $this->getCompanyWebpageMultiCurl($this->tempUrl['generateReport'], $credentials);
                        break;
                    }
                }

                if (empty($this->downloadDeleteUrl)) {
                    $this->tempUrl['baseDownloadDelete'] = array_shift($this->urlSequence);
                }

                print_r($this->tempUrl);

                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['downloadInvesment'];
                $fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, null, false, null, $fileName);
                break;

            case 5:
                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['downloadCashFlow'];
                $fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, null, false, null, $fileName);
                break;

            case 6:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;

            case 7:

                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $scripts = $dom->getElementsByTagName('script');
                foreach ($scripts as $script) {
                    //echo "search scripts: " . SHELL_ENDOFLINE;
                    //echo $script->nodeValue . SHELL_ENDOFLINE;
                    if (strpos($script->nodeValue, "RequestVerificationToken") != false) {
                        echo 'Finded: ' . SHELL_ENDOFLINE;
                        $deleteTokenArray = explode('"', $script->nodeValue);
                        $this->print_r2($deleteTokenArray);
                        $this->deleteToken = $deleteTokenArray[7];
                        echo "---___--- " . $this->deleteToken . " ---___---";
                    }
                }



                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['deleteInvesment'];
                echo "delete: " . $url . SHELL_ENDOFLINE;
                $this->idForSwitch++;
                $this->headers = array("__RequestVerificationToken: " . $this->deleteToken, ":Type: POST", 'Host: www.bondora.com', 'Accept: */*', 'Accept-Language: en-US,en;q=0.5', 'Accept-Encoding: gzip, deflate, br', 'X-Requested-With: XMLHttpRequest', 'Connection: keep-alive', "content-length: 0", "Retry-After: 120");
                $this->getCompanyWebpageMultiCurl($url);
                unset($this->headers);
                break;

            case 8:
                echo $str . SHELL_ENDOFLINE;
                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['deleteCashFlow'];
                $this->idForSwitch++;
                $this->headers = array("__RequestVerificationToken: " . $this->deleteToken, 'Host: www.bondora.com', 'Accept: */*', 'Accept-Language: en-US,en;q=0.5', 'Accept-Encoding: gzip, deflate, br', 'X-Requested-With: XMLHttpRequest', 'Connection: keep-alive');
                $this->getCompanyWebpageMultiCurl($url);
                unset($this->headers);
                break;

            case 9:
                echo $str . SHELL_ENDOFLINE;
                //return $tempArray = 'DEscargando fichero';
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl("https://www.bondora.com/en/dashboard/statnumbers/");
                break;
            case 10:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $tds = $dom->getElementsByTagName('td');
                /* foreach($tds as $key=>$td){
                  echo $key . " is " . $td->nodeValue;
                  } */

                $this->tempArray['global']['activeInInvestments'] = $this->getMonetaryValue($tds[14]->nodeValue);  //Capital vivo
                $this->tempArray['global']['myWallet'] = $this->getMonetaryValue($tds[2]->nodeValue); //My wallet

                $spans = $dom->getElementsByTagName('span');
                /* foreach($spans as $key=>$span){
                  echo $key . " is " . $span->getAttribute('title');
                  } */

                $this->tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($spans[3]->getAttribute('title'));

                print_r($this->tempArray);
                return $this->tempArray();
                break;
            /* case 10:
              sleep(5);
              $this->idForSwitch = 4;
              $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
              break;
             */
        }
    }

    /**
     * 
     * @param type $str
     * @return type
     */
    function collectAmortizationTablesParallel($str) {
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
                    echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . SHELL_ENDOFLINE;
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
                echo 'Doing loging' . SHELL_ENDOFLINE;
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
                    echo $span->nodeValue . SHELL_ENDOFLINE;
                    if (trim($span->nodeValue) == 'Account value') {
                        echo 'Login ok' . SHELL_ENDOFLINE;
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
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }

                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 4:

                if (empty($this->tempUrl['investmentUrl'])) {
                    $this->tempUrl['investmentUrl'] = array_shift($this->urlSequence);
                }
                echo "Loan number " . $this->i . " is " . $this->loanIds[$this->i];
                $url = $this->tempUrl['investmentUrl'] . $this->loanIds[$this->i];
                echo "the table url is: " . $url;
                $this->i++;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url);  // Read individual investment
                break;
            case 5:

                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                echo "Read table: ";
                $tables = $dom->getElementsByTagName('table');

                foreach ($tables as $table) {
                    if ($table->getAttribute('class') == 'table') {
                        $AmortizationTable = new DOMDocument();
                        $clone = $table->cloneNode(TRUE); //Clene the table
                        $AmortizationTable->appendChild($AmortizationTable->importNode($clone, TRUE));
                        $AmortizationTableString = $AmortizationTable->saveHTML();
                        $this->tempArray[$this->loanIds[$this->i - 1]] = $AmortizationTableString;
                        echo $AmortizationTableString;
                    }
                }


                if ($this->i < $this->maxLoans) {
                    $this->idForSwitch = 4;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['investmentUrl'] . $this->loanIds[$this->i - 1]);
                    break;
                } else {
                    return $this->tempArray;
                    break;
                }
        }
    }

}
