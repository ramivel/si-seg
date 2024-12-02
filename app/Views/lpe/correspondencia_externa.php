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
                    <div class="card-header text-center pb-2">
                        <h3 class="mb-1"><?= $fila['correlativo'];?></h3>
                    </div>
                    <div class="card-block">
                        <?= $informacion_tramite;?>
                        <h4 class="sub-title mb-2">Datos del Área Minera</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Código Único:</th>
                                        <td><?= $fila['codigo_unico'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Extensión:</th>
                                        <td><?= $fila['extension'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap" scope="row">Denominación:</th>
                                        <td><?= $fila['denominacion'];?></td>
                                        <th class="text-nowrap" scope="row">Regional:</th>
                                        <td><?= $fila['regional'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap" scope="row">Titular:</th>
                                        <td><?= $fila['titular'];?></td>
                                        <th class="text-nowrap" scope="row">Clasificación Titular:</th>
                                        <td><?= $fila['clasificacion_titular'];?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Departameto(s):</th>
                                        <td><?= $fila['departamentos'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Provincia(s):</th>
                                        <td><?= $fila['provincias'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Municipio(s):</th>
                                        <td><?= $fila['municipios'];?></td>
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
                                            <button type="button" class="btn btn-success recibir_correspondencia" data-direccion="<?= base_url($accion)?>" data-idext="<?=$row['id'];?>" data-docext='<?=$row['documento_externo'];?>'>Recibir Correspondencia</button>
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