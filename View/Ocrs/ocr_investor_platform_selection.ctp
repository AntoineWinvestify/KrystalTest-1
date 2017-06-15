<?php
/*
 * One Click Registration - Investor Panel B
 * Select Platforms to Register
 * 
 * [2017-05-23] Version 0.1
 * Completed view
 * [pending] Click on selected platform to add it on 'investorSelection' div & delete on 'platformSelection'.
 * [pending] Mechanism to generate all platform elements
 * [pending] Mechanism to generate final modal
 * 
 * [2017-06-13] Version 0.2
 * Added spinner on Go button
 * 
 * [2017-06-13] Version 0.3
 * Added user feedback
 */
?>
1
<script>
    total = <?php echo count($selected) ?>;
    var numberCompanies = 0;
    var i = 0;
    var z = 0;
    $(document).ready(function () {
        addEnvent();
        extraEvent();
        selEvent();
        //Flitrado por ajax
        /*$('.filter').change(function () {
         country = $('#filterCountry').val();
         type = $('#filterType').val();
         
         
         var params = {
         country_filter: $('#filterCountry').val(),
         type_filter: $('#filterType').val()
         };
         if ($('#filterCountry').val() === 0) {
         params.country_filter = null;
         }
         if ($('#filterType').val() === 0) {
         params.type_filter = null;
         }
         
         link = "../Ocrs/companyFilter";
         var data = jQuery.param(params);
         getServerData(link, data, successFilter, errorFilter);
         });*/

        //Ajax sent companies
        $(document).on("click", "#sentCompanies", function () {
            $("#sentCompanies").prop("disabled", true);
            var idCompany = new Array();
            var params = {
                numberCompanies: numberCompanies,
            };

            for (var j = 1; j <= z; j++) {
                if ($("[name='company" + j + "']").length) {
                    idCompany.push($("[name='company" + j + "']").attr("value"));
                }

            }
            params["idCompany"] = idCompany;

            link = "/Ocrs/oneClickInvestorI";
            var data = jQuery.param(params);
            getServerData(link, data, successSentCompanies, errorSentCompanies);

            params = {};
            link = "/Ocrs/ocrInvestorDataPanel";
            var data = jQuery.param(params);
            getServerData(link, data, successDataPanel, errorDataPanel);
        });

        $(document).on("click", ".btnSelectedPlatformDB", function () {
            var params = {
                id_company: $(this).parent().parent().parent().attr("value"),
            };
            link = "../Ocrs/deleteCompanyOcr";
            var data = jQuery.param(params);
            getServerData(link, data, successDelete, errorDelete);

        });
    });



    /*function successFilter(result) {
     $("#platformSelection").html("<h5><?php echo __('Search results:'); ?></br></h5>" + result);
     addEnvent();
     }
     function errorFilter() {
     $("#platformSelection").html("<h5><?php echo __('Search error'); ?></h5>");
     }*/


    function successSentCompanies(result) {
        $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Companies saved") ?></strong></div>');
        fadeOutElement(".alert-to-fade", 5000);
    }
    function errorSentCompanies(result) {
        $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Error saving companies") ?></strong></div>');
        fadeOutElement(".alert-to-fade", 5000);
    }



    function successDataPanel(result) {
        $(document).off('click');
        $(document).off('change');
        $("#content").html(result);
    }
    function errorDataPanel(result) {
        $(document).off('click');
        $(document).off('change');
        $("#content").html(result);
    }



    function successDelete() {
        $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Company deleted") ?></strong></div>');
        fadeOutElement(".alert-to-fade", 5000);
        total--;
        recount();
    }
    
    function errorDelete() {
        $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Cant delete company") ?></strong></div>');
        fadeOutElement(".alert-to-fade", 5000);
    }


    function addEnvent() {
        //iCheck plugin
        $('input').iCheck({
            checkboxClass: 'icheckbox_flat-blue'
        });

<?php
foreach ($notShowList as $id) {
    ?>
            $('#selection').append("<input type='hidden' value='" + <?php echo $id ?> + "' class='selected inDB'></input>");
    <?php
}
?>



<?php
foreach ($selected as $sel) {
    $idSel = $sel['companies_ocrs']['company_id'];
    ?>
            logo = $(".<?php echo $sel['companies_ocrs']['company_id']; ?>").find(".logo").attr("src");
            $('#selection').append("<div value='" + <?php echo $idSel ?> + "' class='selected inDB col-xs-12 col-sm-6 col-md-2 col-lg-2'><div class='box box-widget widget-user-2 selectedPlatform'> <div class='widget-user-header'><i class='ion ion-close-circled btnSelectedPlatform btnSelectedPlatformDB' style='color: gray;'></i><img src='" + logo + "' style='max-height: 100px' alt='platform-logotype' class='responsiveImg center-block platformLogo'/></div></div></div>");
    <?php
}
?>



        $("#platformSelection").find(".btnSelect").prop('disabled', true);
        //Te elimina las compañias ya seleccionadas
        $('#selection').children('.selected').each(function () {
            company = $(this).attr("value");
            $('#platformSelection').find("." + company).css("display", "none");
        });

        //Te pasa el seleccionado a su zona
        $(".btnSelect").click(function () {
            id = $(this).attr("id");
            $(this).prop("disabled", true);
            z++;
            $("#" + id).parentsUntil("#platformSelection").fadeOut();
            $("#selection").append("<div value='" + id + "' name ='company" + z + "' class='selected col-xs-12 col-sm-6 col-md-2 col-lg-2'><div class='box box-widget widget-user-2 selectedPlatform'> <div class='widget-user-header'><i class='ion ion-close-circled btnSelectedPlatform btnSelectedPlatformNoDB' style='color: gray;'></i><img src='" + $("#" + id).parentsUntil($("#platformSelection")).find(".logo").attr("src") + "' style='max-height: 100px' alt='platform-logotype' class='responsiveImg center-block platformLogo'/></div></div></div>");
            recount();
            extraEvent();
            $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Company added.") ?></strong></div>');
            fadeOutElement(".alert-to-fade", 5000);
        });


        //Te comprueba los dos checkbox de la plataforma y te habilita o desabilita el select
        $(".iCheck-helper").click(function () {
            //Cada compañia tiene su propio array
            arrayCheck = new Array();
            $(this).parentsUntil(".row", ".checkboxDiv").find(".icheckbox_flat-blue").each(function (index, value) {
                value = $(this).attr("aria-checked");
                arrayCheck[index] = value;
            });
            if (arrayCheck[0] == "true" && arrayCheck[1] == "true") {
                $(this).parentsUntil(".box-footer").find(".btnSelect").prop('disabled', false);
            } else {
                $(this).parentsUntil(".box-footer").find(".btnSelect").prop('disabled', true);
            }
        });
    }
    function extraEvent() {
        //Borra la plataforma cuando le das a l x
        $('.btnSelectedPlatform').click(function () {
            idDel = $(this).parent().parent().parent().attr("value");
            $("#" + idDel).prop("disabled", false);
            $("#platformSelection").find("." + idDel).fadeIn();
            $("#platformSelection").find("." + idDel).find('*').fadeIn();
            $(this).parent().parent().parent().remove();
            recount();
            $("#notification").html('<div class="alert bg-success alert-dismissible alert-win-success fade in alert-to-fade" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-right: 30px;"><span aria-hidden="true">&times;</span></button><strong><?php echo __("Company deleted") ?></strong></div>');
            fadeOutElement(".alert-to-fade", 5000);
        });

    }
    function recount() {
        i = 0;
        $(".btnSelectedPlatform").each(function () {
            i++;
            $("#numberCompanies").val(i - total);
            numberCompanies = i - total;
        });
        selEvent();
    }
    function selEvent() {
        if (total != 0 || $(".btnSelectedPlatformNoDB").length) {
            $("#sel").fadeIn();
        } else {
            $("#sel").fadeOut();
        }

    }
</script>
<style>
    .togetoverlay .overlay  {
        z-index: 50;
        background: rgba(255, 255, 255, 0);
        border-radius: 3px;
        position: absolute;
    }
    .togetoverlay .overlay > .fa {
        font-size: 20px;
        margin-left: -10px;
        margin-top: -25px;
    }
</style>
<div id="1CR_investor_1_platformSelection">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div id="notification"> </div>
            <?php
            /* DIV 1: Selected platforms */
            //print_r($selected);
            //print_r($company);
            ?>
            <div id="sel">
                <h4 class="header1CR"><?php echo __('Your selected platforms:') ?></h4>

                <div id="investorSelection" class="row">

                    <?php
                    echo $this->Form->create(array("id" => "selection"));
                    echo $this->Form->input('numberCompanies', array(
                        'name' => 'numberCompanies',
                        'id' => 'numberCompanies',
                        'label' => false,
                        'type' => 'hidden',
                        'value' => 0
                    ));





//Automatic feedback
                    /* for ($i = 0; $i < count($selected); $i++) {
                      if ($selected['companies_ocrs']['statusOcr'] == 0) {
                      $idSel = $selected[$i]['companies_ocrs']['company_id'];
                      for ($j = 0; $j < count($company); $j++) {
                      if ($idSel == $company[$j]['Company']['id']) {
                      $logo = $company[$j]['Company']['company_logoGUID'];
                      ?>
                      <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                      <div class="box box-widget widget-user-2 pendingPlatform">
                      <div class="widget-user-header">
                      <i class="fa fa-circle-o prueba" style="color: #FF5886"></i>
                      <img src="/img/logo/<?php echo $logo ?>" style="max-height: 100px" alt="platform-logotype" class="img-responsive center-block platformLogo"/>
                      </div>
                      </div>
                      </div>
                      <?php
                      }
                      }
                      }
                      }
                      <!--<div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                      <div class="box box-widget widget-user-2 selectedPlatform">
                      <div class="widget-user-header">
                      <img src="/img/logo/Finanzarel.png" style="max-height: 100px" alt="platform-logotype" class="responsiveImg center-block platformLogo"/>
                      </div>
                      </div>
                      </div>
                      <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                      <div class="box box-widget widget-user-2 pendingPlatform">
                      <div class="widget-user-header">
                      <i class="fa fa-circle-o prueba" style="color: #FF5886"></i>
                      <img src="/img/logo/Zank.png" style="max-height: 100px" alt="platform-logotype" class="img-responsive center-block platformLogo"/>
                      </div>
                      </div>
                      </div>
                      <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                      <div class="box box-widget widget-user-2 registeredPlatform">
                      <div class="widget-user-header">
                      <i class="fa fa-check-circle prueba" style="color: rgb(90, 204, 90)"></i>
                      <img src="/img/logo/Comunitae.png" style="max-height: 100px" alt="platform-logotype" class="img-responsive center-block platformLogo"/>
                      </div>
                      </div>
                      </div>-->
                     */

                    echo $this->Form->end();
                    ?> 


                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <button id="sentCompanies" type="button" class="btn btn-default btn-lg btn1CR btnRounded center-block togetoverlay" style="padding: 10px 50px; margin-bottom: 25px">
                            <div class="overlay">
                                <div class="fa fa-spin fa-spinner" style="color:green">	
                                </div>
                            </div>
                            <?php echo __('Go!') ?>
                        </button>
                    </div>
                    <hr width="100%">
                </div>
            </div>

            <?php /* DIV 2: Filters 
              <div id="investorFilters" class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <h4 class="header1CR"><?php echo __('Filter by:') ?></h4>
              <div class="row">
              <?php echo $this->Form->create(); ?>
              <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
              <?php
              $class = "filter form-control blue_noborder investorCountry" . ' ' . $errorClass;
              echo $this->Form->input('Investor.investor_country', array(
              'name' => 'country',
              'id' => 'filterCountry',
              'label' => false,
              'options' => $filterCompanies1,
              'placeholder' => __('Country'),
              'class' => $class,
              ));
              ?>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
              <?php
              $class = "filter form-control blue_noborder investorModality" . ' ' . $errorClass;
              $modalities = ["select modality", "P2P", "P2B", "Invoice Trading"];
              echo $this->Form->input('Investor.investor_type', array(
              'name' => 'type',
              'id' => 'filterType',
              'label' => false,
              'options' => $filterCompanies2,
              'placeholder' => __('Modality'),
              'class' => $class,
              ));
              ?>
              </div>

              <?php echo $this->Form->end(); ?>
              </div>
              </div>
              </div>
              <hr class="nomargin" width="100%"/> */ ?>
            <?php /* Div 2: Platforms Selection */ ?>
            <h4 class="header1CR"><?php echo __('Select platforms to register') ?></h4>
            <div id="platformSelection" class="row">
                <?php
                foreach ($company as $comp) {
                    ?>

                    <div class="companyDiv col-xs-12 col-sm-6 col-md-3 col-lg-3 <?php echo $comp['Company']['id'] ?>">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header">
                                <div class="row">
                                    <div id="companyLogo" class="col-xs-12 col-sm-12 col-md-12 col-lg-5">
                                        <img src="/img/logo/<?php echo $comp['Company']['company_logoGUID'] ?>" style="max-height: 100px" alt="platform-logotype" class="logo img-responsive center-block platformLogo"/>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7">
                                        <ul class="nav nav-stacked">
                                            <li class = 'country'><img src="/img/flags/<?php echo $comp['Company']['company_country'] ?>.png" alt="Spain Flag"/> <?php echo __($comp['Company']['company_countryName']) ?></li>
                                            <li class = 'type'><?php echo __($comp['Company']['Company_type']) ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer no-padding">
                                <div class="row">
                                    <div class="checkboxDiv col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="input_platforms"><input type="checkbox" class="check check<?php echo $comp['Company']['id'] ?>"> <?php echo __('He leído la ') ?><a href="<?php echo $comp['Company']['Company_privacityUrl'] ?>"><?php echo __('Privacy Policy') ?></a></div>
                                        <div class="input_platforms"><input type="checkbox" class="check check<?php echo $comp['Company']['id'] ?>"> <?php echo __('He leído los ') ?><a href="<?php echo $comp['Company']['Company_termsUrl'] ?>"><?php echo __('Terms and Conditions') ?></a></div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <button id ="<?php echo $comp['Company']['id'] ?>"  class="btnSelect btn btn-default btn1CR btnMargin btnSelected btnRounded pull-right" style="margin-right: 10px !important;"><?php echo __('Select') ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }
                ?>
            </div>
        </div> <!-- /.col 9 -->
    </div> <!-- /.row general -->
</div>
