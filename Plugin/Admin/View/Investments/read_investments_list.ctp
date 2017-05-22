


<?php
/**
 *
 *
 * AJAX format for sending the data requiered for TPV processing
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-01-15
 * @package



2016-01-15		version 0.1





Pending:
define the modal modalActionConfirmation according to the bootstrap documentation
check for matching divs with 		<div id="wrapper">



*/

?>

<script>

var parentController = "admin/<?php echo $parentController ?>";
var parentAction;		
var parentInformationrequestAction;


$(document).ready(function() {
	
$(".changedata").bind("click", function(event) {
app.utils.trace("fired");		
	$('#modalActionConfirmation').modal({backdrop: 'static'});	
	
});	
	
	
	
$(".modifyPaymentState").bind("change", function(event) {
	var newState;
	var investmentId = $(this).attr("id");
	var id;
	
	newState = $("#"+investmentId).val();
	id = investmentId.replace(/\D/g, "");	
app.utils.trace("newstate = " + newState + " id = " + investmentId + " id = " + id);
$('#modalActionConfirmation').modal({backdrop: 'static'});	

	var url_string = "/" + parentController + "/" + "writeInvestmentData";	
    $.ajax({
		type: "POST",
		url: url_string,
		data: {
			 investmentId:	id,
			 newState:		newState
			},	  
		error: function(data){
app.utils.trace("an error has occured");			
			$('#modalKO').modal('show');
		},
		success: function(data){
app.utils.trace("changed performed on server");			
//			$( target ).empty();
//			$("tabsarea").empty();		
//			$(target).html(data);
	    }
	})
})

})
</script>

		<div id="wrapper">
		    <!-- Modal informativa User -->
		    <div class="modal fade modalUser" id="modalInfoUser" tabindex="-1" role="dialog" aria-labelledby="modalInfoUser">
		        <div class="modal-dialog" role="document">
		            <div class="modal-content">
		                <div class="content-iframe">
		                    <iframe runat="server" id="ifrDetUsuario" src="about:_blank" height="600" width="800"></iframe>
		                </div>
		            </div>
		        </div>
		    </div>
		    <!-- fin modal -->


            <!-- Modal actionConfirmation  -->
			<div class="modal fade" id="modalActionConfirmation" tabindex="-1" role="dialog" aria-labelledby="ActionConfirmation">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="ActionConfirmation"><?php echo __("Estás seguro que quiere cambiar el estado")?></h4>
                        </div>
                        <div class="modal-body">
							<div class="modalActionConfirmation">
							</div>
                        </div>

						<a class="btn btn-green" href="/admin/investments/readInvestmentsList" role="button"><?php echo __("Cancelar") ?></a>
							<a class="btn btn-green" href="/admin/investments/readInvestmentsList" role="button"><?php echo __("Continuar") ?></a>
                        <div class="modal-footer">
                           
                        </div>
                    </div>
                </div>
            </div> 
			<!-- fin modal -->

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo __("Listado de inversiones") ?></h1>
            </div>

            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">

                                <div class="col-xs-12 col-lg-2 nomargin-left nopadding-left">
                                    <label class="width100"><?php echo __("Fecha de solicitud desde:")?></label>
                                    <input name="calFechaInicio" type="text" id="FechaInicio" placeholder="dd/mm/aaaa" class="displayBlock inputText _fechaPagoInicio form-control datepicker" />
                                </div>
                                <div class="col-xs-12 col-lg-2 nomargin-left nopadding-left">
                                    <label class="width100"><?php echo __("hasta:")?></label>
                                    <input name="calFechaFin" type="text" id="FechaFin" placeholder="dd/mm/aaaa" class="displayBlock inputText _fechaPagoFin form-control datepicker" />
                                </div>

                                <div class="col-xs-12 col-lg-5 radio nomargin-left  nopadding-left">
                                    <label><?php echo __("Estado del prestamo")?></label>
                                    <table id="ctl00_ContentPlaceHolder1_cbListEstadosPrestamo" class="cbListEstadosPrestamos" cellspacing="5" cellpadding="5">

									</table>
                                </div>
                                <div class="form-group col-xs-12 col-lg-3">
									<a id="btBuscarFiltros" class="btFiltrar floatRight" onclick="filtrar()"><?php echo __("Filtrar Resultados")?></a>

                                </div>
                                <hr class="divider">

								<div id="dataTables-solicitudes_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
								<table id="dataTables-solicitudes" class="table table-striped table-bordered table-hover dataTable no-footer" role="grid" aria-describedby="dataTables-solicitudes_info">
                                    <thead>
							            <tr role="row">
											<th class="sorting_asc" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" aria-label="Número de solicitud: Activar para ordenar la columna de manera descendente" aria-sort="ascending"><?php echo __("Fecha/Hora")?></th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="idth: 277.75px;" aria-label="Nombre/Razón social: Activar para ordenar la columna de manera ascendente"><?php echo __("Nombre Startup")?></th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="idth: 61.75px;" aria-label="Tipo: Activar para ordenar la columna de manera ascendente"><?php echo __("Tipo de Inversión")?></th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="idth: 61.75px;" aria-label="Tipo: Activar para ordenar la columna de manera ascendente"><?php echo __("Inversor")?></th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="idth: 81.75px;" aria-label="Teléfono: Activar para ordenar la columna de manera ascendente"><?php echo __("Cantidad")?></th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="idth: 196.75px;" aria-label="Estado: Activar para ordenar la columna de manera ascendente"><?php echo __("Estado")?></th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="idth: 73.75px;" aria-label="Importe: Activar para ordenar la columna de manera ascendente"><?php echo __("Medio Pago")?></th>
											<th class="no-sort sorting_disabled" rowspan="1" colspan="1" style="width: 20.75px;" aria-label=""></th>
											<th style="display: none; width: 0px;" class="sorting" tabindex="0" aria-controls="dataTables-solicitudesOLD" rowspan="1" colspan="1" aria-label=": Activar para ordenar la columna de manera ascendente"></th>
										</tr>
                                    </thead>
                                    <tbody>

<?php
	foreach ($resultInvestmentsListData as $investment) { ?>
											    <tr class="odd gradeA" id="rowSolicitud">
                                                    <td><?php echo $investment['Investment']['investment_transactionDateTime'] ?></td>
                                                    <td> <?php echo $invest['User']['companyName'];
													$options = $investment['Investment']['invest_option'];
													foreach ($resultInvestOptionData as $investoption) {
														if ($investoption['Investoption']['id'])  {   // a match is ALWAYS found
															$startupName = $investoption['Startup']['startup_name'];
															$optionName =  $investoption['Investoption']['investoption_slogan'];
														}
													}
													echo $startupName;
													?> </td>
													<td> <?php echo $optionName; ?> </td>
                                                    <td> <?php echo $investment['Investor']['investor_name'] . " ";
																echo $investment['Investor']['investor_surnames']; ?>
													</td>
                                                    <td><?php echo $investment['Investment']['investment_amount']/100; ?> €</td>
													<td><?php
													echo $this->Form->input('investment_state', array(
																							'options' 	=> $investmentStateTransitionTable[$investment['Investment']['investment_state']],
																							'label'		=> false,
																							'class'		=> 'modifyPaymentState',
																							'id'		=> 'investment'.$investment['Investment']['id'],
													));
													?>
													</td>
					
													<td><?php echo $investment['investment']['investment_paymentMeans'];?></td>
                                                    <td>
<?php
														$linkUrl = "/admin/loanrequests/readLoanRequest/".$loanRequest['Loanrequest']['id'];
														echo '<a href= ' . $linkUrl  .'><i class="fa fa-pencil"></i></a><td>';
?>
													</td>
                                                </tr>
<?php } ?>
                                    </tbody>
                                </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>

<?php echo $this->Form->end(); ?>


