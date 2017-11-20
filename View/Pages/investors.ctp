<?php
/**
 * +---------------------------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                                               |
 * +---------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 				 |
 * | it under the terms of the GNU General Public License as published by  			 |
 * | the Free Software Foundation; either version 2 of the License, or                           |
 * | (at your option) any later version.                                      			 |
 * | This file is distributed in the hope that it will be useful   				 |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of                          	 |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                                |
 * | GNU General Public License for more details.        			              	 |
 * +---------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2017-08-23
 * @package
 * 
 * Winvestify Investors static page to explain the functionality of Winvestify
 * 
 * [2017-09-25] version 0.1
 * Initial Structure
 * 
 */
?>
<style>
    ul li {
        font-size: medium;
        color: black;
    }
    p {
        font-size: 16px; 
        color: black;
    }
</style>
<div id="winvestifySolutionsInvestors">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-10 col-sm-offset-1 col-lg-10 col-lg-offset-1" style="min-height: 600px;">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><img src="/img/screenshots/dashboard.png" class="imgResponsive investorsImg center-block"/></div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <h2 class="g-color--primary"><?php echo __('Dashboard')?></h2>
                    <h4><?php echo __('Más Información, Más Control')?></h4>
                    <p>Para que sepa a qué ritmo inviertes y donde pierdes dinero</p>
                    <ul>
                        <li>Un análisis a medida para conocer el estado de tus cuentas</li>
                        <li>Un panel de mando con toda la información estandarizada</li>
                        <li>Inversiones agrupadas automáticamente por plataformas</li>
                        <li>Conoce tu nivel de morosidad, comisiones aplicadas y pérdidas</li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <hr class="specialHr">
                <h2 class="g-color--primary"><?php echo __('Global Marketplace')?></h2>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <h4><?php echo ('Más oferta, más diversificación')?></h4>
                    <p><?php echo __('En Winvestify mostramos los mejores préstamos activos para invertir y conectamos con las plataformas de Crowdlending más rentables')?></p>
                    <ul>
                        <li>Miramos por tu interés, sea cual sea la plataforma</li>
                        <li>Concentramos toda la oferta del mercado en un único portal</li>
                        <li>Filtra atendiendo a tus criterios de inversión</li>
                    </ul>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><img src="/img/screenshots/marketplace.png" class="imgResponsive investorsImg center-block"/></div>
            </div>
            <div class="row">
                <hr class="specialHr">
                <h2 class="g-color--primary"><?php echo __('One Click Registration')?></h2>
                <div>
                    <h4><?php echo __('Más accesible, más ágil')?></h4>
                    <p><?php echo __('En Winvestify apostamos fuerte por este modelo de inversión alternativa. Colaboramos activamente con las plataformas más importantes para facilitar tu acceso a ellas:')?></p>
                    <ul>
                        <li>Registrate en cualquier plataformas ¡Sin salir de Winvestify!</li>
                        <li>Un único formulario de registro para acceder a las principales plataformas</li>
                        <li>Proceso sencillo y seguro</li>
                    </ul>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <img src="/img/screenshots/ocr1.png" class="imgResponsive investorsImg center-block"/>
                    <p align="center"><strong><?php echo __('Selecciona tus plataformas preferidas')?></strong></p>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <img src="/img/screenshots/ocr2.png" class="imgResponsive investorsImg center-block"/>
                    <p align="center"><strong><?php echo __('Un solo proceso de registro')?></strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="security">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h6 style="font-weight: 800; color: #23b18f;"><span style="border-bottom: 5px solid #808080;"><?php echo __('SEGURIDAD')?></span></h6>
            <h3 class="g-color--primary"><?php echo __('Nos tomamos la seguridad muy en serio')?></h3>
            <h6><?php echo __('Para Winvestify su comunidad de inversores son lo más valioso y por tanto los datos propiedad de estos son si cabe aún más importantes.')?></h6>
            <br/>
            <p class="divpadding"><strong><i class="fa fa-shield"></i> Alta Seguridad:</strong><?php echo __(' La información está protegida con nivel de seguridad bancaria de 256 bits, que es el mismo nivel de protección que tienen los bancos más avanzados.')?></p>
            <br/>
            <p class="divpadding"><strong><i class="fa fa-user-circle-o"></i> Tus datos:</strong><?php echo __(' Tus datos personales están amparados por la Ley Orgánica de Protección de Datos (LOPD). La información es almacenada en distintos soportes de información con sistemas de cifrado, de forma disociada que la convierte en inútil en caso de no disponer de todos los componentes de la misma.')?></p>
        </div>
    </div>
</div>
<div id="contact">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1 align="center" style="font-weight: 800"><?php echo __('Suggestions')?></h1>
            <h3 align="center" style="font-weight: 500"><?php echo __('Here you can suggest us ideas or enhancements')?></h3><br/>
            <a href="/Contactforms/form/"><button class="btn btn-lg btn1CR center-block" style="border-radius: 25px;"><?php echo __('Contact Us')?></button></a>
        </div>
    </div>
</div>