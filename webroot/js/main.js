


function recargarTabla(datatable) {
/*
	var qSearch = $("#ctl00_hidSearch").val();
    var qOrderColumn = $("#ctl00_hidOrderColumn").val();
    var qOrderDirection = $("#ctl00_hidOrderDirection").val();
    var qPagina = $("#ctl00_hidPagina").val();
    */
    // TableToolsInit.sSwfPath = "media/swf/ZeroClipboard.swf";
    $(datatable).DataTable({
        responsive: true,
        bStateSave: true,
        /*  fnStateSave: function (oSettings, oData) {
              localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
          },
          fnStateLoad: function (oSettings) {
              var data = localStorage.getItem('DataTables_' + window.location.pathname);
              return JSON.parse(data);
          },*/

        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
		
        columnDefs: [
            { type: 'date-euro', targets: ["date-eu"] },
           {
               orderable: false,
               targets: ["no-sort"]
           }],

        dom: 'Bfrtip',

        buttons: [
            'excel'
        ]

    });


    //$(datatable).DataTable().search(qSearch).draw();
    ////$(datatable).DataTable().order([qOrderColumn, qOrderDirection]);
    ////Opcion 1. $(datatable).dataTable().fnPageChange(qPagina);
    ////Opcion 2. $(datatable).DataTable().page(qPagina).draw(false);

    //setTimeout(function () {
    //        $(datatable).dataTable().fnPageChange("3");
    //}, 5000);
    //var eventFiredDataTable = function (type) {
    //    switch (type) {
    //        case "Draw":

    //            break;
    //        case "Order":
    //            //alert("Order");
    //            $("#ctl00_hidOrderColumn").val($(datatable).DataTable().order()[0][0]);
    //            $("#ctl00_hidOrderDirection").val($(datatable).DataTable().order()[0][1]);

    //            break;
    //        case "Search":
    //            $("#ctl00_hidSearch").val($(datatable).DataTable().search());
    //           // alert(qSearch);
    //            break;
    //        case "Page":
    //            // var qPagina = $(datatable).DataTable().page.info();
    //            //$('#pageInfo').html('Showing page: ' + info.page + ' of ' + info.pages);
    //            //alert(info.page);

    //            var table = $(datatable).DataTable();
    //            var info = table.page.info();
    //            $("#ctl00_hidPagina").val(info.page);
    //            break;
    //        default:

    //    }

    //}


    //$(datatable)
    // .on('search.dt', function () { eventFiredDataTable('Search'); })
    // .on('order.dt', function () { eventFiredDataTable('Order'); })
    // .on('page.dt', function () { eventFiredDataTable('Page'); })
    // .on('draw.dt', function () { eventFiredDataTable('Draw'); })
    // .DataTable();


}

function restaurarTabla(datatable)
{
    //vamos a pintar la tabla tal y como estaba antes del postback, para ello usaremos los datos guardados en los distintos eventos.
    var qSearch = $("#ctl00_hidSearch").val();
    var qPagina = $("#ctl00_hidPagina").val();
    var qOrderColumn = $("#ctl00_hidOrderColumn").val();
    var qOrderDirection = $("#ctl00_hidOrderDirection").val();
    $(datatable).DataTable().search(qSearch).draw();
   // $(datatable).DataTable().order([qOrderColumn, qOrderDirection]);
   // $(datatable).dataTable().fnPageChange(qPagina);
}


//variables globales
var maxRequestFile=10485760;
var FileContentTypeRequest = "application/pdf,image/jpeg,image/gif,image/png";
var FileExtensionRequest = "jpeg,jpg,pdf,png";
var textoTypeFile = "El documento seleccionado no es válido. Compruebe que el documento es de alguno de los siguientes tipos: .jpeg, .jpg, .png, .pdf";
var textoSizeFile = "Los ficheros adjuntos no pueden ser mayores de 1 megabyte";




//IE8 ajax support
$.support.cors = true;
//definición de la app
var app = app || {};
//Espacio de nombres
app.main = {};
app.navegacion = {};
app.utils = {};
app.ajax = {};
app.visual = {};
//---//

app.main = {
    logado: false,
    site_URL: window.location.protocol + "//" + window.location.host,
    extensiones_permitidas: new Array("docx", "gif", "png", "jpg", "jpeg", "pdf"),
    extensiones_permitidas_fotos: new Array("gif", "png", "jpg", "jpeg"),
    promo: null,
    promoSubir: null,
    //site_URL: window.location.protocol + "//" + window.location.host + window.location.pathname,
    //site_URL: "http://private-5f5185-zastac.apiary-mock.com",
    user: {
        id: null,
        nombre: null,
        apellidos: null
    },
    avatar: {
        base64: null,
        nombre: null
    },
    boxInversion: null,
    init: function () {
        this.programarBotonesGenerales();

        var url = window.location.href;
//        var urlC = url.split("#");
//        var bloque = urlC[urlC.length - 1];

        if ( app.main.logado && url.indexOf("#datosbancarios") != -1) {
            $('html, body').animate({
                scrollTop: $(".datosbancarios").offset().top - 180
            }, 1000);
        }

        var valorCookie = $.cookie('zstcCookie');

        if (!valorCookie) {
            $(".cookies-alert").show();
        }
        else {
            $(".cookies-alert").hide();
        }

    },

    programarBotonesGenerales: function () {
        $("._registroAlias").attr('readonly', true);


        var isIe = (navigator.userAgent.toLowerCase().indexOf("msie") != -1 || navigator.userAgent.toLowerCase().indexOf("trident") != -1);
        $("._copiarPortapapeles").off("").on("click", function (e) {


            window.prompt("Copy to clipboard: Ctrl+C, Enter", $(this).attr("data-url"));
            //alert("Copiado");
            e.preventDefault();
        });


        $(document).on("slideStop", ".sliderComparInversiones", function () {
            //$(".sliderComparInversiones").on("slideStop", function () {
            app.utils.trace("click slider");
            app.visual.mostrarBeneficio();
        });




        //$("#liAvatar").on("click", function (e) {
        //    window.location = "gestion_privado.aspx";
        //});

        $(document).on("change", ".registroMostrarAlias", function () {

            if ($(this).find("input").is(":checked"))
            {
                $("._registroAlias").attr('readonly', false);
                $("._registroAlias").css('opacity', '1');
            }
            else
            {
                $("._registroAlias").attr('readonly', true);
                $("._registroAlias").css('opacity', '0.5');
                $("._registroAlias").val("");
            }
        });



        $(document).on("click", ".cierePopUp", function () {
            app.utils.trace("upload avatar");
            app.utils.mostrarPopUp(false);
        });



        $(document).on("change", ".masDatosAvatarUpload", function (e) {
            $("#uploadImageModal").find(".catgadorPromo").show();
            var ext = $(this)[0].files[0].name.split('.').pop().toLowerCase();

            if ($.inArray(ext, app.main.extensiones_permitidas_fotos) == -1) {
                //mostramos mensaje
                $("#modalOK").modal("show");
                $("#myModalLabelOK").text("Aviso");
                $("#modalOK").find(".modal-body").html(TEXTOS.T76);
                console.log("no estan permitidas el avatar");
                $("#uploadImageModal").find(".catgadorPromo").hide();
            } else {
                //mostramos el pop-up
                $("#uploadImageModal").modal("show", {
                    backdrop: false,
                    keyboard: false
                });

                //hay que esperar a que salga la modal para representar la imagen en el canvas
                //de lo contrario no funciona bien
                $('#uploadImageModal').off().on('shown.bs.modal', function (ev) {

                    try {
                        var file = e.target.files[0];
                        canvasResize(file, {
                            width: 800,
                            height: 0,
                            crop: false,
                            quality: 95,
                            //rotate: 90,
                            callback: function (data) {
                                app.dibujo.imagenSubida = data;
                                $("#uploadImageModal").find(".catgadorPromo").hide();
                                app.dibujo.pintarCanvas(app.dibujo.imagenSubida, 'imageUserHolder', 280, 280, true, 1);
                                //app.continuarSubidaFoto(data);
                            }
                        });
                    } catch (err) {
                        //subida tradicional para obtener un bytearray. IE9
                        //app.utils.previewImgFile($(this), function (img, nombre) {

                        app.ajax.subirFicheroForm(function (data) {
                            console.log("data:" + data);
                            app.dibujo.imagenSubida = data.data;
                            $("#uploadImageModal").find(".catgadorPromo").hide();
                            app.dibujo.pintarCanvas(app.dibujo.imagenSubida, 'imageUserHolder', 280, 280, true, 1);
                        });
                        //});
                    }
                });
            }
        });
        //
        $("#uploadImageModal .btGuardar").off().on("click", function (e) {
            //guarda la imagen
            app.dibujo.getImagenFinal(1);
        });
        $("#uploadImageModal .btCancelar").off().on("click", function (e) {
            //cierra la modal y borra foto
            $("#uploadImageModal").modal("hide");
            try {
                //app.dibujo.canvasFabric.clear();

            } catch (err) {
            }

        });


        $(document).on("change", ".subirTitularidadBancaria", function () {
            app.utils.trace("upload titularidadBancaria");
            $(".pnSubirTitularidadBancaria").hide();
            $(".cargarTitularidadBancaria").show();
            app.utils.previewImgFile($(this), function (img, nombre) {
                //$(".imgAvatarProp").attr('src', img);
                app.utils.trace("titularidadBancaria");
                app.ajax.subirFichero("titularidadBancaria", nombre, img,
                        function () {
                            $(".subirTitularidadBancaria").parent().parent().parent().find(".file-caption-name").html("");
                            var nombreDoc = '<span class="glyphicon glyphicon-file kv-caption-icon">' + nombre + '</span>';
                            $(".subirTitularidadBancaria").parent().parent().parent().find(".file-caption-name").append(nombreDoc);
                            $(".pnSubirTitularidadBancaria").show();
                            $(".cargarTitularidadBancaria").hide();
                        }, function () {
                    $(".subirTitularidadBancaria").parent().parent().parent().find(".file-caption-name").html("");
                    $(".subirTitularidadBancaria").val("");
                });
            });
        });


        
        $(document).keypress(function(e) {
            if(e.which == 13) {
                if($("#modalPromo").is(":visible")){
                    $("#ContentPlaceHolder1_lnkGuardarPromocion").click();
                }
            }
        });





        $(".btnInversionesActivas").off("").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();

            app.navegacion.navegarASeccion("listado_inversiones.aspx");
        });





        $(".btnGoHome").off("").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();

            app.navegacion.navegarASeccion("Default.aspx")
        });

        $(".btnGuardarPaso3PedirPrestamo").off("").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();

            $(".secPedirPrestamoPaso3").hide();
            $(".secPedirPrestamoPaso4").fadeIn();
            $($(".bs-wizard-step")[3]).addClass("actual");
            $("html, body").animate({scrollTop: 0}, 1000);
        });




        $(document).on("click", ".removeAvatar", function (e) {
            //$(".removeAvatar").off("").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();

            app.visual.eliminarAvatar($(this));
        });





        $(".nuevaValoracio .valoracion .icon-star-empty").hover(
                function () {
                    $(".nuevaValoracio .valoracion .icon-star-empty").removeClass("active");
                    $(this).addClass("active");
                    $(this).prevAll().addClass("active");
                    app.utils.trace("hover")
//                function () {
//                    $(".nuevaValoracio .valoracion .icon-star-empty").removeClass("active");
//                    app.utils.trace("blur");
//                };
                });

        $(".nuevaValoracio .valoracion .icon-star-empty").on("mouseleave", function () {
            $(".nuevaValoracio .valoracion .icon-star-empty").each(function () {
                if (!$(this).hasClass("click")) {
                    $(this).removeClass("active");
                    app.utils.trace("blur");
                } else {
                    $(this).addClass("active");
                }
            });
        });

        $(".nuevaValoracio .valoracion .icon-star-empty").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();


            $(".nuevaValoracio .valoracion .icon-star-empty").removeClass("active click");
            $(this).addClass("active click");
            $(this).prevAll().addClass("active click");
            var valor = $(this).attr("data-id");
            $("#ContentPlaceHolder1_ctrlValoracionesEmpresas_hfValoracionPuntuacion").val(valor);

            $(".nuevaValoracio .valoracion .icon-star-empty").each(function () {
                var val = $(this).attr("data-id");
                if (val <= valor) {
                    $(this).addClass("active");
                }
            });
        });

        $(document).on("click", ".btnEleminarInversion", function (e) {
            //$(".btnEleminarInversion").off().on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).closest(".bl_comparInversion").hide('slow', function () {
                $(this).closest(".bl_comparInversion").remove();
                var codigo = $(this).attr("data-codigo");

                $(".btnCompararInversion[data-codigo='" + codigo + "']").removeClass("comparado");
                app.visual.mostrarBeneficio();
            });


        });




        $(document).on("click", ".close", function () {
            $(".cookies-alert").fadeOut();
            $.cookie('zstcCookie', 'zstcCookie', { path: '/', expires: 3650 });

        });
    }
};

app.navegacion = {
    navegarASeccion: function (queSeccion) {
        window.location.href = queSeccion;
    }
};

app.ajax = {
    webService: function (methodWS, datos, success, error) {

        // app.utils.trace("**AJAX**" + methodWS + "**", datos);
        $.ajax({
            type: 'POST',
            dataType: "json",
            data: datos,
            //url: app.main.site_URL + 'ws.asmx/' + methodWS,
            url: app.main.site_URL + '/' + methodWS,
            success: function (data) {
                if (data.result.toLowerCase() == "ok") {
                    if (!!success) {
                        success(data);
                        app.utils.trace("ok");
                    }
                } else {
                    if (!!error) {
                        //error(data);
                        app.utils.tratarError(TEXTOS.T34, app.ajax.logOut);
                        app.utils.trace("ok");
                    }
                }
            },
            error: function (e) {
                app.utils.trace("error");
                //if (methodWS == "recuperarPassword") {
                //    $("#bloqueAvisoOlvidar p").html(TEXTOS.T3);
                //    $("#bloqueAvisoOlvidar").fadeIn();
                //} else {
                //    app.utils.tratarError(TEXTOS.T34, app.ajax.logOut);
                //}
            },
            xhr: function ()
            {
                var xhr = new window.XMLHttpRequest();
                //Upload progress
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with upload progress
                        console.log("up:" + percentComplete);
                    }
                }, false);
                //Download progress
                xhr.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with download progress
                        console.log("dl:" + percentComplete);
                    }
                }, false);
                return xhr;
            }
        });
    },


}
//    registrarUsuario: function (Tipo, Email,Password, Sexo, Nombre,Apellidos, Alias, OcultarNombre,FechaNacimiento,Ocupacion,  recibir) {
//        var datos = {"tipo": Tipo,"email": Email,"password": Password,"sexo": Sexo,"nombre": Nombre, "apellidos": Apellidos ,"alias":Alias, "ocultarNombre": OcultarNombre, "fechaNacimiento": FechaNacimiento ,"ocupacion": Ocupacion,"recibirInfo": recibir};
//        app.ajax.webService("registrarUsuario", datos,
//                  function (data) {
//                      //success
//                      //app.visual.precargaSoporteOnOff($(".containerAllPosts"), false);
//                      app.main.user.id = data.idUser;
//                      app.navegacion.navegarASeccion("completar_registro.aspx");
//                  },
//                  function (data) {
//                      //error
//                      //app.visual.precargaSoporteOnOff($(".containerAllPosts"), false);
//                      app.utils.tratarError(data.data);
//                  });



app.visual = {
	checkFormInvestment: function() {
		var correctForm = true;
		var result;
		var amount = $(".selectedInvestmentOption").val();				// value in €
		var min = $(".selectedInvestmentOption").prop('min');			// value in €
		var max = $(".selectedInvestmentOption").prop('max');			// value in €
		var step = $(".selectedInvestmentOption").prop('step');		// value in €
		var investmentOption = $(".selectedInvestmentOption").attr('option');
		
		if ( !(result = app.utils.checkAmountMultiple(amount, min, max, step)) == true) {
			$(".amountToInvest"+investmentOption).removeClass("input-border");
			$(".amountToInvest"+investmentOption).addClass("redBorder");
		    $(".ErrorAmount"+investmentOption).find(".errorMessage").html(TEXTOS.T85);
		    $(".ErrorAmount"+investmentOption).fadeIn();
			correctForm = false;
		}
		return correctForm;
	},

	
    checkFormLogin: function () {
        var correctForm = false;
        var campoVacio = false;
        var email = $(".txbEmailLogin").val();
        var contrasena = $(".txbPasswordLogin").val();

		$(".errorInputMessage").hide();		
        $(".formLoginAceder input").removeClass("redBorder");

        $(".formLoginAceder input:not([type=submit],:hidden)").each(function () {
            if ($(this).val() == "") {
                $(this).addClass("redBorder")
                app.utils.trace(this);
                app.utils.sacarMensajeError(true, ".errorInputMessage", TEXTOS.T01);	
                campoVacio = true;
                $("._btnLoginUser").show();
                $("._cargandoLoginUser").hide();
            }
        })
		
        if (!campoVacio) {
            if (app.utils.validoEmail(email)) {
                correctForm = true;
            } else {
                $(".txbEmailLogin").addClass("redBorder");
                app.utils.sacarMensajeError(true, ".errorInputMessage", TEXTOS.T02);
                $("._btnLoginUser").show();
                $("._cargandoLoginUser").hide();
            }
        }
        return correctForm;
    },
	
	
    revisarNewPasswords: function () {
        var correctForm = false;
        var campoVacio = false;
        var password = $(".newPassword").val();
        var password1 = $(".repeatNewPassword").val();
		
        $(".formChangePasswords input").removeClass("redBorder");
		$(".errorInputMessage").hide();	
        app.utils.sacarMensajeError(false, ".mensajeAviso");

        $(".formChangePasswords input:not([type=submit])").each(function () {
            if ($(this).val() == "") {
                $(this).addClass("redBorder")
                app.utils.sacarMensajeError(true, ".mensajeAviso", TEXTOS.T01);
                campoVacio = true;
            }
        })
        if (!campoVacio) {
            if (app.utils.checkPassword(password))
            {
                if (password == password1)
                {
                    correctForm = true;
                }
                else {
                    $(".newPassword").addClass("redBorder");
                    $(".repeatNewPassword").addClass("redBorder");
                    $(".ErrorCambiarPassword").find(".errorMessage").html(TEXTOS.T03);
                    $(".ErrorCambiarPassword").fadeIn();
                    correctForm = false;
                    //app.utils.sacarMensajeError(true, ".mensajeAviso", TEXTOS.T03);
                    //$(".repitNuevaContrasena").addClass("redBorder");
                    //$(".nuevaContrasena").addClass("redBorder");
                }
            } else {
                $(".repeatNewPassword").addClass("redBorder");
                $(".ErrorCambiarPassword").find(".errorMessage").html(TEXTOS.T16);
                $(".ErrorCambiarPassword").fadeIn();
                //app.utils.sacarMensajeError(true, ".mensajeAviso", TEXTOS.T08);
                //$(".nuevaContrasena").addClass("redBorder");
            }
        }

        return correctForm;
    },
	
	
    checkFormRecoverPassword: function () {
        var correctForm = false;
		var email = $(".recoverEmail").val();
 
        $(".recoverEmail").removeClass("redBorder");
        $(".errorInputMessage").hide();

        if (email != "") {
            if (app.utils.validoEmail(email)) {
                correctForm = true;
            } else {
                //el correo introducido no es valido
                $(".recoverEmail").addClass("redBorder");
                app.utils.sacarMensajeError(true, ".ErrorRecoverPassword", TEXTOS.T02);
            }
        } else {
            //el campo correo esta vacio
            $(".recoverEmail").addClass("redBorder");
            app.utils.sacarMensajeError(true, ".ErrorRecoverPassword", TEXTOS.T01);
        }
		app.utils.trace("checkFormRecoverPassword: " + correctForm);
        return correctForm;
    },


    checkFormRegistration: function () {
        var correctForm = true;
	
        $("#ContentPlaceHolder1_btGuardarDatosPersonales").hide();	// CHECK UP
        $(".guardarDatosPersonales").show();				//		 CHECKUP

        var email = $(".userName").val();
        var password1 = $(".password1").val();	
        var password2 = $(".passwordConfirm").val();
        var name = $(".investorName").val().trim();
        var surnames = $(".investorSurnames").val().trim();
        var dateOfBirth = $(".investorDateOfBirth").val();
		var dni = $(".investorDni").val();
        var telephone = $(".investorTelephone").val();
		var isCheckedregisterCompanydata = $(".registerShowCompanyData").is(':checked');
		var isCheckedPrivacyPolicy = $("#ContentPlaceHolder_privacyPolicy").is(':checked');
								
		
        $(".errorInputMessage").hide();
        $(".editDatosPersonales input").removeClass("redBorder");

		if (email == "") {		  
            $(".userName").addClass("redBorder");		
            $(".ErrorEmailRegistration").find(".errorMessage").html(TEXTOS.T15);
            $(".ErrorEmailRegistration").fadeIn();
            correctForm = false;	
        } else {
            if (!app.utils.validoEmail(email)) {
                $(".userName").addClass("redBorder");
                $(".ErrorEmailRegistration").find(".errorMessage").html(TEXTOS.T02);
                $(".ErrorEmailRegistration").fadeIn();
                correctForm = false;
            }
        }        
  
        if (password1 == "" && password2 == "") {
            $(".password1").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T74);
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        }
        else if (password1 != "" && password2 == "") {
            $(".passwordConfirm").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T74);
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        }
        else if (password1 == "" && password2 != "") {
            $(".password1").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T73);
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        }
        else if (password1 != "" && password2 != "") {
            if (!app.utils.checkPassword(password2)) {
                $(".password1").addClass("redBorder");
                $(".passwordConfirm").addClass("redBorder");				
                $(".ErrorPasswordConfirm").find(".errorMessage").html(TEXTOS.T08);
                $(".ErrorPasswordConfirm").fadeIn();
                correctForm = false;
            }
        }

        if (name == "") {
            $(".investorName").addClass("redBorder");
            $(".ErrorName").find(".errorMessage").html(TEXTOS.T19);
            $(".ErrorName").fadeIn();
            correctForm = false;
        }

        if (surnames == "") {
            $(".investorSurnames").addClass("redBorder");
            $(".ErrorSurnames").find(".errorMessage").html(TEXTOS.T20);
            $(".ErrorSurnames").fadeIn();
            correctForm = false;
        }

        if (dateOfBirth == "") {
            $(".investorDateOfBirth").addClass("redBorder");
            $(".ErrorDateOfBirth").find(".errorMessage").html(TEXTOS.T22);
            $(".ErrorDateOfBirth").fadeIn();
            correctForm = false;
        } else {
            if (!app.utils.isValidDate(dateOfBirth)) {
                $(".investorDateOfBirth").addClass("redBorder");
                $(".ErrorDateOfBirth").find(".errorMessage").html(TEXTOS.T05);
                $(".ErrorDateOfBirth").fadeIn();
                correctForm = false;
            } else {
                if (!app.utils.validateAge(dateOfBirth)) {
                    $(".investorDateOfBirth").addClass("redBorder");
                    $(".ErrorDateOfBirth").find(".errorMessage").html(TEXTOS.T09);
                    $(".ErrorDateOfBirth").fadeIn();
                    correctForm = false;
                }
            }
        }

        if (dni == "") {
            $(".investorDni").addClass("redBorder");
            $(".ErrorDni").find(".errorMessage").html(TEXTOS.T38);
            $(".ErrorDni").fadeIn();
			correctForm = false;

        } else {
            if (!app.utils.NIE_valid(dni)) {
                $(".investorDni").addClass("redBorder");
                $(".ErrorDni").find(".errorMessage").html(TEXTOS.T11);
                $(".ErrorDni").fadeIn();
                correctForm = false;
            }
        }        
        
        if (telephone == "") {
            $(".investorTelephone").addClass("redBorder");
            $(".ErrorTelephone").find(".errorMessage").html(TEXTOS.T35);
            $(".ErrorTelephone").fadeIn();
            correctForm = false;
        } else {
            if (!app.utils.checkPhoneNumber(telephone)) {
                $(".investorTelephone").addClass("redBorder");
                $(".ErrorTelephone").find(".errorMessage").html(TEXTOS.T10);
                $(".ErrorTelephone").fadeIn();
                correctForm = false;
            }
        }

	    var visible = $(".companyData").is(":visible"); 
        if ($(".companyData").is(":visible")) {
			var companyName = $(".investorCompanyName").val();
			var companyId = $(".investorCompanyId").val(); 
			app.utils.trace("Company data selected " + correctForm);           
			if (companyName == "") {
                $(".investorCompanyName").addClass("redBorder");
                $(".ErrorCompanyName").find(".errorMessage").html(TEXTOS.T25);
                $(".ErrorCompanyName").fadeIn();
                correctForm = false;
            }
            
            if (companyId == "") {
                $(".investorCompanyId").addClass("redBorder");
                $(".ErrorCompanyId").find(".errorMessage").html(TEXTOS.T28);
                $(".ErrorCompanyId").fadeIn();
                correctForm = false;
            } else {
                if (!app.utils.checkCIF(companyId)) {
                    $(".investorCompanyId").addClass("redBorder");
                    $(".ErrorCompanyId").find(".errorMessage").html(TEXTOS.T24);
                    $(".ErrorCompanyId").fadeIn();
                    correctForm = false;
                }
            }
        }

		if (isCheckedPrivacyPolicy == false) {
			$(".ErrorPrivacyPolicy").find(".errorMessage").html(TEXTOS.T06);
			$(".ErrorPrivacyPolicy").fadeIn();
            correctForm = false;
		}
		
        if (correctForm == false)
        {
            $("#ContentPlaceHolder1_btGuardarDatosPersonales").show();
            $(".guardarDatosPersonales").hide();
        }
        
        return correctForm;
    },


    checkFormPromotionCode: function () {
        var correctForm = true;
		var promotionCode = $(".promoCode").val();	

        $(".errorInputMessage").hide();
        $(".promoCode input").removeClass("redBorder");

		if (promotionCode == "" ) {			
            $(".promoCode").addClass("redBorder");
            $(".ErrorPromotionCode").find(".errorMessage").html(TEXTOS.T88);
			$(".ErrorPromotionCode").fadeIn();			
            correctForm = false;
		}	
		return correctForm;
	},
	
	
    checkFormPublicDoubt: function () { 
        var correctForm = true;
		var userText = $(".textarea-extend").val();
		var email = $(".receiverEmail").val();
		
        $(".errorInputMessage").hide();
        $(".receiverEmail").removeClass("redBorder");
        $(".textarea-extend").removeClass("redBorder");

		if (email == "") {		  
            $(".receiverEmail").addClass("redBorder");		
            $(".ErrorEmail").find(".errorMessage").html(TEXTOS.T15);
            $(".ErrorEmail").fadeIn();
            correctForm = false;	
        } else {
            if (!app.utils.validoEmail(email)) {
                $(".receiverEmail").addClass("redBorder");
                $(".ErrorEmail").find(".errorMessage").html(TEXTOS.T02);
                $(".ErrorEmail").fadeIn();
                correctForm = false;
            }
        } 
		if (userText == "" ) {
            $(".textarea-extend").addClass("redBorder");
            $(".ErrorTextDoubt").find(".errorMessage").html(TEXTOS.T84);
            $(".ErrorTextDoubt").fadeIn();
            correctForm = false;
		} else {
            if (userText.length > 300) {
            $(".textarea-extend").addClass("redBorder");
            $(".ErrorTextDoubt").find(".errorMessage").html(TEXTOS.T89);
            $(".ErrorTextDoubt").fadeIn();
                correctForm = false;
            }				
		}	
		return correctForm;
	},									


    checkFormCreateInvitation: function () { 
        var correctForm = true;
		var email = $(".receiverEmail").val();
		var name = $(".invitationName").val();
		var surnames = $(".invitationSurnames").val();		
		var userText = $(".textarea-extend").val();
		
        $(".errorInputMessage").hide();
        $(".textarea-extend").removeClass("redBorder");
        $(".invitationData input").removeClass("redBorder");

		if (email == "") {
            $(".receiverEmail").addClass("redBorder");		
            $(".ErrorEmail").find(".errorMessage").html(TEXTOS.T15);
            $(".ErrorEmail").fadeIn();
            correctForm = false;	
        } else {
            if (!app.utils.validoEmail(email)) {
                $(".receiverEmail").addClass("redBorder");
                $(".ErrorEmail").find(".errorMessage").html(TEXTOS.T02);
                $(".ErrorEmail").fadeIn();
                correctForm = false;
            }
        } 
		if (userText == "" ) {
            $(".textarea-extend").addClass("redBorder");
            $(".ErrorTextMessage").find(".errorMessage").html(TEXTOS.T94);
            $(".ErrorTextMessage").fadeIn();
            correctForm = false;
		} else {
            if (userText.length > 300) {
            $(".textarea-extend").addClass("redBorder");
            $(".ErrorTextMessage").find(".errorMessage").html(TEXTOS.T89);
            $(".ErrorTextMessage").fadeIn();
                correctForm = false;
            }				
		}	
        if (name == "") {
            $(".invitationName").addClass("redBorder");
            $(".ErrorInvitationName").find(".errorMessage").html(TEXTOS.T92);
            $(".ErrorInvitationName").fadeIn();
            correctForm = false;
        }
        if (surnames == "") {
            $(".invitationSurnames").addClass("redBorder");
            $(".ErrorInvitationSurnames").find(".errorMessage").html(TEXTOS.T93);
            $(".ErrorInvitationSurnames").fadeIn();
            correctForm = false;
        }		
		return correctForm;
	},
			
									
    checkFormUserDataModification: function () {
//app.utils.trace("Enter checkFormuserDataModification");
        var correctForm = true;
		
        $("#ContentPlaceHolder1_btGuardarDatosPersonales").hide();	// CHECK UP
        $(".guardarDatosPersonales").show();				//		 CHECKUP

        var password1 = $(".password1").val();	
        var password2 = $(".passwordConfirm").val();
        var name = $(".investorName").val().trim();
        var surnames = $(".investorSurnames").val();
        var dateOfBirth = $(".investorDateOfBirth").val();
		var dni = $(".investorDni").val();
        var telephone = $(".investorTelephone").val();
		var isCheckedregisterCompanydata = $(".registerShowCompanyData").is(':checked');

        $(".errorInputMessage").hide();
        $(".editDatosPersonales input").removeClass("redBorder");
			
		if (password1 != "" && password2 == "") {
            $(".passwordConfirm").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T74);
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        }
        else if (password1 == "" && password2 != "") {
            $(".password1").addClass("redBorder");
            $(".ErrorPassword").find(".errorMessage").html(TEXTOS.T73);
            $(".ErrorPassword").fadeIn();
            correctForm = false;
        }
        else if (password1 != "" && password2 != "") {
            if (!app.utils.checkPassword(password2)) {
                $(".password1").addClass("redBorder");
                $(".passwordConfirm").addClass("redBorder");				
                $(".ErrorPasswordConfirm").find(".errorMessage").html(TEXTOS.T08);
                $(".ErrorPasswordConfirm").fadeIn();
                correctForm = false;
            }
        }

        if (name == "") {
            $(".investorName").addClass("redBorder");
            $(".ErrorName").find(".errorMessage").html(TEXTOS.T19);
            $(".ErrorName").fadeIn();
            correctForm = false;
        }

        if (surnames == "") {
            $(".investorSurnames").addClass("redBorder");
            $(".ErrorSurnames").find(".errorMessage").html(TEXTOS.T20);
            $(".ErrorSurnames").fadeIn();
            correctForm = false;
        }

        if (dateOfBirth == "") {
            $(".investorDateOfBirth").addClass("redBorder");
            $(".ErrorDateOfBirth").find(".errorMessage").html(TEXTOS.T22);
            $(".ErrorDateOfBirth").fadeIn();
            correctForm = false;
        } else {
            if (!app.utils.isValidDate(dateOfBirth)) {
                $(".investorDateOfBirth").addClass("redBorder");
                $(".ErrorDateOfBirth").find(".errorMessage").html(TEXTOS.T05);
                $(".ErrorDateOfBirth").fadeIn();
                correctForm = false;
            } else {
                if (!app.utils.validateAge(dateOfBirth)) {
                    $(".investorDateOfBirth").addClass("redBorder");
                    $(".ErrorDateOfBirth").find(".errorMessage").html(TEXTOS.T09);
                    $(".ErrorDateOfBirth").fadeIn();
                    correctForm = false;
                }
            }
        }

        if (dni == "") {
            $(".investorDni").addClass("redBorder");
            $(".ErrorDni").find(".errorMessage").html(TEXTOS.T38);
            $(".ErrorDni").fadeIn();
			correctForm = false;

        } else {
            if (!app.utils.NIE_valid(dni)) {
                $(".investorDni").addClass("redBorder");
                $(".ErrorDni").find(".errorMessage").html(TEXTOS.T11);
                $(".ErrorDni").fadeIn();
                correctForm = false;
            }
        }        
        
        if (telephone == "") {
            $(".investorTelephone").addClass("redBorder");
            $(".ErrorTelephone").find(".errorMessage").html(TEXTOS.T35);
            $(".ErrorTelephone").fadeIn();
            correctForm = false;
        } else {
            if (!app.utils.checkPhoneNumber(telephone)) {
                $(".investorTelephone").addClass("redBorder");
                $(".ErrorTelephone").find(".errorMessage").html(TEXTOS.T10);
                $(".ErrorTelephone").fadeIn();
                correctForm = false;
            }
        }

	    var visible = $(".companyData").is(":visible"); 

        if ($(".companyData").is(":visible")) {
			var companyName = $(".investorCompanyName").val();
			var companyId = $(".investorCompanyId").val();            
			if (companyName == "") {
                $(".investorCompanyName").addClass("redBorder");
                $(".ErrorCompanyName").find(".errorMessage").html(TEXTOS.T25);
                $(".ErrorCompanyName").fadeIn();
                correctForm = false;
            }
            
            if (companyId == "") {
                $(".investorCompanyId").addClass("redBorder");
                $(".ErrorCompanyId").find(".errorMessage").html(TEXTOS.T28);
                $(".ErrorCompanyId").fadeIn();
                correctForm = false;
            } else {
                if (!app.utils.checkCIF(companyId)) {
                    $(".investorCompanyId").addClass("redBorder");
                    $(".ErrorCompanyId").find(".errorMessage").html(TEXTOS.T24);
                    $(".ErrorCompanyId").fadeIn();
                    correctForm = false;
                }
            }
        }

        if (correctForm == false)
        {
            $("#ContentPlaceHolder1_btGuardarDatosPersonales").show();
            $(".guardarDatosPersonales").hide();
        }       
        return correctForm;
    },
};




app.utils = {
    mostrarPopUp: function (mostrar, message) {
        if (mostrar) {
            $(".textPopUp").text(message);
            $(".popUp").fadeIn();
        } else {
            $(".popUp").fadeOut();
        }

    },

	fechaEnIntervalo: function (strFechaInicioFiltro, strFechaFinFiltro, fecha)
    {
        var fechaInicioFiltro = NaN;
        var fechaFinFiltro = NaN;
        var devolver = false;
        return true;

        var strFechaInicioFiltro = strFechaInicioFiltro.replace(/-/g, '/');
        var strFechaFinFiltro = strFechaFinFiltro.replace(/-/g, '/');
        var partsHoraInicio = strFechaInicioFiltro.split("/");
        var partsHoraFin = strFechaFinFiltro.split("/");

        if (partsHoraInicio.length == 3) {
            fechaInicioFiltro = new Date(Number(partsHoraInicio[2]), Number(partsHoraInicio[1]) - 1, Number(partsHoraInicio[0]));
            fechaInicioFiltro = fechaInicioFiltro.setHours(0, 0, 0, 0);
        }
        else {
            fechaInicioFiltro = NaN;
        }
        if (partsHoraFin.length == 3) {
            fechaFinFiltro = new Date(Number(partsHoraFin[2]), Number(partsHoraFin[1]) - 1, Number(partsHoraFin[0]));
            fechaFinFiltro = fechaFinFiltro.setHours(23, 59, 59, 999);
        }
        else {
            fechaFin = NaN;
        }

        if (isNaN(fechaInicioFiltro) && isNaN(fechaFinFiltro)) {
            //las dos fecha del filtro están en blanco, entonces el filtro no aplica y hay que devolver cualquier registro.
            devolver = true;
        }
        if (!isNaN(fechaInicioFiltro) && !isNaN(fechaFinFiltro)) {
            //los dos campos fecha del filtro tienen valor, por tanto solo devolveré aquellos registros que tengan su fecha entre las dos de los filtros.
            if (fechaInicioFiltro <= fecha && fecha <= fechaFinFiltro) {
                devolver = true;
            }
        }
        if (isNaN(fechaInicioFiltro) && !isNaN(fechaFinFiltro)) {
            //el campo fechaInicioFiltro NO es fecha y el campo Fecha Fin Si, por tanto solo devolveré aquellos registros que tengan su fecha anterior a la fechaFinFiltro
            if (fecha <= fechaFinFiltro) {
                devolver = true;
            }
        }
        if (!isNaN(fechaInicioFiltro) && isNaN(fechaFinFiltro)) {
            //el campo fechaInicioFiltro SI es fecha y el campo Fecha Fin NO, por tanto solo devolveré aquellos registros que tengan su fecha posterior a la fechaInicioFiltro
            if (fechaInicioFiltro <= fecha) {
                devolver = true;
            }
        }

        return devolver;
    },
    sacarMensajeError: function (mostrar, inputCampo, mensaje) {
        var $bloque = $(inputCampo).parent();
        if (mostrar) {
            if (!!mensaje) {
                $bloque.find(".errorMessage").html(mensaje);
                $bloque.find(".errorInputMessage").fadeIn();
            }
        } else {
            $bloque.find(".errorInputMessage").fadeIn();
        }


    },
    sacarMensajeExito: function (bloque, mensaje) {
        $(bloque).text(mensaje).show();
    },
    formularioRegistro: function () {
        var tipo = $("#tipoPerfil option:selected").text();
        var email = $("#email").val();
        var contrasena = $("#contrasena").val();
        var repeatContrasena = $("#repeatContrasena").val();
    },
    tratarError: function (msgError, f1) {
        //abre ventana modal
        if (!!f1) {

        } else {
            f1 = null;
        }
        this.ventanaModal(TEXTOS.T0, msgError, f1, true);
    },
    notification: function (msgNotification) {
        this.ventanaModal(TEXTOS.T1, msgNotification);
    },
    ventanaModal: function (titulo, mensaje, callback, esError) {
        //muestra una ventana modal y lanza un callback al cerrarla

        if (!!mensaje) {
        } else {
            mensaje = "";
        }
        if (!!titulo) {
        } else {
            titulo = "";
        }
        var elem = $("#myModal");
        elem.addClass("fade");
        $(elem).find("#pieModal").hide();
        $(elem).find("#myModalLabel").html(titulo);
        $(elem).find("#myModalText").html(mensaje);
        //$(elem).fadeIn();

        if (!!esError) {
            //es un error. tratamos de eliminar cualquier otra ventana antes de lanzar esta
            //quitamos la animación para que se elimine directamente
            elem.off("hidden.bs.modal");
            elem.removeClass("fade");
            elem.modal("hide");
        }


        //al ocultar el modal lanzamos el callback
        elem.off("hidden.bs.modal").on("hidden.bs.modal", function () {

            if (!!callback) {
                callback();
            }
        });
        elem.modal({show: true, backdrop: true});
        /**/
    },
    isValidDate: function (dateString) {
        // First check for the pattern
        if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
            return false;

        // Parse the date parts to integers
        var parts = dateString.split("/");
        var day = parseInt(parts[0], 10);
        var month = parseInt(parts[1], 10);
        var year = parseInt(parts[2], 10);

        // Check the ranges of month and year
        if (year < 1000 || year > 3000 || month == 0 || month > 12)
            return false;

        var monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        // Adjust for leap years
        if (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
            monthLength[1] = 29;

        // Check the range of the day
        return day > 0 && day <= monthLength[month - 1];
    },
	validateAge: function(dateString) {
		var bits = dateString.split('/');   
		dateStringNew = parseInt(bits[2]) + "/" + parseInt(bits[1]) + "/" + parseInt(bits[0]);
		var today = new Date();	
		var birthDate = new Date(dateStringNew);
		var age = today.getFullYear() - birthDate.getFullYear();
		var m = today.getMonth() - birthDate.getMonth();
		if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
			age--;
		}
		if (age >= 18) {
		  return true;
		}	
		return false;
	},	
    validoEmail: function (queEmail) {
        //comprueba que el email es válido
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        //var re = /^[a-zA-Z0-9.!#$%&amp;'*+-/=?\^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
        return re.test(queEmail);
    },

// return true or false
    checkAmountMultiple: function (value, min, max, multiple) {
	var result = false;
	var minNumber = parseInt(min);
	var maxNumber = parseInt(max);

		if (value%multiple == 0) {
			if (value >= minNumber && value <= maxNumber) {
				result = true;
			}
		}
		return result;
	},	  
	
    checkPassword: function (password) {
        var strength = false;


        //if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/) && password.length >= 10 || password.match(/([a-zA-Z])/) && password.match(/([0-9])/) && password.match(/([!,%,&,@,#,$,^,*,?,_,~])/) && password.length >= 8) {
        if (password.match(/([A-Z])/) && password.match(/([a-z])/) && password.match(/([0-9])/) && password.match(/([!,%,&,@,#,$,^,*,?,_,~,.,+,-,€])/) && password.length >= 8) {
            strength = true;
        }
        return strength;
        ////if password contains both lower and uppercase characters, increase strength value
        //if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))
        ////if it has numbers and characters, increase strength value
        //if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))
        ////if it has one special character, increase strength value
        //if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))
        ////if it has two special characters, increase strength value
        //if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/))

    },
    checkPhoneNumber: function (queTel) {
        //comprueba que el telefono es válido

        var re = /^[6789]\d{8}$/;
        return re.test(queTel);
    },

    // Validar la Cuenta Corriente de un Banco 
    obtenerDigito: function (valor) {
        valores = new Array(1, 2, 4, 8, 5, 10, 9, 7, 3, 6);
        control = 0;
        for (i = 0; i <= 9; i++)
            control += parseInt(valor.charAt(i)) * valores[i];
        control = 11 - (control % 11);
        if (control == 11)
            control = 0;
        else if (control == 10)
            control = 1;
        return control;
    },
    es_url: function (str) {
        var pattern = /^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i;
        return pattern.test(str);
    },
    esEntero: function (valor) {
        cad = valor.toString();
        for (var i = 0; i < cad.length; i++) {
            var caracter = cad.charAt(i);
            if (caracter < "0" || caracter > "9")
                return false;
        }
        return true;
    },

    NIE_valid: function (valor) {
        var mensaje = "";
        error = 0;
        valor = valor.toUpperCase();
        valor = valor.replace(/^[X|x]/, "0");
        valor = valor.replace(/^[Y|y]/, "1");
        valor = valor.replace(/^[Z|z]/, "2");
        valor = valor.replace(/-/, "");
        var numInicio = parseInt(valor.charAt(0));

        if (isNaN(parseInt(numInicio)) && isFinite(numInicio)) {
            error = 1;
            mensaje = mensaje + "DNI: Formato incorrecto. Recuerda, debes rellenar con 0 hasta completar 8 dígitos.";
        } else {
            var regExp = /^([0-9]{8})[A-Za-z]{1}/;
            if (!regExp.test(valor)) {
                error = 1;
                mensaje = mensaje + "DNI: Formato incorrecto. Recuerda, debes rellenar con 0 hasta completar 8 dígitos.";
            } else {
                var input_nifLetter = valor.charAt(valor.length - 1);
                //var charIndex = parseInt(valor.substr(0, 8).substr(0, 8)) % 23;
                var miDNI = Number(valor.substr(0, 8));
                var charIndex = miDNI % 23;
                var NIF_STRING = "TRWAGMYFPDXBNJZSQVHLCKET";
                if (NIF_STRING.charAt(charIndex) != input_nifLetter) {
                    error = 1;
                    mensaje = mensaje + "DNI: Formato incorrecto. Recuerda, debes rellenar con 0 hasta completar 8 dígitos.";
                }
            }
        }

        if (error == 1) {
            return false;
        }
        else {
            return true;
        }

    },
    checkCIF: function (dato)
    {
        var valor = dato;
        var longitud = valor.length;
        if (longitud == 9)
        {
            dig4 = valor.substr(2, 1)
            dig6 = valor.substr(4, 1)
            dig8 = valor.substr(6, 1)
            dig3 = valor.substr(1, 1)
            dig5 = valor.substr(3, 1)
            dig7 = valor.substr(5, 1)
            dig9 = valor.substr(7, 1)
            dig10 = valor.substr(8, 1)

            dig4 = dig4 - 0
            dig6 = dig6 - 0
            dig8 = dig8 - 0
            dig3 = dig3 - 0
            dig5 = dig5 - 0
            dig7 = dig7 - 0
            dig9 = dig9 - 0
            if (isFinite(dig10))
            {
                dig10 = dig10 - 0
            }


            S1 = dig4 + dig6 + dig8
            S2 = 0
            Resultador = dig3 * 2

            if (Resultador < 10)
            {
                S2 = S2 + Resultador
            }
            else
            {
                Resultador = Resultador.toString()
                r1 = Resultador.substr(0, 1)
                r2 = Resultador.substr(1, 1)
                r1 = r1 - 0
                r2 = r2 - 0
                S2 = S2 + (r1 + r2)
            }


            Resultador = dig5 * 2

            if (Resultador < 10)
            {
                S2 = S2 + Resultador
            }
            else
            {
                Resultador = Resultador.toString()
                r1 = Resultador.substr(0, 1)
                r2 = Resultador.substr(1, 1)
                r1 = r1 - 0
                r2 = r2 - 0
                S2 = S2 + (r1 + r2)
            }


            Resultador = dig7 * 2

            if (Resultador < 10)
            {
                S2 = S2 + Resultador
            }
            else
            {
                Resultador = Resultador.toString()
                r1 = Resultador.substr(0, 1)
                r2 = Resultador.substr(1, 1)
                r1 = r1 - 0
                r2 = r2 - 0
                S2 = S2 + (r1 + r2)
            }

            Resultador = dig9 * 2

            if (Resultador < 10)
            {
                S2 = S2 + Resultador
            }
            else
            {
                Resultador = Resultador.toString()
                r1 = Resultador.substr(0, 1)
                r2 = Resultador.substr(1, 1)
                r1 = r1 - 0
                r2 = r2 - 0
                S2 = S2 + (r1 + r2)
            }

            S = S1 + S2

            if (S < 10)
            {
                D = 10 - S
            }
            else if (S < 20)
            {
                D = 20 - S
            }
            else if (S < 30)
            {
                D = 30 - S
            }
            else if (S < 40)

            {
                D = 40 - S
            }
            else if (S < 50)
            {
                D = 50 - S
            }
            else if (S < 60)

            {
                D = 60 - S
            }
            else if (S < 70)

            {
                D = 70 - S
            }
            else if (S < 80)
            {
                D = 80 - S
            }
            else if (S < 90)
            {
                D = 90 - S
            }
            else
            {
                D = 100 - S
            }

            if (D > 9)
            {
                D = D.toString()
                d1 = D.substr(1, 1)
                D = d1
                D = D - 0
            }

            var da = ''
            if (D == 0)
            {
                da = 'J'
            }
            if (D == 1)
            {
                da = 'A'
            }
            if (D == 2)
            {
                da = 'B'
            }
            if (D == 3)
            {
                da = 'C'
            }
            if (D == 4)
            {
                da = 'D'
            }
            if (D == 5)
            {
                da = 'E'
            }
            if (D == 6)
            {
                da = 'F'
            }
            if (D == 7)
            {
                da = 'G'
            }
            if (D == 8)
            {
                da = 'H'
            }
            if (D == 9)
            {
                da = 'I'
            }


            if (isFinite(dig10))
            {
                if (D == dig10)
                {
                    return true;
                }
            }
            else
            {
                dig10 = dig10.toString()
                dig10 = dig10.toUpperCase()
                if (da == dig10)
                {
                    return true;
                }
            }

        }
        return false;
    },
    esCodigoPostal: function (code) {
        var zip = /^(((0|1|2|3|4)\d{4})|(5(0|1|2){1}\d{3}))$/;
        return zip.test(code);
    },
    trace: function () {
        try {
            if (window.console && window.console.log)
                console.log(arguments);
        } catch (err) {

        }
    },
};


var TEXTOS = {
    T0: "ERROR",
    T01: "Ha dejado vacío algún campo, rellénalo antes de seguir.",
    T02: "La dirección de email introducida no es válido.",
    T03: "Las contraseñas no coinciden.",
 //   T04: "Debe introducir el sexo.",
    T05: "La fecha introducida no es válida.",
    T06: "Debe aceptar la política de privacidad",
    T07: "Debe aceptar el acuerdo de este servicio",
    T08: "El formato de contraseña no es valido. Su contraseña debe tener al menos 8 caracteres y contener mayúsculas y minúsculas, números y otros símbolos",
    T09: "Debe de ser mayor de 18 años",
    T10: "El teléfono introducido no es válido.",
    T11: "DNI: Formato incorrecto.",
 //   T12: "El número de cuenta bancaria introducida no es valida.",
    T13: "Si quiere cambiar su contraseña, introduce la actual y nueva",
    T14: "Indica en que va invertir este dinero",
    T15: "Por favor, introduzca una dirección de email.",
    T16: "Por favor, introduzca una contraseña válida. Su contraseña debe tener al menos 8 caracteres y contener mayúsculas y minúsculas, números y otros símbolos",
    T17: "Por favor, repite su contraseña.",
//    T18: "Por favor, seleccione su sexo.",
    T19: "Por favor, introduzca su nombre.",
    T20: "Por favor, introduzca su apellido.",
    T21: "Por favor, introduzca su alias.",
    T22: "Por favor, introduzca su fecha de nacimiento.",
//    T23: "Por favor, introduzca su ocupacion.",
    T24: "El CIF introducido no es válido.",
    T25: "Por favor, introduzca el nombre de su empresa.",
//    T26: "Por favor, introduzca el sector de su empresa.",
//    T27: "Por favor, introduzca la marca de su empresa.",
    T28: "Por favor, introduzca el CIF de su empresa.",
    T29: "Por favor, introduzca la description corta sobre ti.",
    T30: "Por favor, introduzca la description larga sobre ti.",
 //   T31: "Por favor, introduzca su direción.",
 //   T32: "Por favor, introduzca su código postal.",
 //   T33: "Por favor, introduzca su ciudad.",
 //   T34: "Por favor, introduzca su nacionalidad.",
    T35: "Por favor, introduzca su teléfono.",
 //   T36: "Por favor, introduzca el nombre de titular de la cuenta.",
 //   T37: "Por favor, introduzca el apellido de titular de la cuenta.",
    T38: "Por favor, introduzca su DNI(NIE)",
 //   T39: "Por favor, introduzca el nombre del banco.",
 //   T40: "Por favor, introduzca el numero de la cuenta.",
    T41: "Por favor, introduzca la cantidad .",
 //   T42: "Por favor, introduzca su cargo.",
 //   T43: "Por favor, introduzca su sector laboral.",
    T44: "Por favor, introduzca el nombre de su empresa.",
 //   T45: "Por favor, rellena los campos de ingresos",
 //   T46: "Por favor, rellena los campos de gastos",
    T47: "Por favor, introduzca una fecha válida.",
    T48: "Por favor, introduzca la web de la empresa.",
 //   T49: "Por favor, introduzca el horario de atención.",
 //   T50: "Por favor, suba un documento con su dni.",
 //   T51: "Por favor, suba un documento con su titularidad bancaria.",
 //   T52: "Por favor, suba un documento con sos tres últimas nominas.",
 //   T53: "Por favor, describa para que necesita el dinero.",
 //   T54: "Por favor, introduzca la fecha de validez del dni",
 //   T55: "Por favor, introduzca su estado civil",
 //   T56: "Por favor, introduzca su numero de hijos",
 //   T57: "Por favor, introduzca el número de personas que tiene a su cargo",
 //   T58: "Por favor, introduzca el tipo de vivienda donde reside",
 //   T59: "Por favor, introduzca su nivel academico",
 //   T60: "Por favor, introduzca su situación laboral",
 //   T61: "Por favor, introduzca su tipo de contrato",
 //   T62: "Por favor, introduzca la antiguedad de su contrato",
 //   T63: "Por favor, introduzca su cargo dentro de la empresa",
 //   T64: "Por favor, introduzca el sector laboral de la empresa",
    T65: "Por favor, introduzca el nombre de la empresa",
    T66: "Por favor, introduzca el importe",
 //   T67: "Por favor, introduzca el número de pagas",
 //   T68: "Por favor, introduzca el gasto",
 //   T69: "Por favor, suba un documento con su DNI",
 //   T70: "Por favor, suba un documento con su titularidad bancaria",
 //   T71: "Por favor, suba un documento con sus tres últimas nominas",
    T72: "Por favor, introduzca una cantidad mayor que cero",
    T73: "Por favor, introduzca su contraseña actual",
    T74: "Por favor, introduzca su nueva contraseña",
 //   T75: "El código postal introducido no es válido.",
 //   T76: "Ese tipo de archivo no está permitido.",
 //   T77: "Para acceder a esta sección debe de estar identificado.",
 //   T78: "La solicitud de préstamos solo está disponible para particulares.",
 //   T79: "Por favor, añade sus ingresos.",
 //   T80: "Por favor, añade sus gastos.",
 //   T81: "Debe introducir al menos dos gastos para continuar.",
 //   T82: "Debe introducir la url de la promo.",
    T83: "La url introducida no es válida.",
    T84: "Por favor, introduzca su duda.",
	T85: "Importe incorrecto, debe ser un multiple de 50€ o entre el importe mínimo y máximo.",
    T86: "El email ya esta en uso, seleccione otro.",
    T87: "Se ha enviado un email a la dirección indicada.",
    T88: "Por favor, introduzca el código de acceso.",
	T89: "El mensaje es demasiado largo.",
	T90: "Email o contraseña incorrecto",
	T91: "Una nueva contraseña ha sido enviado al email indicado",
	T92: "Por favor, introduzca el nombre",
	T93: "Por favor, introduzca los apellidos",
    T94: "Por favor, introduzca su mensaje"	
};



 