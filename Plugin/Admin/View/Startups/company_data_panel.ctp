<?php
/**
 *
 *
 * Fabricates the basic structure with all tabs for showing and/or editing the data of the startup
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-05-13
 * @package



2016-05-13		version 0.1

REMOVE ALL REFERENCES TO LOAN AND LOANREQUEST



Pending:
-




*/


?>

<?php //echo $this->Html->script('ckeditor/ckeditor.js'); ?>



<?php
// START OF GENERIC CODE
	echo $this->Form->input('', array(	'name'	=> 'startupId',
										'value'	=> $startupId,
										'id'	=> 'startupId',
										'type'	=> 'hidden'
										));
?>
<?php
	echo $this->Form->input('', array(	'name'	=> 'receivingController',
										'value'	=> $receivingController,
										'id'	=> 'receivingController',
										'type'	=> 'hidden'
										));
// END OF GENERIC CODE	
?>



<script>
	var startupId,
		receivingController,
		target,
		url_string;

	
$(document).ready(function() {
	var action = "readStartupBasicCompanyData";	
	startupId = $('#startupId').val();
	receivingController = $('#receivingController').val();
	receivingController = "admin/" + receivingController ;
app.utils.trace("test1000");	

	target = "#tab1";
	url_string = "/" + receivingController + "/" + action;
app.utils.trace("test2000");	
 // if ($(target).is(':empty')) {
    $.ajax({
		type: "GET",
		url: url_string,
		data: {
			 startupId:	startupId,	
			},	  
		error: function(data){
			$('#modalKO').modal('show'); // this works
		},
		success: function(data){
			$( target ).empty();
			$("tabsarea").empty();		
			$(target).html(data);
	    }
	})


$(function() {

/**
*
* switched between the various tabs of the page
*
*/
$('a[data-toggle="tab"]').on('show.bs.tab', function (event) {
	var target = $(event.target).attr("href"); // activated tab
	var action = $(event.target).attr("id");
	
	url_string = "/" + receivingController + "/" + action;	

 // if ($(target).is(':empty')) {
    $.ajax({
		type: "GET",
		url: url_string,
		data: {
			startupId:	startupId,	
		},	  
		error: function(data){
			$('#modalKO').modal('show'); // this works
		},
		success: function(data){
			$( target ).empty();
			$("tabsarea").empty();		
			$(target).html(data);
	    }
	})
 //}
})


});
});
</script>



<div id="wrapper">

    <!-- Modal informativa User -->

    <div class="modal fade modalUser" id="modalInfoUser" tabindex="-1" role="dialog" aria-labelledby="modalInfoUser">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="content-iframe">
                    <iframe src="about:_blank" id="ctl00_ContentPlaceHolder1_ifrDetUsuario" height="600" width="800"></iframe>

                </div>
            </div>
        </div>
    </div>
    <!-- fin modal -->


    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <?php echo $startupName; ?></h1>
            </div>
            <!-- /.col-lg-12 -->
        </div> 
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default"> 
                    <a id="ctl00_ContentPlaceHolder1_lnkVovler" class="btn btn-green nomargin btVolverAdmin" href="javascript:__doPostBack(&#39;ctl00$ContentPlaceHolder1$lnkVovler&#39;,&#39;&#39;)">Volver
					</a>
					<div id="ctl00_ContentPlaceHolder1_ctrlDetallePrestamo_upPan">
	

						<div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="modalConfirm">
						    <div class="modal-dialog" role="document">
						        <div class="modal-content">
						            <div class="modal-header">
						               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							            <h4 class="modal-title" id="myModalLabelConfirm">Aviso</h4>
						            </div>
									<div class="modal-body">
									</div>
            <div class="modal-footer">
                <input type="submit" name="ctl00$ContentPlaceHolder1$ctrlDetallePrestamo$btConfirmarCambioEstado" value="Confirmar" onclick="; return true;" id="ctl00_ContentPlaceHolder1_ctrlDetallePrestamo_btConfirmarCambioEstado" class="btn btn-green _ConfirmEstado" />
                <button type="button" class="btn " data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default" id="pnDetallePrestamo">
	<div class = "tabs-content panel">
		<div class="row marginTop70"> 
			<ul id="myTab" class="nav nav-tabs nav-justified">
		 		<li class=""><a id="readStartupBasicCompanyData" href="#tab1" data-toggle="tab"><?php echo __('Datos Startup') ?></a></li> 
		 		<li class=""><a id="readStartupKeyPeopleData" href="#tab2" data-toggle="tab"><?php echo __('Datos Emprendedores') ?></a></li>   
				<li class=""><a id="readStartupGlobalData" href="#tab3" data-toggle="tab"><?php echo __('Vision + Global Data') ?></a></li>
				<li class=""><a id="readStartupInvestmentOptionsData" href="#tab4" data-toggle="tab"><?php echo __('Opciones para Invertir') ?></a></li>
			</ul>
 		</div> 
	 	<div id="myTabContent" class="tab-content">
			
			<div class="tab-pane active in" id="tab1">	
			</div>  <!--./tab-pane -->
			
			<div class="tab-pane" id="tab2">
			</div>  <!--./tab-pane -->
			
			<div class="tab-pane" id="tab3">	
			</div>  <!--./tab-pane -->
			
			<div class="tab-pane" id="tab4">	
			</div>  <!--./tab-pane -->
		
		</div>  <!-- /.tabs-content panel -->
	</div>  

                </div>      <!--  ./class="panel panel-default" id="pnDetallePrestamo" -->
                <!-- /.table-responsive           -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
 </div>




<!--   <script src="js/main.js"></script>-->
<!-- Page-Level Demo Scripts - Tables - Use for reference -->



    
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
            table.draw();
        }
		
        function convertDate(inputFormat) {
            function pad(s) { return (s < 10) ? '0' + s : s; }
            var d = new Date(inputFormat);
            return [pad(d.getDate()), pad(d.getMonth() + 1), d.getFullYear()].join('/');
        }
</script>
