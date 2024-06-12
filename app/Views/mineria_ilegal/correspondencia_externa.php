<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row mb-3">
            <div class="col-sm-12 text-left">
                <a href="<?= $url_atras;?>" class="btn btn-success"><i class="feather icon-arrow-left"></i> ATRAS</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header text-center">                        
                        <h3><?= $hoja_ruta['correlativo'];?></h3>
                        <h5>Recibir Correspondencia Externa</h5>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <h4 class="sub-title mb-2">Información</h4>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Tipo de Minería Ilegal:</th>
                                        <td><?= $tipo_denuncias[$denuncia['fk_tipo_denuncia']];?></td>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Fecha y hora Hoja Ruta:</th>
                                        <td><?= $hoja_ruta['fecha_hr'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap text-right" scope="row">Correlativo Formulario de Minería Ilegal:</th>
                                        <td><?= $denuncia['correlativo_denuncia'];?></td>
                                        <th class="text-nowrap text-right" scope="row">Fecha y hora del Formulario de Minería Ilegal:</th>
                                        <td><?= $denuncia['fecha_denuncia'];?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php if($denuncia['fk_tipo_denuncia']==3 || (isset($hojas_rutas) && count($hojas_rutas)>0)){?>
                            <h4 class="sub-title mt-2 mb-2">Origen</h4>
                            <div class="table-responsive">
                                <?php if($denuncia['fk_tipo_denuncia']==3){?>
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="text-nowrap text-right" width="320px" scope="row">Tipo de Origen :</th>
                                            <td><?= $denuncia['origen_oficio'];?></td>
                                        </tr>
                                        <?php if($denuncia['enlace']){?>
                                            <tr>
                                                <th class="text-nowrap text-right" scope="row">Enlace :</th>
                                                <td><a href="<?= $denuncia['enlace'];?>" target="_blank"><?= $denuncia['enlace'];?></a></td>
                                            </tr>
                                        <?php }?>
                                        <tr>
                                            <th class="text-nowrap text-right" scope="row">Informe Técnico :</th>
                                            <td>
                                                <?= $denuncia['informe_tecnico_numero'].' DE '.$denuncia['informe_tecnico_fecha'];?>
                                                &nbsp; <a href="<?=base_url($denuncia['informe_tecnico_digital']);?>" class='btn btn-sm btn-inverse' target='_blank' title='Ver Documento'><i class='fa fa-file-pdf-o'></i> Ver Documento</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-nowrap text-right" scope="row">Breve Descripción :</th>
                                            <td><?= $denuncia['descripcion_oficio'];?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php }?>
                                <?php if(isset($hojas_rutas) && count($hojas_rutas)>0){?>
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap text-center" scope="row">Tipo H.R.</th>
                                                <th class="text-nowrap text-center" scope="row">Correlativo</th>
                                                <th class="text-nowrap text-center" scope="row">Fecha</th>
                                                <th class="text-nowrap text-center" scope="row">Referencia</th>
                                                <th class="text-nowrap text-center" scope="row">Remitente Externo/Interno</th>
                                                <th class="text-nowrap text-center" scope="row">Cite Externo/Interno</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($hojas_rutas as $row){?>
                                                <tr>
                                                    <td class="text-center"><?= $row['tipo_hoja_ruta'];?></td>
                                                    <td class="text-center"><?= $row['correlativo'];?></td>
                                                    <td class="text-center"><?= $row['fecha'];?></td>
                                                    <td class="text-center"><?= $row['referencia'];?></td>
                                                    <td class="text-center"><?= $row['remitente'];?></td>
                                                    <td class="text-center"><?= $row['cite'];?></td>
                                                </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                            <?php }?>
                            </div>
                        <?php }?>
                        <?php if(isset($denunciantes) && count($denunciantes)>0){?>
                            <h4 class="sub-title mt-2 mb-2">Datos del Denunciante(s)</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap text-center" scope="row">Nombre Completo</th>
                                            <th class="text-nowrap text-center" scope="row">Documento Identidad</th>
                                            <th class="text-nowrap text-center" scope="row">Celular</th>
                                            <th class="text-nowrap text-center" scope="row">E-Mail</th>
                                            <th class="text-nowrap text-center" scope="row">Dirección</th>
                                            <th class="text-nowrap text-center" scope="row">Documento Digital</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($denunciantes as $row){?>
                                            <tr>
                                                <td class="text-center"><?= $row['nombres'].' '.$row['apellidos'];?></td>
                                                <td class="text-center"><?= $row['documento_identidad'].' '.$row['expedido'];?></td>
                                                <td class="text-center"><?= $row['telefonos'];?></td>
                                                <td class="text-center"><?= $row['email'];?></td>
                                                <td class="text-center"><?= $row['direccion'];?></td>
                                                <td class='text-center'>
                                                    <a href="<?=base_url($row['documento_identidad_digital']);?>" class='btn btn-sm btn-inverse' target='_blank' title='Ver Documento de Identidad'><i class='fa fa-file-pdf-o'></i> Ver Documento</a> &nbsp;
                                                </td>
                                            </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        <?php }?>
                        <h4 class="sub-title mt-4 mb-2">Descripción de la Actividad Minera</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Departameto(s):</th>
                                        <td><?= $denuncia['departamento'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Provincia(s):</th>
                                        <td><?= $denuncia['provincia'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Municipio(s):</th>
                                        <td><?= $denuncia['municipio'];?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Comunidad/Localidad :</th>
                                        <td><?= $denuncia['comunidad_localidad'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Descripción del lugar o punto de referencia :</th>
                                        <td><?= $denuncia['descripcion_lugar'];?></td>
                                    </tr>
                                    <?php if($denuncia['autores']){ ?>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Nombre(s) del posible(s) autor(es) :</th>
                                        <td><?= $denuncia['autores'];?></td>
                                    </tr>
                                    <?php }?>
                                    <?php if($denuncia['persona_juridica']){ ?>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Nombre de la persona(s) jurídica(s)<br>(empresa, cooperativa u otro) que este vinculado(s) a la actividad :</th>
                                        <td><?= $denuncia['persona_juridica'];?></td>
                                    </tr>
                                    <?php }?>
                                    <?php if($denuncia['descripcion_materiales']){ ?>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Descripción de la maquinaria(s) u objeto(s) utilizado(s) en la actividad :</th>
                                        <td><?= $denuncia['descripcion_materiales'];?></td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-4 mb-4">
                            <div class="col-md-10">
                                <h4 class="sub-title mt-2 mb-2">Correspondencia Externa</h4>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center"> </th>
                                        <?php for($i=0;$i<count($campos_listar);$i++){?>
                                        <th class="text-center"><?php echo $campos_listar[$i];?></th>
                                        <?php }?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($datos) && count($datos)>0){?>
                                    <?php foreach ($datos as $row){?>
                                    <tr id="corre<?= $row['id']; ?>">
                                        <td>
                                            <button type="button" class="btn btn-success recibir_correspondencia" data-direccion="<?= base_url($accion)?>" data-idext="<?=$row['id'];?>" data-docext="<?=$row['documento_externo'];?>">Recibir Correspondencia</button>
                                        </td>
                                        <?php for($i=0;$i<count($campos_reales);$i++){?>
                                            <?php if($campos_reales[$i]=='doc_digital'){ ?>
                                                <td>
                                                    <a href="<?= base_url($row[$campos_reales[$i]]);?>" target="_blank" title="Ver Documento"><i class="feather icon-file"></i> Ver Documento</a>
                                                </td>
                                            <?php }else{?>
                                                <td><?= $row[$campos_reales[$i]];?></td>
                                            <?php }?>
                                        <?php }?>

                                    </tr>
                                    <?php }?>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal fade" id="recibir-correspondencia-modal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Recibir Correspondencia Externa</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row d-none">
                                            <div class="col-sm-10">
                                                <?= form_input(array('type'=>'hidden','name'=>'idext','id'=>'idext'));?>
                                                <span class="messages"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Documento Externo:</label>
                                            <div class="col-sm-9">
                                                <span id="docext"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Tipo Documento*:</label>
                                            <div class="col-sm-9">
                                                <?php
                                                    $campo = 'fk_tipo_documento_externo';
                                                    echo form_dropdown($campo, $tipos_documentos_externos, set_value($campo), array('class' => 'form-control', 'id'=>$campo));
                                                ?>
                                                <span id="<?= 'error_'.$campo;?>"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Observaciones:</label>
                                            <div class="col-sm-9">
                                                <?php
                                                $campo = 'observacion_recepcion';
                                                echo form_textarea(array(
                                                    'name' => $campo,
                                                    'id' => $campo,
                                                    'rows' => '4',
                                                    'class' => 'form-control form-control-uppercase',
                                                    'value' => set_value($campo, 'SIN OBSERVACIONES', false)
                                                ));
                                                ?>
                                                <span id="<?= 'error_'.$campo;?>"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Cerrar</button>
                                        <button type="button" class="btn btn-primary waves-effect waves-light guardar-recibir">Recibir</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>