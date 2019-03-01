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
/**
 * 
 * Configuration for dashboardController.
 * This dashboard tell us the search function and the formatter function for the api 
 * 
 * This configuration tells us the search function and the formatting function according to the type (graph or list), the name (active or defaluted investments ...).
 * The structure of the configuration is strict.
 * In the first level is the type (graph or list), below this level is the name and then the functions to call. 
 * In the first, the name of the model must always be indexed and the name of the function as a value, 
 * in the second the name of the formatting class must be indexed and the name of the formatting function must be value
 * 
 * Example:
 * array (
 *      [TYPE] => //For now only graphics or lists, the type MUST be the same that the one from the url host/api/1.0/dashboards/linkedaccountId/TYPE/name/*
 *          array (
 *              [NAME] => //name of the graphics or lists, active-investments-list for example, this name MUST be the same that the one from the url host/api/1.0/dashboards/linkedaccountId/type/NAME/*
 *                  array => (
 *                      array([MODEL] => "searchfunction"),               //The model name and the formatter function ALWAYS FIRST.
 *                      array([FORMATTERCLASS] => "formatterfunction")    //The formatter class and function ALWAYS SECOND.
 *                      array([.....] => [......])                        //Extra info needed for the formatting, for example, the display name of a graph. IS optional. 
 *                  )                                                     //If ['xAxis'] value is currency, the code search for the linkedacount currency and overwrites the value.
 *          )        
 *      )
 * 
 * 
 */
$config['Dashboard'] = array(
    "graphics" => array(
        "active-investments-graph-data" => array(
            array("Userinvestmentdata" => "readActiveInvestmentsGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Active Investment", "xAxis" => "", "normalize" => "{n}.Userinvestmentdata")
        ),
        "net-deposits-graph-data" => array(
            array("Userinvestmentdata" => "readNetDepositsGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Net Deposits", "xAxis" => "currency", "normalize" => "{n}.Userinvestmentdata")
        ),
        "cash-drag-graph-data" => array(
            array("Userinvestmentdata" => "readCashDragGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Cash Drag", "xAxis" => "%", "normalize" => "{n}.Userinvestmentdata")
        ),
        "invested-assets-graph-data" => array(
            array("Userinvestmentdata" => "readInvestedAssetsGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Invested Assets", "xAxis" => "currency", "normalize" => "{n}.Userinvestmentdata")
        ),
        "reserved-funds-graph-data" => array(
            array("Userinvestmentdata" => "readReservedFundsGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Reserved Funds", "xAxis" => "currency", "normalize" => "{n}.Userinvestmentdata")
        ),
        "cash-graph-data" => array(
            array("Userinvestmentdata" => "readCashGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Cash", "xAxis" => "currency", "normalize" => "{n}.Userinvestmentdata")
        ),
        "nar-last-365-days-graph-data" => array(
            array("Userinvestmentdata" => "readNarLast365daysMultiGraphData"),
            array("dashboardFormatter" => "genericMultiGraphFormatter"),
            array("displayName" => "NAR Last 365 Days", "xAxis" => "%", "normalize" => array( "Dashboard" => "{n}.Userinvestmentdata", "GlobalDashboard" => "{n}.Dashboardoverviewdata")),
        ),
        "nar-past-year-graph-data" => array(
            array("Userinvestmentdata" => "readNarPastYearGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "NAR Past Year", "xAxis" => "%", "normalize" => "{n}.Userinvestmentdata")
        ),
        "nar-total-funds-graph-data" => array(
            array("Userinvestmentdata" => "readNarTotalFundsGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "NAR Total Funds", "xAxis" => "%", "normalize" => "{n}.Userinvestmentdata")
        ),
        "net-earnings-last-365-days-graph-data" => array(
            array("Userinvestmentdata" => "readNetEarningsLast365daysGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Net Earnings Last 365 Days", "xAxis" => "currency", "normalize" => "{n}.Userinvestmentdata")
        ),
        "net-earnings-past-year-graph-data" => array(
            array("Userinvestmentdata" => "readNetEarningsPastYearGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Net Earnings Past Year", "xAxis" => "currency", "normalize" => "{n}.Userinvestmentdata")
        ),
        "net-earnings-total-graph-data" => array(
            array("Userinvestmentdata" => "readNetEarningsTotalGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Net Earnings Total", "xAxis" => "currency", "normalize" => "{n}.Userinvestmentdata")
        ),
        "current-graph-data" => array(
            array("Userinvestmentdata" => "readCurrentTotalGraphData"),
            array("dashboardFormatter" => "gaugeGraphFormatter"),
            array("displayName" => "Current", "maxValue" => "100")
        ),
        /* "exposure-graph-data" => array(
          array("Userinvestmentdata" => "readExposureTotalGraphData"),
          array("dashboardFormatter" => "gaugeGraphFormatter"),
          array("displayName" => "Exposure", "maxValue" => "100")
          ), */
        "payment-delay-graph-data" => array(
            array("Userinvestmentdata" => "readPaymentDelayGraphData"),
            array("dashboardFormatter" => "paymentDelayGraphFormatter"),
            array("displayName" => "Payment Delay")
        ),),
    "lists" => array(
        /* "duplicity-investments-list" => array(
          array("Investment" => "duplicityInvestmentList"),
          array("dashboardFormatter" => "genericInvestmentListFormatter"),
          array("displayName" => "Duplicity Investments")
          ), */
        "active-investments-list" => array(
            array("Investment" => "readActiveInvestmentsList"),
            array("dashboardFormatter" => "genericInvestmentListFormatter"),
            array("displayName" => "Active Investments")
        ),
        "defaulted-investments-list" => array(
            array("Investment" => "readDefaultedInvestmentsList"),
            array("dashboardFormatter" => "genericInvestmentListFormatter"),
            array("displayName" => "Defaulted Investments")
        ),
    ),
);
$config['Globaldashboard'] = array(
    "graphics" => array(
        "active-investments-graph-data" => array(
            array("Globaldashboard" => "readActiveInvestmentsGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Active Investment", "xAxis" => "", "normalize" => "{n}.Globaldashboard")
        ),
        "net-deposits-graph-data" => array(
            array("Globaldashboard" => "readNetDepositsGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Net Deposits", "xAxis" => "currency", "normalize" => "{n}.Globaldashboard")
        ),
        "cash-drag-graph-data" => array(
            array("Globaldashboard" => "readCashDragGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Cash Drag", "xAxis" => "%", "normalize" => "{n}.Globaldashboard")
        ),
        "invested-assets-graph-data" => array(
            array("Globaldashboard" => "readInvestedAssetsGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Invested Assets", "xAxis" => "currency", "normalize" => "{n}.Globaldashboard")
        ),
        "reserved-funds-graph-data" => array(
            array("Globaldashboard" => "readReservedFundsGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Reserved Funds", "xAxis" => "currency", "normalize" => "{n}.Globaldashboard")
        ),
        "cash-graph-data" => array(
            array("Globaldashboard" => "readCashGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Cash", "xAxis" => "currency", "normalize" => "{n}.Globaldashboard")
        ),
        "nar-last-365-days-graph-data" => array(
            array("Globaldashboard" => "readNarLast365daysGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Last 365 Days", "xAxis" => "%", "normalize" => "{n}.Dashboardoverviewdata"),
        ),
        "nar-past-year-graph-data" => array(
            array("Globaldashboard" => "readNarPastYearGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Past Year", "xAxis" => "%", "normalize" => "{n}.Dashboardoverviewdata")
        ),
        "nar-total-funds-graph-data" => array(
            array("Globaldashboard" => "readNarTotalFundsGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Total Funds", "xAxis" => "%", "normalize" => "{n}.Dashboardoverviewdata")
        ),
        "net-earnings-last-365-days-graph-data" => array(
            array("Globaldashboard" => "readNetEarningsLast365daysGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Last 365 Days", "xAxis" => "currency", "normalize" => "{n}.Dashboardoverviewdata")
        ),
        "net-earnings-past-year-graph-data" => array(
            array("Globaldashboard" => "readNetEarningsPastYearGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Past Year", "xAxis" => "currency", "normalize" => "{n}.Dashboardoverviewdata")
        ),
        "net-earnings-total-graph-data" => array(
            array("Globaldashboard" => "readNetEarningsTotalGraphData"),
            array("dashboardFormatter" => "genericGraphFormatter"),
            array("displayName" => "Total", "xAxis" => "currency", "normalize" => "{n}.Dashboardoverviewdata")
        ),
        "current-graph-data" => array(
          array("Globaldashboard" => "readCurrentTotalGraphData"),
          array("dashboardFormatter" => "gaugeGraphFormatter"),
          array("displayName" => "Daily Current", "maxValue" => "100")
          ),
        /* "exposure-graph-data" => array(  //Not in global?
          array("Globaldashboard" => "readExposureTotalGraphData"),
          array("dashboardFormatter" => "gaugeGraphFormatter"),
          array("displayName" => "Platform Exposure", "maxValue" => "100")
          ), */
        "payment-delay-graph-data" => array(
            array("Globaldashboard" => "readPaymentDelayGraphData"),
            array("dashboardFormatter" => "paymentDelayGraphFormatter"),
            array("displayName" => "Payment Delay", "normalize" => "{n}.Globaldashboard")
        ),
    ),
    "lists" => array(
        "kpis" => array(
            array("Userinvestmentdata" => "readDashboardList"),
            array("dashboardFormatter" => "dashboardListFormatter"),
            array("displayName" => "Key Performance Indicators")
        ),
    ),
);


/**
 * 
 * Configuration for dashboardController.
 * This dashboard tell us the basic information for each field of the dashboard.
 * 
 * This configuration tells us the search the display name and the tooltip for each field in the dashboard. It can also tell us the model and the field where to find the value,
 * the type of value (currency or percent, if currency, search the currency in linkedaccount, if percent, multiply *100 and add the %) and the links of the graphs.
 * The name of the key must be the name of the field in dashboard.
 * 
 *
 * Example:
 * array (
 * [BLOCK] => array (
 *      [display_name] => value
 *      [tooltip] => value
 *      [FIELD] => //Name of the field
 *          array (
 *              [display_name] => value                                         //name that will be shown in dashboard - Required
 *              [tooltip] => value                                              //identifier of the tooltip of the field - Required 
 *              [icon] => value                                                 //Graph icon  - Only required if the field have [graphLinksParams]
 *              [value] => array(                                               //Value of the field - Optional but used in most cases.
 *                  [model] => value                                            //Model where we search the value - Required if [value] is used
 *                  [field] => value                                            //Field of the DB that we search. - Required if [value] is used
 *                  [type] => value                                             //"percent" or "currency" - Optional
 *              ) 
 *              [graphLinksParams] => array(                                    //Link to the graph of the field - Optional
 *                  [link] => value                                             //Param for the function genrateLink to generate the link to the graph of the field - Required if the field have graphs 
 *                  [displayName] => value                                      //Name of the graph
 *              )                                                                     
 *      )
 * ))
 * 
 */
$config['DashboardMainData'] = array(
    "investment_indicators" => array(
        "display_name" => "Investment Indicators",
        "tooltip" => DASHBOARD_INVESTMENT_INDICATORS,
        "data" => array(
            "active_investments" => array(
                "display_name" => "Active Investments",
                "tooltip" => DASHBOARD_ACTIVE_INVESTMENTS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_numberActiveInvestments",
                //"type" => "currency" or "percent"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/active-investments-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/active-investments-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "net_deposits" => array(
                "display_name" => "Net Deposits",
                "tooltip" => DASHBOARD_NET_DEPOSITS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_totalNetDeposits",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/net-deposits-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/net-deposits-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "cash_drag" => array(
                "display_name" => "Cash Drag",
                "tooltip" => DASHBOARD_CASH_DRAG,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_cashDrag",
                    "type" => "percent"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/cash-drag-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/cash-drag-graph-data/?period=all", "displayName" => "All")
                )
            ),
        ),
    ),
    "nar" => array(
        "display_name" => "Net Annual Returns",
        "tooltip" => DASHBOARD_NET_ANNUAL_RETURNS,
        "data" => array(
            "nar_past_365_days" => array(
                "default_graph" => true,
                "display_name" => "Last 365 days",
                "tooltip" => DASHBOARD_NAR_LAST_365_DAYS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_netAnnualReturnPast12Months",
                    "type" => "percent"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/nar-last-365-days-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/nar-last-365-days-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "nar_past_year" => array(
                "display_name" => "Past Year",
                "tooltip" => DASHBOARD_NAR_LAST_YEAR,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_netAnnualReturnPastYear",
                    "type" => "percent"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/nar-past-year-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/nar-past-year-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "nar_total_funds" => array(
                "display_name" => "Total Funds",
                "tooltip" => DASHBOARD_NAR_TOTAL_FUNDS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_netAnnualTotalFundsReturn",
                    "type" => "percent"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/nar-total-funds-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/nar-total-funds-graph-data/?period=all", "displayName" => "All")
                )
            ),
        ),
    ),
    "statement_of_funds" => array(
        "display_name" => "Statement of Funds",
        "tooltip" => DASHBOARD_STATEMENT_OF_FUNDS,
        "data" => array(
            "invested_assets" => array(
                "display_name" => "Invested Assets",
                "tooltip" => DASHBOARD_INVESTED_ASSETS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_outstandingPrincipal",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/invested-assets-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/invested-assets-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "reserved_funds" => array(
                "display_name" => "Reserved Funds",
                "tooltip" => DASHBOARD_RESERVED_FUNDS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_reservedAssets",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/reserved-funds-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/reserved-funds-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "cash" => array(
                "display_name" => "Cash",
                "tooltip" => DASHBOARD_CASH,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_cashInPlatform",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/cash-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/cash-graph-data/?period=all", "displayName" => "All")
                )
            ),
        ),
    ),
    "net_earnings" => array(
        "display_name" => "Net Earnings",
        "tooltip" => DASHBOARD_NET_EARNINGS,
        "data" => array(
            "net_earnings_past_365_days" => array(
                "display_name" => "Last 365 days",
                "tooltip" => DASHBOARD_NET_EARNINGS_LAST_365_DAYS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_netReturnPast12Months",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/net-earnings-last-365-days-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/net-earnings-last-365-days-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "net_earnings_past_year" => array(
                "display_name" => "Past Year",
                "tooltip" => DASHBOARD_NET_EARNINGS_LAST_YEAR,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_netReturnPast12Months",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/net-earnings-past-year-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/net-earnings-past-year-graph-data/?period=all", "displayName" => "All")
                )
            ),
        ),
    ),
    "payment_delay" => array(
        "display_name" => "Payment Delay",
        "display_name1" => "More than 90 days",
        "tooltip" => DASHBOARD_PAYMENT_DELAY,
        "data" => array(
            /* "delinquency_rate" => array(
              "display_name" => "Delinquency Rate",
              "tooltip" => DASHBOARD_DEL,
              "graphLinksParams" => array(
              array("link" => "graphics/current-graph-data"),
              )
              ) */
            "outstanding_debt" => array(
                "display_name" => "Outstanding Debt",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "recursive" => "Dashboarddelay",
                    "field" => "dashboarddelay_outstandingDebts",
                    "type" => "currency"
                ),
            ),
            "written_off" => array(
                "display_name" => "Written Off",
                "value" => array(
                    "model" => "Userinvestmentdata",
                    "field" => "userinvestmentdata_writtenOff",
                    "type" => "currency"
                ),
            ),
        )
    ),
    "current" => array(
        "display_name" => "Current",
        "data" => array(
            "current_situation" => array(
                "display_name" => "Current",
                "tooltip" => DASHBOARD_CURRENT,
                "graphLinksParams" => array(
                    array("link" => "graphics/current-graph-data"),
                )
            )
        )
    ),
);

$config['globalDashboardMainData'] = array(
        "investment_indicators" => array(
        "display_name" => "Investment Indicators",
        "tooltip" => GLOBALDASHBOARD_INVESTMENT_INDICATORS,
        "data" => array(
            "active_investments" => array(
                "display_name" => "Active Investments",
                "tooltip" => GLOBALDASHBOARD_ACTIVE_INVESTMENTS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Globaldashboard",
                    "field" => "globaldashboard_numberActiveInvestments",
                //"type" => "currency" or "percent"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/active-investments-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/active-investments-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "net_deposits" => array(
                "display_name" => "Net Deposits",
                "tooltip" => GLOBALDASHBOARD_NET_DEPOSITS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Globaldashboard",
                    "field" => "globaldashboard_totalNetDeposits",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/net-deposits-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/net-deposits-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "cash_drag" => array(
                "display_name" => "Cash Drag",
                "tooltip" => GLOBALDASHBOARD_CASH_DRAG,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Globaldashboard",
                    "field" => "globaldashboard_cashDrag",
                    "type" => "percent"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/cash-drag-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/cash-drag-graph-data/?period=all", "displayName" => "All")
                )
            ),
        ),
    ),
    "nar" => array(
        "display_name" => "Net Annual Returns",
        "tooltip" => GLOBALDASHBOARD_NET_ANNUAL_RETURNS,
        "data" => array(
            "nar_past_365_days" => array(
                "default_graph" => true,
                "display_name" => "Last 365 days",
                "tooltip" => GLOBALDASHBOARD_NAR_LAST_365_DAYS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Dashboardoverviewdata",
                    "field" => "dashboardoverviewdata_netAnnualReturnPast12Months",
                    "type" => "percent"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/nar-last-365-days-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/nar-last-365-days-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "nar_past_year" => array(
                "display_name" => "Past Year",
                "tooltip" => GLOBALDASHBOARD_NAR_LAST_YEAR,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Dashboardoverviewdata",
                    "field" => "dashboardoverviewdata_netAnnualReturnPastYear",
                    "type" => "percent"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/nar-past-year-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/nar-past-year-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "nar_total_funds" => array(
                "display_name" => "Total Funds",
                "tooltip" => GLOBALDASHBOARD_NAR_TOTAL_FUNDS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Dashboardoverviewdata",
                    "field" => "dashboardoverviewdata_netAnnualTotalFundsReturn",
                    "type" => "percent"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/nar-total-funds-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/nar-total-funds-graph-data/?period=all", "displayName" => "All")
                )
            ),
        ),
    ),
    "statement_of_funds" => array(
        "display_name" => "Statement of Funds",
        "tooltip" => GLOBALDASHBOARD_STATEMENT_OF_FUNDS,
        "data" => array(
            "invested_assets" => array(
                "display_name" => "Invested Assets",
                "tooltip" => GLOBALDASHBOARD_INVESTED_ASSETS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Globaldashboard",
                    "field" => "globaldashboard_outstandingPrincipal",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/invested-assets-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/invested-assets-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "reserved_funds" => array(
                "display_name" => "Reserved Funds",
                "tooltip" => GLOBALDASHBOARD_RESERVED_FUNDS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Globaldashboard",
                    "field" => "globaldashboard_reservedAssets",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/reserved-funds-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/reserved-funds-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "cash" => array(
                "display_name" => "Cash",
                "tooltip" => GLOBALDASHBOARD_CASH,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Globaldashboard",
                    "field" => "globaldashboard_cashInPlatform",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/cash-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/cash-graph-data/?period=all", "displayName" => "All")
                )
            ),
        ),
    ),
    "net_earnings" => array(
        "display_name" => "Net Earnings",
        "tooltip" => GLOBALDASHBOARD_NET_EARNINGS,
        "data" => array(
            "net_earnings_past_365_days" => array(
                "default_graph" => true,
                "display_name" => "Last 365 days",
                "tooltip" => GLOBALDASHBOARD_NET_EARNINGS_LAST_365_DAYS,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Dashboardoverviewdata",
                    "field" => "dashboardoverviewdata_netReturnPast12Months",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/net-earnings-last-365-days-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/net-earnings-last-365-days-graph-data/?period=all", "displayName" => "All")
                )
            ),
            "net_earnings_past_year" => array(
                "display_name" => "Past Year",
                "tooltip" => GLOBALDASHBOARD_NET_EARNINGS_LAST_YEAR,
                "icon" => "icon-win-airplay",
                "value" => array(
                    "model" => "Dashboardoverviewdata",
                    "field" => "dashboardoverviewdata_netReturnPastYear",
                    "type" => "currency"
                ),
                "graphLinksParams" => array(
                    array("link" => "graphics/net-earnings-past-year-graph-data/?period=year", "displayName" => "Year"),
                    array("link" => "graphics/net-earnings-past-year-graph-data/?period=all", "displayName" => "All")
                )
            ),
        /* "net_earnings_total" => array(
          "display_name" => "Total",
          "tooltip" => GLOBALDASHBOARD_NAR_TOTAL_FUNDS,
          "icon" => "icon-win-airplay",
          "value" => array(
          "model" => "Dashboardoverviewdata",
          "field" => "dashboardoverviewdata_netAnnualTotalFundsReturn",
          "type" => "percent"
          ),
          "graphLinksParams" => array(
          array("link" => "graphics/net-earnings-total-graph-data/?period=year", "displayName" => "Year"),
          array("link" => "graphics/net-earnings-total-graph-data/?period=all", "displayName" => "All")
          )
          ), */
        ),
    ),
    "payment_delay" => array(
        "display_name" => "Payment Delay",
        "display_name1" => "More than 90 days",
        "tooltip" => GLOBALDASHBOARD_PAYMENT_DELAY,
        "data" => array(
            /* "delinquency_rate" => array(
              "display_name" => "Delinquency Rate",
              "tooltip" => DASHBOARD_DEL,
              "graphLinksParams" => array(
              array("link" => "graphics/current-graph-data"),
              )
              ) */
            "outstanding_debt" => array(
                "display_name" => "Outstanding Debt",
                "value" => array(
                    "model" => "Globaldashboard",
                    "recursive" => "Globaldashboarddelay",
                    "field" => "globaldashboarddelay_outstandingDebts",
                    "type" => "currency"
                ),
            ),
            "written_off" => array(
                "display_name" => "Written Off",
                "value" => array(
                    "model" => "Globaldashboard",
                    "field" => "globaldashboard_writtenOff",
                    "type" => "currency"
                ),
            ),
        )
    ),
    "current" => array(
        "display_name" => "Current",
        "data" => array(
            "current_situation" => array(
                "display_name" => "Current",
                "tooltip" => GLOBALDASHBOARD_CURRENT,
                "graphLinksParams" => array(
                    array("link" => "graphics/current-graph-data"),
                )
            )
        )
    ),
);
