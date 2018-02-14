<?php

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
            $linkaccounts[$investor['Investor']['investor_identity']] = $this->Linkedaccount->getData(['investor_id' => $investor['Investor']['id'], 'company_id' => $hasPreprocess], ['id', 'company_id']);
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
            $linkaccounts[$investor['Investor']['investor_identity']] = $this->Linkedaccount->getData(['investor_id' => $investor['Investor']['id']], ['id', 'company_id']);
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
