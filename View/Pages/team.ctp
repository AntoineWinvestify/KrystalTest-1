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
 * Winvestify Static Page about TEAM MEMBERS
 * 
 */
?>
<script>
    $(function (){
        <?php //Tooltip clicks ?>
        $(document).on("mouseenter", ".teamBox", function() {
            id = $(this).attr("id");
            $("#team_" + id).fadeIn(1000);
            $(".teamBox" + id).addClass("teamBoxColor");
        });
        $(document).on("mouseleave", ".teamBox", function() {
            id = $(this).attr("id");
            $("#team_" + id).fadeOut(1000);
            $(".teamBox" + id).removeClass("teamBoxColor");
        });
    });
</script>
<div id="winvestifyTeam">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-10 col-sm-offset-1 col-lg-10 col-lg-offset-1" style="min-height: 600px;">
            <div class="row">
                <div class="g-text-center--xs">
                    <p class="text-uppercase g-font-size-32--xs g-font-weight--700 g-color--primary g-letter-spacing--2 g-margin-b-25--xs"><?php echo __('Winvestify Team') ?></p>
                </div>
                <p><?php echo __('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus')?></p>
            </div>
            <div class="row" style="display:none;">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                            <img src="/img/teamfiles/prueba_team_Cris.png" class="img-responsive center-block teamImg"/>
                            <span class="teamName center-block"><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Manuel Millán')?></span>
                            <span class="teamPosition center-block"><?php echo __('CEO & Cofounder')?></span>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                            <button class="btn btn1CR btnRounded"><i class="fa fa-linkedin">&nbsp;&nbsp;</i><?php echo __('Follow me on LinkedIn')?></button>
                            
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <img src=""/>
                            SOCIAL MEDIAAA
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                            <p><?php echo __('hola soy un párrafo de título que mola más.')?></p>
                            <p><?php echo __('hola soy un párrafo.')?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxmanuel" id="manuel">
                            <img src="/img/teamfiles/prueba_team_Cris.png" class="img-responsive center-block teamImg"/>
                            <div id="team_manuel" style="display:none;">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Manuel Millán')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Chief Executive Officer & Cofounder')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><i class="fa fa-linkedin">&nbsp;&nbsp;</i><?php echo __('Follow me on LinkedIn')?></button>
                                <p class="teamParagraph"><?php echo __('hola soy un párrafo hablando sobre lo bonita que es mi vida..')?></p>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxolena" id="olena">
                            <img src="/img/teamfiles/prueba_team_Cris.png" class="img-responsive center-block teamImg"/>
                            <div id="team_olena" style="display:none;">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Olena Tatarin')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Chief Financial Officer & Cofounder')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><i class="fa fa-linkedin">&nbsp;&nbsp;</i><?php echo __('Follow me on LinkedIn')?></button>
                                <p class="teamParagraph"><?php echo __('hola soy un párrafo hablando sobre lo bonita que es mi vida..')?></p>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxantoine" id="antoine">
                            <img src="/img/teamfiles/prueba_team_Cris.png" class="img-responsive center-block teamImg"/>
                            <div id="team_antoine" style="display:none;">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Antoine de Poorter')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Chief Technology Officer & Cofounder')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><i class="fa fa-linkedin">&nbsp;&nbsp;</i><?php echo __('Follow me on LinkedIn')?></button>
                                <p class="teamParagraph"><?php echo __('hola soy un párrafo hablando sobre lo bonita que es mi vida..')?></p>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxklaus" id="klaus">
                            <img src="/img/teamfiles/prueba_team_Cris.png" class="img-responsive center-block teamImg"/>
                            <div id="team_klaus" style="display:none;">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Klaus')?></center></span>
                                <span class="teamPosition"><center><?php echo __('???')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><i class="fa fa-linkedin">&nbsp;&nbsp;</i><?php echo __('Follow me on LinkedIn')?></button>
                                <p class="teamParagraph"><?php echo __('hola soy un párrafo hablando sobre lo bonita que es mi vida..')?></p>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxcris" id="cris">
                            <img src="/img/teamfiles/prueba_team_Cris.png" class="img-responsive center-block teamImg"/>
                            <div id="team_cris" style="display:none;">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Cristina Ortega')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Programador Junior')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><i class="fa fa-linkedin">&nbsp;&nbsp;</i><?php echo __('Follow me on LinkedIn')?></button>
                                <p class="teamParagraph"><?php echo __('hola soy un párrafo hablando sobre lo bonita que es mi vida..')?></p>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxantonio" id="antonio">
                            <img src="/img/teamfiles/prueba_team_Cris.png" class="img-responsive center-block teamImg"/>
                            <div id="team_antonio" style="display:none;">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Antonio Ibañez')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Programador Junior')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><i class="fa fa-linkedin">&nbsp;&nbsp;</i><?php echo __('Follow me on LinkedIn')?></button>
                                <p class="teamParagraph"><?php echo __('hola soy un párrafo hablando sobre lo bonita que es mi vida..')?></p>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxeduardo" id="eduardo">
                            <img src="/img/teamfiles/prueba_team_Cris.png" class="img-responsive center-block teamImg"/>
                            <div id="team_eduardo" style="display:none;">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Eduardo Ibañez')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Programador Junior')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><i class="fa fa-linkedin">&nbsp;&nbsp;</i><?php echo __('Follow me on LinkedIn')?></button>
                                <p class="teamParagraph"><?php echo __('hola soy un párrafo hablando sobre lo bonita que es mi vida..')?></p>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxana" id="ana">
                            <img src="/img/teamfiles/prueba_team_Cris.png" class="img-responsive center-block teamImg"/>
                            <div id="team_ana" style="display:none;">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Ana Marín')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Atención al Inversor')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><i class="fa fa-linkedin">&nbsp;&nbsp;</i><?php echo __('Follow me on LinkedIn')?></button>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>