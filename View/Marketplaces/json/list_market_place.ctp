<?php
/**
 *
 *
 * Global view of total marketplace
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-08-04
 * @package



2016-08-04		version 0.1
Initial version

2017-01-21		version 0.2
Use of DataTable
show results in a tabular list, with responsive view								[OK]
corrected href of button															[OK]



Pending:



*/
?>
	


<?php
	$index = 0;
	foreach ($marketPlaceResults as $market) {
		$index = $index + 1;
		$companyId = $market['Marketplace']['company_id'];	
		$companyName = $companyResults[$companyId]['company_name'];
		$loanId = $market['Marketplace']['marketplace_loanReference'];
		$companyUrl = $companyResults[$companyId]['company_url'];
		$onClickFunction = "ga_investLinkClicked('" . $loanId . "','" . $companyId . "','" . $companyUrl . "')";		
		
		$data['marketplace_name'] = '<img class="logotype_marketplace" src="/'. IMAGES_URL .'logo/' . $companyResults[$companyId]['company_logoGUID'] . '" class="img-responsive center-block"/>'
									. '<div name="companyId" style="display:none;">' . $companyResults[$companyId]['company_name'] . '"</div>';				
				
		$data['marketplace_purpose'] = $market['Marketplace']['marketplace_purpose'];
                
                
		$data['marketplace_interestRate'] = number_format((float)$market['Marketplace']['marketplace_interestRate']/100, 2, ',', '') . " &#37;";		
		
		$data['marketplace_duration'] = $market['Marketplace']['marketplace_duration'] . " " . $durationPublic[$market['Marketplace']['marketplace_durationUnit']];
		$data['marketplace_rating'] = $market['Marketplace']['marketplace_rating'];

		$data['marketplace_subscriptionProgress'] =
			'<div class="demo-content" style="padding-top: 18px;">
				<div class="progress">      					
					<div class="progress-bar progress-bar-marketplace progress-bar-striped active" role="progressbar" aria-valuenow="'.$market['Marketplace']['marketplace_subscriptionProgress'].'" style="width: '. (int) $market['Marketplace']['marketplace_subscriptionProgress']/100 .'%">
						<span>'. intval($market['Marketplace']['marketplace_subscriptionProgress']/100) .'%</span>
					</div>
				</div>										
			</div>';			

		$tempAmount = $market['Marketplace']['marketplace_amount'] / 100;
		$data['marketplace_amount'] = ' 
						<script type="text/javascript">
						var temp;
						var options = {
						symbol : " €",
						decimal : ",",
						thousand: ".",
						precision : 0,
						format: "%v%s"
						};
						var temp = accounting.formatMoney(' . $tempAmount .', options);
						$("#table_' . $index . '").append(temp);
						</script>
						<div id="table_' . $index . '"></div>';
	
		// create the "Invest" button
		
									
		
		$data['marketplace_action'] = '<a><button type="button"  onclick="' . $onClickFunction . ';"  target="_blank"  class="btn btn-primary btn-invest btnRounded">' . __('Invest') . '</button></a>';

		$rowsData[] = $data;
	}


	$marketPlaceData = array(
							"draw"	=> 1,
					 "recordsTotal" => count($rowsData),
				"recordsFiltered" 	=> count($rowsData),
						  "data"	=> $rowsData
						  );

echo json_encode ($marketPlaceData);
?>			
