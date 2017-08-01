

function recargarTabla(datatable) {
   

   // TableToolsInit.sSwfPath = "media/swf/ZeroClipboard.swf";
    $(datatable).DataTable({

        responsive: true,
        bStateSave: true,
        fnStateSave: function (oSettings, oData) {
            localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
        },
        fnStateLoad: function (oSettings) {
            var data = localStorage.getItem('DataTables_' + window.location.pathname);
            return JSON.parse(data);
        },
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

    }).search('').columns().search('').draw();



}



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
  
    site_URL: window.location.protocol + "//" + window.location.host,
    avatar: {
        base64: null,
        nombre: null
    },
    //site_URL: window.location.protocol + "//" + window.location.host + window.location.pathname,
    //site_URL: "http://private-5f5185-zastac.apiary-mock.com",
    init: function () {
        this.programarBotonesGenerales();
    },
    programarBotonesGenerales: function () {

        $(document).on("change", ".subirNomina", function () {
            app.utils.trace("upload document");
            app.utils.previewImgFile($(this), function (img, nombre) {
                //$(".imgAvatarProp").attr('src', img);
                app.utils.trace("div" + this);

                app.ajax.subirFichero("nomina", nombre, img, function () {
                    $(".subirNomina").parent().parent().parent().find(".file-caption-name").html("");
                    var nombreDoc = '<span class="glyphicon glyphicon-file kv-caption-icon">' + nombre + '</span>';
                    $(".subirNomina").parent().parent().parent().find(".file-caption-name").append(nombreDoc);
                });
            });
        });

        $(document).on("change", ".subirDni", function () {
            app.utils.trace("upload dni");
            app.utils.previewImgFile($(this), function (img, nombre) {
                //$(".imgAvatarProp").attr('src', img);
                app.utils.trace("dni");
                app.ajax.subirFichero("DNI", nombre, img, function () {
                    $(".subirDni").parent().parent().parent().find(".file-caption-name").html("");
                    var nombreDoc = '<span class="glyphicon glyphicon-file kv-caption-icon">' + nombre + '</span>';
                    $(".subirDni").parent().parent().parent().find(".file-caption-name").append(nombreDoc);
                });
            });
        });

        $(document).on("change", ".subirTitularidadBancaria", function () {
            app.utils.trace("upload titularidadBancaria");
            app.utils.previewImgFile($(this), function (img, nombre) {
                //$(".imgAvatarProp").attr('src', img);
                app.utils.trace("titularidadBancaria");
                app.ajax.subirFichero("titularidadBancaria", nombre, img, function () {
                    $(".subirTitularidadBancaria").parent().parent().parent().find(".file-caption-name").html("");
                    var nombreDoc = '<span class="glyphicon glyphicon-file kv-caption-icon">' + nombre + '</span>';
                    $(".subirTitularidadBancaria").parent().parent().parent().find(".file-caption-name").append(nombreDoc);
                });
            });
        });

        $(document).on("change", ".subirContrato", function () {
            app.utils.trace("upload contrato");
            app.utils.previewImgFile($(this), function (img, nombre) {
                //$(".imgAvatarProp").attr('src', img);
                app.utils.trace("contrato");
                app.ajax.subirFichero("contrato", nombre, img, function () {
                    $(".subirDni").parent().parent().parent().find(".file-caption-name").html("");
                    var nombreDoc = '<span class="glyphicon glyphicon-file kv-caption-icon">' + nombre + '</span>';
                    $(".subirContrato").parent().parent().parent().find(".file-caption-name").append(nombreDoc);
                });
            });
        });

        $(document).on("change", ".subirTitularidadBancaria", function () {
            app.utils.trace("upload titularidadBancaria");
            app.utils.previewImgFile($(this), function (img, nombre) {
                //$(".imgAvatarProp").attr('src', img);
                app.utils.trace("titularidadBancaria");
                app.ajax.subirFichero("titularidadBancaria", nombre, img, function () {
                    $(".subirTitularidadBancaria").parent().parent().parent().find(".file-caption-name").html("");
                    var nombreDoc = '<span class="glyphicon glyphicon-file kv-caption-icon">' + nombre + '</span>';
                    $(".subirTitularidadBancaria").parent().parent().parent().find(".file-caption-name").append(nombreDoc);
                });
            });
        });
        
        $(document).on("change", ".subirDocumentoGenerico", function () {

            var documento = $(this);
            app.utils.trace("upload subirDocumentoGenerico");
            app.utils.previewImgFile($(this), function (img, nombre) {
                //$(".imgAvatarProp").attr('src', img);
                app.utils.trace("subirDocumentoGenerico");
                //app.ajax.subirFichero("subirDocumentoGenerico", nombre, img, function () {
                    documento.parent().parent().parent().find(".file-caption-name").html("");
                    var nombreDoc = '<span class="glyphicon glyphicon-file kv-caption-icon">' + nombre + '</span>';
                    documento.parent().parent().parent().find(".file-caption-name").append(nombreDoc);
                //});
            });
        });


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
            }
        });
    },
    subirFichero: function (Tipo, NombreCompleto, ficheroBase64, success, error) {

        var datos = { "tipo": Tipo, "nombre": NombreCompleto, "fichero": ficheroBase64 };
        app.ajax.webService("guardarArchivoTemp.ashx", datos,
                function (data) {
                    //success

                    if (!!success) {
                        success();
                        app.utils.trace("succeess");
                    }
                },
                function (data) {
                    app.utils.trace("error");
                    if (!!error) {
                        error();

                    }
                    //error
                    //app.visual.precargaSoporteOnOff($(".containerAllPosts"), false);
                    // app.utils.tratarError(data.data);
                });
    }
}

app.utils = {
    
    previewImgFile: function (queInput, callback) {

        //sube una imagen y muestra la preview si puede el browser
        //
        //comprobamos que no ha cancelado la ventana
        app.utils.trace("preview image");
        var $input = $(queInput);

        if ($input.val() != "") {
            try {
                if ($input[0].files && $input[0].files[0]) {
                    app.main.avatar.nombre = $input[0].files[0].name;
                    app.utils.trace($input[0].files[0].name)
                    var reader = new FileReader();
                    reader.onload = function (e) {

                        if (!!callback) {
                            callback(e.target.result, $input[0].files[0].name);
                            app.utils.trace("callback");
                            //app.main.avatar.base64 = e.target.result
                            //app.ajax.subirFichero(tipo, $input[0].files[0].name, e.target.result)
                        }
                        //else {

                        //$(soportePreview).attr('src', e.target.result);
                        // }
                    };

                    reader.readAsDataURL($input[0].files[0]);

                }
            } catch (err) {
                //navegador no compatible. IE9/IE8 ¿?
                //hay que subir la foto y luego bajarla para preview. maxsize input??
            }
        }
    },
    trace: function () {
        try {
            if (window.console && window.console.log)
                console.log(arguments);
        } catch (err) {

        }
    }
    // Validar la Cuenta Corriente de un Banco 
    
    
};


