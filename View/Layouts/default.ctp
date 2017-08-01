<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Winvestify</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/css/compare_styles.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="/js/local.js"></script>
<script>
    function back() {
        window.history.back();
    }
</script>
<style>
    a:link, a:visited, a:active {
        text-decoration: none;
        cursor: pointer;
    }
</style>
<?php  echo $this->Html->script('stars.js'); ?>

</head> 
<body>
    <div style="margin: 0 auto; width: 800px;">
        <div style="height: auto;">
            <h1 class="text-left" style="padding-top: 10px; padding-bottom: 10px;"><img src="/img/logo_winvestify/Logo.png" style="text-align:center; max-width:45px;"/><img src="/img/logo_winvestify/Logo_texto.png" style="text-align:center; max-width:200px;"/></h1>
        </div>
        <div style="background-color: #eeeeee; padding: 40px;">
            <?php /*<h2><?php echo $name; ?></h2>
            <p class="error">
                <strong><?php echo __d('cake', 'Error'); ?>: </strong>
            </p> 
            <h4>
                <?php printf(
                    __d('cake', 'The requested address %s was not found on this server.'),
                    "<strong>'{$url}'</strong>"
                ); ?>
            </h4>
            <?php
                if (Configure::read('debug') > 0):
                    //echo $this->element('exception_stack_trace');
                endif;
            ?>
             * 
             *              */?>
            <h3>Error 404</h3>
            <h1>
                <?php echo __('Page not found')?>
            </h1>
            <h4><?php echo __('The link may be outdated, the page may not exist or it may have been typed incorrectly')?></h4>
            <?php  echo $this->Html->script('jquery.js'); ?>
            <?php echo $this->Js->writeBuffer(array('cache'  => TRUE));                    // Write cached scripts   ?> 
        </div>
        <div style="padding: 20px;">
            <h2><?php echo __('What can you do now?')?></h2>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="text-align: center">
                    <h4><i class="fa fa-envelope"></i> <a href="<?php echo Router::fullbaseUrl();?>/Contactforms/form"><?php echo __('Contact Support')?></a></h4>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="text-align: center">
                    <h4><i class="fa fa-home"></i> <a href="<?php echo Router::fullbaseUrl();?>"><?php echo __('Go to Homepage')?></a></h4>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="text-align: center">
                    <h4><i class="fa fa-undo"></i> <a style="text-decoration: none; cursor: pointer;" onClick="back();"><?php echo __('Go Back')?></a></h4>
                </div>
            </div>
        </div>
        <div id="footer" style="text-align: center;">
            <a href="<?php echo Router::fullbaseUrl();?>"><img src="/img/logo_winvestify/Logo.png" style="max-width:30px;"/></a>
        </div>
    </div>
</body>
</html>
