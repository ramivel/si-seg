<nav class="pcoded-navbar">
    <div class="pcoded-inner-navbar main-menu">

        <?php if(in_array(100, session()->get('registroPermisos'))){?>
            <div class="pcoded-navigatio-lavel">Administración</div>
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu <?= (isset($menu_actual) && in_array($menu_actual, array('oficinas', 'tramites', 'perfiles', 'usuarios', 'estado_tramite', 'tipo_documento', 'tipo_documento_externo', 'documentos'))) ? 'pcoded-trigger' : '';?>">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="feather icon-settings"></i></span>
                        <span class="pcoded-mtext">Configuraciones</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'oficinas') ? 'active' : '';?>">
                            <a href="<?= base_url('oficinas');?>">
                                <span class="pcoded-mtext">Direcciones</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'perfiles') ? 'active' : '';?>">
                            <a href="<?= base_url('perfiles');?>">
                                <span class="pcoded-mtext">Cargos</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'tramites') ? 'active' : '';?>">
                            <a href="<?= base_url('tramites');?>">
                                <span class="pcoded-mtext">Tramites</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'estado_tramite') ? 'active' : '';?>">
                            <a href="<?= base_url('estado_tramite');?>">
                                <span class="pcoded-mtext">Estados Tramites</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'tipo_documento') ? 'active' : '';?>">
                            <a href="<?= base_url('tipo_documento');?>">
                                <span class="pcoded-mtext">Tipos Documentos</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'usuarios') ? 'active' : '';?>">
                            <a href="<?= base_url('usuarios');?>">
                                <span class="pcoded-mtext">Usuarios</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'tipo_documento_externo') ? 'active' : '';?>">
                            <a href="<?= base_url('tipo_documento_externo');?>">
                                <span class="pcoded-mtext">Tipos Doc. Externos</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="pcoded-hasmenu <?= (isset($menu_actual) && ($menu_actual === 'documentos/buscador' || $menu_actual === 'documentos/buscador_sincobol') ) ? 'pcoded-trigger' : '';?>">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="feather icon-file-minus"></i></span>
                        <span class="pcoded-mtext">Desanexar</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'documentos/buscador') ? 'active' : '';?>">
                            <a href="<?= base_url('documentos/buscador');?>">
                                <span class="pcoded-mtext">Buscador Documentos</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'documentos/buscador_sincobol') ? 'active' : '';?>">
                            <a href="<?= base_url('documentos/buscador_sincobol');?>">
                                <span class="pcoded-mtext">Buscador H.R. SINCOBOL</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        <?php }?>

        <?php if(in_array(11, session()->get('registroPermisos'))){?>
            <div class="pcoded-navigatio-lavel">Correspondencia Externa</div>
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu <?= (isset($menu_actual) && strpos($menu_actual, 'correspondencia_externa/') !== false ) ? 'pcoded-trigger' : '';?>">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                        <span class="pcoded-mtext">CAM</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'correspondencia_externa/buscador_tramites_cam') ? 'active' : '';?>">
                            <a href="<?= base_url('cam/buscador_ventanilla');?>">
                                <span class="pcoded-mtext">Buscador</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'correspondencia_externa/agregar') ? 'active' : '';?>">
                            <a href="<?= base_url('correspondencia_externa/agregar');?>">
                                <span class="pcoded-mtext">Nuevo Ingreso</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'correspondencia_externa/mis_ingresos') ? 'active' : '';?>">
                            <a href="<?= base_url('correspondencia_externa/mis_ingresos');?>">
                                <span class="pcoded-mtext">Mis Ingresos</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'correspondencia_externa/generar_codigo_seguimiento') ? 'active' : '';?>">
                            <a href="<?= base_url('cam/generar_codigo_seguimiento');?>">
                                <span class="pcoded-mtext">Código de Seguimiento</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="pcoded-hasmenu <?= (isset($menu_actual) && strpos($menu_actual, 'mineria_ilegal/') !== false ) ? 'pcoded-trigger' : '';?>">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                        <span class="pcoded-mtext">Minería Ilegal</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'mineria_ilegal/buscador_ventanilla') ? 'active' : '';?>">
                            <a href="<?= base_url('mineria_ilegal/buscador_ventanilla');?>">
                                <span class="pcoded-mtext">Buscador</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'mineria_ilegal/agregar_minilegal') ? 'active' : '';?>">
                            <a href="<?= base_url('correspondencia_externa/agregar_minilegal');?>">
                                <span class="pcoded-mtext">Nuevo Ingresos</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'mineria_ilegal/mis_ingresos_minilegal') ? 'active' : '';?>">
                            <a href="<?= base_url('correspondencia_externa/mis_ingresos_minilegal');?>">
                                <span class="pcoded-mtext">Mis Ingresos</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'mineria_ilegal/agregar_ventanilla') ? 'active' : '';?>">
                            <a href="<?= base_url('mineria_ilegal/agregar_ventanilla');?>">
                                <span class="pcoded-mtext">Nueva Denuncia</span>
                            </a>
                        </li>
                        <li class="<?= (isset($menu_actual) && $menu_actual === 'mineria_ilegal/mis_ingresos') ? 'active' : '';?>">
                            <a href="<?= base_url('mineria_ilegal/mis_ingresos');?>">
                                <span class="pcoded-mtext">Denuncias Ingresadas</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

        <?php }?>

        <?php if(isset($tramites_menu) && count($tramites_menu) > 0){?>
            <div class="pcoded-navigatio-lavel">Trámites</div>
            <?php foreach($tramites_menu as $row){ ?>
                <ul class="pcoded-item pcoded-left-item">
                    <?php if($row['controlador'] == 'cam/'){?>
                        <li class="pcoded-hasmenu <?= (isset($menu_actual) && strpos($menu_actual, $row['controlador']) !== false ) ? 'pcoded-trigger' : '';?>">
                            <a href="javascript:void(0)">
                                <span class="pcoded-micon"><i class="feather icon-folder"></i></span>
                                <span class="pcoded-mtext"><?= $row['menu'] ?></span>
                            </a>
                            <ul class="pcoded-submenu">

                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'documento/listado') ? 'active' : '';?>">
                                    <a href="<?= base_url('documentos/listado/'.$row['id']);?>">
                                        <span class="pcoded-mtext">Mis Documentos</span>
                                    </a>
                                </li>

                                <?php if(in_array(2, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'documento/listado_anulacion') ? 'active' : '';?>">
                                        <a href="<?= base_url('documentos/listado_anulacion/'.$row['id']);?>">
                                            <span class="pcoded-mtext">Anular Documentos</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <?php if(in_array(1, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'agregar') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'agregar');?>">
                                            <span class="pcoded-mtext">Migrar SOL-CAM</span>
                                        </a>
                                    </li>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'agregar_cmn_cmc') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'agregar_cmn_cmc');?>">
                                            <span class="pcoded-mtext">Migrar CMN/CMC</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'listado_recepcion') ? 'active' : '';?>">
                                    <a href="<?= base_url($row['controlador'].'listado_recepcion');?>">
                                        <span class="pcoded-mtext">Recepción de Trámites</span>
                                    </a>
                                </li>

                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'mis_tramites') ? 'active' : '';?>">
                                <a href="<?= base_url($row['controlador'].'mis_tramites');?>">
                                        <span class="pcoded-mtext">Mis Trámites</span>
                                    </a>
                                </li>
                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'correspondencia_externa') ? 'active' : '';?>">
                                <a href="<?= base_url('correspondencia_externa/mis_recepciones/'.$row['id']);?>">
                                        <span class="pcoded-mtext">Mi Correspondencia Externa</span>
                                    </a>
                                </li>
                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'buscador_mis_tramites') ? 'active' : '';?>">
                                    <a href="<?= base_url($row['controlador'].'buscador_mis_tramites');?>">
                                        <span class="pcoded-mtext">Reporte de mis Trámites como Responsable</span>
                                    </a>
                                </li>
                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'buscador') ? 'active' : '';?>">
                                    <a href="<?= base_url($row['controlador'].'buscador');?>">
                                        <span class="pcoded-mtext">Buscador de Tramites</span>
                                    </a>
                                </li>

                                <?php if(in_array(10, session()->get('registroPermisos'))){?>
                                <!--
                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'documentacion_digital') ? 'active' : '';?>">
                                    <a href="<?= base_url($row['controlador'].'documentacion_digital');?>">
                                        <span class="pcoded-mtext">Documentación Digital</span>
                                    </a>
                                </li>
                                -->
                                <?php }?>

                                <?php if(in_array(6, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'reporte_usuarios') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'reporte_usuarios');?>">
                                            <span class="pcoded-mtext">Reporte por Responsable</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <?php if(in_array(7, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'documentos/reporte') ? 'active' : '';?>">
                                        <a href="<?= base_url('documentos/reporte/'.$row['id']);?>">
                                            <span class="pcoded-mtext">Reporte Documentos</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <?php if(in_array(6, session()->get('registroPermisos')) || in_array(17, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'reporte_fecha_mecanizada') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'reporte_fecha_mecanizada');?>">
                                            <span class="pcoded-mtext">Reporte por Fecha Mecanizada</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <?php if(in_array(8, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'reporte') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'reporte');?>">
                                            <span class="pcoded-mtext">Reporte Ejecutivo</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <?php if(in_array(16, session()->get('registroPermisos'))){?>
                                    <li class="pcoded-hasmenu <?= (isset($menu_actual) && ($menu_actual == $row['controlador'].'reporte_responsable' || $menu_actual == $row['controlador'].'reporte_mis_tramites' || $menu_actual == $row['controlador'].'reporte_documentos') ) ? 'pcoded-trigger' : '';?> ">
                                        <a href="javascript:void(0)">
                                            <span class="pcoded-mtext">Reportes Administración</span>
                                        </a>
                                        <ul class="pcoded-submenu">
                                            <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'reporte_responsable') ? 'active' : '';?>">
                                                <a href="<?= base_url($row['controlador'].'reporte_responsable');?>">
                                                    <span class="pcoded-mtext">Por Responsable</span>
                                                </a>
                                            </li>
                                            <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'reporte_mis_tramites') ? 'active' : '';?>">
                                                <a href="<?= base_url($row['controlador'].'reporte_mis_tramites');?>">
                                                    <span class="pcoded-mtext">Bandeja de Trámites</span>
                                                </a>
                                            </li>
                                            <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'reporte_documentos') ? 'active' : '';?>">
                                                <a href="<?= base_url('documentos/reporte_documentos/'.$row['id']);?>">
                                                    <span class="pcoded-mtext">Documentos por Usuario</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                <?php }?>

                            </ul>
                        </li>
                    <?php }?>

                    <?php if($row['controlador'] == 'mineria_ilegal/'){?>
                        <li class="pcoded-hasmenu <?= (isset($menu_actual) && strpos($menu_actual, $row['controlador']) !== false ) ? 'pcoded-trigger' : '';?>">
                            <a href="javascript:void(0)">
                                <span class="pcoded-micon"><i class="feather icon-folder"></i></span>
                                <span class="pcoded-mtext"><?= $row['menu'] ?></span>
                            </a>
                            <ul class="pcoded-submenu">

                                <?php if(in_array(15, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'denuncia_manual_fmi') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'denuncia_manual_fmi');?>">
                                            <span class="pcoded-mtext">Agregar F.M.I. Manual</span>
                                        </a>
                                    </li>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'denuncia_manual') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'denuncia_manual');?>">
                                            <span class="pcoded-mtext">Agregar H.R. y F.M.I. Manual</span>
                                        </a>
                                    </li>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'listado_denuncias_manuales') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'listado_denuncias_manuales');?>">
                                            <span class="pcoded-mtext">Mis Denuncias Manuales</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <?php if(in_array(12, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'denuncias_web') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'denuncias_web');?>">
                                            <span class="pcoded-mtext">Denuncias Página Web</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <?php if(in_array(13, session()->get('registroPermisos')) || in_array(14, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'agregar_oficio') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'agregar_oficio');?>">
                                            <span class="pcoded-mtext">Verificación de Oficio</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'documento/listado') ? 'active' : '';?>">
                                    <a href="<?= base_url('documentos/listado/'.$row['id']);?>">
                                        <span class="pcoded-mtext">Mis Documentos</span>
                                    </a>
                                </li>

                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'listado_recepcion') ? 'active' : '';?>">
                                    <a href="<?= base_url($row['controlador'].'listado_recepcion');?>">
                                        <span class="pcoded-mtext">Recepción de H.R.</span>
                                    </a>
                                </li>

                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'mis_tramites') ? 'active' : '';?>">
                                <a href="<?= base_url($row['controlador'].'mis_tramites');?>">
                                        <span class="pcoded-mtext">Mis Hojas de Ruta</span>
                                    </a>
                                </li>

                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'correspondencia_externa') ? 'active' : '';?>">
                                <a href="<?= base_url('correspondencia_externa/mis_recepciones/'.$row['id']);?>">
                                        <span class="pcoded-mtext">Mi Correspondencia Externa</span>
                                    </a>
                                </li>

                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'buscador') ? 'active' : '';?>">
                                    <a href="<?= base_url($row['controlador'].'buscador');?>">
                                        <span class="pcoded-mtext">Buscador Hojas Rutas</span>
                                    </a>
                                </li>

                                <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'historico_sincobol') ? 'active' : '';?>">
                                    <a href="<?= base_url($row['controlador'].'historico_sincobol');?>">
                                        <span class="pcoded-mtext">Histórico SINCOBOL</span>
                                    </a>
                                </li>

                                <?php if(in_array(18, session()->get('registroPermisos')) || in_array(19, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'reporte_denuncias_fechas') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'reporte_denuncias_fechas');?>">
                                            <span class="pcoded-mtext">Reporte Denuncias por Fecha</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <?php if(in_array(8, session()->get('registroPermisos'))){?>
                                    <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'reporte') ? 'active' : '';?>">
                                        <a href="<?= base_url($row['controlador'].'reporte');?>">
                                            <span class="pcoded-mtext">Reporte Ejecutivo</span>
                                        </a>
                                    </li>
                                <?php }?>

                                <?php if(in_array(16, session()->get('registroPermisos'))){?>
                                    <li class="pcoded-hasmenu <?= (isset($menu_actual) && ($menu_actual == $row['controlador'].'reporte_responsable' || $menu_actual == $row['controlador'].'reporte_mis_tramites' || $menu_actual == $row['controlador'].'reporte_documentos') ) ? 'pcoded-trigger' : '';?> ">
                                        <a href="javascript:void(0)">
                                            <span class="pcoded-mtext">Reportes Administración</span>
                                        </a>
                                        <ul class="pcoded-submenu">                                            
                                            <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'reporte_mis_tramites') ? 'active' : '';?>">
                                                <a href="<?= base_url($row['controlador'].'reporte_mis_tramites');?>">
                                                    <span class="pcoded-mtext">Bandeja de Trámites</span>
                                                </a>
                                            </li>
                                            <li class="<?= (isset($menu_actual) && $menu_actual == $row['controlador'].'reporte_documentos') ? 'active' : '';?>">
                                                <a href="<?= base_url('documentos/reporte_documentos/'.$row['id']);?>">
                                                    <span class="pcoded-mtext">Documentos por Usuario</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                <?php }?>

                            </ul>
                        </li>
                    <?php }?>

                </ul>
            <?php }?>
        <?php } ?>

        <div class="pcoded-navigatio-lavel">Hojas de Ruta</div>
        <ul class="pcoded-item pcoded-left-item">
            <li class="<?= (isset($menu_actual) && $menu_actual === 'libro_registro') ? 'active' : '';?>">
                <a href="<?= base_url('libro_registro');?>">
                    <span class="pcoded-micon"><i class="feather icon-printer"></i></span>
                    <span class="pcoded-mtext">Imprimir Libro de Registro</span>
                </a>
            </li>
        </ul>

        <div class="pcoded-navigatio-lavel">Documentación Sistema</div>
        <ul class="pcoded-item pcoded-left-item">
            <li class="<?= (isset($menu_actual) && $menu_actual === 'video_tutorial') ? 'active' : '';?>">
                <a href="<?= base_url('video_tutorial');?>">
                    <span class="pcoded-micon"><i class="feather icon-monitor"></i></span>
                    <span class="pcoded-mtext">Video Tutoriales</span>
                </a>
            </li>
        </ul>

    </div>
</nav>