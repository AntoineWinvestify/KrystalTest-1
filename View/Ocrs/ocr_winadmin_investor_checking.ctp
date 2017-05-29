<?php

/* 
 * One Click Registration - Winvestify Admin Users data checking
 * User data checking with checkboxes on every confirmed data.
 */
?>
<script src="/plugins/intlTelInput/js/intlTelInput.js"></script>
<script src="/plugins/intlTelInput/js/utils.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/intlTelInput/css/intlTelInput.css">
<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/plugins/datepicker/datepicker3.css">
<link rel="stylesheet" href="/plugins/iCheck/all.css">
<script type="text/javascript" src="/plugins/iCheck/icheck.min.js"></script>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<script>
$(document).ready(function(){
  //iCheck plugin
  $('input').iCheck({
    checkboxClass: 'icheckbox_flat-blue'
  });
  //telephone
  $('#ContentPlaceHolder_telephone').intlTelInput();

  //Date picker
  $('#ContentPlaceHolder_dateOfBirth').datepicker({
      autoclose: true,
      format: 'dd/mm/yyyy'
  });
});
</script>
<div id="OCR_WinAdminPanelA">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h4 class="header1CR"><?php echo __('Investors registered on your platform')?></h4>
                    <div class="table-responsive">  
                        <table id="usersTable" class="table table-striped dataTable display" width="100%" cellspacing="0"
                                                                        data-order='[[ 2, "asc" ]]' data-page-length='25'>
                                <thead>
                                        <tr>
                                            <th width="10%"><?php echo __('Date')?></th>
                                            <th width="10%"><?php echo __('Name')?></th>
                                            <th width="10%"><?php echo __('Surname')?></th>
                                            <th width="10%"><?php echo __('Telephone')?></th>
                                            <th><?php echo __('Email')?></th>
                                            <th width="15%"><?php echo __('Status')?></th>
                                            <th width="5%"><?php echo __('Action')?></th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#990000"><i class="fa fa-times"></i> <?php echo __('Incorrect')?></span></td>
                                        <td><button class="btn btn-default btn-invest"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning')?></span></td>
                                        <td><button class="btn btn-default btn-invest"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct')?></span></td>
                                        <td><button class="btn btn-default btn-invest"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#3399ff"><i class="fa fa-thumb-tack"></i> <?php echo __('Validating')?></span></td>
                                        <td><button class="btn btn-default btn-invest"><?php echo __('View')?></button></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo __('2017-01-01')?></td>
                                        <td><?php echo __('Nameeeeeee')?></td>
                                        <td><?php echo __('Surnameeee')?></td>
                                        <td><?php echo __('+34123456789')?></td>
                                        <td><?php echo __('example@example.com')?></td>
                                        <td><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet')?></span></td>
                                        <td><button class="btn btn-default btn-invest" href="" target="_blank"><?php echo __('View')?></button></td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>
                    <h4 class="header1CR"><?php echo __('Uploaded Documents')?></h4>
                    <div class="table-responsive">  
                        <table id="documentsTable" class="table table-striped display dataTable" width="100%" cellspacing="0"
                               data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                            <thead>
                                <tr>
                                    <th><?php echo __('Type of Document') ?></th>
                                    <th><?php echo __('Name') ?></th>
                                    <th><?php echo __('Status') ?></th>
                                    <th><?php echo __('Download') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo __('document type (IBAN, NIF...)')?></td>
                                    <td><?php echo __('Name of document')?></td>
                                    <td><span style="color:#990000"><i class="fa fa-times"></i> <?php echo __('Incorrect') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Download') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo __('document type (IBAN, NIF...)')?></td>
                                    <td><?php echo __('Name of document')?></td>
                                    <td><span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Download') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo __('document type (IBAN, NIF...)')?></td>
                                    <td><?php echo __('Name of document')?></td>
                                    <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Download') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo __('document type (IBAN, NIF...)')?></td>
                                    <td><?php echo __('Name of document')?></td>
                                    <td><span style="color:#3399ff"><i class="fa fa-thumb-tack"></i> <?php echo __('Validating') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Download') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo __('document type (IBAN, NIF...)')?></td>
                                    <td><?php echo __('Name of document')?></td>
                                    <td><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Download') ?></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <h4 class="header1CR"><?php echo __('Selected Platforms')?></h4>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    
    
    
    
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="row">
                <h4 class="header1CR"><?php echo __('Investor Data')?></h4>
                <!-- Investor complete data -->
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <!-- User data -->
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4"> <!-- Name -->
                            <div class="form-group">
                                <label for="ContentPlaceHolder_name"><?php echo __('Name')?></label> <input type="checkbox" id="checkName">
                                    <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_name',$investorValidationErrors)) {
                                                    $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue investorName". ' ' . $errorClass;
                                             echo $this->Form->input('Investor.investor_name', array(
                                                                                                    'name'		=> 'name',
                                                                                                    'id' 		=> 'ContentPlaceHolder_name',
                                                                                                    'label' 		=> false,
                                                                                                    'placeholder' 	=>  __('Name'),
                                                                                                    'class' 		=> $class,
                                                                                                    'value'			=> $resultUserData[0]['Investor']['investor_name'],						
                                                                                            ));
                                    ?>
                            </div>					
                        </div>
                        <!-- /name -->
                        
                        <!-- Surname(s) -->
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_surname"><?php echo __('Surname(s)')?></label> <input type="checkbox" id="checkSurname">
                                <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_surname',$investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue investorSurname". ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_surname', array(
                                                                                                   'name'		=> 'surname',
                                                                                                   'id' 		=> 'ContentPlaceHolder_surname',
                                                                                                   'label' 		=> false,
                                                                                                   'placeholder' 	=>  __('Surname'),
                                                                                                   'class' 		=> $class,
                                                                                                   'value'		=> $resultUserData[0]['Investor']['investor_surname'],						
                                                                                        ));
                                ?>
                            </div>		
                        </div>
                        <!-- /Surname(s) -->
                        
                        <!-- NIF -->
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_dni"><?php echo __('Id')?></label> <input type="checkbox" id="checkId">
                                    <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_DNI',$investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue investorDni". ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_DNI', array(
                                                                                               'name'			=> 'dni',
                                                                                               'id' 			=> 'ContentPlaceHolder_dni',
                                                                                               'label' 		=> false,
                                                                                               'placeholder' 	=>  __('Id'),
                                                                                               'class' 		=> $class,
                                                                                               'value'			=> $resultUserData[0]['Investor']['investor_DNI'],						
                                                                                            ));
                                    ?>
                            </div>
                        </div>
                        <!-- /NIF -->
                    </div>
                    <div class="row">
                        <!-- Date of Birth -->
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_dateOfBirth"><?php echo __('Date of Birth')?></label> <input type="checkbox" id="checkDateOfBirth">
                                    <div class="input-group input-group-sm blue date">
                                        <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_dateOfBirth',$investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control pull-right investorDateOfBirth". ' ' . $errorClass;
                                        ?>
                                        <div class="input-group-addon" style="border-radius:8px; border: none;">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" style="border-radius:8px; border:none;" class="<?php echo $class ?>" name="dateOfBirth" placeholder="<?php echo __('Date of Birth')?>" id="ContentPlaceHolder_dateOfBirth" value="<?php $resultUserData[0]['Investor']['investor_dateOfBirth'] ?>">
                                    </div>
                            </div>
                        </div>
                        <!-- /Date of Birth -->
                        
                        <!-- email -->
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_email"><?php echo __('Email')?></label> <input type="checkbox" id="checkEmail">
                                    <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_email',$investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue investorEmail". ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_email', array(
                                                                                               'name'			=> 'dni',
                                                                                               'id' 			=> 'ContentPlaceHolder_email',
                                                                                               'label' 		=> false,
                                                                                               'placeholder' 	=>  __('Email'),
                                                                                               'class' 		=> $class,
                                                                                               'value'			=> $resultUserData[0]['Investor']['investor_email'],						
                                                                                            ));
                                    ?>
                            </div>
                        </div>
                        <!-- /email -->
                        
                        <!-- Telephone -->
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_telephone"><?php echo __('Telephone')?></label> <input type="checkbox" id="checkTelephone">
                                <div class="form-control blue">
                                    <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_telephone', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "telephoneNumber center-block". ' ' . $errorClass;

                                        echo $this->Form->input('Investor.investor_telephone', array(
                                                                                                    'name'			=> 'telephone',
                                                                                                    'id' 			=> 'ContentPlaceHolder_telephone',
                                                                                                    'label' 		=> false,
                                                                                                    'placeholder' 	=>  __('Telephone'),
                                                                                                    'class' 		=> $class,
                                                                                                    'type'			=> 'tel',
                                                                                                    'value'			=> $resultUserData[0]['Investor']['investor_telephone']
                                                                                                    ));
                                        $errorClassesForTexts = "errorInputMessage ErrorPhoneNumber col-xs-offset-1";
                                        if (array_key_exists('investor_telephone',$validationResult)) {
                                            $errorClassesForTexts .= " ". "actived";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <!-- /telephone -->
                    </div>
                    <div class="row">
                        <!-- Postal code -->
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_postCode"><?php echo __('PostCode')?></label> <input type="checkbox" id="checkPostCode">
                                    <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_postCode',$investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue investorPostCode". ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_postCode', array(
                                                                                                    'name'		=> 'investor_postCode',
                                                                                                    'id' 		=> 'ContentPlaceHolder_postCode',
                                                                                                    'label' 		=> false,
                                                                                                    'placeholder' 	=>  __('PostCode'),
                                                                                                    'class' 		=> $class,
                                                                                                    'value'		=> $resultUserData[0]['Investor']['investor_postCode'],						
                                                                                            ));
                                    ?>
                            </div>
                        </div>
                        <!-- /postal code -->
                        <!-- Address -->
                        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_address1"><?php echo __('Address')?></label> <input type="checkbox" id="checkAdress">
                                    <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_address1',$investorValidationErrors)) {
                                                    $errorClass = "redBorder";
                                            }
                                            $class = "form-control blue investorSurname". ' ' . $errorClass;
                                            echo $this->Form->input('Investor.investor_address1', array(
                                                                                                       'name'		=> 'address1',
                                                                                                       'id' 		=> 'ContentPlaceHolder_address1',
                                                                                                       'label' 		=> false,
                                                                                                       'placeholder' 	=>  __('Address'),
                                                                                                       'class' 		=> $class,
                                                                                                       'value'		=> $resultUserData[0]['Investor']['investor_address1'],						
                                                                                            ));
                                    ?>
                            </div>
                        </div>
                        <!-- /Address -->
                    </div>
                    <div class="row">
                     
                        <!-- city -->
                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="exampleInputPassword1"><?php echo __('City')?></label> <input type="checkbox" id="checkCity">
                                    <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_city',$investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue investorCity". ' ' . $errorClass;
                                        echo $this->Form->input('ContentPlaceHolder_city', array(
                                                                                                'name'		=> 'city',
                                                                                                'id' 		=> 'ContentPlaceHolder_city',
                                                                                                'label' 	=> false,
                                                                                                'placeholder' 	=>  __('City'),
                                                                                                'class' 	=> $class,
                                                                                                'value'		=> $resultUserData[0]['Investor']['investor_city'],						
                                                                                            ));
                                            ?>
                            </div>	
                        </div>
                        <!-- /city -->
                        
                        <!-- Country -->
                        <div class="col-xs-12 col-sm-4 col-md-8 col-lg-8">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_country"><?php echo __('Country')?></label> <input type="checkbox" id="checkCountry">
                                <?php
                                    $errorClass = "";
                                    if (array_key_exists('investor_country',$investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                    }
                                    $class = "form-control blue investorCountry". ' ' . $errorClass;	
                                    echo $this->Form->input('Investor.investor_country', array(
                                            'name'			=> 'country',
                                            'id' 			=> 'ContentPlaceHolder_country',
                                            'label' 		=> false,
                                            'options'               => $countryData,
                                            'placeholder' 	=>  __('Country'),
                                            'class' 		=> $class,
                                            'value'			=> $resultUserData[0]['Investor']['investor_country'],						
                                    ));
                                ?>
                            </div>	
                        </div>
                        <!-- /country -->
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_iban"><?php echo __('IBAN')?></label> <input type="checkbox" id="checkIBAN">
                                <input type="text" class="form-control blue">
                            </div>
                        </div><!-- /Cif + Business Name -->
                    </div>
                <!-- /User data -->
                </div>
                <!-- /Investor complete data -->
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row">
                        <!-- CIF -->
                        <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_cif"><?php echo __('CIF')?></label> <input type="checkbox" id="checkCIF">
                                <input type="text" class="form-control blue">
                            </div>
                        </div>
                        <!-- /CIF -->

                        <!-- Business Name -->
                        <div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_businessName"><?php echo __('Business Name')?></label> <input type="checkbox" id="checkBusinessName">
                                <input type="text" class="form-control blue">
                            </div>
                        </div>
                        <!-- /Business Name -->
                        <!-- /Business Data -->
                    </div>
                </div>
            </div>
            <div class="row">
                <h4 class="header1CR"><?php echo __('Uploaded Documents')?></h4>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    
                </div>
            </div>
            <div class="row">
                <h4 class="header1CR"><?php echo __("Investor's selected platforms")?></h4>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                <button type="button" class="btn btn-default btn-lg btn-win1 center-block" style="padding: 10px 50px; margin-bottom: 25px"><?php echo __('Approve')?></button>
        </div>
    </div>
</div>

