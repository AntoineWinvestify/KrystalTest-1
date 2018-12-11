<?php

/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               |
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
 * @author 
 * @version 0.1
 * @date 2018-12-03
 * @package
 */
class DeleteDataShell extends AppShell {

    //Class variables
    protected $amortizationTableFilter;                                         //Amortizationtable id list to delete tables that have this as reference.
    protected $investmentsliceFilter;                                           //Investmentslice id list to delete tables that have this as reference.
    protected $investmentFilter;                                                //Investment id list to delete tables that have this as reference.
    protected $linkAccountFilter;                                               //Linkedaccount id list to delete tables that have this as reference.

    /**
     * Constructor of the class
     */
    function __construct() {
        
    }

    /**
     * Function for deleting data from an user, specific linkedaccount/s  or all investment data, without deleting the linkedaccount definition data.
     * 
     * @param  int    $investorId The id of the investor that we will delete the data
     * @param  string   $linkedaccountsId The id of the linkedaccounts that will be deleted.
     */
    public function deleteFromUser() {

        $investorId = $this->args[0];
        $linkedaccountsId = explode(",", $this->args[1]);

        if (empty($linkedaccountsId[0])) {
            $linkedaccountsId = null;
        }

        if (strtoupper($investorId) == 'HELP' || strtoupper($investorId) == 'H') {
            echo 'This function will delete the date of the given investor, if not linkedaccount passed, it will delete the data of all linkedaccount data of '
            . 'that investor\n';
            echo 'DeleteData deleteFromUser investorId [LinkaccountId1,LinkaccountId2,LinkaccountId3, ...]\n';
            exit;
        } 
        else if (!is_numeric($investorId) || (!$this->is_numeric_array($linkedaccountsId) && !empty($linkedaccountsId))) {
            echo 'investorId and LinkaccountId must be integer';
            exit;
        }

        echo "Are you sure you want to do this, this will delete data from the DB?  Type 'yes' to continue:\n";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (strtoupper(trim($line)) != 'YES') {
            echo "ABORTING!\n";
            exit;
        }
        fclose($handle);
        echo "\n";
        echo "Start deleting process\n";
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $this->Investment = ClassRegistry::init('Investment');
        $this->Investmentslice = ClassRegistry::init('Investmentslice');
        $this->AmortizationTable = ClassRegistry::init('Amortizationtable');
        $this->Amortizationpayment = ClassRegistry::init('Amortizationpayment');
        $this->Roundingerrorcompensation = ClassRegistry::init('Roundingerrorcompensation');
        $this->Payment = ClassRegistry::init('Payment');
        $this->Paymenttotal = ClassRegistry::init('Paymenttotal');
        $this->Globalcashflowdata = ClassRegistry::init('Globalcashflowdata');
        $this->Globaltotalsdata = ClassRegistry::init('Globaltotalsdata');
        $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');
        $this->GlobalamortizationtablesInvestmentslice = ClassRegistry::init('GlobalamortizationtablesInvestmentslice');

        $this->calculateDeleteFilters($investorId, $linkedaccountsId);

        //Stop the proccess if a linkedaccount doesn't belong to the investor
        if (!empty($linkedaccountsId)) {
            $this->checkLinkedaccountIds($investorId, $linkedaccountsId);
        }

        //Stop the process if a queue for one of that linkedaccounts is running
        $this->checkQueue2();

        echo "Deleting Amortizationpayment\n";
        $this->Amortizationpayment->deleteAll($this->amortizationTableFilter, false);

        echo "Deleting AmortizationTable\n";
        $this->AmortizationTable->deleteAll($this->investmentsliceFilter, false);
        echo "Deleting GlobalamortizationtablesInvestmentslice\n";
        $this->GlobalamortizationtablesInvestmentslice->deleteAll($this->investmentsliceFilter, false);

        echo "Deleting Paymenttotal\n";
        $this->Paymenttotal->deleteAll($this->investmentFilter, false);
        echo "Deleting Payment\n";
        $this->Payment->deleteAll($this->investmentFilter, false);
        echo "Deleting Roundingerrorcompensation table\n";
        $this->Roundingerrorcompensation->deleteAll($this->investmentFilter, false);
        echo "Deleting Investmentslice\n";
        $this->Investmentslice->deleteAll($this->investmentFilter, false);

        echo "Deleting Investment\n";
        $this->Investment->deleteAll($this->linkAccountFilter, false);
        echo "Deleting Globalcashflowdata\n";
        $this->Globalcashflowdata->deleteAll($this->linkAccountFilter, false);
        echo "Deleting Globaltotalsdata\n";
        $this->Globaltotalsdata->deleteAll($this->linkAccountFilter, false);
        echo "Deleting Userinvestmentdata\n";
        $this->Userinvestmentdata->deleteAll($this->linkAccountFilter, false);

        echo 'Deleting process finished\n';
        /* echo "Deleting Dashboardoverview table\n";
          $this->Dashboardoverviewdata = ClassRegistry::init('Dashboardoverviewdata');
          $this->Dashboardoverviewdata->deleteAll(array('Dashboardoverviewdata.id >' => 0), false); */
    }

    /**
     * Calculate the filters that reference the DB tables,
     * 
     * @param type $investorId
     * @param type $linkedaccountsId
     */
    public function calculateDeleteFilters($investorId, $linkedaccountsId = null) {
        //Linkaccount filter to delete most of the tables
        unset($idArray);
        if (!empty($linkedaccountsId)) {
            $this->linkAccountFilter = array('linkedaccount_id' => $linkedaccountsId);
        } 
        else {
            $preFilter = array('investor_id' => $investorId);                      //Find all linkedaccount of the investor

            $idlist = $this->Linkedaccount->getData($preFilter, array('id'));
            foreach ($idlist as $id) {
                $idArray[] = $id['Linkedaccount'] ['id'];
            }
            $this->linkAccountFilter = array('linkedaccount_id' => $idArray);
        }

        //Get investment filter to delete Payment, Paymenttotals, Roundingerrors, Investmentslice
        unset($idArray);
        $idlist = $this->Investment->getData($this->linkAccountFilter, array('id'));
        foreach ($idlist as $id) {
            $idArray[] = $id['Investment'] ['id'];
        }
        $this->investmentFilter = array('investment_id' => $idArray);


        //Get slice of the investment to delete AmortizationTables and GlobalamortizationtablesInvestmentslices
        unset($idArray);
        $idlist = $this->investmentslice->getData($this->investmentFilter, array('id'));
        foreach ($idlist as $id) {
            $idArray[] = $id['Investmentslice'] ['id'];
        }
        $this->investmentsliceFilter = array('investmentslice_id' => $idArray);


        //Get amortizationtable and globalarmotizationtable to delete amortization payments
        unset($idArray);
        $idlist = $this->AmortizationTable->getData($this->investmentsliceFilter, array('id'));
        foreach ($idlist as $id) {
            $idArray[] = $id['AmortizationTable'] ['id'];
        }
        $this->amortizationTableFilter = array('amortizationtable_id' => $idArray);
    }

    /**
     * Check if the linkedaccounts are owned by the given investor.
     * If not, cancel the execution.
     */
    public function checkLinkedaccountIds($investorId, $linkedaccountsId) {
        $preFilter = array('investor_id' => $investorId);                       //Find all linkedaccount of the investor       
        $idlist = $this->Linkedaccount->getData($preFilter, array('id'));
        foreach ($idlist as $id) {
            $idArray[] = $id['Linkedaccount'] ['id'];
        }
        foreach ($linkedaccountsId as $id) {
            if (!in_array($id, $idArray)) {
                echo "The linkedaccount $id doesn't belong to the investor $investorId";
                exit;
            }
        }
    }

    /**
     * Check if the linkedaccounts are active in a queue2 entry.
     * If is active, cancel the execution.
     */
    public function checkQueue2() {
        $this->Queue2 = ClassRegistry::init('Queue2');
        $queueList = $this->Queue2->getData(array('queue2_status !=' => array(WIN_QUEUE_STATUS_CONSOLIDATION_FINISHED, WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED)));
        foreach ($queueList as $queueInfo) {
            $queueInfoArray = json_decode($queueInfo['Queue2']['queue2_info'], true);
            $companiesInFlow = $queueInfoArray['companiesInFlow'];
            foreach ($this->linkAccountFilter['linkedaccount_id'] as $linkedaccountId) {
                if (in_array($linkedaccountId, $companiesInFlow)) {
                    echo $linkedaccountId . " is in an unfinished queue process, you can't delete the data\n";
                    exit;
                }
            }
        }
    }

    /**
     * Function to check if an array only has numeric values
     * 
     * @param type $array
     * @return boolean
     */
    function is_numeric_array($array) {
        foreach ($array as $a => $b) {
            if (!is_numeric($b)) {
                return false;
            }
        }
        return true;
    }

}
