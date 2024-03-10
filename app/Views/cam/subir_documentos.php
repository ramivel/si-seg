<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Cargar Documento(s) Anexado(s)</h5>
                        <span>Se debe subir los documentos digitales para terminar la derivación.</span>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <h4 class="sub-title mb-2">DATOS HOJA RUTA MADRE</h4>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap" scope="row">Correlativo:</th>
                                        <td colspan="3"><?= $fila['correlativo'];?></td>
                                    </tr>
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
                                </tbody>
                            </table>
                        </div>
                        <h4 class="sub-title mb-2">Datos del Área Minera</h4>
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
                                        <th class="text-nowrap" scope="row">Solicitante:</th>
                                        <td><?= $fila['titular'];?></td>
                                        <th class="text-nowrap" scope="row">Clasificación del APM:</th>
                                        <td><?= $fila['clasificacion_titular'];?></td>
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
                        <div class="row mt-4 mb-4">
                            <div class="col-md-10">
                                <h4 class="sub-title mt-2 mb-2">Documento(s) Anexado(s)</h4>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <?= form_open_multipart('', ['id'=>'formulario_subir_documentos']);?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <?php for($i=0;$i<count($cabecera_documentos);$i++){?>
                                        <th class="text-center"><?php echo $cabecera_documentos[$i];?></th>
                                        <?php }?>
                                        <th class="text-center">Subir Archivo (.pdf) (Max. 20MB)</th>
                                        <th class="text-center"> </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($documentos) && count($documentos)>0){?>
                                    <?php foreach ($documentos as $row){?>
                                    <tr>
                                        <?php for($i=0;$i<count($campos_documentos);$i++){?>
                                            <?php if($campos_documentos[$i]=='doc_digital'){ ?>
                                                <?php if($row[$campos_documentos[$i]]){?>
                                                    <td><a href="<?= base_url($ruta_archivos.$row[$campos_documentos[$i]]);?>" target="_blank" title="Ver Documento"><i class="feather icon-file"></i> Ver Documento</a></td>
                                                <?php }else{?>
                                                    <td class="text-center"><strong><div id="label-<?= $row['id']?>">NO ADJUNTADO</div></strong></td>
                                                <?php }?>
                                            <?php }else{?>
                                                <td><?= $row[$campos_documentos[$i]];?></td>
                                            <?php }?>
                                        <?php }?>
                                        <td><?php echo form_upload('doc-'.$row['id'],'',array('id'=>'doc-'.$row['id'], 'accept'=>'application/pdf'));?></td>
                                        <td>
                                            <button type="button" class="btn btn-success subir_archivo" data-direccion="<?= base_url($controlador.'ajax_subir_archivo')?>" data-idoc="<?=$row['id'];?>">SUBIR</button>                                            
                                        </td>
                                    </tr>
                                    <?php }?>
                                <?php }?>
                                </tbody>
                            </table>
                            <?= form_close();?>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-12 text-left">
                                <input type="hidden" id="verificar" value="<?= (!empty($documentos) && count($documentos)>0) ? '' : 'true';?>" />
                                <a href="<?= $url_atras;?>" class="btn btn-primary finalizar_cargado">FINALIZAR CARGADO</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>