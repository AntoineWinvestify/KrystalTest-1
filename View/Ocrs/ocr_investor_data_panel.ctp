<?php
/*
 * One Click Registration - Investor Data Panel
 * Investor data panel to collect all data to register on platforms
 * 
 * [2017-05-23] Completed view
 *              [pending] data saving
 */
?>

<link rel="stylesheet" type="text/css" href="/plugins/intlTelInput/css/intlTelInput.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="/plugins/datepicker/datepicker3.css">
<script src="/plugins/intlTelInput/js/utils.js"></script>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="/plugins/intlTelInput/js/intlTelInput.js"></script>


<script>
    $(function () {
        //telephone
        $('#ContentPlaceHolder_telephone').intlTelInput();

        //Date picker
        $('#ContentPlaceHolder_dateOfBirth').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        });

        $(document).on("change", "#vehicle", function () {
            if ($(this).is(":checked")) {
                $("#investmentVehicle").show();
            } else {
                $("#investmentVehicle").hide();
            }
        });



        $("#activateOCR").click(function () {
            var params = {
                investor_name: $("#ContentPlaceHolder_name").val(),
                investor_surname: $("#ContentPlaceHolder_surname").val(),
                investor_DNI: $("#dni").val(),
                investor_dateOfBirth: $("#ContentPlaceHolder_dateOfBirth").val(),
                investor_telephone: $("#ContentPlaceHolder_telephone").intlTelInput("getNumber"),
                investor_address1: $("#ContentPlaceHolder_address1").val(),
                investor_postCode: $("#ContentPlaceHolder_postCode").val(),
                investor_city: $("#ContentPlaceHolder_city").val(),
                investor_country: $("#ContentPlaceHolder_country").val()
            };

            if ($("#vehicle").is(':checked')) {
                params.vehicle = 1;
                params.cif = $("#CIF").val();
                params.businessName = $("#BusinessName").val();
                params.iban = $("#Iban").val();

            } else {
                params.vehicle = 0;
                params.iban = $("#Iban").val();
            }
            link = $("#activateOCR").attr('href');
            var data = jQuery.param(params);
            getServerData(link, data, success, error);


        });

<?php if ($ocr[0]['Ocr']['Ocr_vehicle']) { ?>
            if (<?php echo $ocr[0]['Ocr']['Ocr_vehicle'] ?> == 1) {
                $("#vehicle").prop('checked', true);
                $("#investmentVehicle").show();
            }
<?php } ?>


    });


    function error() {

    }

    function success() {

    }

</script>
<?php /*<div id="OCR_InvestorPanelA">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <p align="justify"><?php
                        echo __('One Click Registration Le permite registrarse con un solo click en cualquier plataforma'
                                . ' que Winvestify tenga habilitada. Para ello, cumpliendo con la Ley 10/2012, del 28 de Abril, de prevención del'
                                . ' blanqueo de capitales y de Financiación del Terrorismo deberá aportar la siguiente documentación para que las'
                                . ' PFP puedan validar y autenticar su identidad.')
                        ?></p>
                </div>
            </div>
            <div class="row">
                <!-- Investor complete data -->
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h4 class="header1CR"><?php echo __('Personal Data:') ?></h4>
                    <!-- User data -->
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4"> <!-- Name -->
                            <div class="form-group">
                                <label for="ContentPlaceHolder_name"><?php echo __('Name') ?></label>
                                <?php
                                echo $this->Form->create('OCR', array('default' => false));
                                $errorClass = "";
                                if (array_key_exists('investor_name', $investorValidationErrors)) {
                                    $errorClass = "redBorder";
                                }
                                $class = "form-control blue investorName" . ' ' . $errorClass;
                                echo $this->Form->input('Investor.investor_name', array(
                                    'name' => 'name',
                                    'id' => 'ContentPlaceHolder_name',
                                    'label' => false,
                                    'placeholder' => __('Name'),
                                    'class' => $class,
                                    'value' => $investor[0]['Investor']['investor_name'],
                                ));
                                ?>									
                            </div>					
                        </div>
                        <!-- /name -->

                        <!-- Surname(s) -->
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_surname"><?php echo __('Surname(s)') ?></label>
                                <?php
                                $errorClass = "";
                                if (array_key_exists('investor_surname', $investorValidationErrors)) {
                                    $errorClass = "redBorder";
                                }
                                $class = "form-control blue investorSurname" . ' ' . $errorClass;
                                echo $this->Form->input('Investor.investor_surname', array(
                                    'name' => 'surname',
                                    'id' => 'ContentPlaceHolder_surname',
                                    'label' => false,
                                    'placeholder' => __('Surname'),
                                    'class' => $class,
                                    'value' => $investor[0]['Investor']['investor_surname'],
                                ));
                                ?>
                            </div>		
                        </div>
                        <!-- /Surname(s) -->

                        <!-- NIF -->
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="dni"><?php echo __('Id') ?></label>
                                <?php
                                $errorClass = "";
                                if (array_key_exists('investor_DNI', $investorValidationErrors)) {
                                    $errorClass = "redBorder";
                                }
                                $class = "form-control blue investorDni" . ' ' . $errorClass;
                                echo $this->Form->input('Investor.investor_DNI', array(
                                    'name' => 'dni',
                                    'id' => 'dni',
                                    'label' => false,
                                    'placeholder' => __('Id'),
                                    'class' => $class,
                                    'value' => $investor[0]['Investor']['investor_DNI'],
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
                                <label for="ContentPlaceHolder_dateOfBirth"><?php echo __('Date of Birth') ?></label>
                                <div class="input-group input-group-sm blue date">
                                    <?php
                                    $errorClass = "";
                                    if (array_key_exists('investor_dateOfBirth', $investorValidationErrors)) {
                                        $errorClass = "redBorder";
                                    }
                                    $class = "form-control pull-right investorDateOfBirth" . ' ' . $errorClass;
                                    ?>
                                    <div class="input-group-addon" style="border-radius:8px; border: none;">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" style="border-radius:8px; border:none;" class="<?php echo $class ?>" name="dateOfBirth" placeholder="<?php echo __('Date of Birth') ?>" id="ContentPlaceHolder_dateOfBirth" value="<?php echo $investor[0]['Investor']['investor_dateOfBirth']; ?>">
                                </div>
                            </div>
                        </div>
                        <!-- /Date of Birth -->

                        <!-- email -->
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_email"><?php echo __('Email') ?></label>
                                <?php
                                $errorClass = "";
                                if (array_key_exists('investor_email', $investorValidationErrors)) {
                                    $errorClass = "redBorder";
                                }
                                $class = "form-control blue investorEmail" . ' ' . $errorClass;
                                echo $this->Form->input('Investor.investor_email', array(
                                    'name' => 'dni',
                                    'id' => 'ContentPlaceHolder_email',
                                    'label' => false,
                                    'placeholder' => __('Email'),
                                    'class' => $class,
                                    'value' => $investor[0]['Investor']['investor_email'],
                                ));
                                ?>
                            </div>
                        </div>
                        <!-- /email -->

                        <!-- Telephone -->
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_telephone"><?php echo __('Telephone') ?></label>
                                <div class="form-control blue">
                                    <?php
                                    $errorClass = "";
                                    if (array_key_exists('investor_telephone', $investorValidationErrors)) {
                                        $errorClass = "redBorder";
                                    }
                                    $class = "telephoneNumber center-block" . ' ' . $errorClass;

                                    echo $this->Form->input('Investor.investor_telephone', array(
                                        'name' => 'telephone',
                                        'id' => 'ContentPlaceHolder_telephone',
                                        'label' => false,
                                        'placeholder' => __('Telephone'),
                                        'class' => $class,
                                        'type' => 'tel',
                                        'value' => $investor[0]['Investor']['investor_telephone']
                                    ));
                                    $errorClassesForTexts = "errorInputMessage ErrorPhoneNumber col-xs-offset-1";
                                    if (array_key_exists('investor_telephone', $validationResult)) {
                                        $errorClassesForTexts .= " " . "actived";
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
                                <label for="ContentPlaceHolder_postCode"><?php echo __('PostCode') ?></label>
                                <?php
                                $errorClass = "";
                                if (array_key_exists('investor_postCode', $investorValidationErrors)) {
                                    $errorClass = "redBorder";
                                }
                                $class = "form-control blue investorPostCode" . ' ' . $errorClass;
                                echo $this->Form->input('Investor.investor_postCode', array(
                                    'name' => 'investor_postCode',
                                    'id' => 'ContentPlaceHolder_postCode',
                                    'label' => false,
                                    'placeholder' => __('PostCode'),
                                    'class' => $class,
                                    'value' => $investor[0]['Investor']['investor_postCode'],
                                ));
                                ?>
                            </div>
                        </div>
                        <!-- /postal code -->
                        <!-- Address -->
                        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_address1"><?php echo __('Address') ?></label>
                                <?php
                                $errorClass = "";
                                if (array_key_exists('investor_address1', $investorValidationErrors)) {
                                    $errorClass = "redBorder";
                                }
                                $class = "form-control blue investorSurname" . ' ' . $errorClass;
                                echo $this->Form->input('Investor.investor_address1', array(
                                    'name' => 'address1',
                                    'id' => 'ContentPlaceHolder_address1',
                                    'label' => false,
                                    'placeholder' => __('Address'),
                                    'class' => $class,
                                    'value' => $investor[0]['Investor']['investor_address1'],
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
                                <label for="exampleInputPassword1"><?php echo __('City') ?></label>
                                <?php
                                $errorClass = "";
                                if (array_key_exists('investor_city', $investorValidationErrors)) {
                                    $errorClass = "redBorder";
                                }
                                $class = "form-control blue investorCity" . ' ' . $errorClass;
                                echo $this->Form->input('ContentPlaceHolder_city', array(
                                    'name' => 'city',
                                    'id' => 'ContentPlaceHolder_city',
                                    'label' => false,
                                    'placeholder' => __('City'),
                                    'class' => $class,
                                    'value' => $investor[0]['Investor']['investor_city'],
                                ));
                                ?>
                            </div>	
                        </div>
                        <!-- /city -->

                        <!-- Country -->
                        <div class="col-xs-12 col-sm-4 col-md-8 col-lg-8">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_country"><?php echo __('Country') ?></label>
                                <?php
                                $errorClass = "";
                                if (array_key_exists('investor_country', $investorValidationErrors)) {
                                    $errorClass = "redBorder";
                                }
                                $class = "form-control blue investorCountry" . ' ' . $errorClass;
                                echo $this->Form->input('Investor.investor_country', array(
                                    'name' => 'country',
                                    'id' => 'ContentPlaceHolder_country',
                                    'label' => false,
                                    'options' => $countryData,
                                    'placeholder' => __('Country'),
                                    'class' => $class,
                                    'value' => $investor[0]['Investor']['investor_country'],
                                ));
                                ?>
                            </div>	
                        </div>
                        <!-- /country -->
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_iban"><?php echo __('IBAN') ?></label>
                                <input id="Iban" type="text" class="form-control blue" value = <?php echo $ocr[0]['Ocr']['Investor_iban'] ?>>
                            </div>
                        </div><!-- /Cif + Business Name -->
                    </div>
                    <!-- /User data -->
                </div>
                <!-- /Investor complete data -->
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <!-- Checkbox -->
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="checkbox">
                                <label>
                                    <input id="vehicle" type="checkbox"> <?php echo __('I use my company as investment vehicle') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- /checkbox -->
                    <div class="row" id="investmentVehicle">
                        <!-- CIF -->
                        <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_cif"><?php echo __('CIF') ?></label>
                                <input id="CIF" type="text" class="form-control blue" value = "<?php echo $ocr[0]['Ocr']['Investor_cif'] ?>">
                            </div>
                        </div>
                        <!-- /CIF -->

                        <!-- Business Name -->
                        <div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="ContentPlaceHolder_businessName"><?php echo __('Business Name') ?></label>
                                <input id="BusinessName" type="text" class="form-control blue" value="<?php echo $ocr[0]['Ocr']['Investor_businessName'] ?>">
                            </div>
                        </div>
                        <!-- /Business Name -->
                        <!-- /Business Data -->
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Uploading Documents -->
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h4 class="header1CR"><?php echo __('Document Uploading:') ?></h4>
                    <p><?php
                        echo __('párrafo sobre la subida de documentos en el que se va a explicar para qué son los archivos '
                                . 'que se están pidiendo en este apartado (sería buena idea colocar un tooltip en cada uno de ellos, por separado, que pueda'
                                . 'hacer referencia a la Ley de Protección de datos que se asocie a dicho elemento)')
                        ?></p>
                    <div class="table-responsive">  
                        <table id="documentsTable" class="table table-striped display dataTable" width="100%" cellspacing="0"
                               data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                            <thead>
                                <tr>
                                    <th><?php echo __('Date') ?></th>
                                    <th><?php echo __('Name') ?></th>
                                    <th><?php echo __('Status') ?></th>
                                    <th><?php echo __('Upload') ?></th>
                                    <th><?php echo __('Delete') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>01-01-2017</td>
                                    <td>NIF_Front</td>
                                    <td><span style="color:#990000"><i class="fa fa-times"></i> <?php echo __('Incorrect') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>01-01-2017</td>
                                    <td>NIF_Back</td>
                                    <td><span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>01-01-2017</td>
                                    <td>IBAN</td>
                                    <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>01-01-2017</td>
                                    <td>Another one</td>
                                    <td><span style="color:#3399ff"><i class="fa fa-thumb-tack"></i> <?php echo __('Validating') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>01-01-2017</td>
                                    <td>Another one</td>
                                    <td><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet') ?></span></td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-default" style="background-color:#990000; color:white;" disabled><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Button Next -->
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                <!-- Activate Button -->
                <hr width="100%" style="margin:10px;">
                <button type="button" href="../Ocrs/oneClickInvestorII" id="activateOCR" class="btn btn-primary btn-lg btn-win5"><?php echo __('Activate 1CR') ?></button>
                <?php echo $this->Form->end(); ?>
                <!-- /activate button -->
            </div>
        </div> <!-- /.col 9 -->
    </div> <!-- /.row general -->
</div>
<?php 
echo $this->Form->create('Files',array('action' => '../Ocrs/upload', 'type' => 'file'));
echo $this->Form->file('nif');
echo $this->Form->file('iban');
echo $this->Form->submit(__('Upload', true));

?>
*/?>


<div id="OCR_InvestorPanelA">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title"><strong><?php echo __('One Click Registration')?></strong></h4>
                    <p class="category"><?php echo __('Investor One Click Registration Data')?></p>
                </div>
                <div class="card-content table-responsive">
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <p><?php
                                echo __('One Click Registration Le permite registrarse con un solo click en cualquier plataforma'
                                        . ' que Winvestify tenga habilitada. Para ello, cumpliendo con la Ley 10/2012, del 28 de Abril, de prevención del'
                                        . ' blanqueo de capitales y de Financiación del Terrorismo deberá aportar la siguiente documentación para que las'
                                        . ' PFP puedan validar y autenticar su identidad.')
                                ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Investor complete data -->
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- User data -->
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4"> <!-- Name -->
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_name"><?php echo __('Name') ?></label>
                                        <?php
                                        echo $this->Form->create('OCR', array('default' => false));
                                        $errorClass = "";
                                        if (array_key_exists('investor_name', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder2 investorName" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_name', array(
                                            'name' => 'name',
                                            'id' => 'ContentPlaceHolder_name',
                                            'label' => false,
                                            'placeholder' => __('Name'),
                                            'class' => $class,
                                            'value' => $investor[0]['Investor']['investor_name'],
                                        ));
                                        ?>									
                                    </div>					
                                </div>
                                <!-- /name -->

                                <!-- Surname(s) -->
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_surname"><?php echo __('Surname(s)') ?></label>
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_surname', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder2 investorSurname" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_surname', array(
                                            'name' => 'surname',
                                            'id' => 'ContentPlaceHolder_surname',
                                            'label' => false,
                                            'placeholder' => __('Surname'),
                                            'class' => $class,
                                            'value' => $investor[0]['Investor']['investor_surname'],
                                        ));
                                        ?>
                                    </div>		
                                </div>
                                <!-- /Surname(s) -->

                                <!-- NIF -->
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="dni"><?php echo __('Id') ?></label>
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_DNI', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder2 investorDni" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_DNI', array(
                                            'name' => 'dni',
                                            'id' => 'dni',
                                            'label' => false,
                                            'placeholder' => __('Id'),
                                            'class' => $class,
                                            'value' => $investor[0]['Investor']['investor_DNI'],
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
                                        <label for="ContentPlaceHolder_dateOfBirth"><?php echo __('Date of Birth') ?></label>
                                        <div class="input-group input-group-sm blue_noborder2 date">
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_dateOfBirth', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "form-control pull-right investorDateOfBirth" . ' ' . $errorClass;
                                            ?>
                                            <div class="input-group-addon" style="border-radius:8px; border: none;">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" style="border-radius:8px; border:none;" class="<?php echo $class ?>" name="dateOfBirth" placeholder="<?php echo __('Date of Birth') ?>" id="ContentPlaceHolder_dateOfBirth" value="<?php echo $investor[0]['Investor']['investor_dateOfBirth']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <!-- /Date of Birth -->

                                <!-- email -->
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_email"><?php echo __('Email') ?></label>
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_email', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder2 investorEmail" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_email', array(
                                            'name' => 'dni',
                                            'id' => 'ContentPlaceHolder_email',
                                            'label' => false,
                                            'placeholder' => __('Email'),
                                            'class' => $class,
                                            'value' => $investor[0]['Investor']['investor_email'],
                                        ));
                                        ?>
                                    </div>
                                </div>
                                <!-- /email -->

                                <!-- Telephone -->
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_telephone"><?php echo __('Telephone') ?></label>
                                        <div class="form-control blue_noborder2">
                                            <?php
                                            $errorClass = "";
                                            if (array_key_exists('investor_telephone', $investorValidationErrors)) {
                                                $errorClass = "redBorder";
                                            }
                                            $class = "telephoneNumber center-block" . ' ' . $errorClass;

                                            echo $this->Form->input('Investor.investor_telephone', array(
                                                'name' => 'telephone',
                                                'id' => 'ContentPlaceHolder_telephone',
                                                'label' => false,
                                                'placeholder' => __('Telephone'),
                                                'class' => $class,
                                                'type' => 'tel',
                                                'value' => $investor[0]['Investor']['investor_telephone']
                                            ));
                                            $errorClassesForTexts = "errorInputMessage ErrorPhoneNumber col-xs-offset-1";
                                            if (array_key_exists('investor_telephone', $validationResult)) {
                                                $errorClassesForTexts .= " " . "actived";
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
                                        <label for="ContentPlaceHolder_postCode"><?php echo __('PostCode') ?></label>
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_postCode', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder2 investorPostCode" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_postCode', array(
                                            'name' => 'investor_postCode',
                                            'id' => 'ContentPlaceHolder_postCode',
                                            'label' => false,
                                            'placeholder' => __('PostCode'),
                                            'class' => $class,
                                            'value' => $investor[0]['Investor']['investor_postCode'],
                                        ));
                                        ?>
                                    </div>
                                </div>
                                <!-- /postal code -->
                                <!-- Address -->
                                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_address1"><?php echo __('Address') ?></label>
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_address1', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder2 investorSurname" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_address1', array(
                                            'name' => 'address1',
                                            'id' => 'ContentPlaceHolder_address1',
                                            'label' => false,
                                            'placeholder' => __('Address'),
                                            'class' => $class,
                                            'value' => $investor[0]['Investor']['investor_address1'],
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
                                        <label for="exampleInputPassword1"><?php echo __('City') ?></label>
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_city', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder2 investorCity" . ' ' . $errorClass;
                                        echo $this->Form->input('ContentPlaceHolder_city', array(
                                            'name' => 'city',
                                            'id' => 'ContentPlaceHolder_city',
                                            'label' => false,
                                            'placeholder' => __('City'),
                                            'class' => $class,
                                            'value' => $investor[0]['Investor']['investor_city'],
                                        ));
                                        ?>
                                    </div>	
                                </div>
                                <!-- /city -->

                                <!-- Country -->
                                <div class="col-xs-12 col-sm-4 col-md-8 col-lg-8">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_country"><?php echo __('Country') ?></label>
                                        <?php
                                        $errorClass = "";
                                        if (array_key_exists('investor_country', $investorValidationErrors)) {
                                            $errorClass = "redBorder";
                                        }
                                        $class = "form-control blue_noborder2 investorCountry" . ' ' . $errorClass;
                                        echo $this->Form->input('Investor.investor_country', array(
                                            'name' => 'country',
                                            'id' => 'ContentPlaceHolder_country',
                                            'label' => false,
                                            'options' => $countryData,
                                            'placeholder' => __('Country'),
                                            'class' => $class,
                                            'value' => $investor[0]['Investor']['investor_country'],
                                        ));
                                        ?>
                                    </div>	
                                </div>
                                <!-- /country -->
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_iban"><?php echo __('IBAN') ?></label>
                                        <input id="Iban" type="text" class="form-control blue_noborder2" value = <?php echo $ocr[0]['Ocr']['Investor_iban'] ?>>
                                    </div>
                                </div><!-- /Cif + Business Name -->
                            </div>
                            <!-- /User data -->
                        </div>
                        <!-- /Investor complete data -->
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Checkbox -->
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="checkbox">
                                        <label>
                                            <input id="vehicle" type="checkbox"> <?php echo __('I use my company as investment vehicle') ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- /checkbox -->
                            <div class="row" id="investmentVehicle">
                                <!-- CIF -->
                                <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_cif"><?php echo __('CIF') ?></label>
                                        <input id="CIF" type="text" class="form-control blue_noborder2" value = "<?php echo $ocr[0]['Ocr']['Investor_cif'] ?>">
                                    </div>
                                </div>
                                <!-- /CIF -->

                                <!-- Business Name -->
                                <div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label for="ContentPlaceHolder_businessName"><?php echo __('Business Name') ?></label>
                                        <input id="BusinessName" type="text" class="form-control blue_noborder2" value="<?php echo $ocr[0]['Ocr']['Investor_businessName'] ?>">
                                    </div>
                                </div>
                                <!-- /Business Name -->
                                <!-- /Business Data -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <button type="submit" href="/investors/editUserProfileData" id="editUserData" class="btn btn-win5 btnRounded pull-right"><?php echo __('Save')?></button>
                            </div>
                        </div>	
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="OCR_InvestorPanelB">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title"><strong><?php echo __('One Click Registration')?></strong></h4>
                    <p class="category"><?php echo __('Document Uploading')?></p>
                </div>
                <div class="card-content table-responsive">
                    <div class="row firstParagraph">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <p><?php
                                echo __('One Click Registration Le permite registrarse con un solo click en cualquier plataforma'
                                        . ' que Winvestify tenga habilitada. Para ello, cumpliendo con la Ley 10/2012, del 28 de Abril, de prevención del'
                                        . ' blanqueo de capitales y de Financiación del Terrorismo deberá aportar la siguiente documentación para que las'
                                        . ' PFP puedan validar y autenticar su identidad.')
                                ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Investor complete data -->
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="table-responsive">  
                                <table id="documentsTable" class="table table-striped display dataTable" width="100%" cellspacing="0"
                                       data-order='[[ 2, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                    <thead>
                                        <tr>
                                            <th><?php echo __('Date') ?></th>
                                            <th><?php echo __('Name') ?></th>
                                            <th><?php echo __('Status') ?></th>
                                            <th><?php echo __('Upload') ?></th>
                                            <th><?php echo __('Delete') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>01-01-2017</td>
                                            <td>NIF_Front</td>
                                            <td><span style="color:#990000"><i class="fa fa-times"></i> <?php echo __('Incorrect') ?></span></td>
                                            <td>
                                                <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>01-01-2017</td>
                                            <td>NIF_Back</td>
                                            <td><span style="color:#cc6600"><i class="fa fa-exclamation-triangle"></i> <?php echo __('Warning') ?></span></td>
                                            <td>
                                                <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>01-01-2017</td>
                                            <td>IBAN</td>
                                            <td><span style="color:#33cc33"><i class="fa fa-check"></i> <?php echo __('Correct') ?></span></td>
                                            <td>
                                                <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>01-01-2017</td>
                                            <td>Another one</td>
                                            <td><span style="color:#3399ff"><i class="fa fa-thumb-tack"></i> <?php echo __('Validating') ?></span></td>
                                            <td>
                                                <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default" style="background-color:#990000; color:white;"><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>01-01-2017</td>
                                            <td>Another one</td>
                                            <td><span style="color:#808080"><i class="fa fa-exclamation"></i> <?php echo __('Not uploaded yet') ?></span></td>
                                            <td>
                                                <button type="button" class="btn btn-default" style="background-color:#3399ff; color:white;"><i class="fa fa-upload"></i> <?php echo __('Upload') ?></button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-default" style="background-color:#990000; color:white;" disabled><i class="fa fa-times"></i> <?php echo __('Delete') ?></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>