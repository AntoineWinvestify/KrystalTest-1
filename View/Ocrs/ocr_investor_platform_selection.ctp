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
 */
?>

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
        $('#sentCompanies').click(function () {
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

            link = "../Ocrs/oneClickInvestorI";
            var data = jQuery.param(params);
            getServerData(link, data, successSentCompanies, errorSentCompanies);

            params = {};
            link = "../Ocrs/ocrInvestorDataPanel";
            var data = jQuery.param(params);
            getServerData(link, data, successDataPanel, errorDataPanel);
        });

        $('.btnSelectedPlatformDB').click(function () {

            var params = {
                id_company: $(this).parent().parent().parent().attr("value"),
            };
            link = "../Ocrs/deleteCompanyOcr";
            var data = jQuery.param(params);
            getServerData(link, data, successDelete, errorDelete);

        });
    });



    function successFilter(result) {
        $("#platformSelection").html("<h5><?php echo __('Search results:'); ?></br></h5>" + result);
        addEnvent();
    }
    function errorFilter() {
        $("#platformSelection").html("<h5><?php echo __('Search error'); ?></h5>");
    }


    function successSentCompanies(result) {}
    function errorSentCompanies(result) {}



    function successDataPanel(result) {
        $("#OCR_InvestorPanel").html(result);
    }
    function errorDataPanel(result) {
        $("#OCR_InvestorPanel").html(result);
    }



    function successDelete(result) {
        $("#report").html("Compañia eliminada");
        total--;
        recount();
    }
    function errorDelete(result) {
    }


    function addEnvent() {
        //iCheck plugin
        $('input').iCheck({
            checkboxClass: 'icheckbox_flat-blue'
        });



<?php
foreach ($selected as $selected) {
    $idSel = $selected['companies_ocrs']['company_id'];
    ?>
            logo = $(".<?php echo $selected['companies_ocrs']['company_id']; ?>").find(".logo").attr("src");
            $('#selection').append("<div value='" + <?php echo $idSel ?> + "' class='selected inDB col-xs-12 col-sm-6 col-md-2 col-lg-2'><div class='box box-widget widget-user-2 selectedPlatform'> <div class='widget-user-header'><i class='ion ion-close-circled btnSelectedPlatform btnSelectedPlatformDB' style='color: gray;'></i><img src='" + logo + "' style='max-height: 100px' alt='platform-logotype' class='responsiveImg center-block platformLogo'/></div></div></div>");
    <?php
}
?>



        $("#platformSelection").find(".btnSelect").prop('disabled', true);
        //Te elimina las compañias ya seleccionadas despues de un filtro
        $('#selection').children('.selected').each(function () {
            company = $(this).attr("value");
            $('#platformSelection').find("."+company).css("display", "none");    
        });

        //Te pasa el seleccionado a su zona
        $(".btnSelect").click(function () {
            z++;
            $(this).parentsUntil("#platformSelection").fadeOut();
            $("#selection").append("<div value='" + $(this).attr("id") + "' name ='company" + z + "' class='selected col-xs-12 col-sm-6 col-md-2 col-lg-2'><div class='box box-widget widget-user-2 selectedPlatform'> <div class='widget-user-header'><i class='ion ion-close-circled btnSelectedPlatform btnSelectedPlatformNoDB' style='color: gray;'></i><img src='" + $(this).parentsUntil($("#platformSelection")).find(".logo").attr("src") + "' style='max-height: 100px' alt='platform-logotype' class='responsiveImg center-block platformLogo'/></div></div></div>");
            recount();
            extraEvent();
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
            $("#platformSelection").find("." + idDel).fadeIn();
            $("#platformSelection").find("." + idDel).find('*').fadeIn();
            $(this).parent().parent().parent().remove();
            recount();
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
<div id="OCR_InvestorPanel">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div id="report"> </div>
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
                        <button id="sentCompanies" type="button" class="btn btn-default btn-lg btn-win1 center-block" style="padding: 10px 50px; margin-bottom: 25px"><?php echo __('Go!') ?></button>
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
                foreach ($company as $company) {
                    ?>
                    
                        <div class="companyDiv col-xs-12 col-sm-6 col-md-3 col-lg-3 <?php echo $company['Company']['id'] ?>">
                            <div class="box box-widget widget-user-2">
                                <div class="widget-user-header">
                                    <div class="row">
                                        <div id="companyLogo" class="col-xs-12 col-sm-12 col-md-12 col-lg-5">
                                            <img src="/img/logo/<?php echo $company['Company']['company_logoGUID'] ?>" style="max-height: 100px" alt="platform-logotype" class="logo img-responsive center-block platformLogo"/>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7">
                                            <ul class="nav nav-stacked">
                                                <li class = 'country'><img src="/img/flags/<?php echo $company['Company']['company_country'] ?>.png" alt="Spain Flag"/> <?php echo __($company['Company']['company_countryName']) ?></li>
                                                <li class = 'type'><?php echo __($company['Company']['Company_type']) ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer no-padding">
                                    <div class="row">
                                        <div class="checkboxDiv col-xs-12 col-sm-12 col-md-12 col-lg-8">
                                            <div class="input_platforms"><input type="checkbox" class="check"> <?php echo __('He leído la ') ?><a href="<?php echo $company['Company']['Company_privacityUrl'] ?>"><?php echo __('Privacy Policy') ?></a></div>
                                            <div class="input_platforms"><input type="checkbox" class="check"> <?php echo __('He leído los ') ?><a href="<?php echo $company['Company']['Company_termsUrl'] ?>"><?php echo __('Terms and Conditions') ?></a></div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
                                            <button id ="<?php echo $company['Company']['id'] ?>"  class="btnSelect btn btn-default btn-win2 btnMargin center-block btnSelected" href = "#"><?php echo __('Select') ?></button>
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
