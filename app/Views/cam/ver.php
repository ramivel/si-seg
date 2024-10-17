<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row mb-3">
            <div class="col-sm-12 text-left">
                <a href="<?= $url_atras;?>" class="btn btn-success"><i class="feather icon-arrow-left"></i> ATRAS</a>
                <a href="<?= base_url($controlador.'hoja_ruta_pdf/'.$fila['id']);?>" target="_blank" class="btn btn-warning"><i class="fa fa-print"></i> IMPRIMIR H.R.</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h3><?= $fila['correlativo'];?></h3>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <h4 class="sub-title mb-2">DATOS HOJA RUTA MADRE</h4>
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row" rowspan="2">Remitente:</th>
                                        <td rowspan="2">
                                            <?= $hr_remitente['nombre_completo'];?>
                                            <br><b><?= $hr_remitente['cargo'];?></b>
                                            <br><b><?= $hr_remitente['institucion'];?></b>
                                        </td>
                                        <th class="text-nowrap" scope="row">Fecha Mecanizada:</th>
                                        <td><?= $fila['fecha_mecanizada'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Cantidad de fojas:</th>
                                        <td><?= $hr_remitente['cantidad_fojas'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Responsable Actual:</th>
                                        <td><?= $ultima_derivacion['usuario_responsable'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Estado Actual del Trámite:</th>
                                        <td><?= $ultima_derivacion['estado_actual_tramite'];?></td>
                                    </tr>
                                    <?php if($ultima_derivacion['recurso_jerarquico']=='t' || $ultima_derivacion['recurso_revocatoria']=='t' || $ultima_derivacion['oposicion']=='t'){?>
                                        <tr>
                                            <th class="text-nowrap" width="180px" scope="row">El APM Presento:</th>
                                            <td colspan="3">
                                                <?php
                                                if($ultima_derivacion['recurso_jerarquico']=='t')
                                                    echo "RECURSO JERÁRQUICO ";
                                                if($ultima_derivacion['recurso_revocatoria']=='t')
                                                    echo "RECURSO DE REVOCATORIA ";
                                                if($ultima_derivacion['oposicion']=='t')
                                                    echo "OPOSICIÓN ";
                                                ?>
                                            </td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                        <?php if(isset($hr_anexadas) && count($hr_anexadas) > 0){?>
                        <div class="table-responsive">
                            <h4 class="sub-title mt-2 mb-2">HOJAS DE RUTA ANEXADAS</h4>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>N°</th>
                                        <th>Correlativo</th>
                                        <th>Fecha</th>
                                        <th>Referencia</th>
                                    </tr>
                                    <?php foreach($hr_anexadas as $n=>$row){?>
                                    <tr>
                                        <td><?= ($n+1);?></td>
                                        <td><?= $row['correlativo'];?></td>
                                        <td><?= $row['fecha'];?></td>
                                        <td><?= $row['referencia'];?></td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                        <?php }?>
                        <h4 class="sub-title mt-2 mb-2">Datos del Área Minera</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Código Único:</th>
                                        <td><?= $area_minera['codigo_unico'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Extensión:</th>
                                        <td><?= $fila['extension'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap" scope="row">Denominación:</th>
                                        <td><?= $area_minera['nombre'];?></td>
                                        <th class="text-nowrap" scope="row">Regional:</th>
                                        <td><?= $area_minera['regional'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap" scope="row">Representante Legal:</th>
                                        <td><?= $fila['representante_legal'];?></td>
                                        <th class="text-nowrap" scope="row">Nacionalidad:</th>
                                        <td><?= $fila['nacionalidad'];?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Solicitante:</th>
                                        <td><?= $fila['titular'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Clasificación APM:</th>
                                        <td><?= $fila['clasificacion_titular'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Teléfono(s):</th>
                                        <td><?= $ultima_derivacion['telefono_solicitante'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Domicilio Legal:</th>
                                        <td colspan="5"><?= $ultima_derivacion['domicilio_legal'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Domicilio Procesal:</th>
                                        <td colspan="5"><?= $ultima_derivacion['domicilio_procesal'];?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Departameto(s):</th>
                                        <td><?= $area_minera['departamentos'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Provincia(s):</th>
                                        <td><?= $area_minera['provincias'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Municipio(s):</th>
                                        <td><?= $area_minera['municipios'];?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php if($fila['area_protegida_adicional']){ ?>
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="text-nowrap" width="180px" scope="row">Restricción(es) Adicional(es):</th>
                                            <td><?= $fila['area_protegida_adicional'];?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php }?>
                        </div>
                        <?php if($registro_minero && $deuda){?>
                            <h4 class="sub-title mt-3">Datos Catastro Minero</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <?php if($registro_minero){?>
                                            <tr>
                                                <th class="text-nowrap" width="350px" scope="row">Fecha de Registro Minero:</th>
                                                <td><?= $registro_minero['fecha_inscripcion_minera'];?></td>
                                                <th class="text-nowrap" width="180px" scope="row">Fecha de Vencimiento:</th>
                                                <td><?= $registro_minero['fecha_vencimiento'];?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-nowrap" scope="row">Número de Gaceta de Publicación de Ley:</th>
                                                <td><?= $registro_minero['gaceta_numero'];?></td>
                                                <th class="text-nowrap" scope="row">Fecha de Publicación de Gaceta:</th>
                                                <td><?= $registro_minero['gaceta_fecha'];?></td>
                                            </tr>
                                        <?php }?>
                                        <?php if($deuda){?>
                                            <tr>
                                                <th class="text-nowrap" width="350px" scope="row">Deuda de Patente Minera (Años):</th>
                                                <td><?= $deuda['anio_deuda'];?></td>
                                                <th class="text-nowrap" width="180px" scope="row">Total (Bs.):</th>
                                                <td><?= $deuda['deuda_total'];?></td>
                                            </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        <?php }?>
                    </div>
                </div>
            </div>

            <?= $seccion;?>

        </div>
    </div>
</div>