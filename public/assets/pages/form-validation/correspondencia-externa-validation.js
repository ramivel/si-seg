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
        fk_acto_administrativo: {
            presence: {
                message: "^Debe seleccionar el Correlativo de H.R. Madre"
            },
        },
        cite: {
            presence: true,
        },
        fecha_cite: {
            presence: true,
        },
        fk_persona_externa: {
            presence: {
                message: "^Debe seleccionar el Remitente"
            },
        },
        referencia: {
            presence: true,
        },
        fojas: {
            presence: true,
        },
        adjuntos: {
            presence: true,
        },
        doc_digital: {
            presence: true,
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

    function errorDocumento(){
        var anexar_documentos = $('#anexar_documentos').val();
        var fk_estado_tramite_padre = $('#fk_estado_tramite').val();
        var ultimo_fk_estado_tramite_padre = $('#ultimo_fk_estado_tramite_padre').val();
        var fk_estado_tramite_hijo = $('#fk_estado_tramite_hijo').val();
        var ultimo_fk_estado_tramite_hijo = $('#ultimo_fk_estado_tramite_hijo').val();
        if(ultimo_fk_estado_tramite_padre == fk_estado_tramite_padre && ultimo_fk_estado_tramite_hijo == fk_estado_tramite_hijo){
            return false;
        }else if(ultimo_fk_estado_tramite_padre == fk_estado_tramite_padre){
            return false;
        }else if (anexar_documentos == 'NO'){
            return false;
        }

        return true;
    }

});