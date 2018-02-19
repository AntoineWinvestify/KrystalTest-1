<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +-----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                |
 * | GNU General Public License for more details.        			|
 * +-----------------------------------------------------------------------------+
 *
 *
 * @author 
 * @version 0.1
 * @date 2018-02-13
 * @package
 *
 *
 *  Shell for cron methods of queues2
 *
 */

require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');
App::uses('CakeEvent', 'Event');
App::uses('CakeTime', 'Utility');

class Queue2Shell extends AppShell {

    public function main() {
        
    }

    /**
     * Put a new request for the preprocess into the queue for Dashboard 2.0
     * 
     */
    public function cronAddToQueue2Preprocess() {

        $this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $this->Queue2 = ClassRegistry::init('Queue2');
        $this->Company = ClassRegistry::init('Company');

        $investors = $this->Investor->getData(null, ['id', 'investor_identity']);

        $companiesWithPreprocess = $this->Company->getData(['company_typeAccessPreprocess !=' => null], ['id']);                //if company_typeAccessPreprocess isnt null, the company must have preprocess
        foreach ($companiesWithPreprocess as $companyWithPreprocess) {
            $hasPreprocess[] = $companyWithPreprocess['Company']['id'];
        }

        foreach ($investors as $investor) {
            $linkaccounts[$investor['Investor']['investor_identity']] = $this->Linkedaccount->getData(['investor_id' => $investor['Investor']['id'], 'company_id' => $hasPreprocess, 'linkedaccount_linkingProcess' => WIN_LINKING_WORK_IN_PROCESS], ['id', 'company_id']);
        }

        foreach ($linkaccounts as $investorIdentity => $linkaccount) {
            foreach ($linkaccount as $linkedPfp) {
                $inFlow = $linkedPfp['Linkedaccount'] ['id'] . "," . $inFlow;
            }

            $infoString = '{"originExecution":2,"companiesInFlow":[' . rtrim($inFlow, ",") . ']}';

            if (!empty($inFlow)) {
                $data[] = array(
                    "queue2_userReference" => $investorIdentity,
                    "queue2_info" => $infoString,
                    "queue2_type" => FIFO,
                    "queue2_status" => WIN_QUEUE_STATUS_START_PREPROCESS,
                );

                $inFlow = '';
            }
        }

        $this->Queue2->saveMany($data);
    }

    /**
     * Put a new request into the queue for Dashboard 2.0
     * 
     */
    public function cronAddToQueue2() {

        $this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $this->Queue2 = ClassRegistry::init('Queue2');

        $investors = $this->Investor->getData(null, ['id', 'investor_identity']);

        foreach ($investors as $investor) {
            $linkaccounts[$investor['Investor']['investor_identity']] = $this->Linkedaccount->getData(['investor_id' => $investor['Investor']['id'], 'linkedaccount_linkingProcess' => WIN_LINKING_WORK_IN_PROCESS], ['id', 'company_id']);
        }

        foreach ($linkaccounts as $investorIdentity => $linkaccount) {
            foreach ($linkaccount as $linkedPfp) {
                $inFlow = $linkedPfp['Linkedaccount'] ['id'] . "," . $inFlow;
            }

            $infoString = '{"originExecution":2,"companiesInFlow":[' . rtrim($inFlow, ",") . ']}';

            if (!empty($inFlow)) {
                $data[] = array(
                    "queue2_userReference" => $investorIdentity,
                    "queue2_info" => $infoString,
                    "queue2_type" => FIFO,
                    "queue2_status" => WIN_QUEUE_STATUS_START_COLLECTING_DATA,
                );

                $inFlow = '';
            }
        }

        //print_r($data);
        $this->Queue2->saveMany($data);
    }

}
