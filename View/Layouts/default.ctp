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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="/js/local.js"></script>
<?php  echo $this->Html->script('stars.js'); ?>

</head> 
<body>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3" style="margin-top: 20px;">
            <img src="/img/logo_winvestify/Logo.png" height="50"/>
            <img src="/img/logo_winvestify/Logo_texto.png" height="50"/>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3" id="errorBox">
            <h1><?php echo __('Error:')?> <strong><?php echo __('Page Not Found')?></strong></h1>
            <h3><?php echo __('The page you were looking for appears to have been moved, deleted or does not exist.')?></h3>
            <?php if (Configure::read('debug') > 0) { ?>
                <div style="margin-left: 5%;">
                <h2><?php echo $name; ?></h2>
                <p class="error">
                    <strong><?php echo __d('cake', 'Error'); ?>: </strong>
                    <?php printf(
                        __d('cake', 'The requested address %s was not found on this server.'),
                        "<strong>'{$url}'</strong>"
                    ); ?>
                </p>
                <?php  echo $this->Html->script('jquery.js'); ?>
                <?php echo $this->Js->writeBuffer(array('cache'  => TRUE));                    // Write cached scripts   ?> 
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h2><?php echo __('What can you do now?')?></h2>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <h3 align="center"><i class="fa fa-home"></i> <a href=""><?php echo __('Go Home')?></a></h3>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <h3 align="center"><i class="fa fa-question-circle"></i> <a href="/Contactforms/form"><?php echo __('Contact Winvestify')?></a></h3>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <h3 align="center"><i class="fa fa-undo"></i> <a onClick="back();"><?php echo __('Go back')?></a></h3>
                </div>
            </div>
        </div>
    </div>
    <?php /*<div style="background-color: black; height: auto;">
        <h1 class="text-center" style="padding-top: 10px; padding-bottom: 10px;"><img src="/img/logo_winvestify/Logo.png" style="float:center; max-width:100px;"/><img src="/img/logo_winvestify/Logo_texto.png" style="float:center; max-width:350px;"/></h1>
    </div>
    <div style="margin-left: 5%;">
        <h2><?php echo $name; ?></h2>
        <p class="error">
            <strong><?php echo __d('cake', 'Error'); ?>: </strong>
            <?php printf(
                __d('cake', 'The requested address %s was not found on this server.'),
                "<strong>'{$url}'</strong>"
            ); ?>
        </p>
        <?php
            if (Configure::read('debug') > 0):
                //echo $this->element('exception_stack_trace');
            endif;
        ?>
        <?php  echo $this->Html->script('jquery.js'); ?>
        <?php echo $this->Js->writeBuffer(array('cache'  => TRUE));                    // Write cached scripts   ?> 
    </div>*/ ?>
</body>
</html>
