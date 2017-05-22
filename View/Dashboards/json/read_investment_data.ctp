<?php

/**
 *
 *
 * Returns the investment data of a company for the Dashboard page as a JSON object
 *
 * @author Antoine de Poorter
 * @version 0.2
 * @date 2017-01-25
 * @package



  2016-12-19		version 0.1

  2017-01-25		version 0.2
  fields that represent money and percentage are divided by 100 to show the "real and correct" value		[OK]
  added icon for status																					[OK]



 */
?>
<?php

//number_format((float)$companyInvestmentDetail['interest'] / 100, 2, ',', '')
foreach ($companyInvestmentDetails as $key => $companyInvestmentDetail) {
    // Filter out all finished investments
        $companyInvestmentDetails[$key]['interest'] = number_format((float) $companyInvestmentDetail['interest'] / 100, 2, ',', '') . " &#37;";
        $companyInvestmentDetails[$key]['invested'] = number_format((float) $companyInvestmentDetail['invested'] / 100, 2, ',', '') . " &euro;";
        $companyInvestmentDetails[$key]['commission'] = number_format((float) $companyInvestmentDetail['commission'] / 100, 2, ',', '') . " &euro;";
        $companyInvestmentDetails[$key]['amortized'] = number_format((float) $companyInvestmentDetail['amortized'] / 100, 2, ',', '') . " &euro;";
        $companyInvestmentDetails[$key]['profitGained'] = number_format((float) $companyInvestmentDetail['profitGained'] / 100, 2, ',', '') . " &euro;";

// Do mapping to generic status. PENDING and OK will show green indication, DELAYED_PAYMENT and DEFAULTED will show red indication
        if ($companyInvestmentDetail['status'] <= OK) {
            $companyInvestmentDetails[$key]['status'] = '<i class="fa fa-thumbs-up center-block" style="color:green;"></i>';
        } else {
            $companyInvestmentDetails[$key]['status'] = '<i class="fa fa fa-thumbs-down center-block" style="color:red;"></i>';
        }
    
}

$companyInvestmentData = array(
    "draw" => 1,
    "recordsTotal" => count($companyInvestmentDetails),
    "recordsFiltered" => count($companyInvestmentDetails),
    "data" => $companyInvestmentDetails
);

echo json_encode($companyInvestmentData);
?>