'use strict';
$(document).ready(function () {
    validate.validators.presence.message = "^Este campo es obligatorio.";
    validate.extend(validate.validators.datetime, {

        parse: function (value, options) {

            return +moment.utc(value);
        },
        // Input is a unix timestamp
        format: function (value, options) {

            var format = options.dateOnly ? "DD/MM/YYYY" : "DD/MM/YYYY";
            return moment.utc(value).format(format);
        }
    });

    // These are the constraints used to validate the form
    var constraints = {
        fk_oficina:{
            presence: {
                message: "^Debe seleccionar una opción."
            },
        },
        n_correlativo_hoja_ruta: {
            presence: true,
        },
        fecha_hoja_ruta: {
            presence: true,
        },
        fk_usuario_destino: {
            presence: true,
        },
        n_correlativo_denuncia: {
            presence: true,
        },
        fecha_denuncia: {
            presence: true,
        },
        fk_tipo_denuncia: {
            presence: true,
        },
        origen_oficio: {
            presence: function(){
                if($("#fk_tipo_denuncia").val() == '3')
                    return true;
                else
                    return false;
            }
        },
        descripcion_oficio: {
            presence: function(){
                if($("#fk_tipo_denuncia").val() == '3')
                    return true;
                else
                    return false;
            }
        },
        hr_anexados: {
            presence: {
                message: "^Debe anexar al menos una Hoja de Ruta."
            },
        },
        denunciantes_anexados: {
            presence: function(){
                if($("#fk_tipo_denuncia").val() == '3')
                    return false;
                else
                    return true;
            }
        },
        departamento:{
            presence: {
                message: "^Debe seleccionar un Departamento."
            },
        },
        provincia:{
            presence: {
                message: "^Debe seleccionar una Provincia."
            },
        },
        fk_municipio:{
            presence: {
                message: "^Debe seleccionar un Municipio."
            },
        },
        comunidad_localidad: {
            presence: true,
        },
        descripcion_lugar: {
            presence: true,
        },
        fk_estado_tramite: {
            presence: {
                message: "^Debe seleccionar el Estado del Tramite."
            },
        },
        fk_estado_tramite_hijo:{
            presence: function(){
                if($('#fk_estado_tramite').children('option:selected').data('padre') == 't')
                    return true;
                else
                    return false;
            }
        },
    };

    // Hook up the form so we can prevent it from being posted
    var form = document.querySelector("form#formulario");
    form.addEventListener("submit", function (ev) {
        ev.preventDefault();
        handleFormSubmit(form);
    });

    // Hook up the inputs to validate on the fly
    var inputs = document.querySelectorAll("input, textarea, select")
    for (var i = 0; i < inputs.length; ++i) {

        inputs.item(i).addEventListener("change", function (ev) {

            var errors = validate(form, constraints) || {};
            showErrorsForInput(this, errors[this.name]);

        });
    }

    function handleFormSubmit(form, input) {
        // validate the form aainst the constraints
        var errors = validate(form, constraints);
        // then we update the form to reflect the results
        showErrors(form, errors || {});
        if (!errors) {
            showSuccess();
        }else{
            swal("Error de Validación", "Existe errores en el formulario.", "error");
        }
    }

    // Updates the inputs with the validation errors
    function showErrors(form, errors) {
        // We loop through all the inputs and show the errors for that input
        _.each(form.querySelectorAll("input[name], select[name], textarea[name]"), function (input) {
            // Since the errors can be null if no errors were found we need to handle
            // that
            showErrorsForInput(input, errors && errors[input.name]);
        });
    }

    // Shows the errors for a specific input
    function showErrorsForInput(input, errors) {
        // This is the root of the input

        var formGroup = closestParent(input.parentNode, "form-group");
        // Find where the error messages will be insert into

        var messages = formGroup.querySelector(".messages");
        // First we remove any old messages and resets the classes
        resetFormGroup(formGroup);
        // If we have errors
        if (errors) {
            // we first mark the group has having errors
            formGroup.classList.add("has-error");
            // then we append all the errors
            _.each(errors, function (error) {

                addError(messages, error, input);
            });
        } else {
            // otherwise we simply mark it as success
            formGroup.classList.add("has-success");
        }
    }

    // Recusively finds the closest parent that has the specified class
    function closestParent(child, className) {
        if (!child || child == document) {
            return null;
        }
        if (child.classList.contains(className)) {
            return child;
        } else {
            return closestParent(child.parentNode, className);
        }
    }

    function resetFormGroup(formGroup) {

        // Remove the success and error classes
        formGroup.classList.remove("has-error");
        formGroup.classList.remove("has-success");
        // and remove any old messages
        _.each(formGroup.querySelectorAll(".text-danger"), function (el) {
            el.parentNode.removeChild(el);
        });
    }

    // Adds the specified error with the following markup
    // <p class="help-block error">[message]</p>
    function addError(messages, error, input) {

        var block = document.createElement("p");
        block.classList.add("text-danger");
        block.classList.add("error");
        block.innerText = error;
        messages.appendChild(block);
        $(input).addClass("input-danger");
    }

    function showSuccess() {
        // We made it \:D/
        swal({
            title: "Esta seguro de Guardar?",
            //text: "You will not be able to recover this imaginary file!",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, Guardar!",
            cancelButtonText: "No, Cancelar!",
            closeOnConfirm: false
        },
        function(isConfirm) {
            if (isConfirm) {
                document.getElementById("formulario").submit();
            }
        });
    }

});