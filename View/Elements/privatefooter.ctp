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
<?php
	$actualDate = getDate();
         $version = Configure::read('winvestify');
?>


<footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> <?php echo $version['version'] ?>
    </div>
	<strong>Copyright &copy; <?php echo $actualDate['year']; echo "&nbsp;" . __("Winvestify") . ". ";
	echo "</strong>";
	echo "All rights reserved";
	?>
    
</footer>
