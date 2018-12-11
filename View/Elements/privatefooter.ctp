<?php
/**
* 
* Footer for internal portal
*
*
* @author Antoine de Poorter
* @version 0.1
* @date 2016-12-13
* @package
*


2016-12-13		version 0.1



Pending:
-


*/
?>



<footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b><?php echo __('Version') ?></b>
      <?php echo $runTimeParameters['runtimeconfiguration_softwareVersion'] ?>
    </div>
	<strong>Copyright &copy; 
            <?php 
                $actualDate = getDate(); 
                echo $actualDate['year']; echo "&nbsp;" . __("Winvestify") . ". ";
                echo "</strong>";
                echo "All rights reserved";
            ?>
    
</footer>
