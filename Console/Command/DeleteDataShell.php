<?php

/*
 * Copyright (C) 2018 frodo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class DeleteDataShell extends AppShell {

    /**
     * Constructor of the class
     */
    function __construct() {
        
    }

    /**
     * Function for delete data from a certain user or linkaccount/s without delete all DB.
     */
    public function deleteFromUser() {

        $investorId = $this->args[0];
        $linkaccountsId = explode(",", $this->args[1]);

        if (empty($linkaccountsId[0])) {
            $linkaccountsId = null;
        }

        if (strtoupper($investorId) == 'HELP' || strtoupper($investorId) == 'H') {
            echo 'This function will delete the date of the given investor, if not linkaccount passed, it will delete the data of all linkedaccount of'
            . 'that user';
            echo 'DeleteData deleteFromUser investorId [LinkaccountId1,LinkaccountId2,LinkaccountId3, ...]';
            exit;
        } else if (!is_numeric($investorId) || (!$this->is_numeric_array($linkaccountsId) && !empty($linkaccountsId))) {
            echo 'investorId and LinkaccountId must be an int';
            exit;
        }

        echo "Are you sure you want to do this, this will delete data from the DB?  Type 'yes' to continue: ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (strtoupper(trim($line)) != 'YES' && strtoupper(trim($line)) != 'Y') {
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

        $this->getFiltersToDelete($investorId, $linkaccountsId);

        /*//Stop the process if a queue for that linkaccount is running
        $this->Queue2 = ClassRegistry::init('Queue2');
        $queueList = $this->Queue2->getData(array('queue2_status !=' => array(WIN_QUEUE_STATUS_CONSOLIDATION_FINISHED, WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED)));
        foreach ($queueList as $queueInfo) {
            $queueInfoArray = json_decode($queueInfo['Queue2']['queue2_info'], true);
            $companiesInFlow = $queueInfoArray['companiesInFlow'];
            foreach ($this->LinkAccountFilter['linkedaccount_id'] as $linkaccountId) {
                if (in_array($linkaccountId, $companiesInFlow)) {
                    echo $linkaccountId . " is in a queue unfinished proccess, you can't delete the data";
                    exit;
                }
            }
        }*/

        echo "Deleting Amortizationpayment\n";
        $this->Amortizationpayment->deleteAll($this->AmortizationTableFilter, false);

        echo "Deleting AmortizationTable\n";
        $this->AmortizationTable->deleteAll($this->InvestmentsliceFilter, false);
        echo "Deleting GlobalamortizationtablesInvestmentslice\n";
        $this->GlobalamortizationtablesInvestmentslice->deleteAll($this->InvestmentsliceFilter, false);

        echo "Deleting Paymenttotal\n";
        $this->Paymenttotal->deleteAll($this->InvestmentFilter, false);
        echo "Deleting Payment\n";
        $this->Payment->deleteAll($this->InvestmentFilter, false);
        echo "Deleting Roundingerrorcompensation table\n";
        $this->Roundingerrorcompensation->deleteAll($this->InvestmentFilter, false);
        echo "Deleting Investmentslice\n";
        $this->Investmentslice->deleteAll($this->InvestmentFilter, false);

        echo "Deleting Investment\n";
        $this->Investment->deleteAll($this->LinkAccountFilter, false);
        echo "Deleting Globalcashflowdata\n";
        $this->Globalcashflowdata->deleteAll($this->LinkAccountFilter, false);
        echo "Deleting Globaltotalsdata\n";
        $this->Globaltotalsdata->deleteAll($this->LinkAccountFilter, false);
        echo "Deleting Userinvestmentdata\n";
        $this->Userinvestmentdata->deleteAll($this->LinkAccountFilter, false);

        /* echo "Deleting Dashboardoverview table\n";
          $this->Dashboardoverviewdata = ClassRegistry::init('Dashboardoverviewdata');
          $this->Dashboardoverviewdata->deleteAll(array('Dashboardoverviewdata.id >' => 0), false); */
    }

    public function getFiltersToDelete($investorId, $linkaccountsId = null) {
        //Linkaccount filter to delete most of the tables
        unset($IdArray);
        if (!empty($linkaccountsId)) {
            $this->LinkAccountFilter = array('linkedaccount_id' => $linkaccountsId);
        } else {
            $Prefilter = array('investor_id' => $investorId);                      //Find all linkaccount of the investor

            $Idlist = $this->Linkedaccount->getData($Prefilter, array('id'));
            foreach ($Idlist as $id) {
                $IdArray[] = $id['Linkedaccount'] ['id'];
            }
            $this->LinkAccountFilter = array('linkedaccount_id' => $IdArray);
        }

        //Get investment filter to delete Payment, Paymenttotals, Roundingerrors, Investmentslice
        unset($IdArray);
        $Idlist = $this->Investment->getData($this->LinkAccountFilter, array('id'));
        foreach ($Idlist as $id) {
            $IdArray[] = $id['Investment'] ['id'];
        }
        $this->InvestmentFilter = array('investment_id' => $IdArray);


        //Get slice of the investment to delete AmortizationTables and GlobalamortizationtablesInvestmentslices
        unset($IdArray);
        $Idlist = $this->Investmentslice->getData($this->InvestmentFilter, array('id'));
        foreach ($Idlist as $id) {
            $IdArray[] = $id['Investmentslice'] ['id'];
        }
        $this->InvestmentsliceFilter = array('investmentslice_id' => $IdArray);


        //Get amortizationtable and globalarmotizationtable to delete amortization payments
        unset($IdArray);
        $Idlist = $this->AmortizationTable->getData($this->InvestmentsliceFilter, array('id'));
        foreach ($Idlist as $id) {
            $IdArray[] = $id['AmortizationTable'] ['id'];
        }
        $this->AmortizationTableFilter = array('amortizationtable_id' => $IdArray);
        
        /*print_r( $this->InvestmentFilter);
        exit;*/
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
