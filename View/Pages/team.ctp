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
        $(".teamBox").hover(function() {
            id = $(this).attr("id");
            $(".teamBox" + id).toggleClass("teamBoxColor");
        });
        /*$(document).on("mouseleave", ".teamBox", function() {
            id = $(this).attr("id");
            $(".teamBox" + id).removeClass("teamBoxColor");
        });*/
    });
</script>
<div id="winvestifyTeam">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-10 col-sm-offset-1 col-lg-10 col-lg-offset-1" style="min-height: 600px;">
            <div class="row">
                <div class="g-text-center--xs">
                    <p class="text-uppercase g-font-size-32--xs g-font-weight--700 g-color--primary g-letter-spacing--2 g-margin-b-25--xs"><?php echo __('El equipo de Winvestify') ?></p>
                </div>
                <p><?php echo __('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus')?></p>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxmanuel" id="manuel">
                            <img src="/img/teamfiles/team_ManuelMillan.png" class="img-responsive center-block teamImg"/>
                            <div id="team_manuel">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Manuel Millán')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Chief Executive Officer & Cofounder')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><a href="https://www.linkedin.com/in/manuel-mill%C3%A1n-miras-55695195/" target="_blank"><i class="fa fa-linkedin"></i></a></button>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxolena" id="olena">
                            <img src="/img/teamfiles/team_OlenaTatarin.png" class="img-responsive center-block teamImg"/>
                            <div id="team_olena">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Olena Tatarin')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Chief Financial Officer & Cofounder')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><a href="https://www.linkedin.com/in/olena-tatarin-parashyuk-a25851101/" target="_blank"><i class="fa fa-linkedin"></i></a></button>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxantoine" id="antoine">
                            <img src="/img/teamfiles/team_AntoineDePoorter.png" class="img-responsive center-block teamImg"/>
                            <div id="team_antoine">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Antoine de Poorter')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Chief Technology Officer & Cofounder')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><a href="https://www.linkedin.com/in/antoine-de-poorter-9b4aa6114/" target="_blank"><i class="fa fa-linkedin"></i></a></button>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxklaus" id="klaus">
                            <img src="/img/teamfiles/team_KlausKukovetz.png" class="img-responsive center-block teamImg"/>
                            <div id="team_klaus">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Klaus Kukovetz')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Investor Relations')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><a href="" target="_blank"><i class="fa fa-linkedin"></i></a></button>
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
                            <img src="/img/teamfiles/team_CristinaOrtega.png" class="img-responsive center-block teamImg"/>
                            <div id="team_cris">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Cristina Ortega')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Junior Software Designer')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><a href="https://www.linkedin.com/in/crisortegadc/" target="_blank"><i class="fa fa-linkedin"></i></a></button>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxantonio" id="antonio">
                            <img src="/img/teamfiles/team_AntonioIbanez.png" class="img-responsive center-block teamImg"/>
                            <div id="team_antonio">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Antonio Ibañez')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Junior Software Designer')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><a href="https://www.linkedin.com/in/antonio-jes%C3%BAs-ib%C3%A1%C3%B1ez-garc%C3%ADa-47107871/" target="_blank"><i class="fa fa-linkedin"></i></a></button>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxeduardo" id="eduardo">
                            <img src="/img/teamfiles/team_EduardoIbanez.png" class="img-responsive center-block teamImg"/>
                            <div id="team_eduardo">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Eduardo Ibañez')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Junior Software Designer')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><a href="https://www.linkedin.com/in/eduardo-iba%C3%B1ez-carmona-565147146/" target="_blank"><i class="fa fa-linkedin"></i></a></button>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 teamBox teamBoxana" id="ana">
                            <img src="/img/teamfiles/team_AnaMarin.png" class="img-responsive center-block teamImg"/>
                            <div id="team_ana">
                                <span class="teamName"><center><i class="fa fa-circle-o" style="color: #47badf;"></i>&nbsp;&nbsp;<?php echo __('Ana Marín')?></center></span>
                                <span class="teamPosition"><center><?php echo __('Costumer Care')?></center></span>
                                <button class="btn btn1CR btnRounded center-block"><a href="https://www.linkedin.com/in/a-m-61a229143/" target="_blank"><i class="fa fa-linkedin"></i></a></button>
                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>