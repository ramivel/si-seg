'use strict';
$(document).ready(function () {
    $( "#aprobar" ).click(function() {
        swal({
            title: "Esta seguro de APROBAR LA ANULACIÓN?",
            //text: "You will not be able to recover this imaginary file!",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, Aprobar!",
            cancelButtonText: "No, Cancelar!",
            closeOnConfirm: false
        },
        function(isConfirm) {
            if (isConfirm) {
                $("#envio").val('APROBAR');
                $("#formulario").submit();
            }
        });        
    });
    $( "#rechazar" ).click(function() {
        swal({
            title: "Esta seguro de RECHAZAR LA ANULACIÓN?",
            //text: "You will not be able to recover this imaginary file!",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, Rechazar!",
            cancelButtonText: "No, Cancelar!",
            closeOnConfirm: false
        },
        function(isConfirm) {
            if (isConfirm) {
                $("#envio").val('RECHAZAR');
                $("#formulario").submit();
            }
        });
    });
});