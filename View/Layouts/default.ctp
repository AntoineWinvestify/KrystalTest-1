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
    <div style="background-color: black; height: auto;">
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
    </div>
</body>
</html>
