"use strict";
const baseUrl = "/garnet/";
$(document).ready(function () {
  /* Documentos */

  $(".documentos-area-minera-ajax").select2({
    language: "es",
    placeholder: "Escriba el código único o la denominación",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + "documentos/ajax_area_minera",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  /* Fin Documentos */

  /* Hoja Ruta Madre CAM */
  $(".hoja-ruta-madre-cam-ajax").select2({
    language: "es",
    placeholder:
      "Escriba la Hoja de Ruta Madre o el Código Único del Área Minera...",
    minimumInputLength: 1,
    ajax: {
      url: baseUrl + "cam/ajax_hoja_ruta",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  $(".hoja-ruta-madre-cam-ajax").on("select2:select", function (e) {
    var data = e.params.data;
    $.ajax({
      url: baseUrl + "cam/ajax_datos_hr",
      type: "POST",
      data: data,
      dataType: "json",
      success: function (result) {
        $("#fk_area_minera").val(result.fk_area_minera);
        $("#referencia").val(result.referencia);
        $("#fecha_mecanizada").val(result.fecha_mecanizada);
        $("#denominacion").val(result.denominacion);
        $("#codigo_unico").val(result.codigo_unico);
        $("#extension").val(result.extension);
        $("#departamentos").val(result.departamentos);
        $("#provincias").val(result.provincias);
        $("#municipios").val(result.municipios);
        $("#area_protegida").val(result.area_protegida);
        $("#regional").val(result.regional);
        $("#representante_legal").val(result.representante_legal);
        $("#nacionalidad").val(result.nacionalidad);
        $("#titular").val(result.titular);
        $("#clasificacion").val(result.clasificacion);
        $("#domicilio_legal").val(result.domicilio_legal);
        $("#domicilio_procesal").val(result.domicilio_procesal);
        $("#telefono_solicitante").val(result.telefono_solicitante);
      },
    });
  });

  /* Hoja Ruta CMC CMN */
  $(".hoja-ruta-cmn-cmc-ajax").select2({
    language: "es",
    placeholder: "Escriba la Hoja de Ruta CMN/CMC",
    minimumInputLength: 1,
    ajax: {
      url: baseUrl + "cam/ajax_hoja_ruta_cmn_cmc",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  $(".hoja-ruta-cmn-cmc-ajax").on("select2:select", function (e) {
    var data = e.params.data;
    $.ajax({
      url: baseUrl + "cam/ajax_datos_hr_cmn_cmc",
      type: "POST",
      data: data,
      dataType: "json",
      success: function (result) {
        $("#referencia").val(result.referencia);
        $("#fecha_mecanizada").val(result.fecha_mecanizada);
        $("#cite_documento").val(result.cite_documento);
        $("#procedencia").val(result.procedencia);
        $("#remitente").val(result.remitente);
        $("#cargo").val(result.cargo);
      },
    });
  });

  /* Area minera CMC CMN */
  $(".area-minera-cmn-cmc-ajax").select2({
    language: "es",
    placeholder: "Escriba el Código Único o Denominación",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + "cam/ajax_area_minera_cmn_cmc",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  $(".area-minera-cmn-cmc-ajax").on("select2:select", function (e) {
    var data = e.params.data;
    $.ajax({
      url: baseUrl + "cam/ajax_datos_area_minera_cmn_cmc",
      type: "POST",
      data: data,
      dataType: "json",
      success: function (result) {
        $("#denominacion").val(result.denominacion);
        $("#codigo_unico").val(result.codigo_unico);
        $("#extension").val(result.extension);
        $("#departamentos").val(result.departamentos);
        $("#provincias").val(result.provincias);
        $("#municipios").val(result.municipios);
        $("#area_protegida").val(result.area_protegida);
        $("#regional").val(result.regional);
        $("#representante_legal").val(result.representante_legal);
        $("#nacionalidad").val(result.nacionalidad);
        $("#titular").val(result.titular);
        $("#clasificacion").val(result.clasificacion);
        $("#domicilio_legal").val(result.domicilio_legal);
        $("#domicilio_procesal").val(result.domicilio_procesal);
        $("#telefono_solicitante").val(result.telefono_solicitante);
      },
    });
  });

  $(".documentos-ajax").select2({
    language: "es",
    placeholder: "Escriba el Correlativo del Documento...",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + "documentos/ajax_documentos",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
          id_acto_administrativo: $("#id_acto_administrativo").val(),
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  $(".agregar_documento").click(function () {
    var fk_documento = $("#fk_documento").val();
    if (fk_documento.length > 0) {
      $.ajax({
        url: baseUrl + "documentos/ajax_datos_documento",
        type: "POST",
        data: {
          id_documento: fk_documento,
        },
        dataType: "json",
        success: function (result) {
          var notificacion = "hidden";
          if (result.notificacion == "t") notificacion = "date";
          var markup =
            "<tr id='" +
            result.id +
            "'>" +
            "<td class='text-center'><input type='hidden' name='id_documentos[]' value='" +
            result.id +
            "' />" +
            result.correlativo +
            "</td>" +
            "<td class='text-center'>" +
            result.fecha +
            "</td>" +
            "<td class='text-center'>" +
            result.tipo_documento +
            "</td>" +
            "<td class='text-center'><input type='" +
            notificacion +
            "' name='fecha_notificaciones[]' class='form-control' /></td>" +
            "<td class='text-center'><button type='button' class='btn btn-sm btn-danger waves-effect waves-light' title='Desanexar Documento' onclick='desanexar_documento(" +
            result.id +
            ");'><span class='icofont icofont-ui-delete'></span></button></td>" +
            "</tr>";
          $("#tabla_documentos tbody").append(markup);
          $(".documentos-ajax").val("").trigger("change");
          $(".documentos-mineria-ilegal-ajax").val("").trigger("change");
          $("#documentos_anexados").val("SI");
        },
      });
    }
  });

  $(".documentos-mineria-ilegal-ajax").select2({
    language: "es",
    placeholder: "Escriba el Correlativo del Documento...",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + "documentos/ajax_documentos_mineria_ilegal",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
          id_hoja_ruta: $("#id_hoja_ruta").val(),
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  /* Fin Hoja de Ruta Madre CAM */

  $(".analista-destinatario-ajax").select2({
    language: "es",
    placeholder: "Escriba el Nombre o Cargo...",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + "cam/ajax_analista_destinario",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  $(".analista-destinatario-mineria-ilegal-ajax").select2({
    language: "es",
    placeholder: "Escriba el Nombre o Cargo...",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + "mineria_ilegal/ajax_analista_destinario",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  $(".documentos-limpiar").click(function () {
    $(".documentos-ajax").val("").trigger("change");
  });

  $(".documentos-all-ajax").select2({
    language: "es",
    placeholder: "Escriba el Correlativo",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + "documentos/ajax_documentos",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
          all: true,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  $(".documentos-all-limpiar").click(function () {
    $(".documentos-all-ajax").val("").trigger("change");
  });

  $(".hr-in-ex-ajax").select2({
    language: "es",
    placeholder: "Correlativo HR ..",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + "cam/ajax_hr_in_ex",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });
  $(".agregar_hr_in_ex").click(function () {
    var fk_hoja_ruta = $("#fk_hoja_ruta").val();
    if (fk_hoja_ruta.length > 0) {
      $.ajax({
        url: baseUrl + "cam/ajax_datos_hr_in_ex",
        type: "POST",
        data: {
          id: fk_hoja_ruta,
        },
        dataType: "json",
        success: function (result) {
          if(result.estado == 'success'){
            var markup = "<tr id='hr"+result.id+"'>"+
              "<td class='text-center form-group'><input type='hidden' name='id_hojas_rutas[]' value='"+result.id+"' /><span class='messages'></span>"+result.tipo_hoja_ruta+"</td>"+
              "<td class='text-center'>"+result.correlativo+"</td>"+
              "<td class='text-center'>"+result.fecha+"</td>"+
              "<td class='text-center'>"+result.referencia+"</td>"+
              "<td class='text-center'>"+result.remitente+"</td>"+
              "<td class='text-center'>"+result.cite+"</td>"+
              "<td class='text-center'><button type='button' class='btn btn-sm btn-danger waves-effect waves-light' title='Desanexar Hoja Ruta' onclick='desanexar_hoja_ruta("+result.id+");'><span class='icofont icofont-ui-delete'></span></button></td>"+
            "</tr>";
            $("#tabla_hojas_rutas tbody").append(markup);
            $(".hr-in-ex-ajax").val('').trigger('change');
            $("#hr_anexados").val('SI');
          }else{
            console.log(result);
          }
        },
      });
    }
  });
  $(".hr-in-ex-mejorado-ajax").select2({
    language: "es",
    placeholder: "Escriba el correlativo de la Hoja de Ruta Interna o Externa..",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + $(".hr-in-ex-mejorado-ajax").data('controlador') + "ajax_hr_in_ex",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });
  $(".agregar_hr_in_ex_mejorado").click(function () {
    var fk_hoja_ruta = $("#fk_hoja_ruta").val();
    var controlador = $(this).data('controlador');
    var selector = $(this).data('selector');
    var tabla = $(this).data('tabla');
    if (fk_hoja_ruta.length > 0) {
      $.ajax({
        url: baseUrl + controlador + "ajax_datos_hr_in_ex",
        type: "POST",
        data: {
          id: fk_hoja_ruta,
        },
        dataType: "json",
        success: function (result) {
          if(result.estado == 'success'){
            var markup = "<tr id='hr"+result.id+"'>"+
              "<td class='text-center form-group'><input type='hidden' name='id_hojas_rutas[]' value='"+result.id+"' /><span class='messages'></span>"+result.tipo_hoja_ruta+"</td>"+
              "<td class='text-center'>"+result.correlativo+"</td>"+
              "<td class='text-center'>"+result.fecha+"</td>"+
              "<td class='text-center'>"+result.referencia+"</td>"+
              "<td class='text-center'>"+result.remitente+"</td>"+
              "<td class='text-center'>"+result.cite+"</td>"+
              "<td class='text-center'><button type='button' class='btn btn-sm btn-danger waves-effect waves-light' title='Desanexar Hoja Ruta' onclick='desanexar_hoja_ruta("+result.id+");'><span class='icofont icofont-ui-delete'></span></button></td>"+
            "</tr>";
            $(tabla + " tbody").append(markup);
            $(selector).val('').trigger('change');
            $("#hr_anexados").val('SI');
          }else{
            console.log(result);
          }
        },
      });
    }
  });

  /* Buscador Hoja de Ruta Mineria Ilegal*/
  $(".hoja-ruta-mineria-ilegal-ajax").select2({
    language: "es",
    placeholder: "Escriba la Hoja de Ruta Minería Ilegal...",
    minimumInputLength: 1,
    ajax: {
      url: baseUrl + "mineria_ilegal/ajax_hoja_ruta_mineria_ilegal",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
          id_hoja_ruta_actual: $("#id_hoja_ruta").val(),
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });
  /* Fin Buscador Hoja de Ruta Mineria Ilegal*/

  /* Estado Tramite Hijo */
  if (
    $("#fk_estado_tramite").children("option:selected").data("padre") == "t"
  ) {
    $("#estado_tramite_hijo").show();
  } else {
    $("#estado_tramite_hijo").hide();
  }

  $("#fk_estado_tramite").on("change", function () {
    if ($(this).children("option:selected").data("padre") == "t") {
      $.ajax({
        type: "POST",
        url: baseUrl + "cam/ajax_estado_tramite_hijo",
        data: {
          id_padre: this.value,
        },
        error: function () {
          console.log("error ajax.");
        },
        success: function (html) {
          $("#fk_estado_tramite_hijo").html(html);
          $("#estado_tramite_hijo").show();
        },
      });
    } else {
      $("#fk_estado_tramite_hijo").html(
        '<option value="">SELECCIONE UNA OPCIÓN</option>'
      );
      $("#estado_tramite_hijo").hide();
    }

    if ($(this).children("option:selected").data("anexar") == "f")
      $("#anexar_documentos").val("NO");
    else $("#anexar_documentos").val("SI");
  });

  $("#fk_estado_tramite_hijo").on("change", function () {
    if ($(this).children("option:selected").data("anexar") == "f")
      $("#anexar_documentos").val("NO");
    else $("#anexar_documentos").val("SI");
  });

  /* CORRESPONDENCIA EXTERNA */
  $(".buscar-tramite-ajax").select2({
    language: "es",
    placeholder:
      "Escriba la Hoja de Ruta Madre o el Código Único del Área Minera",
    minimumInputLength: 1,
    ajax: {
      url: baseUrl + "cam/ajax_buscar_tramite",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  $(".buscar-tramite-ajax").on("select2:select", function (e) {
    var data = e.params.data;
    $.ajax({
      url: baseUrl + "cam/ajax_datos_tramite",
      type: "POST",
      data: data,
      dataType: "json",
      success: function (result) {
        $("#codigo_unico").val(result.codigo_unico);
        $("#denominacion").val(result.denominacion);
        $("#representante_legal").val(result.representante_legal);
        $("#nacionalidad").val(result.nacionalidad);
        $("#titular").val(result.titular);
        $("#clasificacion").val(result.clasificacion);
        $("#remitente").val(result.remitente);
        $("#destinatario").val(result.destinatario);
        $("#responsable").val(result.responsable);
      },
    });
  });

  $(".persona-externa-ajax").select2({
    language: "es",
    placeholder: "Escriba el Documento de Identidad o Nombre de la Persona",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + "persona_externa/ajax_buscar_persona_externa",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  /* FIN CORRESPONDENCIA EXTERNA */

  /* UBICACION MINERIA ILEGAL */
  $("#departamento").on("change", function () {
    $("#provincia").html('<option value="">SELECCIONE LA PROVINCIA</option>');
    $("#fk_municipio").html(
      '<option value="">SELECCIONE EL MUNICIPIO</option>'
    );
    if (this.value.length > 0) {
      $.ajax({
        type: "POST",
        url: baseUrl + "mineria_ilegal/ajax_provincias",
        data: {
          departamento: this.value,
        },
        error: function () {
          console.log("error ajax.");
        },
        success: function (html) {
          $("#provincia").html(html);
        },
      });
    }
  });
  $("#provincia").on("change", function () {
    $("#fk_municipio").html(
      '<option value="">SELECCIONE EL MUNICIPIO</option>'
    );
    if (this.value.length > 0) {
      $.ajax({
        type: "POST",
        url: baseUrl + "mineria_ilegal/ajax_municipios",
        data: {
          departamento: $("#departamento").val(),
          provincia: this.value,
        },
        error: function () {
          console.log("error ajax.");
        },
        success: function (html) {
          $("#fk_municipio").html(html);
        },
      });
    }
  });

  /* BUSCADOR DE DENUNCIANTE */
  $(".denunciante-ajax").select2({
    language: "es",
    placeholder: "Escriba el Documento de Identidad o Nombre de la Persona",
    minimumInputLength: 1,
    ajax: {
      url: baseUrl + "mineria_ilegal/ajax_denunciante",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });

  $(".agregar_denunciante").click(function () {
    var id_denunciante = $("#id_denunciante").val();
    if (id_denunciante.length > 0) {
      $.ajax({
        url: baseUrl + "mineria_ilegal/ajax_datos_denunciante",
        type: "POST",
        data: {
          id: id_denunciante,
        },
        dataType: "json",
        success: function (result) {
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
        },
      });
    }
  });

  /* FIN BUSCADOR DE DENUNCIANTE */

  /* AGREGAR ADJUNTO */
  $(".agregar_adjunto").click(function () {
    var tmp_adjuntos = parseInt($("#tmp_adjuntos").val()) + 1;
    var tipo = $(this).data('tipo');
    $.ajax({
      url: baseUrl + "mineria_ilegal/ajax_tr_adjunto",
      type: "POST",
      data: {
        n: tmp_adjuntos,
        tipo: tipo,
      },
      success: function (result) {
        $("#tabla_adjuntos tbody").append(result);
        $("#tmp_adjuntos").val(tmp_adjuntos);
      },
    });
  });
  /* FIN AGREGAR ADJUNTO */

  /* MINERIA ILEGAL AREAS MINERAS */
  $(".area-minera-mineria-ilegal-ajax").select2({
    language: "es",
    placeholder: "Escriba el Código Único o Denominación",
    minimumInputLength: 3,
    ajax: {
      url: baseUrl + "mineria_ilegal/ajax_area_minera_mineria_ilegal",
      dataType: "json",
      type: "POST",
      delay: 250,
      data: function (params) {
        return {
          texto: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data,
        };
      },
      cache: true,
    },
  });
  $(".agregar_area_minera_mineria_ilegal").click(function () {
    var fk_area_minera = $("#fk_area_minera").val();
    if (fk_area_minera.length > 0) {
      $.ajax({
        url: baseUrl + "mineria_ilegal/ajax_datos_area_minera_mineria_ilegal",
        type: "POST",
        data: {
          id: fk_area_minera,
        },
        dataType: "json",
        success: function (result) {
          if(result.estado == 'success'){
            var markup = "<tr id='am"+result.id+"'>"+
              "<td class='text-center form-group'><input type='hidden' name='id_areas_mineras[]' value='"+result.id+"' /><span class='messages'></span>"+result.codigo_unico+"</td>"+
              "<td class='text-center'>"+result.area_minera+"</td>"+
              "<td class='text-center'>"+result.tipo_area_minera+"</td>"+
              "<td class='text-center'>"+result.extension+"</td>"+
              "<td class='text-center'>"+result.titular+"</td>"+
              "<td class='text-center'>"+result.clasificacion+"</td>"+
              "<td class='text-center'>"+result.departamentos+"</td>"+
              "<td class='text-center'>"+result.provincias+"</td>"+
              "<td class='text-center'>"+result.municipios+"</td>"+
              "<td class='text-center'><button type='button' class='btn btn-sm btn-danger waves-effect waves-light' title='Desanexar Área Minera' onclick='desanexar_area_minera_mineria_ilegal("+result.id+");'><span class='icofont icofont-ui-delete'></span></button></td>"+
            "</tr>";
            $("#tabla_areas_mineras tbody").append(markup);
            $(".area-minera-mineria-ilegal-ajax").val('').trigger('change');
          }else{
            console.log(result);
          }
        },
      });
    }
  });
  /* FIN MINERIA ILEGAL AREAS MINERAS */

  /* ORIGEN CAMBIAR */

  verificarOrigen();

  $("#origen_oficio").on("change", function () {
    verificarOrigen();
  });
  /* ORIGEN CAMBIAR */

  /* TIPO MINERIA ILEGAL MANUAL*/
  verificarTipoMineriaManual();
  $("#fk_tipo_denuncia").on("change", function () {
    verificarTipoMineriaManual();
  });
  /* FIN TIPO MINERIA ILEGAL MANUAL*/

  /* Reporte Administracion Usuarios */
  $("#oficina-reporte").on("change", function () {
    $("#usuario-reporte").html('<option value="">SELECCIONE UN USUARIO</option>');
    if (this.value.length > 0) {
      $.ajax({
        type: "POST",
        url: baseUrl + "usuarios/ajax_direccion_usuarios",
        data: {
          fk_oficina: this.value,
        },
        error: function () {
          console.log("error ajax.");
        },
        success: function (html) {
          console.log(html);
          $("#usuario-reporte").html(html);
        },
      });
    }
  });
  /* Fin Reporte Administracion Usuarios */

});
function desanexar_documento(idDocumento) {
  var row = document.getElementById(idDocumento);
  row.parentNode.removeChild(row);
  var id_documentos = document.getElementsByName("id_documentos[]")[0];
  if (typeof id_documentos === "undefined")
    document.getElementById("documentos_anexados").value = "";
}
function desanexar_hoja_ruta(n) {
  var row = document.getElementById("hr" + n);
  row.parentNode.removeChild(row);
}
function desanexar_denunciante(n) {
  var row = document.getElementById("den" + n);
  row.parentNode.removeChild(row);
}
function desanexar_area_minera_mineria_ilegal(n) {
  var row = document.getElementById("am" + n);
  row.parentNode.removeChild(row);
}
function eliminar_adjunto(n) {
  var row = document.getElementById("adj" + n);
  row.parentNode.removeChild(row);
}
function verificarOrigen(){
  switch($("#origen_oficio").val()) {
    case 'HOJA DE RUTA EXTERNA/INTERNA':
      $('#origen_enlace').hide();
      $('#origen_hr').show();
      break;
    case 'NOTICIA':
    case 'REDES SOCIALES':
      $('#origen_enlace').show();
      $('#origen_hr').hide();
      break;
    default:
      $('#origen_enlace').hide();
      $('#origen_hr').hide();
      break;
  }
}
function verificarTipoMineriaManual(){
  switch($("#fk_tipo_denuncia").val()) {
    case '3':
      $('#verificacion-oficio-manual').show();
      break;
    default:
      $('#verificacion-oficio-manual').hide();
      break;
  }
}


