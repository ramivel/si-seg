<div id="tabla_datos" class="col-sm-12">
    <?= $tabs;?>
    <!-- Tab panes -->
    <div class="tab-content tabs card-block" style="background-color:#fff; border-left: 1px solid #ddd; border-right: 1px solid #ddd; border-bottom: 1px solid #ddd;">
        <div class="tab-pane active">
            <?php if($datos){?>
                <h4 class="sub-title mb-2"><strong>INFORMACIÓN DEL ANALISTA RESPONSABLE</strong></h4>
                <div class="table-responsive">
                    <table class="table table-bordered mb-2">
                        <tbody>
                            <tr>
                                <th class="text-nowrap" width="180px" scope="row">Nombres y Apellidos:</th>
                                <td><?= $datos['persona_responsable'];?></td>
                                <th class="text-nowrap" width="180px" scope="row">Cargo:</th>
                                <td><?= $datos['cargo_responsable'];?></td>
                            </tr>
                            <tr>
                                <th class="text-nowrap" scope="row">Departamental:</th>
                                <td><?= $datos['oficina_responsable'];?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h4 class="sub-title mb-2"><strong>ACTO ADMINISTRATIVO </strong></h4>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th class="text-nowrap" width="180px" scope="row">Estado del Tramite:</th>
                                <td colspan="3"><?= $datos['estado_tramite'];?></td>
                                <th class="text-nowrap" scope="row">Fecha de Actualización:</th>
                                <td><?= $datos['fecha_actualizacion'];?></td>
                            </tr>
                            <tr>
                                <th class="text-nowrap" scope="row">Acto Administrativo:</th>
                                <td><?= $datos['acto_administrativo'];?></td>
                                <th class="text-nowrap" scope="row">Fecha de Emisión:</th>
                                <td><?= $datos['fecha_emision'];?></td>
                                <th class="text-nowrap" scope="row">Fecha de Notificación:</th>
                                <td><?= $datos['fecha_notificacion'];?></td>
                            </tr>
                            <tr>
                                <th class="text-nowrap" width="180px" scope="row">Observación:</th>
                                <td colspan="5"><?= $datos['observaciones'];?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php }?>
        </div>
    </div>
</div>