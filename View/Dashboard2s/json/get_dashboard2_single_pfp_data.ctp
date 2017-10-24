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
//P info

$InvestorPfpData[] = $dataResult;
$InvestorPfpData[] = $pfpData;


echo json_encode($InvestorPfpData);
?>