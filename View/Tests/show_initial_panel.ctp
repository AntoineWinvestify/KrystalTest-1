<?php
/**
 * +--------------------------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                                              |
 * +--------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify                          |
 * | it under the terms of the GNU General Public License as published by                       |
 * | the Free Software Foundation; either version 2 of the License, or                          |
 * | (at your option) any later version.                                                        |
 * | This file is distributed in the hope that it will be useful                                |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of                             |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                               |
 * | GNU General Public License for more details.                                               |
 * +--------------------------------------------------------------------------------------------+
 * 
 * @author
 * @version 0.4
 * @date 2017-05-29
 * @package
 * 
 * DASHBOARD 2.0 - Initial view where user can select it it's accredited user or not. Links go to
 * 1CR or Account Linking.
 * 
 * [2017-08-01] version 0.1
 */
?>
<script>
    $(function (){
        //Click on Account Linking btn
        $(document).on("click", "#btnAccountLinking", function(){
            
        });
        //Click on Start btn
        $(document).on("click", "#btnStart", function(){
            
        });
    });
</script>

<div id="dashboardInitialPanel" class="modal show" role="dialog">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="green" id="wizardProfile">
                        <div class="wizard-header text-center">
                            <button type="button" class="close closeBtn" data-dismiss="modal" aria-hidden="true" style="margin-right: 15px;">&times;</button>
                            <img src="/img/logo_winvestify/Logo.png" style="max-width:75px;"/>
                            <img src="/img/logo_winvestify/Logo_texto.png" class="center-block" style="max-width:250px;"/>
                        </div>
                        <div class="tab-content">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <p><?php echo __('Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido '
                                            . 'el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se '
                                            . 'dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro '
                                            . 'de textos especimen. No sólo sobrevivió 500 años, sino que tambien ingresó como texto de relleno en documentos '
                                            . 'electrónicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creación de las hojas '
                                            . '"Letraset", las cuales contenian pasajes de Lorem Ipsum, y más recientemente con software de autoedición, como por '
                                            . 'ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum.')?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    //Nunca he invertido
                                    <input type='button' id="btnAccountLinking" class='btn btn-default' name='accountLinking' value='<?php echo __('Go to Account Linking')?>' />
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                    //Invierto en Crowdlending
                                    <input type='button' id="btnStart" class='btn btn-default' name='start' value='<?php echo __('Start')?>' />
                                </div>
                            </div>
                        </div> <!-- /tab-content 
                        <div class="wizard-footer">
                            <div class="pull-right">
                                
                            </div>
                            <div class="pull-left">
                                
                            </div>
                            <div class="clearfix"></div>
                        </div>-->
                    </div>  <!-- /wizard-card -->
                </div> <!-- /wizard-container -->
            </div> <!-- /modal -->
        </div>
    </div>
</div>
