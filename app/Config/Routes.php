<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Autenticacion');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Autenticacion::index');

$routes->get('seguimiento_cam', 'Publico::seguimientoCAM');
$routes->post('seguimiento_cam', 'Publico::seguimientoCAM');
$routes->get('seguimiento_mineria_ilegal', 'Publico::seguimientoMineriaIlegal');
$routes->post('seguimiento_mineria_ilegal', 'Publico::seguimientoMineriaIlegal');

$routes->get('denuncia_mineria_ilegal', 'Publico::denunciaMineriaIlegal');
$routes->post('denuncia_mineria_ilegal', 'Publico::denunciaMineriaIlegal');
$routes->add('mensaje_mineria_ilegal', 'Publico::mensajeMineriaIlegal');
$routes->add('pdf_formulario_denuncia/(:num)', 'Publico::pdfFormularioDenuncia/$1');

$routes->add('ajax_provincias', 'Publico::ajaxProvincias');
$routes->add('ajax_municipios', 'Publico::ajaxMunicipios');

/* Descargar Documentos */
$routes->add('documentos/descargar/(:num)', 'Documentos::descargar/$1');

/*$routes->group('usuarios', function($routes){
    $routes->get('index', 'Usuarios::index', ['as'=>'usuarios.index']);
});*/

$routes->group('', ['filter'=>'AutenticacionCheck'], function($routes){
    // Agregar todas las rutas que necesitan proteccion
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('video_tutorial', 'Dashboard::VideoTutorial');
    $routes->get('libro_registro', 'Documentos::libroRegistro');
    $routes->post('imprimir_libro_registro', 'Documentos::imprimirLibroRegistro');
    $routes->add('ajax_buscar_hoja_ruta', 'Documentos::ajaxBuscarHojaRuta');
    $routes->add('ajax_tr_hr', 'Documentos::ajaxTrHr');

    $routes->group('oficinas', function($routes){
        $routes->add('/', 'Oficinas::index');
        $routes->add('agregar', 'Oficinas::agregar');
        $routes->add('editar/(:num)', 'Oficinas::editar/$1');
        $routes->post('guardar_editar', 'Oficinas::guardar_editar');
        $routes->add('estado/(:num)', 'Oficinas::estado/$1');
        $routes->add('eliminar/(:num)', 'Oficinas::eliminar/$1');
    });

    $routes->group('tramites', function($routes){
        $routes->add('/', 'Tramites::index');
        $routes->add('agregar', 'Tramites::agregar');
        $routes->add('editar/(:num)', 'Tramites::editar/$1');
        $routes->post('guardar_editar', 'Tramites::guardar_editar');
        $routes->add('estado/(:num)', 'Tramites::estado/$1');
        $routes->add('eliminar/(:num)', 'Tramites::eliminar/$1');
    });

    $routes->group('perfiles', function($routes){
        $routes->add('/', 'Perfiles::index');
        $routes->add('agregar', 'Perfiles::agregar');
        $routes->add('editar/(:num)', 'Perfiles::editar/$1');
        $routes->post('guardar_editar', 'Perfiles::guardar_editar');
        $routes->add('eliminar/(:num)', 'Perfiles::eliminar/$1');
    });

    $routes->group('usuarios', function($routes){
        $routes->add('/', 'Usuarios::index');
        $routes->add('agregar', 'Usuarios::agregar');
        $routes->post('guardar', 'Usuarios::guardar');
        $routes->add('editar/(:num)', 'Usuarios::editar/$1');
        $routes->post('guardar_editar', 'Usuarios::guardar_editar');
        $routes->add('activar/(:num)', 'Usuarios::activar/$1');
        $routes->add('desactivar/(:num)', 'Usuarios::desactivar/$1');
        $routes->add('eliminar/(:num)', 'Usuarios::eliminar/$1');
        $routes->add('cambiar_contraseña_usuario', 'Usuarios::cambiarContraseñaUsuario');
        $routes->post('guardar_cambiar_contraseña_usuario', 'Usuarios::guardarCambiarContraseñaUsuario');
        $routes->add('cambiar_contraseña/(:num)', 'Usuarios::cambiarContraseña/$1');
        $routes->post('guardar_cambiar_contraseña', 'Usuarios::guardarCambiarContraseña');

        $routes->add('ajax_direccion_usuarios', 'Usuarios::ajaxDireccionUsuarios');

    });

    $routes->group('estado_tramite', function($routes){
        $routes->add('/', 'EstadoTramite::index');
        $routes->add('categoria/(:num)', 'EstadoTramite::categoria/$1');
        $routes->add('agregar_categoria/(:num)', 'EstadoTramite::agregar_categoria/$1');
        $routes->add('editar_categoria/(:num)', 'EstadoTramite::editar_categoria/$1');
        $routes->post('guardar_editar_categoria', 'EstadoTramite::guardar_editar_categoria');
        $routes->add('eliminar_categoria/(:num)', 'EstadoTramite::eliminar_categoria/$1');
        $routes->add('subcategoria/(:num)', 'EstadoTramite::subcategoria/$1');
        $routes->add('agregar_subcategoria/(:num)', 'EstadoTramite::agregar_subcategoria/$1');
        $routes->add('editar_subcategoria/(:num)', 'EstadoTramite::editar_subcategoria/$1');
        $routes->post('guardar_editar_subcategoria', 'EstadoTramite::guardar_editar_subcategoria');
        $routes->add('eliminar_subcategoria/(:num)', 'EstadoTramite::eliminar_subcategoria/$1');
    });

    $routes->group('tipo_documento', function($routes){
        $routes->add('/', 'TipoDocumento::index');
        $routes->add('agregar', 'TipoDocumento::agregar');
        $routes->add('editar/(:num)', 'TipoDocumento::editar/$1');
        $routes->post('guardar_editar', 'TipoDocumento::guardar_editar');
        $routes->add('eliminar/(:num)', 'TipoDocumento::eliminar/$1');
        $routes->add('descargar/(:num)', 'TipoDocumento::descargar/$1');
    });

    $routes->group('cam', function($routes){
        $routes->add('mis_tramites', 'Cam::misTramites');
        $routes->add('listado_recepcion', 'Cam::listadoRecepcion');
        $routes->add('recibir/(:num)', 'Cam::recibir/$1');
        $routes->post('recibir_multiple', 'Cam::recibirMultiple');
        $routes->add('agregar', 'Cam::agregar');
        $routes->add('agregar_cmn_cmc', 'Cam::agregarCmnCmc');
        $routes->add('atender/(:num)', 'Cam::atender/$1');
        $routes->post('guardar_atender', 'Cam::guardarAtender');
        $routes->add('editar/(:num)', 'Cam::editar/$1');
        $routes->post('guardar_editar', 'Cam::guardarEditar');
        $routes->add('finalizar/(:num)', 'Cam::finalizar/$1');
        $routes->post('guardar_finalizar', 'Cam::guardarFinalizar');
        $routes->add('espera/(:num)', 'Cam::Espera/$1');
        $routes->post('guardar_espera', 'Cam::guardarEspera');
        $routes->add('ajax_hoja_ruta', 'Cam::ajaxHojaRutaMadre');
        $routes->add('ajax_datos_hr', 'Cam::ajaxDatosHR');
        $routes->add('ajax_hoja_ruta_cmn_cmc', 'Cam::ajaxHojaRutaCmnCmc');
        $routes->add('ajax_datos_hr_cmn_cmc', 'Cam::ajaxDatosHRCmnCmc');
        $routes->add('ajax_area_minera_cmn_cmc', 'Cam::ajaxAreaMineraCmnCmc');
        $routes->add('ajax_datos_area_minera_cmn_cmc', 'Cam::ajaxDatosAreaMineraCmnCmc');
        $routes->add('ajax_estado_tramite_hijo', 'Cam::ajaxEstadoTramiteHijo');
        $routes->add('ajax_analista_destinario', 'Cam::ajaxAnalistaDestinatario');
        $routes->add('ajax_hr_in_ex', 'Cam::ajaxHrInEx');
        $routes->add('ajax_datos_hr_in_ex', 'Cam::ajaxDatosHrInEx');
        $routes->add('ajax_guardar_devolver', 'Cam::ajaxGuardarDevolver');
        $routes->add('buscador_mis_tramites', 'Cam::buscadorMisTramites');
        $routes->add('buscador', 'Cam::buscador');
        $routes->add('buscador_ventanilla', 'Cam::buscadorVentanilla');
        $routes->add('reporte', 'Cam::reporte');
        $routes->add('reporte_usuarios', 'Cam::reporteUsuarios');
        $routes->add('ver/(:num)/(:num)', 'Cam::ver/$1/$2');
        $routes->add('ver_correspondencia_externa/(:num)/(:num)', 'Cam::verCorrespondenciaExterna/$1/$2');
        $routes->add('ver_documentos_generados/(:num)/(:num)', 'Cam::verDocumentosGenerados/$1/$2');
        $routes->add('ver_hojas_ruta_anexadas/(:num)/(:num)', 'Cam::verHojasRutaAnexadas/$1/$2');
        $routes->add('ver_historico_sincobol/(:num)/(:num)', 'Cam::verHistoricoSincobol/$1/$2');
        $routes->add('ver_seguimiento_historico_sincobol/(:num)/(:num)', 'Cam::verSeguimientoHistoricoSincobol/$1/$2');
        $routes->add('documentacion_digital', 'Cam::documentacionDigital');
        $routes->add('subir_documentos/(:num)', 'Cam::subirDocumentos/$1');
        $routes->add('ajax_subir_archivo', 'Cam::ajaxSubirArchivo');
        $routes->add('ajax_buscar_tramite', 'Cam::ajaxBuscarTramite');
        $routes->add('ajax_datos_tramite', 'Cam::ajaxDatosTramite');
        $routes->add('correspondencia_externa/(:num)', 'Cam::correspondenciaExterna/$1');
        $routes->add('generar_codigo_seguimiento', 'Cam::generarCodigoSeguimiento');
        $routes->add('pdf_seguimiento/(:num)', 'Cam::pdfCodigoSeguimiento/$1');

        $routes->add('migrar_sol_cam', 'Cam::migrarSolCam');
        $routes->add('migrar_cmn_cmc', 'Cam::migrarCmcCmc');

        $routes->add('reporte_responsable', 'Cam::reporteResponsable');
        $routes->add('reporte_mis_tramites', 'Cam::reporteMisTramites');
        $routes->add('reporte_fecha_mecanizada', 'Cam::reporteFechaMecanizada');

        $routes->add('hoja_ruta_pdf/(:num)', 'Cam::hojaRutaPdf/$1');

        //$routes->add('actualizar_poligono_area_minera', 'Cam::actualizarPoligonoAreaMinera');

    });

    $routes->group('tipo_documento_externo', function($routes){
        $routes->add('/', 'TipoDocumentoExterno::index');
        $routes->add('agregar', 'TipoDocumentoExterno::agregar');
        $routes->add('editar/(:num)', 'TipoDocumentoExterno::editar/$1');
        $routes->post('guardar_editar', 'TipoDocumentoExterno::guardarEditar');
        $routes->add('eliminar/(:num)', 'TipoDocumentoExterno::eliminar/$1');
    });

    $routes->group('correspondencia_externa', function($routes){
        $routes->add('mis_ingresos', 'CorrespondenciaExterna::misIngresos');
        $routes->add('agregar', 'CorrespondenciaExterna::agregar');
        $routes->add('editar/(:num)', 'CorrespondenciaExterna::editar/$1');
        $routes->post('guardar_editar', 'CorrespondenciaExterna::guardarEditar');
        $routes->add('ajax_recibir', 'CorrespondenciaExterna::recibirAjax');

        $routes->add('mis_ingresos_minilegal', 'CorrespondenciaExterna::misIngresosMinilegal');
        $routes->add('agregar_minilegal', 'CorrespondenciaExterna::agregarMinilegal');
        $routes->add('editar_minilegal/(:num)', 'CorrespondenciaExterna::editarMinilegal/$1');
        $routes->post('guardar_editar_minilegal', 'CorrespondenciaExterna::guardarEditarMinilegal');

        $routes->add('mis_recepciones/(:num)', 'CorrespondenciaExterna::misRecepciones/$1');

        $routes->post('guardar_atender', 'CorrespondenciaExterna::guardarAtender');

        //$routes->add('actualizar_path', 'CorrespondenciaExterna::actualizarPath');

    });

    $routes->group('persona_externa', function($routes){
        $routes->add('ajax_agregar', 'PersonaExterna::agregarAjax');
        $routes->add('ajax_buscar_persona_externa', 'PersonaExterna::buscarPersonaExternaAjax');

    });

    $routes->group('mineria_ilegal', function($routes){
        $routes->add('denuncias_web', 'MineriaIlegal::denunciasWeb');
        $routes->add('atender_denuncia_web/(:num)', 'MineriaIlegal::atenderDenunciaWeb/$1');
        $routes->post('aprobar_denuncia_web', 'MineriaIlegal::aprobarDenunciaWeb');
        $routes->post('archivar_denuncia_web', 'MineriaIlegal::archivarDenunciaWeb');
        $routes->add('listado_recepcion', 'MineriaIlegal::listadoRecepcion');
        $routes->add('recibir/(:num)', 'MineriaIlegal::recibir/$1');
        $routes->post('recibir_multiple', 'MineriaIlegal::recibirMultiple');
        $routes->add('atender/(:num)', 'MineriaIlegal::atender/$1');
        $routes->post('guardar_atender', 'MineriaIlegal::guardarAtender');
        $routes->add('editar/(:num)', 'MineriaIlegal::editar/$1');
        $routes->post('guardar_editar', 'MineriaIlegal::guardarEditar');
        $routes->add('anexar/(:num)', 'MineriaIlegal::anexar/$1');
        $routes->post('guardar_anexar', 'MineriaIlegal::guardarAnexar');
        $routes->add('ajax_area_minera_mineria_ilegal', 'MineriaIlegal::ajaxAreaMineraMineriaIlegal');
        $routes->add('ajax_datos_area_minera_mineria_ilegal', 'MineriaIlegal::ajaxDatosAreaMineraMineriaIlegal');
        $routes->add('ajax_analista_destinario', 'MineriaIlegal::ajaxAnalistaDestinatario');
        $routes->add('ajax_guardar_devolver', 'MineriaIlegal::ajaxGuardarDevolver');

        $routes->add('ver/(:num)/(:num)', 'MineriaIlegal::ver/$1/$2');
        $routes->add('ver_correspondencia_externa/(:num)/(:num)', 'MineriaIlegal::verCorrespondenciaExterna/$1/$2');

        $routes->add('mis_ingresos', 'MineriaIlegal::misIngresos');
        $routes->add('agregar_ventanilla', 'MineriaIlegal::agregarVentanilla');
        $routes->add('editar_ventanilla/(:num)', 'MineriaIlegal::editarVentanilla/$1');
        $routes->post('guardar_editar_ventanilla', 'MineriaIlegal::guardarEditarVentanilla');
        $routes->add('ajax_denunciante', 'MineriaIlegal::ajaxDenunciante');
        $routes->add('ajax_datos_denunciante', 'MineriaIlegal::ajaxDatosDenunciante');
        $routes->add('ajax_tr_adjunto', 'MineriaIlegal::ajaxTrAdjunto');
        $routes->add('ajax_agregar_denunciante', 'MineriaIlegal::ajaxAgregarDenunciante');

        $routes->add('agregar_oficio', 'MineriaIlegal::agregarOficio');

        $routes->add('mis_tramites', 'MineriaIlegal::misTramites');
        $routes->add('agregar_fiscalizacion', 'MineriaIlegal::agregarFiscalizacion');
        $routes->add('formulario_denuncia_pdf/(:num)', 'MineriaIlegal::formularioDenunciaPdf/$1');
        $routes->add('hoja_ruta_pdf/(:num)', 'MineriaIlegal::hojaRutaPdf/$1');
        $routes->add('ajax_provincias', 'MineriaIlegal::ajaxProvincias');
        $routes->add('ajax_municipios', 'MineriaIlegal::ajaxMunicipios');

        $routes->add('denuncia_manual', 'MineriaIlegal::denunciaManual');
        $routes->add('denuncia_manual_fmi', 'MineriaIlegal::denunciaManualFmi');
        $routes->add('listado_denuncias_manuales', 'MineriaIlegal::listadoDenunciasManuales');
        $routes->add('revisar_denuncia_manual/(:num)', 'MineriaIlegal::revisarDenunciaManual/$1');
        $routes->post('guardar_revisar_denuncia_manual', 'MineriaIlegal::guardarRevisarDenunciaManual');

        $routes->add('buscador', 'MineriaIlegal::buscador');
        $routes->add('ajax_hr_in_ex', 'MineriaIlegal::ajaxHrInEx');
        $routes->add('ajax_datos_hr_in_ex', 'MineriaIlegal::ajaxDatosHrInEx');
        $routes->add('ajax_hoja_ruta_mineria_ilegal', 'MineriaIlegal::ajaxHojaRutaMineriaIlegal');

        $routes->add('buscador_ventanilla', 'MineriaIlegal::buscadorVentanilla');
        $routes->add('ajax_buscar_tramite', 'MineriaIlegal::ajaxBuscarTramite');
        $routes->add('ajax_datos_tramite', 'MineriaIlegal::ajaxDatosTramite');

        $routes->add('correspondencia_externa/(:num)', 'MineriaIlegal::correspondenciaExterna/$1');
    });

    /*$routes->group('acto_administrativo', function($routes){
        $routes->add('/', 'ActoAdministrativo::index');
        $routes->add('agregar', 'ActoAdministrativo::agregar');
        $routes->add('ajax_hoja_ruta', 'ActoAdministrativo::ajaxHojaRutaMadre');
        $routes->add('ajax_datos_hr', 'ActoAdministrativo::ajaxDatosHR');

        $routes->add('atender/(:num)', 'ActoAdministrativo::atender/$1');
        $routes->post('guardar_atender', 'ActoAdministrativo::guardar_atender');
        $routes->add('modificar/(:num)', 'ActoAdministrativo::modificar/$1');
        $routes->post('guardar_modificar', 'ActoAdministrativo::guardar_modificar');
        $routes->add('ver/(:num)/(:num)', 'ActoAdministrativo::ver/$1/$1');
        $routes->add('eliminar/(:num)', 'ActoAdministrativo::eliminar/$1');
    });*/

    $routes->group('documentos', function($routes){
        $routes->add('agregar/(:num)/(:num)', 'Documentos::agregar/$1/$2');
        $routes->add('editar/(:num)/(:num)', 'Documentos::editar/$1/$2');
        $routes->post('guardar_editar', 'Documentos::guardarEditar');
        $routes->add('subir/(:num)/(:num)', 'Documentos::subir/$1/$2');
        $routes->post('guardar_subir', 'Documentos::guardarSubir');
        $routes->add('ajax_area_minera', 'Documentos::ajaxAreaMinera');
        $routes->add('listado/(:num)', 'Documentos::index/$1');
        $routes->add('listado_anulacion/(:num)', 'Documentos::listadoAnulacion/$1');
        $routes->add('anular/(:num)/(:num)', 'Documentos::anular/$1/$2');
        $routes->post('guardar_anular', 'Documentos::guardarAnular');
        $routes->add('aprobar_anulacion/(:num)/(:num)', 'Documentos::aprobarAnulacion/$1/$2');
        $routes->add('rechazar_anulacion/(:num)/(:num)', 'Documentos::rechazarAnulacion/$1/$2');
        $routes->add('ajax_documentos', 'Documentos::ajaxDocumentos');
        $routes->add('ajax_datos_documento', 'Documentos::ajaxDatosDocumento');

        $routes->add('reporte/(:num)', 'Documentos::reporte/$1');
        $routes->add('ajax_documentos_mineria_ilegal', 'Documentos::ajaxDocumentosMineriaIlegal');

        $routes->add('reporte_documentos/(:num)', 'Documentos::reporteDocumentos/$1');

        $routes->add('buscador', 'Documentos::buscador');
        $routes->add('desanexar/(:num)', 'Documentos::desanexar/$1');
        $routes->add('buscador_sincobol', 'Documentos::buscadorSincobol');
        $routes->add('desanexar_sincobol/(:num)/(:num)/(:num)/(:num)', 'Documentos::desanexarSincobol/$1/$2/$3/$4');

        //$routes->add('actualizar_path', 'Documentos::actualizarPath');

    });
});
$routes->group('', ['filter'=>'AlreadyLogged'], function($routes){
    // Agregar todas las rutas que necesitan proteccion
    $routes->get('autenticacion', 'Autenticacion::index');
});
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
