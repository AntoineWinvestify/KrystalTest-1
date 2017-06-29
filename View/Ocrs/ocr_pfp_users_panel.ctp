<?php
/**
 * +--------------------------------------------------------------------------------------------+
 * | Copyright (C) 2016, http://www.winvestify.com                                              |
 * +--------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify                          |
 * | it under the terms of the GNU General Public License as published by                       |
 * | the Free Software Foundation; either version 2 of the License, or                          |
 * | (at your option) any later version.                                                        |
 * | This file is distributed in the hope that it will be useful                                |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of                             |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                               |
 * | GNU General Public License for more details.                                               |
 * +--------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.2
 * @date 2017-05-23
 * @package
 * 
 * One Click Registration - PFD Admin Users Selection
 * Users registered by Winvestify or consulted by PFD Admin data panel
 * Tallyman Service.
 * 
 * [2017-05-23] Principal table done
 *              [pending] subdata table con every table row
 *              [pending] Update filters
 *              [pending] Add chart
 *              [pending] Update table view
 * 
 * [2017-06-13] Version 0.2
 * Added documentation
 * Added orange box
 * Added style to overlay
 * Deleted unnecessary script
 * 
 * 
 * [2017-06-26] Version 0.2
 * Table from db
 * 
 * 
 * [2017-06-28] Version 0.3
 * Zip download
 * Added Datatable javascript
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<style>
    .togetoverlay .overlay  {
        z-index: 50;
        background: rgba(255, 255, 255, 0);
        border-radius: 3px;
    }
    .togetoverlay .overlay > .fa {
        font-size: 50px;
    }
</style>
<script>
    $(function () {
        $("#usersTable").DataTable();

    });

</script>
<div id="1CR_pfpAdmin_1_usersPanel">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header" data-background-color="orange">
                    <h4 class="title"><strong><?php echo __('PFPAdmin - New users Panel') ?></strong></h4>
                </div>
                <div class="card-content table-responsive togetoverlay">
                    <!--<div class="overlay">
                        <div class="fa fa-spin fa-spinner" style="color:green">	
                        </div>
                    </div>-->
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
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10 col-lg-offset-1">
                                    <div class="table-responsive">  
                                        <table id="usersTable" class="table table-striped display dataTable" width="100%" cellspacing="0"
                                               data-order='[[ 0, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
                                            <thead>
                                                <tr>
                                                    <th><?php echo __('Date') ?></th>
                                                    <th><?php echo __('Name') ?></th>
                                                    <th><?php echo __('Surname') ?></th>
                                                    <th><?php echo __('Telephone') ?></th>
                                                    <th><?php echo __('Email') ?></th>
                                                    <th><?php echo __('Status') ?></th>
                                                    <th><?php echo __('Action') ?></th>
                                                    <th><?php echo __('Tallyman') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($ocrList as $ocr) {
                                                    ?>
                                                    <tr>
                                                        
                                                        <?php if ($pfpStatus == SER_ACTIVE) { //If service is active, show the data?>
                                                        
                                                            <td><?php echo __($ocr[1]['invesotrInfo']['Ocr']['ocr_sent']) ?></td>
                                                            <td><?php echo __($ocr[1]['invesotrInfo']['Investor']['investor_name']) ?></td>
                                                            <td><?php echo __($ocr[1]['invesotrInfo']['Investor']['investor_surname']) ?></td>
                                                            <td><?php echo __($ocr[1]['invesotrInfo']['Investor']['investor_telephone']) ?></td>
                                                            <td><?php echo __($ocr[1]['invesotrInfo']['Investor']['investor_email']) ?></td>
                                                            <td><?php echo __('cosa del estado del usuario') ?></td>
                                                            <td>
                                                                <a href="/files/generateZip/<?php echo $ocr[1]['invesotrInfo']['Investor']['id'] . DS . $ocr[1]['invesotrInfo']['Investor']['user_id'] ?>">Descargar</a>
                                                                <?php /* <button value="<?php echo $ocr[1]['invesotrInfo']['Investor']['id'] ?>" class="btn  btnPFPAdmin btnRounded download"   ><a href="/files/generateZip/<?php echo $ocr[1]['invesotrInfo']['Investor']['id'] . DS . $ocr[1]['invesotrInfo']['Investor']['user_id'] ?>"></a><?php echo __('Download') ?></button> */ ?>
                                                            </td>
                                                            <td><button value="<?php echo $ocr[1]['invesotrInfo']['Investor']['id'] ?>" class="btn  btnPFPAdmin btnRounded"><?php echo __('Tallyman') ?></button></td>
                                                            
                                                        <?php } else if ($pfpStatus == SER_SUSPENDED) { // If servoce is active, hide the full data?>
                                                            <td><?php echo __($ocr[1]['invesotrInfo']['Ocr']['ocr_sent']) ?></td>
                                                            <td><?php echo __($ocr[1]['invesotrInfo']['Investor']['investor_name']) ?></td>
                                                            <td><?php echo __($ocr[1]['invesotrInfo']['Investor']['investor_surname']) ?></td>
                                                            <td><?php echo __(substr_replace($ocr[1]['invesotrInfo']['Investor']['investor_telephone'],'*******',5)) ?></td>
                                                            <td><?php echo __(substr_replace($ocr[1]['invesotrInfo']['Investor']['investor_email'],'********',5) ) ?></td>
                                                            <td><?php echo __('cosa del estado del usuario') ?></td>
                                                            <td>
                                                                <a disabled href="/files/generateZip/<?php echo $ocr[1]['invesotrInfo']['Investor']['id'] . DS . $ocr[1]['invesotrInfo']['Investor']['user_id'] ?>">Descargar</a>
                                                                <?php /* <button value="<?php echo $ocr[1]['invesotrInfo']['Investor']['id'] ?>" class="btn  btnPFPAdmin btnRounded download"   ><a href="/files/generateZip/<?php echo $ocr[1]['invesotrInfo']['Investor']['id'] . DS . $ocr[1]['invesotrInfo']['Investor']['user_id'] ?>"></a><?php echo __('Download') ?></button> */ ?>
                                                            </td>
                                                            <td><button disabled value="<?php echo $ocr[1]['invesotrInfo']['Investor']['id'] ?>" class="btn  btnPFPAdmin btnRounded"><?php echo __('Tallyman') ?></button></td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /.col 9 -->
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /.row general -->
</div>
