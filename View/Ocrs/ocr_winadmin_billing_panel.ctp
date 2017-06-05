<?php

/* 
 * One Click Registration - WinAdmin Billing Panel
 * Billing Panel to upload billings on PFD's Panel
 */
?>

<div id="OCR_winadminPabelB">
    <div id="billingFilters" class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h4 class="header1CR"><?php echo __('Filter PFD:') ?></h4>
                <?php 
                $class = "form-control blue_noborder investorCountry". ' ' . $errorClass;
                $filters = ["select PFD", "pfd1", "pfd2", "pfd3"];      
                                                                    echo $this->Form->input('Investor.investor_country', array(
                                                                            'name'			=> 'country',
                                                                            'id' 			=> 'ContentPlaceHolder_country',
                                                                            'label' 		=> false,
                                                                            'options'               => $filters,
                                                                            'placeholder' 	=>  __('Country'),
                                                                            'class' 		=> $class,
                                                                            'value'			=> $resultUserData[0]['Investor']['investor_country'],						
                                            ));
                ?>
        </div>
    </div>
    <div id="billings" class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <h4 class="header1CR"><?php echo __('Billing data:') ?></h4>
            patata
        </div>
    </div>
    <div id="uploadBtn" class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <button type="button" class="btn btn-default btn-lg btn-win1 center-block" style="padding: 10px 50px; margin-bottom: 25px"><?php echo __('Upload')?></button>
        </div>
    </div>
</div>
