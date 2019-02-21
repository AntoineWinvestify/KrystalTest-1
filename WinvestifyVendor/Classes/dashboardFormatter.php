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
     * @param $dummy                                                            //not used
     * @param array $graphInfo                                                  /Extra info for the graph
     * @return array
     */
    function genericGraphFormatter($data, $dummy, $graphInfo) {
        $resultNormalized = Hash::extract($data, $graphInfo['normalice']);
        if ($graphInfo['xAxis'] === '%') {
            foreach ($resultNormalized as $key => $data) {
                $resultNormalized[$key]['value'] = round($resultNormalized[$key]['value'] * 100, WIN_SHOW_DECIMAL);
            }
        }
        else {
            foreach ($resultNormalized as $key => $data) {
                $resultNormalized[$key]['value'] = round($resultNormalized[$key]['value'], WIN_SHOW_DECIMAL);
            }
        }
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
     * @param $dummy                                                            //not used
     * @param array $graphInfo                                                  /Extra info for the graph
     * @return array
     */
    function genericMultiGraphFormatter($data, $dummy, $graphInfo) {
        $resultNormalized['Dashboard'] = Hash::extract($data['Dashboard'], $graphInfo['normalice']);
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
     * @param $dummy                                                            //not used
     * @param array $graphInfo                                                  /Extra info for the graph
     * @return array
     */
    function gaugeGraphFormatter($data, $dummy, $graphInfo) {
        
        $this->Tooltip = ClassRegistry::init('Tooltip');
        $tooltip = $this->Tooltip->getTooltip($data['tooltip'], $this->language, $graphInfo['linkedaccountId']);
        unset($data['tooltip']);
        $this->graphicsResults = ["dataset" =>
            [
                "display_name" => $graphInfo['displayName'],
                "tooltip_display_name" => $tooltip[$data['tooltip']],
                "data" => [
                    "max_value" => $graphInfo["maxValue"],
                    "percent" => round(bcmul($data['data']['Dashboardelay']['dashboardelay_current_outstanding'], 100, 16), WIN_SHOW_DECIMAL)
                ],
            ]
        ];
        return $this->graphicsResults;
    }

    /**
     *  Formatter for delays graph 
     * 
     * @param array $data
     * @param $dummy                                                            //not used
     * @param array $graphInfo                                                  /Extra info for the graph
     * @return array
     */
    function paymentDelayGraphFormatter($data, $dummy, $graphInfo) {
        
        $this->Tooltip = ClassRegistry::init('Tooltip');
        $tooltip = $this->Tooltip->getTooltip($data['tooltip'], $this->language, $graphInfo['linkedaccountId']);
        
        $dataResult = array();
        $dataResult[0]['range_display_name'] = '1-7 days';
        $dataResult[1]['range_display_name'] = '8-30 days';
        $dataResult[2]['range_display_name'] = '31-60 days';
        $dataResult[3]['range_display_name'] = '61-90 days';
        $dataResult[4]['range_display_name'] = '> 90 days';

        $dataResult[0]['percent'] = round(bcmul($data['data']['Dashboardelay']['dashboardelay_delay_1-7_outstanding'], 100, 16), WIN_SHOW_DECIMAL);
        $dataResult[1]['percent'] = round(bcmul($data['data']['Dashboardelay']['dashboardelay_delay_8-30_outstanding'], 100, 16), WIN_SHOW_DECIMAL);
        $dataResult[2]['percent'] = round(bcmul($data['data']['Dashboardelay']['dashboardelay_delay_31-60_outstanding'], 100, 16), WIN_SHOW_DECIMAL);
        $dataResult[3]['percent'] = round(bcmul($data['data']['Dashboardelay']['dashboardelay_delay_61-90_outstanding'], 100, 16), WIN_SHOW_DECIMAL);
        $dataResult[4]['percent'] = round(bcmul($data['data']['Dashboardelay']['dashboardelay_delay_>90_outstanding'], 100, 16), WIN_SHOW_DECIMAL);

        $this->graphicsResults = ["dataset" =>
            [
                "display_name" => $graphInfo['displayName'],
                "tooltip_display_name" => $tooltip[$data['tooltip']],
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
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $currency = $this->Linkedaccount->getCurrency($listInfo['linkedaccountId']);
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
                        $temp[$key][3]["value"]["amount"] = round($value, WIN_SHOW_DECIMAL);
                        $temp[$key][3]["value"]["currency_code"] = $currency;
                        $i++;
                        break;
                    case 'interestFloat':
                        $temp[$key][4]["value"]["percent"] = round($value, WIN_SHOW_DECIMAL);
                        $i++;
                        break;
                    case 'progressFloat':
                        $temp[$key][5]["value"]["percent"] = round($value, WIN_SHOW_DECIMAL);
                        $i++;
                        break;
                    case 'outstandingFloat':
                        $temp[$key][6]["value"]["amount"] = round($value, WIN_SHOW_DECIMAL);
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
        $tooltip = $this->Tooltip->getTooltip([INVESTMENT_LIST_GLOBALTOOLTIP], $this->language, $this->companyId);
        $this->investmentListsResult['tooltip_display_name'] = $tooltip[INVESTMENT_LIST_GLOBALTOOLTIP];
        $this->investmentListsResult['data'] = $temp;

        return $this->investmentListsResult;
    }

    /**
     * Special formatter for the kpi list.
     * 
     * @param array $data                                                       //Data of kpis lists
     * @param type $dummy
     * @param type $info                                                        //Extra info for the lists
     * @return array
     */
    public function dashboardListFormatter($data, $dummy, $info) {
        $this->Tooltip = ClassRegistry::init('Tooltip');

        //Basic data
        $kpisList['display_name'] = "Key Performance Indicators";
        $mainTooltip = $this->Tooltip->getTooltip(array(GLOBALDASHBOARD_KPIS), $this->language);
        $kpisList['tooltip_display_name'] = $mainTooltip[GLOBALDASHBOARD_KPIS];

        //Table headers
        $tooltipIdentifiers = [
            GLOBALDASHBOARD_KPI_PLATFORM,
            GLOBALDASHBOARD_KPI_YIELD,
            GLOBALDASHBOARD_KPI_TOTAL_VOLUME,
            GLOBALDASHBOARD_KPI_CASH,
            GLOBALDASHBOARD_KPI_EXPOSURE,
            GLOBALDASHBOARD_KPI_CURRENT
        ];
        $displayHeaders = [
            "Platform/Username",
            "Yield",
            "Total Volume",
            "Cash",
            "Exposure",
            "Current",
        ];
        $tooltips = $this->Tooltip->getTooltip($tooltipIdentifiers, $this->language);
        $i = 0;
        foreach ($displayHeaders as $key => $displayHeader) {
            $header[$i]['displayName'] = $displayHeader;
            if (array_key_exists($tooltipIdentifiers[$key], $tooltips)) {
                $header[$i]['tooltipDisplayName'] = $tooltips[$tooltipIdentifiers[$key]] . "key = $key";
            }
            $i++;
        }
        $kpisList['table_header'] = $header;

        //Table values
        $z = 0;
        foreach ($data as $key => $item) {

            $kpisList['table_data'][$z][0]['datum'] = $item['Userinvestmentdata']['pfp'] . '(' . $item['Userinvestmentdata']['linkedaccount_accountDisplayName'] . ')';
            $kpisList['table_data'][$z][1]['datum']['percent'] = $item['Userinvestmentdata']['userinvestmentdata_netAnnualReturnPast12Months'];
            $kpisList['table_data'][$z][2]['datum']['amount'] = round($item['Userinvestmentdata']['userinvestmentdata_outstandingPrincipal'] + $item['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] + $item['Userinvestmentdata']['userinvestmentdata_reservedAssets'], WIN_SHOW_DECIMAL);
            $kpisList['table_data'][$z][2]['datum']['currency_code'] = $item['Userinvestmentdata']['linkedaccount_currency'];
            $kpisList['table_data'][$z][3]['datum']['amount'] = round($item['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], WIN_SHOW_DECIMAL);
            $kpisList['table_data'][$z][3]['datum']['currency_code'] = $item['Userinvestmentdata']['linkedaccount_currency'];
            $kpisList['table_data'][$z][4]['datum']['percent'] = round(0, WIN_SHOW_DECIMAL);
            $kpisList['table_data'][$z][5]['datum']['percent'] = round(0, WIN_SHOW_DECIMAL);
            $z++;
        }

        return $kpisList;
    }

    /**
     * Read the headers and tooltips for the investment list.
     * 
     * @return array
     */
    public function createActiveInvestmentsListHeader() {

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
