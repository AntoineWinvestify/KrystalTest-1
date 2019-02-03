<?php
/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2019, http://www.winvestify.com                   	  	|
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
 * 
 * @author
 * @version 0.1
 * @date  2019-01-25
 * @package
 *
 *
 * 2019-01-25		version 0.1
 * Basic version
 */

class dashboardFormatter {
    

    function genericGrahpFormatter($data){
        echo $data;
    }
    
    
    function genericInvestmentListFormatter($investmentResults) {
        $this->companyId = $investmentResults['company_id'];
        unset($investmentResults['company_id']);
        $investmentResultsNormalized = Hash::extract($investmentResults, '{n}.Investment');
        foreach ($investmentResultsNormalized as $key => $item) {
            foreach ($item as $key1 => $value) {
                switch ($key1) {
                    case 'investment_loanId':
                        $temp[$key][1] = $value;
                        $i++;
                        break;
                    case 'date':
                        $temp[$key][2]['date'] = $value;
                        $temp[$key][2]['date_alias'] = str_replace("-", "", $value);
                        $i++;
                        break;
                    case 'myInvestmentFloat':
                        $temp[$key][3]["value"]["amount"] = $value;
                        $temp[$key][3]["value"]["currency_code"] = $currency;
                        $i++;
                        break;
                    case 'interestFloat':
                        $temp[$key][4]["value"]["percent"] = $value;
                        $i++;
                        break;
                    case 'progressFloat':
                        $temp[$key][5]["value"]["percent"] = $value;
                        $i++;
                        break;
                    case 'outstandingFloat':
                        $temp[$key][6]["value"]["amount"] = $value;
                        $temp[$key][6]["value"]["currency_code"] = $currency;
                        $i++;
                        break;
                    case 'investment_nextPaymentDate':
                        $temp[$key][7]['date'] = $value;
                        $temp[$key][7]['date_alias'] = str_replace("-", "", $value);
                        $i++;
                        break;
                    case 'investment_paymentStatus':
                        $temp[$key][8]["value"]["delay"] = $value;
                        $temp[$key][8]["value"]["unit"] = "days";
                        $i++;
                        break;
                }
            }
        }

        $this->Tooltip = ClassRegistry::init('Tooltip');
        $this->investmentListsResult["display_name"] = "Active Investments";
        $this->investmentListsResult['header'] = $this->createActiveInvestmentsListHeader();
        $tooltip = $this->Tooltip->getTooltip([ INVESTMENT_LIST_GLOBALTOOLTIP], $this->language, $this->companyId);  
        $this->investmentListsResult['tooltip_display_name'] = $tooltip[INVESTMENT_LIST_GLOBALTOOLTIP];     
        $this->investmentListsResult['data'] = $temp; 

        return $this->investmentListsResult;
    }
    
    
    
    
    
    
    
    
   
    /**
     * Read the data of an investment list
     * 
     * @param int  $linkedAccountId The object reference for the linked account
     * @return boolean
     */  
    public function createActiveInvestmentsListHeader()  { 

        $tooltipIdentifiers = [
            INVESTMENT_LIST_LOANID,
            INVESTMENT_LIST_INVESTMENTDATE, 
            INVESTMENT_LIST_MYINVESTMENT, 
            INVESTMENT_LIST_INTEREST, 
            INVESTMENT_LIST_INSTALLMENTPROGRESS,
            INVESTMENT_LIST_OUTSTANDINGPRINCIPAL,
            INVESTMENT_LIST_NEXTPAYMENT, 
            INVESTMENT_LIST_STATUS,
        ];

        $displayHeaders = [
            "Loan ID", 
            "Investment Date",
            "My Investment",
            "Interest", 
            "Instalment Progress",
            "Outstanding Principal",
            "Next Payment",
            "Status"
        ];
        $tooltips = $this->Tooltip->getTooltip($tooltipIdentifiers, $this->language, $this->companyId);
        $i = 0;
        foreach ($displayHeaders as $key => $displayHeader) {
            $header[$i]['displayName'] = $displayHeader;
            if (array_key_exists($tooltipIdentifiers[$key], $tooltips)) {
                $header[$i]['tooltipDisplayName'] = $tooltips[$tooltipIdentifiers[$key]] . "key = $key";
            }
            $i++;
        }
        return $header;
    } 
}
