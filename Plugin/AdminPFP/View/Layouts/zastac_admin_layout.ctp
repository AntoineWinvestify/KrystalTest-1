<!DOCTYPE html>
<html lang="en">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="utf-8" /><meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description" /><meta name="author" />
<title>
	Zastac Equity- Administration Site
</title>

    <!-- Bootstrap Core CSS -->
<?php	  	echo $this->Html->css('bootstrap.min.css'); ?>
    <!--<link href="css/bootstrap-slider.css" rel="stylesheet" /><link href="css/bootstrap-select.min.css" rel="stylesheet" />-->
  <!--   <link href="css/bootstrap-datepicker.css" rel="stylesheet" />   -->
<?php	  	echo $this->Html->css('bootstrap-datepicker.css'); ?>	
    <!-- MetisMenu CSS -->   
<?php	  	echo $this->Html->css('bower_components/metisMenu/dist/metisMenu.min.css'); ?>
    <!-- datatables css -->
  <!--    <link href="css/datatables.min.css" rel="stylesheet" />  -->
<?php	  	echo $this->Html->css('datatables.min.css'); ?>
    <!-- Custom CSS -->
   <!--   <link href="dist/css/sb-admin-2.css" rel="stylesheet" />
   <link href="dist/css/style-admin.css?v=1" rel="stylesheet" />  -->
<?php	  	echo $this->Html->css('sb-admin-2.css'); ?>
<?php	  	echo $this->Html->css('style-admin.css?v=1'); ?>


    <!-- Custom Fonts -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
	<link href="//fonts.googleapis.com/css?family=Roboto:500,300,700,400&amp;subset=latin,latin-ext" rel="stylesheet" type="text/css" />
<?php	echo $this->Html->css('fileinput.min.css'); ?>
<?php	echo $this->Html->script('bower_components/jquery/dist/jquery.min.js'); ?>


<?php	echo $this->Html->script('enscroll-0.6.1.min.js'); ?>

    <!-- Bootstrap Core JavaScript -->
<?php echo $this->Html->script('bootstrap.min.js'); ?>	
<?php //echo $this->Html->script('bootstrap-slider.min.js'); ?>
<?php //echo $this->Html->script('bootstrap-select.min.js'); ?>	
<?php echo $this->Html->script('bootstrap-datepicker.js'); ?>
<?php //echo $this->Html->script('css.js?v=1'); ?>
<?php echo $this->Html->script('fileinput.min.js'); ?>
<?php echo $this->Html->script('fileinput_locale_es.js'); ?>
<?php echo $this->Html->script('jquery.maskedinput.min.js'); ?>

    <!-- Metis Menu Plugin JavaScript -->
<?php	echo $this->Html->script('bower_components/metisMenu/dist/metisMenu.min.js'); ?>

    <!-- DataTables JavaScript -->
<?php echo $this->Html->script('datatables.min.js'); ?>

    <!-- Custom Theme JavaScript -->
<?php echo $this->Html->script('dist/js/sb-admin-2.js'); ?>
<?php echo $this->Html->script('plugins/sort-eu.js'); ?>

<?php echo $this->Html->script('jquery.cookies.js'); ?>

<?php  echo $this->Html->script('main.js'); ?>

    <script>

        function scrollToBottom() {
            // $(".scrollConversacion").scrollTop($(".scrollConversacion")[0].scrollHeight);
            $("html, body").animate({ scrollTop: $(document).height() }, 1000);
        }
    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>
<body>
	
<?php
	echo $this->fetch('meta');
	echo $this->fetch('css');
	echo $this->fetch('script');
?>  

        <!-- Modal OK -->
        <div class="modal fade" id="modalOK" tabindex="-1" role="dialog" aria-labelledby="modalOK">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabelOK"><?php echo __("Guardado Correctamente")?></h4>
                    </div>
                    <div class="modal-body">
                        <?php echo __("Datos guardados correctamente")?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal"> <?php echo __("Aceptar")?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin modal -->
		
        <!-- Modal KO -->
        <div class="modal fade" id="modalKO" tabindex="-1" role="dialog" aria-labelledby="modalKO">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabelKO"> <?php echo __("Error")?></h4>
                    </div>
                    <div class="modal-body">
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn " data-dismiss="modal"><?php echo __("Aceptar")?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin modal -->


        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/admin/default.aspx"></a>
                </div>
                <!-- /.navbar-header -->

                <ul class="nav navbar-top-links navbar-right">

                    <!-- /.dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i><i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="/users/logout"><i class="fa fa-sign-out fa-fw"></i><?php echo __("Desconectar")?></a>
                            </li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    <!-- /.dropdown -->
                </ul>
                <!-- /.navbar-top-links -->

                <div id="ctl00_pnMenu" class="navbar-default sidebar" role="navigation">
	
<?php					echo $this->element('sidemenu'); ?>

                
				</div>
                <!-- /.navbar-static-side -->
            </nav>
            

	
	
<?php
			echo $this->Session->flash();
			echo $this->Session->flash('auth');
			echo $this->fetch('content');
?>	


        </div>  <!-- /#wrapper -->





    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
        $(document).ready(function () {
            app.main.init();

            recargarTabla('#dataTables-solicitudes');

            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    
                    var devolver=false;

                    //Estado del prestamo
                    var input = data[5];
                    var horaSeparada = input.split(" ");
                    var parts = horaSeparada[0].split("/");
                    var fecha = new Date(Number(parts[2]), Number(parts[1]) - 1, Number(parts[0]));
                 
                    var strFechaInicioFiltro = $("._fechaPagoInicio").val();
                    var strFechaFinFiltro = $("._fechaPagoFin").val();

                    devolver= app.utils.fechaEnIntervalo(strFechaInicioFiltro, strFechaFinFiltro, fecha);
                    
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

//            $('#dataTables-solicitudes').DataTable().draw();
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


    
    

</body>
</html>
