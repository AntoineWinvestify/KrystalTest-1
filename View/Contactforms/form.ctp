
<!--========== PAGE CONTENT ==========-->
<!-- Feedback Form -->
<style>
    .s-form-v2__input {
        text-transform: none;
    }
</style>
<div class="container">
    <div class="g-text-center--xs">
        <p class="text-uppercase g-font-size-32--xs g-font-weight--700 g-color--primary g-letter-spacing--2 g-margin-b-25--xs"><?php echo __('Do you need help?')?></p>
    </div>
    <?php
    echo $this->Form->create('ContactForm', array('default' => false));
    ?>
    <form>
        <div class="row">
            <div id="reporting"></div>
            <div class="col-sm-8 col-sm-offset-2 g-margin-b-20--xs g-margin-b-0--md">
                <h4 align="left"><?php echo __('If you have any questions or are missing something, please contact our customer service')?></h4>
                <div id="overlay">
                    <div id="spinner">  
                    </div>
                </div>
                <br/>
                <label><?php echo __('Name') ?>:</label>
                <div class="g-margin-b-20--xs">
                    <?php
                    echo $this->Form->input('name', array('type' => 'text', 
                                                        'class' => "username form-control blue_noborder4 s-form-v2__input", 
                                                        'placeholder' => __("Your Name"), 
                                                        'label' => false));
                    ?>
                </div>
                <div class="errorName redBorderText" style="display: none;">
                    <i class="ti-info-alt"></i>
                    <?php echo __('Name not valid') ?>
                </div>
                <label><?php echo __('Email') ?>:</label>
                <div class="g-margin-b-20--xs">
                    <?php
                    echo $this->Form->input('email', array('type' => 'email', 
                                                            'class' => "useremail form-control blue_noborder4 s-form-v2__input", 
                                                            'placeholder' => __("Your Email"), 
                                                            'label' => false));
                    ?>
                </div>
                <div class="errorEmail redBorderText" style="display: none;">
                    <i class="ti-info-alt"></i>
                    <?php echo __('Email not valid') ?>
                </div>
                <label><?php echo __('Subject') ?>:</label>
                <div class="g-margin-b-20--xs">
                    <?php
                    echo $this->Form->input('Subject', array(
                                                'label' => false,
                                                'options' => $subjectContactForm,
                                                'class' => 'blue_noborder4 form-control s-form-v2__input'
                    ));
                    ?>
                </div>
                <div class="errorSubject redBorderText" style="display: none;">
                    <i class="ti-info-alt"></i>
                    <?php echo __('Subject not selected') ?>
                </div>
                <label><?php echo __('Message') ?>:</label>
                <?php
                echo $this->Form->textarea('text', array(
                    'class' => "usermsg blue_noborder4 form-control s-form-v2__input", 
                    'rows' => '8', 
                    'placeholder' => __("Your message"), 
                    'label' => false, 
                    'name' => 'contactFormMessage'));
                ?>
                <div class="errorMessage redBorderText" style="display: none; margin-top: 0px !important;">
                    <i class="ti-info-alt"></i>
                    <?php echo __('Message too short') ?>
                </div>
                <div class="row">
                    <?php
                    echo $this->Form->hidden('result', array('value' => $captcha_result));
                    ?>
                    <div class='col-xs-12 col-sm-4 col-md-4 col-lg-4'>
                       <label style='font-size:16px; margin-top: 10px;'><?php echo __('Calculate this: ') . $captcha ?></label>
                       <label style='text-align: center'><small><?php echo __('Enter the result in numbers') ?></small></label>
                    </div>
                    <div class='col-xs-12 col-sm-8 col-md-8 col-lg-8' style="margin-top: 10px;">
                        <?php 
                            echo $this->Form->input('captcha', array('label' => false, 
                                                                    'class' => 'blue_noborder4 form-control s-form-v2__input'));
                        ?>
                    </div>
                </div>
                <div class="errorCaptcha redBorderText" style="display: none; margin-top: 2px;">
                    <i class="ti-info-alt"></i>
                    <?php echo __('Invalid result') ?>
                </div>
            </div>
        </div>
        <div class="g-text-center--xs">
            <?php
            echo $this->Form->button(__('Send'), array('id' => 'send', 
                                                    'class' => 'text-uppercase s-btn s-btn--md s-btn--primary-bg g-radius--50 g-padding-x-80--xs submitButton', 
                                                    'href' => '../Contactforms/ContactFormSend', 
                                                    'style' => 'margin-top:15px;'));
            echo $this->Form->end();
            ?>
        </div>
        <br/>
</div>
<!-- End Feedback Form -->
</div>
<!-- End Terms & Conditions -->







<script src="/modals/assets/js/jquery-2.2.4.min.js" type="text/javascript"></script>
<script>
    

    $(document).ready(function () {
        $("#send").click(function (event) {
            event.preventDefault();
            contactForm();
        });
        $(document).bind('DOMSubtreeModified',function(){
//            fadeOutElement("#reporting", 5000);
        });
    });
</script>
<!--========== END PAGE CONTENT ==========-->
