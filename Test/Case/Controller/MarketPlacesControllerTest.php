<?php

class MarketPlacesControllerTest extends ControllerTestCase {
    
    public function testCronQueueIsCorrect() {

        $result_array = $this->testAction("/marketplaces/cronQueueEventParallel");
        
        //$result_array = json_decode($result_json, $assoc = true);
        /*$data_array = json_decode($data, $assoc = true);
        foreach ($result_array as $key => $item) {
        	echo $item;
        }*/
        $this->assertTrue(is_array($result_array));
        return $result_array;
    }
    
    /**
     * @depends testCronQueueIsCorrect
     */
    public function testCronQueueHasWallet(array $result_array)
    {
        $this->assertArrayHasKey('wallet', $result_array);
    }

    /**
     * @depends testCronQueueIsCorrect
     */
    public function testCronQueueHasAmountInvested(array $result_array)
    {
        $this->assertArrayHasKey('amountInvested', $result_array);
    }
    
    /**
     * @depends testCronQueueIsCorrect
     */
    public function testCronQueueHasProfitibilityAccumulative(array $result_array) {
        $this->assertArrayHasKey('profitibilityAccumulative', $result_array);
    }
    
    /**
     * @depends testCronQueueIsCorrect
     */
    public function testCronQueueHasTotalInvestments(array $result_array) {
        $this->assertArrayHasKey('totalInvestments', $result_array);
    }
    
    /**
     * @depends testCronQueueIsCorrect
     */
    public function testCronQueueHasActiveInvestments(array $result_array) {
        $this->assertArrayHasKey('activeInvestments', $result_array);
    }
    
    /**
     * @depends testCronQueueIsCorrect
     */
    public function testCronQueueHasInvestments(array $result_array) {
        $this->assertArrayHasKey('investments', $result_array);
        return $result_array["investments"];
    }
    
    /**
     * @depends testCronQueueHasInvestments
     */
    public function testCronQueueHasComunitae(array $result_investments) {
        $this->assertArrayHasKey('Comunitae', $result_investments);
    }
    
    /**
     * @depends testCronQueueHasInvestments
     */
    public function testCronQueueHasZank(array $result_investments) {
        $this->assertArrayHasKey('Zank', $result_investments);
    }
    
    /**
     * @depends testCronQueueHasInvestments
     */
    public function testCronQueueHasGrowly(array $result_investments) {
        $this->assertArrayHasKey('Grow.ly', $result_investments);
    }
    
    /**
     * @depends testCronQueueHasInvestments
     */
    public function testCronQueueHasCirculantis(array $result_investments) {
        $this->assertArrayHasKey('Circulantis', $result_investments);
    }
    
    /**
     * @depends testCronQueueHasInvestments
     */
    public function testCronQueueHasArboribus(array $result_investments) {
        $this->assertArrayHasKey('Arboribus', $result_investments);
    }
    
    /**
     * @depends testCronQueueHasInvestments
     */
    public function testCronQueueHasLoanbook(array $result_investments) {
        $this->assertArrayHasKey('Loanbook', $result_investments);
    }
    
    /**
     * @depends testCronQueueHasInvestments
     */
    public function testCronQueueHasLendix(array $result_investments) {
        $this->assertArrayHasKey('Lendix', $result_investments);
    }
    
    /**
     * @depends testCronQueueHasInvestments
     */
    public function testCronQueueHasMyTripleA(array $result_investments) {
        $this->assertArrayHasKey('MyTripleA', $result_investments);
    }
    
    
    
    /*private function verifyJson($data) {
            "amountInvested":30100,
   "wallet":4173,
   "totalEarnedInterest":1070,
   "profitibilityAccumulative":3952,
   "totalInvestments":6217,
   "activeInvestments":6,
    }*/
}

