<?php
/**
 *
 *
 * Simple login screen
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-01-30
 * @package

 
 
2016-01-30		version 0.1
multi-language support added
 
 this is not actually being used. The login as defined in the main landing page is used
 
 */





echo $this->Form->create('User');	
?> 


    <div >
        <?php 
            echo $this->Form->input('username');
            echo $this->Form->input('password');
        ?>
            <button type="submit">
                    <?php echo __('LOGIN') ?>
            </button>
    </div>
<?php
echo $this->Form->end();
?>               
    









