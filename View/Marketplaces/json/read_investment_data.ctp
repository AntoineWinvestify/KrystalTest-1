<?php
/**
 *
 *
 * Returns the investment data for the Dashboard page as a JSON object
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-12-19
 * @package

 
 
2016-12-19		version 0.1
 
*/

?>
<?php
	$companyInvestmentData = array(
							"draw"	=> 1,
					 "recordsTotal" => count($companyInvestmentDetails),
				"recordsFiltered" 	=> count($companyInvestmentDetails),
						  "data"	=> $companyInvestmentDetails
						  );
echo json_encode ($companyInvestmentData);	
?>