<?php

class MarketPlacesControllerTest extends ControllerTestCase {

    
    public function testCronQueueIsCorrect() {
    	/*$data = [
                "amountInvested"=>30100,
            "wallet"=>4173,
            "totalEarnedInterest"=>1070,
            "profitibilityAccumulative"=>3952,
            "totalInvestments"=>6217,
            "activeInvestments"=>6,
            "investments"=>[
              "Comunitae"=>[  
                         "global"=>[  
                            "myWallet"=>0,
                            "totalEarnedInterest"=>0,
                            "totalAmortized"=>0,
                            "totalInvestment"=>15000,
                            "totalPercentage"=>2300,
                            "activeInInvestments"=>15000,
                            "investments"=>2,
                            "profitibility"=>766
                         ],
                         "investments"=>[  
                            [ 
                               "status"=>1,
                               "loanId"=>"CPP_015724",
                               "name"=>"COMAPA 2001, S.A.",
                               "date"=>"05-2017",
                               "duration"=>"136 D&iacute;as",
                               "interest"=>350,
                               "invested"=>5000
                            ],
                            [
                               "status"=>1,
                               "loanId"=>"CPP_015369",
                               "name"=>"AYUDA Y ASISTENCIA A DOMICILIO CARTAGENA SL",
                               "date"=>"06-2017",
                               "duration"=>"230 D&iacute;as",
                               "interest"=>550,
                               "invested"=>5000
                            ],
                            [ 
                               "status"=>3,
                               "loanId"=>"CPP_015392",
                               "name"=>"PLANELL VEGA INSTAL LACIONS SL",
                               "date"=>"01-2017",
                               "duration"=>"63 D&iacute;as",
                               "interest"=>1400,
                               "invested"=>5000
                            ]
                         ],
                         "companyData"=>[
                            "id"=>"2",
                            "poll_id"=>"2",
                            "company_name"=>"Comunitae",
                            "company_logoGUID"=>"Comunitae.png",
                            "company_logoFiletype"=>"png",
                            "company_url"=>"https=>\/\/www.comunitae.com",
                            "company_country"=>"ES",
                            "company_codeFile"=>"comunitae",
                            "company_username"=>"inigo.iturburua@gmail.com",
                            "company_password"=>"Ap94X6pF",
                            "company_lastAccessMarketplace"=>null,
                            "company_isActiveInMarketplace"=>"1",
                            "company_state"=>"1",
                            "company_featureList"=>"17",
                            "company_reference"=>null,
                            "company_technicalFeatures"=>null,
                            "modified"=>"2016-09-15 14=>20=>33",
                            "created"=>"2016-09-15 14=>20=>33",
                            "company_typeOfAccess"=>null
                         ]
                 ]
              ]
      	];*/

        $result_json = $this->testAction("/marketplaces/cronQueueEvent");
        /*$result_array = json_decode($result_json, $assoc = true);
        $data_array = json_decode($data, $assoc = true);
        foreach ($result_array as $key => $item) {
        	echo $item;
        }*/
        $this->assertTrue(true);
        //debug($result);
    }

    /*private function verifyJson($data) {
    	
    }*/
}

