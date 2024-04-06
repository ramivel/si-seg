"use strict";
$(document).ready(function() {

    var $window = $(window);
    //add id to main menu for mobile menu start
    var getBody = $("body");
    var bodyClass = getBody[0].className;
    $(".main-menu").attr('id', bodyClass);
    //add id to main menu for mobile menu end

    // card js start
    $(".card-header-right .close-card").on('click', function() {
        var $this = $(this);
        $this.parents('.card').animate({
            'opacity': '0',
            '-webkit-transform': 'scale3d(.3, .3, .3)',
            'transform': 'scale3d(.3, .3, .3)'
        });

        setTimeout(function() {
            $this.parents('.card').remove();
        }, 800);
    });

    $(".card-header-right .minimize-card").on('click', function() {
        var $this = $(this);
        var port = $($this.parents('.card'));
        var card = $(port).children('.card-block').slideToggle();
        $(this).toggleClass("icon-minus").fadeIn('slow');
        $(this).toggleClass("icon-plus").fadeIn('slow');
    });
    $(".card-header-right .full-card").on('click', function() {
        var $this = $(this);
        var port = $($this.parents('.card'));
        port.toggleClass("full-card");
        $(this).toggleClass("icon-maximize");
        $(this).toggleClass("icon-minimize");
    });

    $("#more-details").on('click', function() {
        $(".more-details").slideToggle(500);
    });
    $(".mobile-options").on('click', function() {
        $(".navbar-container .nav-right").slideToggle('slow');
    });
    // card js end
    $.mCustomScrollbar.defaults.axis = "yx";
    $("#styleSelector .style-cont").slimScroll({
        setTop: "10px",
        height:"calc(100vh - 440px)",
    });
    $(".main-menu").mCustomScrollbar({
        setTop: "10px",
        setHeight: "calc(100% - 80px)",
    });
    /*chatbar js start*/

    /*chat box scroll*/
    var a = $(window).height() - 80;
    $(".main-friend-list").slimScroll({
        height: a,
        allowPageScroll: false,
        wheelStep: 5,
        color: '#1b8bf9'
    });

    // search
    $("#search-friends").on("keyup", function() {
        var g = $(this).val().toLowerCase();
        $(".userlist-box .media-body .chat-header").each(function() {
            var s = $(this).text().toLowerCase();
            $(this).closest('.userlist-box')[s.indexOf(g) !== -1 ? 'show' : 'hide']();
        });
    });

    // open chat box
    $('.displayChatbox').on('click', function() {
        var my_val = $('.pcoded').attr('vertical-placement');
        if (my_val == 'right') {
            var options = {
                direction: 'left'
            };
        } else {
            var options = {
                direction: 'right'
            };
        }
        $('.showChat').toggle('slide', options, 500);
    });


    //open friend chat
    $('.userlist-box').on('click', function() {
        var my_val = $('.pcoded').attr('vertical-placement');
        if (my_val == 'right') {
            var options = {
                direction: 'left'
            };
        } else {
            var options = {
                direction: 'right'
            };
        }
        $('.showChat_inner').toggle('slide', options, 500);
    });
    //back to main chatbar
    $('.back_chatBox').on('click', function() {
        var my_val = $('.pcoded').attr('vertical-placement');
        if (my_val == 'right') {
            var options = {
                direction: 'left'
            };
        } else {
            var options = {
                direction: 'right'
            };
        }
        $('.showChat_inner').toggle('slide', options, 500);
        $('.showChat').css('display', 'block');
    });
    // /*chatbar js end*/
    $(".search-btn").on('click', function() {
        $(".main-search").addClass('open');
        $('.main-search .form-control').animate({
            'width': '200px',
        });
    });
    $(".search-close").on('click', function() {
        $('.main-search .form-control').animate({
            'width': '0',
        });
        setTimeout(function() {
            $(".main-search").removeClass('open');
        }, 300);
    });
    $('#mobile-collapse i').addClass('icon-toggle-right');
    $('#mobile-collapse').on('click', function() {
        $('#mobile-collapse i').toggleClass('icon-toggle-right');
        $('#mobile-collapse i').toggleClass('icon-toggle-left');
    });
});
$(document).ready(function() {
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
    $('.theme-loader').fadeOut('slow', function() {
        $(this).remove();
    });
});

/* Documentos */
$(document).ready(function() {
    $(".subir_archivo").on('click', function() {
        const maxupload = 20 * 1024 * 1024;
        var idoc = $(this).data('idoc');
        var direccion = $(this).data('direccion');
        var campo = '#doc-'+idoc;
        var file = $(campo)[0].files[0];
        if(!file || file.type != 'application/pdf' || file.size > maxupload){
            swal({
                title: "Debe seleccionar el PDF del DOCUMENTO y no debe pasar de los 5MB!",
                type: "error",
            });
        }else{
            var formData = new FormData();
            formData.append('file',file);
            formData.append('idoc',idoc);
            $.ajax({
                url: direccion,
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if(response.error.length > 0){
                        swal({
                            title: "Error al subir el archivo: "+response.error,
                            type: "error",
                        });
                    }else{
                        swal({
                            title: "Se ha subido el archivo PDF correctamente.",
                            type: "success",
                        });
                        $("#label-"+response.id_doc).html("<a href='"+response.url+"' target='_blank' title='Ver Documento'><i class='feather icon-file'></i> Ver Documento</a>");
                        if(response.finalizar)
                            $("#verificar").val('true');
                    }
                }
            });
        }
        $(campo).val('');
    });
});
/* Fin Documentos * */
$(document).ready(function() {

    /* CORRESPONDENCIA EXTERNA - PERSONAS */
    $(".guardar-persona").on('click', function() {
        var validate = true;
        var m_nombres = $("#m_nombres").val();
        var m_apellidos = $("#m_apellidos").val();
        var m_documento_identidad = $("#m_documento_identidad").val();
        var m_expedido = $("#m_expedido").val();
        var m_telefonos = $("#m_telefonos").val();
        var m_email = $("#m_email").val();
        var m_direccion = $("#m_direccion").val();
        var m_institucion = $("#m_institucion").val();
        var m_cargo = $("#m_cargo").val();

        var msj_error = '<p class="text-danger error">Este campo es obligatorio.</p>';
        if(!m_nombres){
            validate = false;
            $("#error_m_nombres").html(msj_error);
        }else{
            $("#error_m_nombres").html('');
        }
        if(!m_apellidos){
            validate = false;
            $("#error_m_apellidos").html(msj_error);
        }else{
            $("#error_m_apellidos").html('');
        }
        if(!m_documento_identidad){
            validate = false;
            $("#error_m_documento_identidad").html(msj_error);
        }else{
            $("#error_m_documento_identidad").html('');
        }
        if(!m_expedido){
            validate = false;
            $("#error_m_expedido").html(msj_error);
        }else{
            $("#error_m_expedido").html('');
        }
        if(!m_telefonos){
            validate = false;
            $("#error_m_telefonos").html(msj_error);
        }else{
            $("#error_m_telefonos").html('');
        }
        if(!m_direccion){
            validate = false;
            $("#error_m_direccion").html(msj_error);
        }else{
            $("#error_m_direccion").html('');
        }
        if(validate){
            var formData = new FormData();
            formData.append('nombres',m_nombres);
            formData.append('apellidos',m_apellidos);
            formData.append('documento_identidad',m_documento_identidad);
            formData.append('expedido',m_expedido);
            formData.append('telefonos',m_telefonos);
            formData.append('email',m_email);
            formData.append('direccion',m_direccion);
            formData.append('institucion',m_institucion);
            formData.append('cargo',m_cargo);
            $.ajax({
                url: '/garnet/persona_externa/ajax_agregar',
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(resultado) {
                    if(resultado.id > 0){
                        $(".persona-externa-ajax").select2("trigger", "select", { data: resultado });
                        $('#persona-modal').modal('toggle');
                        $("#m_nombres").val('');
                        $("#m_apellidos").val('');
                        $("#m_documento_identidad").val('');
                        $("#m_expedido").val('');
                        $("#m_telefonos").val('');
                        $("#m_email").val('');
                        $("#m_direccion").val('');
                        $("#m_institucion").val('');
                        $("#m_cargo").val('');
                        swal({
                            title: "Se ha guardado correctamente la información.",
                            type: "success",
                        });
                    }else{
                        console.log(resultado);
                    }
                }
            });
        }
    });
    /* FIN CORRESPONDENCIA EXTERNA - PERSONAS */

    /* DEVOLVER TRAMITE */
    $(".devolver_correspondencia").on('click', function() {
        $("#idtra").val($(this).data('idtra'));
        $("#hr").html($(this).data('hr'));
        $("#motivo_devolucion").val('');
        $('#devolver-modal').modal('toggle');
    });
    $(".guardar-devolucion").on('click', function() {
        var validate = true;
        var idtra = $("#idtra").val();
        var motivo_devolucion = $("#motivo_devolucion").val();
        var msj_error = '<p class="text-danger error">Este campo es obligatorio.</p>';
        if(!motivo_devolucion){
            validate = false;
            $("#error_motivo_devolucion").html(msj_error);
        }else{
            $("#error_motivo_devolucion").html('');
        }
        if(validate){
            var formData = new FormData();
            formData.append('id',idtra);
            formData.append('motivo_devolucion',motivo_devolucion);
            $.ajax({
                url: '/garnet/cam/ajax_guardar_devolver',
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(resultado) {
                    if(resultado.idtra > 0){
                        $('#hr'+resultado.idtra).remove();
                        $('#devolver-modal').modal('toggle');
                        $("#motivo_devolucion").val('');
                        swal({
                            title: "Se ha guardado correctamente la información.",
                            type: "success",
                        });
                    }else{
                        console.log(resultado);
                    }
                }
            });
        }
    });
    $(".guardar-devolucion-mineria-ilegal").on('click', function() {
        var validate = true;
        var idtra = $("#idtra").val();
        var motivo_devolucion = $("#motivo_devolucion").val();
        var msj_error = '<p class="text-danger error">Este campo es obligatorio.</p>';
        if(!motivo_devolucion){
            validate = false;
            $("#error_motivo_devolucion").html(msj_error);
        }else{
            $("#error_motivo_devolucion").html('');
        }
        if(validate){
            var formData = new FormData();
            formData.append('id',idtra);
            formData.append('motivo_devolucion',motivo_devolucion);
            $.ajax({
                url: '/garnet/mineria_ilegal/ajax_guardar_devolver',
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(resultado) {
                    if(resultado.idtra > 0){
                        $('#hr'+resultado.idtra).remove();
                        $('#devolver-modal').modal('toggle');
                        $("#motivo_devolucion").val('');
                        swal({
                            title: "Se ha guardado correctamente la información.",
                            type: "success",
                        });
                    }else{
                        console.log(resultado);
                    }
                }
            });
        }
    });
    /* FIN DEVOLVER TRAMITE */

    /* CORRESPONDENCIA EXTERNA - RECIBIR */
    $(".recibir_correspondencia").on('click', function() {
        $("#idext").val($(this).data('idext'));
        $("#docext").html($(this).data('docext'));
        $("#fk_tipo_documento_externo").val('');
        $("#observacion_recepcion").val('SIN OBSERVACIONES');
        $('#recibir-correspondencia-modal').modal('toggle');
    });
    $(".guardar-recibir").on('click', function() {
        var validate = true;
        var idext = $("#idext").val();
        var fk_tipo_documento_externo = $("#fk_tipo_documento_externo").val();
        var observacion_recepcion = $("#observacion_recepcion").val();
        var msj_error = '<p class="text-danger error">Este campo es obligatorio.</p>';
        if(!fk_tipo_documento_externo){
            validate = false;
            $("#error_fk_tipo_documento_externo").html(msj_error);
        }else{
            $("#error_fk_tipo_documento_externo").html('');
        }
        if(!observacion_recepcion){
            validate = false;
            $("#error_observacion_recepcion").html(msj_error);
        }else{
            $("#error_observacion_recepcion").html('');
        }
        if(validate){
            var formData = new FormData();
            formData.append('idext',idext);
            formData.append('fk_tipo_documento_externo',fk_tipo_documento_externo);
            formData.append('observacion_recepcion',observacion_recepcion);
            $.ajax({
                url: '/garnet/correspondencia_externa/ajax_recibir',
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(resultado) {
                    if(resultado.idext > 0){
                        $('#corre'+resultado.idext).remove();
                        $('#recibir-correspondencia-modal').modal('toggle');
                        $("#fk_tipo_documento_externo").val('');
                        $("#observacion_recepcion").val('SIN OBSERVACIONES');
                        swal({
                            title: "Se ha guardado correctamente la información.",
                            type: "success",
                        });
                    }else{
                        console.log(resultado);
                    }
                }
            });
        }
    });
    /* FIN CORRESPONDENCIA EXTERNA - RECIBIR */

    /* ARCHIVADO DENUNCIA MINERIA ILEGAL */
    $(".archivado-denuncia").on('click', function() {
        $('#archivado-denuncia-modal').modal('toggle');
    });
    /* FIN ARCHIVADO DENUNCIA MINERIA ILEGAL */

    /* MINERIA ILEGAL NUEVO DENUNCIANTE */
    $(".nuevo_denunciante").on('click', function() {
        $("#d_nombres").val('');
        $("#d_apellidos").val('');
        $("#d_documento_identidad").val('');
        $("#d_expedido").val('');
        $("#d_telefonos").val('');
        $("#d_email").val('');
        $("#d_direccion").val('');
        $("#d_documento_identidad_digital").val('');
        $('#nuevo-denunciante-modal').modal('toggle');
    });
    $(".guardar-denunciante").on('click', function() {
        const maxupload = 20 * 1024 * 1024;
        var validate = true;
        var nombres = $("#d_nombres").val().trim();
        var apellidos = $("#d_apellidos").val().trim();
        var documento_identidad = $("#d_documento_identidad").val().trim();
        var expedido = $("#d_expedido").val().trim();
        var telefonos = $("#d_telefonos").val().trim();
        var email = $("#d_email").val().trim();
        var direccion = $("#d_direccion").val().trim();
        var documento_identidad_digital = $("#d_documento_identidad_digital")[0].files[0];

        var msj_error = '<p class="text-danger error">Este campo es obligatorio.</p>';
        if(!nombres){
            validate = false;
            $("#error_d_nombres").html(msj_error);
        }else{
            $("#error_d_nombres").html('');
        }
        if(!apellidos){
            validate = false;
            $("#error_d_apellidos").html(msj_error);
        }else{
            $("#error_d_apellidos").html('');
        }
        if(!documento_identidad){
            validate = false;
            $("#error_d_documento_identidad").html(msj_error);
        }else{
            $("#error_d_documento_identidad").html('');
        }
        if(!expedido){
            validate = false;
            $("#error_d_expedido").html(msj_error);
        }else{
            $("#error_d_expedido").html('');
        }
        if(!telefonos){
            validate = false;
            $("#error_d_telefonos").html(msj_error);
        }else{
            $("#error_d_telefonos").html('');
        }
        if(!direccion){
            validate = false;
            $("#error_d_direccion").html(msj_error);
        }else{
            $("#error_d_direccion").html('');
        }
        if(!documento_identidad_digital || documento_identidad_digital.type != 'application/pdf' || documento_identidad_digital.size > maxupload){
            validate = false;
            $("#error_d_documento_identidad_digital").html('<p class="text-danger error">Este campo es obligatorio, debe cumplir con el formato y tamaño máximo.</p>');
        }else{
            $("#error_d_documento_identidad_digital").html('');
        }

        if(validate){
            var formData = new FormData($('#formulario_denunciante')[0]);
            $.ajax({
                url: '/garnet/mineria_ilegal/ajax_agregar_denunciante',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                success: function(result) {
                    $('#nuevo-denunciante-modal').modal('toggle');
                    if(result.estado == 'success'){
                        var markup = "<tr id='den"+result.id+"'>"+
                            "<td class='text-center form-group'><input type='hidden' name='id_denunciantes[]' value='"+result.id+"' /><span class='messages'></span>"+result.nombres+" "+result.apellidos+"</td>"+
                            "<td class='text-center'>"+result.documento_identidad+" "+result.expedido+"</td>"+
                            "<td class='text-center'>"+result.telefonos+"</td>"+
                            "<td class='text-center'>"+result.email+"</td>"+
                            "<td class='text-center'>"+result.direccion+"</td>"+
                            "<td class='text-center'><a href='"+baseUrl+result.documento_identidad_digital+"' class='btn btn-sm btn-inverse' target='_blank' title='Ver Documento de Identidad'><i class='fa fa-file-pdf-o'></i></a> <button type='button' class='btn btn-sm btn-danger waves-effect waves-light' title='Desanexar Denunciante' onclick='desanexar_denunciante("+result.id+");'><span class='icofont icofont-ui-delete'></span></button></td>"+
                        "</tr>";
                        $("#tabla_denunciantes tbody").append(markup);
                        $(".denunciante-ajax").val('').trigger('change');
                        $("#denunciantes_anexados").val('SI');
                        swal({
                            title: "Se ha guardado correctamente la información del Denunciante.",
                            type: "success",
                        });
                    }else{
                        swal({
                            title: "No se ha guardado la información del Denunciante.",
                            type: "error",
                        });
                        console.log(result.texto);
                    }
                }
            });
        }
    });
    /* FIN MINERIA ILEGAL NUEVO DENUNCIANTE */

    /* SELECCIONAR TODOS */
    $("#seleccionar-todo").click(function () {
      if (this.checked) {
        $(".seleccionado").prop("checked", true);
      } else {
        $(".seleccionado").prop("checked", false);
      }
    });
    /* FIN SELECCIONAR TODOS */

});

/* VER/OCULTAR CONTRASEÑA */
$(document).ready(function() {
    $(".contrasena_ver").show();
    $(".contrasena_ocultar").hide();
    $('.contrasena_ver').click(function(){
        var contrasena_actual = $("#contrasena_actual");
        var nueva_contrasena = $("#nueva_contrasena");
        var confirmar_nueva_contrasena = $("#confirmar_nueva_contrasena");
        contrasena_actual.attr('type') === 'password' ? contrasena_actual.attr('type','text') : contrasena_actual.attr('type','password');
        nueva_contrasena.attr('type') === 'password' ? nueva_contrasena.attr('type','text') : nueva_contrasena.attr('type','password');
        confirmar_nueva_contrasena.attr('type') === 'password' ? confirmar_nueva_contrasena.attr('type','text') : confirmar_nueva_contrasena.attr('type','password');
        $(".contrasena_ver").hide();
        $(".contrasena_ocultar").show();
    });
    $('.contrasena_ocultar').click(function(){
        var contrasena_actual = $("#contrasena_actual");
        var nueva_contrasena = $("#nueva_contrasena");
        var confirmar_nueva_contrasena = $("#confirmar_nueva_contrasena");
        contrasena_actual.attr('type') === 'password' ? contrasena_actual.attr('type','text') : contrasena_actual.attr('type','password');
        nueva_contrasena.attr('type') === 'password' ? nueva_contrasena.attr('type','text') : nueva_contrasena.attr('type','password');
        confirmar_nueva_contrasena.attr('type') === 'password' ? confirmar_nueva_contrasena.attr('type','text') : confirmar_nueva_contrasena.attr('type','password');
        $(".contrasena_ver").show();
        $(".contrasena_ocultar").hide();
    });
});

// toggle full screen
function toggleFullScreen() {
    var a = $(window).height() - 10;
    if (!document.fullscreenElement && // alternative standard method
        !document.mozFullScreenElement && !document.webkitFullscreenElement) { // current working methods
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
    $('.full-screen').toggleClass('icon-maximize');
    $('.full-screen').toggleClass('icon-minimize');
}