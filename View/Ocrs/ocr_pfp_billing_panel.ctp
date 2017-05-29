<?php
/* 
 * One Click Registration - PFD Billing Panel
 * Panel with all billings related to PFD
 * 
 * [2017-05-23] Completed view
 *              [pending] Update filters
 */
?>
<script src="/plugins/datatables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<div id="OCR_PFDPanelA">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div id="investorFilters" class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <p align="justify"><?php echo __('Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro de textos especimen. No sólo sobrevivió 500 años, sino que tambien ingresó como texto de relleno en documentos electrónicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creación de las hojas "Letraset", las cuales contenian pasajes de Lorem Ipsum, y más recientemente con software de autoedición, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum.')?></p>
                    <h4 class="header1CR"><?php echo __('Filter by:') ?></h4>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <?php 
                            $class = "form-control blue investorCountry". ' ' . $errorClass;
                            $countries = ["select country", "país1", "país2", "país3"];      
										echo $this->Form->input('Investor.investor_country', array(
											'name'			=> 'country',
											'id' 			=> 'ContentPlaceHolder_country',
											'label' 		=> false,
                                                                                        'options'               => $countries,
											'placeholder' 	=>  __('Country'),
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_country'],						
							));
                            ?>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <?php 
                            $class = "form-control blue investorCountry". ' ' . $errorClass;
                            $modalities = ["select modality", "P2P", "P2B", "Invoice Trading"];      
										echo $this->Form->input('Investor.investor_country', array(
											'name'			=> 'country',
											'id' 			=> 'ContentPlaceHolder_country',
											'label' 		=> false,
                                                                                        'options'               => $modalities,
											'placeholder' 	=>  __('Country'),
											'class' 		=> $class,
											'value'			=> $resultUserData[0]['Investor']['investor_country'],						
							));
                            ?>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group input-group-sm blue">
                                <input type="text" style="border:none; border-radius:7px;" class="form-control" placeholder="Search for...">
                                <span class="input-group-btn">
                                  <button class="btn btn-secondary" style="border-top-right-radius: 7px; border-bottom-right-radius: 7px;" type="button">Go!</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                    <div class="table-responsive">  
                        <table id="billingTable" class="table table-striped dataTable display " width="100%" cellspacing="0"
                                                                        data-order='[[ 2, "asc" ]]' data-page-length='25'>
                                <thead>
                                        <tr>
                                                <th width="10%"><?php echo __('Date')?></th>
                                                <th width="20%"><?php echo __('Number')?></th>
                                                <th><?php echo __('Concept')?></th>
                                                <th width="10%"><?php echo __('Amount')?></th>
                                                <th width="10%"><?php echo __('Action')?></th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>01-01-2017</td>
                                        <td>number ofhgfg billing</td>
                                        <td>conceptsgnbhgtttt</td>
                                        <td align="right">0.0550 €</td>
                                        <td>
                                            <button class="btn btn-default btn-win1"><?php echo __('Download')?></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>01-01-2017</td>
                                        <td>numberdsghh of billing</td>
                                        <td>concefssdpttttt</td>
                                        <td align="right">0.0567470 €</td>
                                        <td>
                                            <button class="btn btn-default btn-win1"><?php echo __('Download')?></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>01-01-2017</td>
                                        <td>number ofhgfhf billing</td>
                                        <td>concesdfsfpttttt</td>
                                        <td align="right">0.066660 €</td>
                                        <td>
                                            <button class="btn btn-default btn-win1"><?php echo __('Download')?></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>01-01-2017</td>
                                        <td>number sdfghgof billing</td>
                                        <td>concepgghgggttttt</td>
                                        <td align="right">0.099990 €</td>
                                        <td>
                                            <button class="btn btn-default btn-win1"><?php echo __('Download')?></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>01-01-2017</td>
                                        <td>numbedfdffr of billing</td>
                                        <td>conhhghhcepttttt</td>
                                        <td align="right">22220.00 €</td>
                                        <td>
                                            <button class="btn btn-default btn-win1"><?php echo __('Download')?></button>
                                        </td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- /.col 9 -->
    </div> <!-- /.row general -->
</div>