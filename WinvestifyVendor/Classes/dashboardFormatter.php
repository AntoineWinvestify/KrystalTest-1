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
    

    /**
     * Generic formatter for Graphs. only accept one set of data.
     * 
     * @param array $data
     * @param $companyId                                                        //not used
     * @param array $graphInfo                                                  /Extra info for the graph
     * @return array
     */
    function genericGrahpFormatter($data, $companyId, $graphInfo) {
        $resultNormalized = Hash::extract($data, '{n}.Userinvestmentdata');
        $this->graphicsResults = [
            "graphics_data" => [
                "dataset" =>
                ["display_name" => $graphInfo['displayName'],
                    "x-axis_unit" => $graphInfo['xAxis'],
                    "data" => $resultNormalized]
            ]
        ];
        return $this->graphicsResults;
    }

    /**
     * Generic formatter for Graphs. This format accept two sets of data.
     * 
     * @param array $data
     * @param $companyId                                                        //not used
     * @param array $graphInfo                                                  /Extra info for the graph
     * @return array
     */
    function genericMultiGrahpFormatter($data, $companyId, $graphInfo) {
        $resultNormalized['Dashboard'] = Hash::extract($data['Dashboard'], '{n}.Userinvestmentdata');
        $resultNormalized['GlobalDashboard'] = Hash::extract($data['GlobalDashboard'], '{n}.Dashboardoverviewdata');

        $this->graphicsResults = [
            "graphics_data" => [
                "dataset" =>
                ["display_name" => $graphInfo['displayName'],
                    "x-axis_unit" => $graphInfo['xAxis'],
                    "data" => $resultNormalized['Dashboard']],
                "dataset_1" =>
                ["display_name" => $graphInfo['displayName'],
                    "x-axis_unit" => $graphInfo['xAxis'],
                    "data" => $resultNormalized['GlobalDashboard']]
            ]
        ];
        return $this->graphicsResults;
    }

    /**
     *  Formatter for Gauge Graph
     * 
     * @param array $data
     * @param $companyId                                                        //not used
     * @param array $graphInfo                                                  /Extra info for the graph
     * @return array
     */
    function gaugeGrahpFormatter($data, $companyId, $graphInfo) {
        $this->graphicsResults = 
            ["dataset" =>
                [
                "display_name" => $graphInfo['displayName'],
                "data" => [
                        "max_value" => $graphInfo["maxValue"],
                        "percent" => $data
                        ],
                ]
            ];
        return $this->graphicsResults;
    }

    /**
     *  Formatter for delays graph 
     * 
     * @param array $data
     * @param $companyId                                                        //not used
     * @param array $graphInfo                                                  /Extra info for the graph
     * @return array
     */
    function paymentDelayGrahpFormatter($data, $companyId, $graphInfo) {
        $dataResult = array();
        $dataResult[0]['range_display_name'] = '1-7 days';
        $dataResult[1]['range_display_name'] = '8-30 days';
        $dataResult[2]['range_display_name'] = '31-60 days';
        $dataResult[3]['range_display_name'] = '61-90 days';
        $dataResult[4]['range_display_name'] = '> 90 days';
        
        $dataResult[0]['percent'] = $data['1-7']['Userinvestmentdata']['userinvestmentdata_delay_1-7'];
        $dataResult[1]['percent'] = $data['8-30']['Userinvestmentdata']['userinvestmentdata_delay_8-30'];
        $dataResult[2]['percent'] = $data['31-60']['Userinvestmentdata']['userinvestmentdata_delay_31-60'];
        $dataResult[3]['percent'] = $data['61-90']['Userinvestmentdata']['userinvestmentdata_delay_61-90'];
        $dataResult[4]['percent'] = $data['>90']['Userinvestmentdata']['userinvestmentdata_delay_>90'];
                
        $this->graphicsResults = 
            ["dataset" =>
                [
                "display_name" => $graphInfo['displayName'],
                "data" => $dataResult,
                ]
            ];
        return $this->graphicsResults;
    }
    
    /**
     * Generic formatter for most of the investment list
     * 
     * @param array $investmentResults
     * @param int   $companyId                                                  //Necesary for the tooltips                                               
     * @param array $listInfo                                                  //Extra info for the list
     * @return array
     */
    function genericInvestmentListFormatter($investmentResults, $companyId, $listInfo) {
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
        
        $this->companyId = $companyId;
        $this->Tooltip = ClassRegistry::init('Tooltip');
        $this->investmentListsResult["display_name"] = $listInfo['displayName'];
        $this->investmentListsResult['header'] = $this->createActiveInvestmentsListHeader();
        $tooltip = $this->Tooltip->getTooltip([ INVESTMENT_LIST_GLOBALTOOLTIP], $this->language, $this->companyId);  
        $this->investmentListsResult['tooltip_display_name'] = $tooltip[INVESTMENT_LIST_GLOBALTOOLTIP];     
        $this->investmentListsResult['data'] = $temp; 
        
        
        return $this->investmentListsResult;
    }
    
    /**
     * Read the headers and tooltips for the investment list.
     * 
¡    * @return array
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

