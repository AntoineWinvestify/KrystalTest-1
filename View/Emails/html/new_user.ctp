    <?php
    /*

    Available variables:
    -

    */

    ?>


            <!-- 1 Column Text : BEGIN --> 
            <tr>
                <td bgcolor="#ffffff" style="padding: 40px; text-align: center; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                    <h2><?php echo __('Welcome to <strong>Winvestify</strong>') ?>
                        <?php //echo __('¡Gracias por registrarse en <strong>Winvestify</strong>!') ?></h2>
                    <?php echo __("We are the first community for investors specialized in Crowdlending and Invoice Trading.") ?>
                    <?php // echo __('Somos la primera comunidad de inversores especializada en Crowdlending e Invoice Trading.') ?><br/>
                    <?php 
             //             echo __("Our tool let's you centralize all your investment accounts and connect to the principal platforms.");
               //           echo __("it helps you to organize and manage all your investments much more efficiently.") 
                    ?>
                    <?php // echo __('Nuestra herramienta le permite centralizar todas sus cuentas de inversión y conectar con las principales plataformas desde un único portal. 
                    //Le ayudará a organizar y gestionar todas sus inversiones de un modo más eficiente.') ?>
                                                                      <br/> 
                </td>
            </tr>
            <!-- 1 Column Text : BEGIN -->

            <!-- Thumbnail Right, Text Left : BEGIN -->
            <tr>
                <td bgcolor="#ffffff"  style="text-align: center; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                    <h2 style=" color: #13b1cd; margin: -10px 0px;" align="center"><?php echo __('First steps') ?>
                    <?php //echo __('Primeros pasos') ?></h2>
                </td>
            </tr>
            <tr>
                <td bgcolor="#ffffff" dir="rtl" align="center" valign="top" width="100%" style="padding: 10px;">
                    <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <!-- Column : BEGIN -->
                            <td width="33.33%" class="stack-column-center">
                                <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="padding: 0 10px;">
                                            <?php
                                                $options = array ("width"   => "200",
                                                                    "height" => "",
                                                                    "alt"   => "1st Step" ,
                                                                    "border" => "0",
                                                                    "align" => "center",
                                                                    "class" => "g-img img-responsive"
                                                                  );
                                                echo $this->Html->image(Router::fullbaseUrl() . DS . APP_DIR . DS . WEBROOT_DIR . '/img/emails/ion-person-stalker.png', $options);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                            <!-- Column : BEGIN -->
                            <td width="66.66%" class="stack-column-center">
                                <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; padding: 10px; text-align: left;" class="center-on-narrow">
                                            <strong style="color:#111111;"><?php echo __('1) Complete your investment profile') ?>
                                                <?php //echo __('1) Complete su perfil de inversor') ?></strong>
                                            <br><br>
                                            <?php echo __('Enter your personal data in order to connect your investment accounts to our platform') ?>
                                            <?php // echo __('Rellene sus datos personales para poder enlazar sus cuentas') ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- Thumbnail Right, Text Left : END -->

            <!-- Thumbnail Left, Text Right : BEGIN -->
            <tr>
                <td bgcolor="#ffffff" dir="ltr" align="center" valign="top" width="100%" style="padding: 10px;">
                    <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <!-- Column : BEGIN -->
                            <td width="33.33%" class="stack-column-center">
                                <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="padding: 0 10px;">
                                            <?php
                                                $options = array ("width"   => "200",
                                                                    "height" => "",
                                                                    "alt"   => "1st Step" ,
                                                                    "border" => "0",
                                                                    "align" => "center",
                                                                    "class" => "g-img img-responsive"
                                                                  );
                                                echo $this->Html->image(Router::fullbaseUrl() . DS . APP_DIR . DS . WEBROOT_DIR . '/img/emails/ion-link.png', $options);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                            <!-- Column : BEGIN -->
                            <td width="66.66%" class="stack-column-center">
                                <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: left;" class="center-on-narrow">
                                            <strong style="color:#111111;"><?php echo __('2) Link your accounts') ?>
                                            <?php //echo __('2) Enlace sus cuentas') ?></strong>
                                            <br><br>
                                            <?php echo __('Connect to the principal alternative financing plataforms') ?>
                                            <?php //echo __('Conecte con las principales plataformas de financiación participativa') ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- Thumbnail Left, Text Right : END -->


            <!-- Thumbnail Right, Text Left : BEGIN -->
            <tr>
                <td bgcolor="#ffffff" dir="rtl" align="center" valign="top" width="100%" style="padding: 10px;">
                    <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <!-- Column : BEGIN -->
                            <td width="33.33%" class="stack-column-center">
                                <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="padding: 0 10px;">
                                            <?php
                                                $options = array ("width"   => "200",
                                                                    "height" => "",
                                                                    "alt"   => "1st Step" ,
                                                                    "border" => "0",
                                                                    "align" => "center",
                                                                    "class" => "g-img img-responsive"
                                                                  );
                                                echo $this->Html->image(Router::fullbaseUrl() . DS . APP_DIR . DS . WEBROOT_DIR . '/img/emails/ion-ios-settings-strong.png', $options);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                            <!-- Column : BEGIN -->
                            <td width="66.66%" class="stack-column-center">
                                <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; padding: 10px; text-align: left;" class="center-on-narrow">
                                            <strong style="color:#111111;"><?php echo __('3) Manage your investments') ?>
                                                <?php //echo __('3) Gestione sus inversiones') ?></strong>
                                            <br><br>
                                            <?php echo __('All your investments will be automatically completely organized') ?>
                                            <?php //echo __('Sus inversiones estarán completamente organizadas automáticamente') ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- Thumbnail Right, Text Left : END -->


            <!-- Thumbnail Left, Text Right : BEGIN -->
            <tr>
                <td bgcolor="#ffffff" dir="ltr" align="center" valign="top" width="100%" style="padding: 10px;">
                    <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <!-- Column : BEGIN -->
                            <td width="33.33%" class="stack-column-center">
                                <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="padding: 0 10px;">
                                            <?php
                                                $options = array ("width"   => "200",
                                                                    "height" => "",
                                                                    "alt"   => "1st Step" ,
                                                                    "border" => "0",
                                                                    "align" => "center",
                                                                    "class" => "g-img img-responsive"
                                                                  );
                                                echo $this->Html->image(Router::fullbaseUrl() . DS . APP_DIR . DS . WEBROOT_DIR . '/img/emails/ion-world.png', $options);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                            <!-- Column : BEGIN -->
                            <td width="66.66%" class="stack-column-center">
                                <table role="presentation" aria-hidden="true" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: left;" class="center-on-narrow">
                                            <strong style="color:#111111;"><?php echo __('4) Global Market') ?></strong>
                                            <br><br>
                                            <?php echo __('Access all active investment opportunities in real time') ?>
                                            <?php //echo __('Acceda a todas las oportunidades de inversión activas en tiempo real') ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <!-- Column : END -->
                        </tr>
                    </table>

                    <!-- Button : Begin -->
                    <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" style="float:right;">
                        <tr>
                            <br/><br/>
                            <td style="margin: 10px 20px; border: 1px solid gray;"  class="button-td">
                                <a href="https://www.winvestify.com" class="button-a">
                                    <?php echo __('Go to Winvestify') ?>
                                </a>
                            </td>
                        </tr>
                    </table>
                    <!-- Button : END -->
                </td>
            </tr>
            <!-- Thumbnail Left, Text Right : END -->