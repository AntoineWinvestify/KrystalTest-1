<?php

/**
 *
 *
 * Shows the generated billing list of students on the screen
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2013-11-01
 * @package


Version 0.1
very basic version without any fancy formatting nor javascript  etc.




pending:
make ajax call even more generic, load part of url from hidden variable
if a checkbox is marked and unmarked it will show up as empty in the json string






*/
?>
<script language="javascript" type="text/javascript">
	var jsonList = {};	
	var receivingController;
	
$(document).ready(function() {
	var receivingController = $('#receivingController').val();   // TO GET THE VALUE OF A VARIABLE


	
// This should work for checkboxes and ordinary inputfields
$("input, select").bind("change", function(event) {
	var id = $(this).attr("id");
	var value = $("#"+id).val();
	var name = $("#"+id).attr("name");
		
	if (value == ""){
		delete jsonList[name];
	}
	else {
		jsonList[name] = value;
	}
});
   


$("input[type=checkbox][checked]").bind("click", function(event) {
	var id = $(this).attr("id");
	var value = $("#"+id).val();
	var name = $("#"+id).attr("name");

	var resultChecked = $(this).prop('checked');
	if (!resultChecked) {
		delete jsonList[name];
	}
	else {
		jsonList[name] = value;	
	}
});
 
 
   
/**
*
*	Read loanrequests according to the filtering conditions
*
*
*/
$("#btBuscarFiltros1").bind("click", function(event) {

	var url_string = "/loanrequests/writeLoanRequestLoanData";
alert("jsonList = " + JSON.stringify(jsonList));
 // if ($(target).is(':empty')) {
	$.ajax({
		type: "POST",
		url: url_string,
		data: {
			 jsonList		: JSON.stringify(jsonList),
			},	  
		error: function(data){
			$('#modalKO').modal('show'); 
		},
		success: function(data){
			if (data == true ) {
				$('#modalOK').modal('show'); 
			}
			else {
				$('#modalKO').modal('show');				
			}
		}
	})  	
});




/**
*
*	"Back" button was clicked
*
*
*/
$("#btnReturn").bind("click", function(event) {
	var url_string = receivingController + "/returnBtn";
	$.ajax({
		type: "POST",
		url: url_string,
		data: {
			},
		error: function(data){
			$('#modalKO').modal('show');
		},
		success: function(data){
			if (data == true ) {
				alert(data);
				window.location = data;
			}
			else {
				$('#modalKO').modal('show');
			}
		}
	})
});
   
   
   
});
</script>

<?php /*
<script>

$(document).ready(function () {
		app.main.init();

            recargarTabla('#dataTables-solicitudes');

            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var fechaInicio = new Date($("._fechaPagoInicio").val()) || NaN;
                    var fechaFin = new Date($("._fechaPagoFin").val()) || NaN;

                    var devolver;


                    //Estado del prestamo
                    var input = data[5];

                    var horaSeparada = input.split(" ");

                    var parts = horaSeparada[0].split("/");
                    var fecha = new Date(Number(parts[2]), Number(parts[1]) - 1, Number(parts[0]));
                    if ((isNaN(fechaInicio) && isNaN(fechaFin)) ||
                         (isNaN(fechaFin) && fecha.setHours(0, 0, 0, 0) >= fechaInicio.setHours(0, 0, 0, 0)) ||
                        (isNaN(fechaInicio) && fecha.setHours(0, 0, 0, 0) <= fechaFin.setHours(0, 0, 0, 0)) ||
                         (fecha.setHours(0, 0, 0, 0) >= fechaInicio.setHours(0, 0, 0, 0) && fecha.setHours(0, 0, 0, 0) <= fechaFin.setHours(0, 0, 0, 0))) {
                        devolver = true;
                    }
                    else {
                        devolver = false;
                    }

                    if (devolver) {
                        var estadoSolicitud = data[8];

                        var arrayEstados = [];
                        $("[id*=cbListEstadosPrestamo] input:checked").each(function () {
                            arrayEstados.push($(this).val());
                        });
                        if (arrayEstados.length > 0) {
                            if ($.inArray(estadoSolicitud, arrayEstados) < 0) {
                                devolver = false;
                            }
                        }
                    }
                    return devolver;
                }
            );

            $('#dataTables-solicitudes').DataTable().draw();
        });

function filtrar() {
	var table = $('#dataTables-solicitudes').DataTable();
	table.draw()
}
		
function convertDate(inputFormat) {
	function pad(s) { return (s < 10) ? '0' + s : s; }
	var d = new Date(inputFormat);
	return [pad(d.getDate()), pad(d.getMonth() + 1), d.getFullYear()].join('/');
}
		
	
});
	
		
});		
</script>
*/
?>

<?php
// START OF GENERIC CODE
	echo $this->Form->input('', array(	'name'	=> 'receivingController',
										'value'	=> $receivingController,
										'id'	=> 'receivingController',
										'type'	=> 'hidden'
										));
// END OF GENERIC CODE
?>


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



    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Listado de operaciones</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">


                                <div class="col-xs-12 col-lg-2 nomargin-left nopadding-left">
                                    <label class="width100">Fecha desde:</label>
                                    <input name="calFechaInicio" type="text" id="FechaInicio" placeholder="dd/mm/aaaa" class="displayBlock inputText _fechaPagoInicio form-control datepicker" />
                                </div>
                                <div class="col-xs-12 col-lg-2 nomargin-left nopadding-left">
                                    <label class="width100">Fecha hasta:</label>
                                    <input name="calFechaFin" type="text" id="FechaFin" placeholder="dd/mm/aaaa" class="displayBlock inputText _fechaPagoFin form-control datepicker" />
                                </div>

                                <div class="form-group col-xs-12 col-lg-3">
									<a id="btBuscarFiltros" class="btFiltrar floatRight" onclick="filtrar()">Filtrar Resultados</a>
                     				 
                                </div>
                                <hr class="divider">
 
								<div id="dataTables-solicitudes_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
								<table id="dataTables-solicitudes" class="table table-striped table-bordered table-hover dataTable no-footer" role="grid" aria-describedby="dataTables-solicitudes_info">
                                    <thead>
							            <tr role="row">
											<th class="sorting_asc" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" aria-label="Número de solicitud: Activar para ordenar la columna de manera descendente" aria-sort="ascending">Fecha operación</th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="width: 277.75px;" aria-label="Nombre/Razón social: Activar para ordenar la columna de manera ascendente">Nombre/Razón social</th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="width: 61.75px;" aria-label="Tipo: Activar para ordenar la columna de manera ascendente">Tipo</th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="width: 81.75px;" aria-label="Teléfono: Activar para ordenar la columna de manera ascendente">Importe</th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="width: 196.75px;" aria-label="Estado: Activar para ordenar la columna de manera ascendente">TPV solicitud</th>
											<th class="date-eu sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="width: 148.75px;" aria-label="Fecha solicitud: Activar para ordenar la columna de manera ascendente">Fecha solicitud</th>
											<th class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" style="width: 73.75px;" aria-label="Importe: Activar para ordenar la columna de manera ascendente"></th>
											<th class="no-sort sorting_disabled" rowspan="1" colspan="1" style="width: 20.75px;" aria-label=""></th>
											<th style="display: none; width: 0px;" class="sorting" tabindex="0" aria-controls="dataTables-solicitudes" rowspan="1" colspan="1" aria-label=": Activar para ordenar la columna de manera ascendente"></th>
										</tr>									
                                    </thead>
									
                                    <tbody>
										
<?php
	foreach ($readAllOperationsData as $operation) { ?>											
											    <tr class="odd gradeA" id="rowSolicitud">
                                                    <td> <?php echo $operation['Operation']['id'] ?></td>
                                                    <td> <?php echo $operation['Operation']['name'] . " ";
																echo $operation['Operation']['surnames'] ." / " ;
																echo $operation['Operation']['companyName']; ?>
													</td>
													<td><?php echo $operation['Operation']['User']['typeOfUser'] ?></td>
                                                    <td><?php echo $operation['Operation']['amount'] ; echo "€"; ?></td>
                                                    <td><?php echo $operation['Operation']['tpvOrder'] ?></td>
                                                    <td><?php echo $operation['Operation']['created'] ?></td>
                                                    <td><?php echo $operation['Operation']['dni']/100; ?></td>
                                                    <td>
<?php
														$linkUrl = "/loanrequests/readLoanRequest/".$loanRequest['Loanrequest']['id'];
														echo '<a href= ' . $linkUrl  .'><i class="fa fa-pencil"></i></a><td>';
?>													
                                                            
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



