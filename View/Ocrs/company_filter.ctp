1
<?php
foreach ($company as $company) {
    ?>
    <span class="company">
        <div id ="<?php echo $company['Company']['id'] ?>" class="companyDiv col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <div class="box box-widget widget-user-2">
                <div class="widget-user-header">
                    <div class="row">
                        <div id="companyLogo" class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                            <img src="/img/logo/<?php echo $company['Company']['company_logoGUID'] ?>" style="max-height: 100px" alt="platform-logotype" class="logo img-responsive center-block platformLogo"/>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                            <ul class="nav nav-stacked">
                                <li class = 'country'><img src="/img/flags/<?php echo $company['Company']['company_country'] ?>.png" alt="Spain Flag"/> <?php echo __($company['Company']['company_countryName']) ?></li>
                                <li class = 'type'><?php echo __($company['Company']['Company_type']) ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="box-footer no-padding">
                    <div class="row">
                        <div class="checkboxDiv col-xs-12 col-sm-12 col-md-8 col-lg-8">
                            <div class="input_platforms"><input type="checkbox" class="check"> <?php echo __('He leído la ') ?><a href="<?php echo $company['Company']['Company_privacityUrl'] ?>"><?php echo __('Privacy Policy') ?></a></div>
                            <div class="input_platforms"><input type="checkbox" class="check"> <?php echo __('He leído los ') ?><a href="<?php echo $company['Company']['Company_termsUrl'] ?>"><?php echo __('Terms and Conditions') ?></a></div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <!-- by clicking this button it must charge a modal with terms & conditions + Yes/No buttons to CONFIRM the selection -->
                            <button class="btnSelect btn btn-default btn-win2 btnMargin center-block" href = "#"><?php echo __('Select') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
    <?php
}
?>