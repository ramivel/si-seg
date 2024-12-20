'use strict';
$(document).ready(function () {

    $('.cambiar_estado').on('click',function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        swal({
            title: "Esta seguro de CAMBIAR EL ESTADO?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si, Cambiar!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        },
        function(){
            window.location.href = link;
        });
    });

    $('.eliminar').on('click',function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        swal({
            title: "Esta seguro de ELIMINAR?",
            text: "Se eliminará la relación de la Base de Datos.",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si, Eliminar!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        },
        function(){
            window.location.href = link;
        });
    });

    $('.aprobar_anulacion').on('click',function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        swal({
            title: "Esta seguro de APROBAR LA ANULACIÓN?",
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Si, APROBAR!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        },
        function(){
            window.location.href = link;
        });
    });

    $('.rechazar_anulacion').on('click',function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        swal({
            title: "Esta seguro de RECHAZAR LA ANULACIÓN?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-warning",
            confirmButtonText: "Si, RECHAZAR!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        },
        function(){
            window.location.href = link;
        });
    });

    $('.finalizar_cargado').on('click',function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        swal({
            title: "Esta seguro de FINALIZAR EL CARGADO DE DOCUMENTOS DIGITALES?",
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Si, FINALIZAR!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        },
        function(){
            if(!$("#verificar").val()){
                swal({
                    title: "Debe cargar todos los DOCUMENTOS DIGITALES para FINALIZAR!",
                    type: "error",
                });
            }else{
                window.location.href = link;
            }
        });
    });

    $('.recibir_tramite').on('click',function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        swal({
            title: "Esta seguro de RECIBIR EL TRÁMITE?",
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Si, RECIBIR!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        },
        function(){
            window.location.href = link;
        });
    });

    $('.rechazar-denuncia-web').on('click',function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        swal({
            title: "Esta seguro de RECHAZAR EL FORMULARIO DE DENUNCIA DE MINERÍA ILEGAL?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Si, Rechazar!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        },
        function(){
            window.location.href = link;
        });
    });

    $('.recibir-form').on('click',function(e){
        e.preventDefault();
        var form = $(this).parents('form');
        swal({
            title: "Esta seguro de RECIBIR LOS TRÁMITES SELECCIONADOS?",
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Si, RECIBIR!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        },
        function(){
            form.submit();
        });
    });

    $('.desanexar').on('click',function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        swal({
            title: "Esta seguro de DESANEXAR?",
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Si, DESANEXAR!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        },
        function(){
            window.location.href = link;
        });
    });

    $('.guardar-form').on('click',function(e){
        e.preventDefault();
        var form = $(this).parents('form');
        swal({
            title: "Esta seguro de Guardar?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, Guardar!",
            cancelButtonText: "No, Cancelar!",
            closeOnConfirm: false
        },
        function(){
            form.submit();
        });
    });

});