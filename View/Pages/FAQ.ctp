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
 * Winvestify Frequently Asqued Questions
 * 
 */
?>
<style>
    .textBold {
        font-weight: bold;  
    }
    .questionTitle {
        padding-top: 20px;
    }
</style>
<script>
    $(function (){
        <?php //Tooltip clicks ?>
        $(document).on("click", ".questionTitle", function() {
            id = $(this).attr("id");
            $("#answer_" + id).slideToggle(600);
            $(this).toggleClass("textBold");
        });
    });
</script>
<div class="winvestifyFAQ">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-10 col-sm-offset-1 col-lg-10 col-lg-offset-1" style="min-height: 600px;">
            <div class="row">
                <div class="g-text-center--xs">
                    <p class="text-uppercase g-font-size-32--xs g-font-weight--700 g-color--primary g-letter-spacing--2 g-margin-b-25--xs"><?php echo __('Frequently Asked Questions') ?></p>
                </div>
            </div>
            <div class="row">	
                <ul  class="nav nav-pills">
                    <li class="active"><a class="greenTab" href="#about" data-toggle="tab"><?php echo __('Sobre Winvestify')?></a></li>
                    <li><a class="greenTab" href="#security" data-toggle="tab"><?php echo __('Seguridad y privacidad')?></a></li>
                    <li><a class="greenTab" href="#account" data-toggle="tab"><?php echo __('Tu cuenta')?></a></li>
                </ul>   
                <div class="tab-content clearfix">
                    <div class="tab-pane active" id="about">
                        <ul style="list-style-type: none;">
                            <li id="a1_1" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Qué es Winvestify?')?></li>
                            <div id="answer_a1_1" style="display:none">
                                <?php echo __('Winvestify es una herramienta para la gestión de inversiones multiplataforma mediante Crowdlending. Podrás conectar con las principales Plataformas de Financiación Participativa desde un único portal para controlar el estado de todas tus inversiones en tiempo real.')?>
                            </div>
                            <li id="a1_2" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Para qué sirve Winvestify?')?></li>
                            <div id="answer_a1_2" style="display:none">
                                <?php echo __('El objetivo de Winvestify es que puedas sacar el máximo partido a tu dinero:')?><br/>
                                <?php echo __('Te ayuda a entender, gestionar de un modo más eficiente y preciso la información de  tus inversiones para que puedas potenciar tu rentabilidad, canalizar mayor volumen de inversión con menor riesgo.')?><br/>
                                <?php echo __('Te da más poder porque te ayuda a entender mejor como operan todas tus plataformas ya que estandarizamos toda la información de un sencillo y funcional')?>
                            </div>
                            <li id="a1_3" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Cuál es el Objetivo de Winvestify?')?></li>
                            <div id="answer_a1_3" style="display:none">
                                <?php echo __('Winvestify se ha creado para crear y reforzar nuevas relaciones entre la  actual comunidad de inversores y las propias Plataformas de Financiación Participativa. Somos el nuevo catalizador de la economía real y un complemento necesarios entre ambos actores')?>
                            </div>
                            <li id="a1_4" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Necesito agregar una plataforma para utilizar Winvestify?')?></li>
                            <div id="answer_a1_4" style="display:none">
                                <?php echo __('Para sacarle el máximo partido a tus inversiones es imprescindible agregar una plataforma. De esta manera podrás estandarizar y gestionar toda tu información de un modo eficiente.  Estarás al día de lo que ocurre en tus cuentas.')?>
                            </div>
                            <li id="a1_5" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Puedo solicitar financiación a través de Winvestify?')?></li>
                            <div id="answer_a1_5" style="display:none">
                                <?php echo __('No. Winvestify solo muestra los proyectos invertibles que ofrecen las principales Plataformas de Financiación Participativa. Por lo tanto te recomendamos que lo solicite a través de ellas. ')?>
                            </div>
                            <li id="a1_6" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Cómo gana dinero Winvestify?')?></li>
                            <div id="answer_a1_6" style="display:none">
                                <?php echo __('La forma de generar ingresos se debe el análisis estadístico de la información disociada, acuerdos con Plataformas de Financiación Participativa y otros partners.')?><br/>
                                <?php echo __('Winvestify analiza los servicios existentes del mercado que pueden ser mejorables para los inversores y ayuda a las actuales Plataformas de Financiación Participativa a entender mejor las necesidades de su comunidad de inversores.')?><br/>
                            </div>
                        </ul>
                    </div>
                    <div class="tab-pane" id="security">
                         <ul style="list-style-type: none;">
                            <li id="a2_1" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Qué necesito para crearme una cuenta en Winvestify?')?></li>
                            <div id="answer_a2_1" style="display:none"><?php echo __('Para crear una cuenta de usuario en Winvestify es necesario un correo electrónico, teléfono móvil y una contraseña.')?></div>
                            <li id="a2_2" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Desde Winvestify puedo cambiar la clave de acceso de mis plataformas?')?></li>
                            <div id="answer_a2_2" style="display:none"><?php echo __('En Winvestify no puedes cambiar la clave de acceso de tus cuentas asociadas, para realizar esta tarea siempre tendrás que realizarlo en la plataforma donde tengas operativa tu cuenta de inversor.')?></div>
                            <li id="a2_3" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Qué ocurre si cambio la clave de acceso de mi plataforma?')?></li>
                            <div id="answer_a2_3" style="display:none"><?php echo __('Si cambias la clave de acceso de tu plataforma, debes actualizar esa información en Winvestify para seguir visualizando tu información actualizada.')?></div>
                            <li id="a2_4" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('Tengo problemas al  agregar  mis plataformas, ¿Qué puedo hacer?')?></li>
                            <div id="answer_a2_4" style="display:none">
                                <?php echo __('A veces cuando agregamos tus plataformas de financiación participativa nos encontramos con problemas que no podemos solucionar en el momento. ')?><br/>
                                <?php echo __('Si no consigues agregar alguna plataforma por favor escríbenos a info@winvestify.com o accede a nuestro ')?><a href="/Contactforms/form"><?php echo __('formulario de contacto')?></a>
                            </div>
                            <li id="a2_5" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Por qué no aparecen todas las Plataformas de Fiananciación Participativa?')?></li>
                            <div id="answer_a2_5" style="display:none"><?php echo __('Las principales plataformas de financiación participativa ya están incorporadas a Winvestify. No obstante, estamos trabajando en incluir las demás y nos sería muy útil si nos dijeras en a info@winvestify.com cuál es la que no encuentras para darle prioridad.')?></div>
                            <li id="a2_6" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Cómo actualizo una plataforma?')?></li>
                            <div id="answer_a2_6" style="display:none"><?php echo __('Las cuentas se actualizan para tu comodidad todas las semanas y en inversores profesionales y acrediatados todas las noches. De esta manera cuando accedes por la mañana a tu información lo tienes todo al día.')?></div>
                        </ul>
                    </div>
                    <div class="tab-pane" id="account">
                        <ul style="list-style-type: none;">
                            <li id="a3_1" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Cómo se tratan mis datos en Winvestify? ¿Es seguro?')?></li>
                            <div id="answer_a3_1" style="display:none">
                                <?php echo __('Su seguridad está garantizada. Para Winvestify su comunidad de inversores son lo más valioso y por tanto los datos propiedad de estos son si cabe aún más importantes. Toda comunicación viaja cifrada con los máximos niveles de encriptación, 256 bits como realizan los bancos en sus operativas. La información es almacenada en soportes de información con sistemas de cifrado, de forma disociada que la convierte en inútil en caso de no disponer de todos los componentes de la misma, y anonimizada por lo que nunca puede ser relacionada a una persona en concreto salvo que se rompan los tres niveles de seguridad anteriormente indicados (encriptación, disociación y a anonimización). Y para más detalle, ponemos a tu disposición nuestra política de protección de datos para que puedas consultarla y resolver cualquier duda que te haya quedado.')?><br/>
                                <?php echo __('Para mayor seguridad, una vez dentro todo información está gestionado por algoritmos, nunca por humanos')?>
                            </div>
                            <li id="a3_2" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Puede hacer Winvestify operaciones con mis credenciales?')?></li>
                            <div id="answer_a3_2" style="display:none"><?php echo __('No. En Winvestify no podemos realizar operaciones entre cuentas, por ello, las claves que te pedimos son  exclusivamente para la lectura de tu información. Adicionalmente colaboramos activamente con la Plataformas  de Financiación Participativa y registramos todas las comunicaciones efectuadas desde nuestro portal para mayor seguridad.')?></div>
                            <li id="a3_3" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Si entra un Hacker, cuál es mi riesgo?')?></li>
                            <div id="answer_a3_3" style="display:none"><?php echo __('Toda la información de tus inversiones se almacena en entornos distintos al de las claves de lectura de acceso, por lo que no se relacionan estos ficheros. La información es almacenada en soportes de información con sistemas de cifrado, de forma disociada que la convierte en inútil en caso de no disponer de todos los componentes de la misma. También mantenemos auditorías internas del código para verificar que todas las operaciones  registradas se desarrollan correctamente atendiendo a nuestros protocolos de seguridad.')?></div>
                            <li id="a3_4" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Cómo elimino mi cuenta de Winvestify?')?></li>
                            <div id="answer_a3_4" style="display:none"><?php echo __('En la página de inicio, en la sección "Contáctanos" podrá darse de baja rellenado el formulario de contacto especificándolo. Una vez enviada tu petición, nuestro equipo de soporte te notificará la baja y  todos tus datos serán eliminados. Si quieres que te ayudemos escríbenos a info@winvestify.com')?></div>
                            <li id="a3_5" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Para entrar en Winvestify que usuario y contraseña debo usar?')?></li>
                            <div id="answer_a3_5" style="display:none"><?php echo __('Para acceder a  tu cuenta de Winvestify, deberá indtruducir las credenciales que con las que se dio de alta. Es recomendable el uso de una contraseña distinta al restos de tus cuentas asociadas.')?></div>
                            <li id="a3_6" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Puedo cambiar mi dirección de correo electrónico?')?></li>
                            <div id="answer_a3_6" style="display:none">
                                <?php echo __('Si. Para cambiar algún dato de su credencial debe rellenar el formulario de contacto y especificar que quiere cambiar su correo electrónico.')?>
                                <?php echo __('Puede acceder al formulario de contacto haciendo click ')?><a href="/Contactforms/form"><?php echo __('aquí')?></a>
                            </div>
                            <li id="a3_7" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Cómo puedo cambiar mi contraseña en Winvestify?')?></li>
                            <div id="answer_a3_7" style="display:none"><?php echo __('Para cambiar su contraseña en Winvestify debe hacer el log-in y una vez en nuestra web dirigirse a datos personales y cambiar la contraseña. Deberá rellenar todos los campos obligatorios.')?></div>
                            <li id="a3_8" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Cómo puedo modificar los datos de mi perfil?')?></li>
                            <div id="answer_a3_8" style="display:none"><?php echo __('Debe dirigirse a la parte del menú situado en su lateral izquierdo y hacer clic en ‘Datos personales’, ahí podrá actualizar todos sus datos excepto el teléfono.')?></div>
                            <li id="a3_9" class="questionTitle"><i class="fa fa-circle-o"></i>&nbsp;&nbsp;<?php echo __('¿Cómo puedo cerrar sesión en Winvestify?')?></li>
                            <div id="answer_a3_9" style="display:none"><?php echo __('Seleccione el icono de usuario en la parte superior derecha y pulse en "Salir". En caso, de que su cuenta se quede  inactiva durante un tiempo prolongado,  la sesión  caducará automáticamente.')?></div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>