<?php
/**
 *
 *
 * Screen which is part of the registration phase
 *
 * @author Antoine de Poorter
 * @version 0.2 
 * @date 2017-03-06
 * @package



  2016-10-25		version 0.1
  initial test version


  2017-03-06		version 0.2
  Javascript was moved to register_panel_a.ctp
  modal has been updated


  2017-03-08      version 0.3
  modal updated with new css & classes
  ajax spinner

  2017-04-01      version 0.4
  Print mobile phone number to which the confirmation code was sent is shown in modal         [OK, tested]
  Hide "Request New Code" if number of requests has exceeded a limit                          [OK, tested]

 */
?>


<?php
if ($error) {
    echo "0";
} else {
    echo "1";
}
?>

<script type="text/javascript">
// Fade in button to request a new confirmation code via SMS in requestPanelB.ctp
fadeInElement("#fadeBtn", 5000);
</script>
<?php
echo $this->Form->input('', array('name' => 'username',
    'value' => $username,
    'id' => 'username',
    'type' => 'hidden'
));


echo $this->Form->input('', array('name' => 'telephone',
    'value' => $telephone,
    'id' => 'telephone',
    'type' => 'hidden'
));
?>
<div id="registerModal" class="modal show" role="dialog">
    <!--   Big container   -->
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="green" id="wizardProfile">
                        <div class="overlay">
                            <div class="fa fa-spin fa-spinner" style="color:green">	
                            </div>
                        </div>
                        <?php echo $this->element("progresswizard", array("progressIndicatorStep" => REGISTRATION_PROGRESS_2)); ?>
                        <div class="tab-content" style="padding-top: 15px;">
                            <?php echo $this->Form->create('User', array('url' => "login",)); ?>
                            <form class="form">	
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <div class="pull-left">
                                            <?php echo __("A confirmation code has been sent to") . " " . $telephone; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-offset-1 col-sm-10">
                                        <div class="form-group">
                                            <label><?php echo __("Enter Code"); ?></label>
                                                <?php
                                                    if (!empty($errorMsg)) {
                                                        $errorClass = "redBorder";
                                                    }
                                                    $class = "center-block blue_noborder4 form-control confirmationCode" . " " . $errorClass;
                                                    echo $this->Form->input('Confirmationcode', array("label" => false,
                                                        "placeholder" => "1234567",
                                                        "class" => $class,
                                                    ));

                                                    $errorClassesText = "errorInputMessage ErrorConfirmationCode col-xs-offset-1";
                                                    if (!empty($errorMsg)) {
                                                        $errorClassesText .= " " . "actived";
                                                    }
                                                ?>
                                            <div class="<?php echo $errorClassesText ?>">
                                                <i class="fa fa-exclamation-circle"></i>
                                                <span id="ContentPlaceHolder_ErrorConfirmationCode" class="errorMessage"><?php echo $errorMsg ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if ($newRequestsAllowed == true) {
                                        ?>
                                    <div class="col-sm-offset-1 col-sm-5 col-xs-12" style="display:none; position: absolute; bottom: 22px; left: 20px;" id="fadeBtn">
                                        <div class="form-group pull-left">
                                            <label align="center"><?php echo __("You did not receive a code?"); ?></label>
                                            <br/>
                                            <?php
                                                echo $this->Form->button(__('Request New Code'), $options = array('name' => 'btnRequestNewCode',
                                                    'id' => 'btnRequestNewCode',
                                                    'href' => '/users/registerPanelB',
                                                    'class' => 'btn'));
                                                echo $this->Form->end();
                                            ?>
                                        </div>
                                    </div>
    <?php
}
?>
                                    <div class="col-sm-offset-6 col-sm-5 col-xs-12" id="activateBtn" style="position: absolute; bottom: 22px; right: 60px">
                                        <div class="form-group pull-right">
                                            <?php
                                                echo $this->Form->button(__('ACTIVATE'), $options = array('name' => 'btnRegisterUser',
                                                    'id' => 'btnReturnSMSCode',
                                                    'href' => '/users/registerPanelB',
                                                    'class' => 'btn center-block'));
                                            ?>
                                        </div>
                                    </div> 
                                </div>

                            </form>
                        </div> <!-- /tab-content -->
                    </div>  <!-- /wizard-card -->
                </div> <!-- /wizard-container -->
            </div> 
        </div>
    </div>
</div><!-- /modal -->