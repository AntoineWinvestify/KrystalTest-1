 

$(document).ready(function (event) {


    $('.sliderh').slider({
        tooltip: 'always',
        formatter: function (value) {
            return value + '\u20ac';
        }
    });


    $('.sliderm').slider({
        tooltip: 'always',
        formatter: function (value) {
            return value + ' meses';
        }
    });


    $('input[type=checkbox], input[type=radio]').change(function () {
        $.each($('input[type=checkbox], input[type=radio]'), function () {
            if ($(this).is(":checked")) {
                $(this).parent('label').addClass('active');
            } else {
                $(this).parent('label').removeClass('active');
            }
        });

    });


    /*$('.nav a').click(function (e) {
     e.preventDefault();
     $(this).tab('show');
     });*/


    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'es'
    });
    


    //if($('.foto-upload').exists()){		
    //	$(".foto-upload").fileinput({    
    //		initialPreview: [
    //    		"<img src='images/user.svg' class='file-preview-image img-circle masDatosImg'/>"
    //		],
    //		previewFileType: "image",
    //		showUpload: false,
    //		showCaption: false,
    //		browseClass: "btn btn-light btn-lg",
    //		removeClass: "btn btn-light error btnCloseFoto",
    //		browseIcon:"",   
    //		allowedFileTypes: ["image"],
    //		previewFileIcon: "",
    //		browseLabel: "A&ntilde;ade tu foto",
    //		removeLabel: "Eliminar"
    //	});	
    //}


    //if($('.img-upload').exists()){		
    //	$(".img-upload").fileinput({    
    //		previewFileType: "image",
    //		showUpload: false,
    //		showCaption: false,
    //		browseClass: "btn btn-light btn-lg",
    //		removeClass: "btn btn-light error btnCloseFoto",
    //		browseIcon:"",   
    //		allowedFileTypes: ["image"],
    //		previewFileIcon: "",
    //		browseLabel: "Cambiar cover",
    //		removeLabel: "Eliminar"
    //	});	
    //}


    //if($('.file-upload').exists()){		
    //	$(".file-upload").fileinput({   
    //		showPreview:false,
    //		showCaption:true,
    //		showUpload: false,
    //		browseClass: "btn btn-light btn-lg",
    //		removeClass: "btn btn-light error btnCloseFoto",
    //		browseIcon:"",
    //		allowedFileExtensions: ["txt", "pdf", "doc", "docx", "zip", "rar"],
    //		previewFileIcon: "",
    //		browseLabel: "Cambiar",
    //		removeLabel: "Eliminar"
    //	});	
    //}


    if ($('.knob').exists()) {
        $(".knob").knob({
            'width': '90%',
            'format': function (value) {
                return value + '%';
            }
        });
    }


    $('a.page-scroll').bind('click', function (event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top - 180
        }, 1000);
        event.preventDefault();
    });


    $('.carousel').carousel({interval: 5000});

});

jQuery.fn.exists = function () {
    return this.length > 0;
}
  