"use strict";
const baseUrl = '/garnet/';
$(document).ready(function(){

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })


    const captcha =new Captcha($('#codigo_seguridad_imagen'), {
        width: 250,
        height: 50,
        length: 5,
        caseSensitive:true,
    });

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {

        event.preventDefault();
        event.stopPropagation();

        var guardar = true;

        if(!form.checkValidity())
            guardar = false;
        if($('#codigo_seguridad').val()){
            if(!captcha.valid($('#codigo_seguridad').val())){                
                swal({                    
                    title: "El Código de Seguridad es Incorrecto.",
                    type: "error",
                });
                guardar = false;
                captcha.refresh();
                $('#codigo_seguridad').val('');
            }
        }
        form.classList.add('was-validated');

        if(guardar){
            swal({
                title: "Esta seguro de ENVIAR LA DENUNCIA?",
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Si, Enviar!",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false
            },
            function(){
                form.submit();
            });
        }

      }, false)
    });

    $('#codigo_seguridad_refresh').on('click',function() { captcha.refresh(); });

    /* UBICACION MINERIA ILEGAL */
    $('#departamento').on('change', function() {
        $('#provincia').html('<option value="">SELECCIONE LA PROVINCIA</option>');
        $('#fk_municipio').html('<option value="">SELECCIONE EL MUNICIPIO</option>');
        if(this.value.length > 0){
            $.ajax({
                type:'POST',
                url:baseUrl+"ajax_provincias",
                data: {
                    departamento: this.value,
                },
                error: function() {
                    console.log('error ajax.')
                },
                success:function(html){
                    $('#provincia').html(html);
                }
            });
        }
    });
    $('#provincia').on('change', function() {
        $('#fk_municipio').html('<option value="">SELECCIONE EL MUNICIPIO</option>');
        if(this.value.length > 0){
            $.ajax({
                type:'POST',
                url:baseUrl+"ajax_municipios",
                data: {
                    departamento: $('#departamento').val(),
                    provincia: this.value,
                },
                error: function() {
                    console.log('error ajax.')
                },
                success:function(html){
                    $('#fk_municipio').html(html);
                }
            });
        }
    });

});