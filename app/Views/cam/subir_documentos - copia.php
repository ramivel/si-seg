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
                        <h3><?= $fila['correlativo'];?></h3>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <h4 class="sub-title mb-2">DATOS HOJA RUTA MADRE</h4>
                            <table class="table table-bordered">
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
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <?php for($i=0;$i<count($cabecera_documentos);$i++){?>
                                        <th class="text-center"><?php echo $cabecera_documentos[$i];?></th>
                                        <?php }?>
                                        <th class="text-center">Subir Archivo<br>(.pdf)</th>
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
                                                    <td>NO TIENE</td>
                                                <?php }?>
                                            <?php }else{?>
                                                <td><?= $row[$campos_documentos[$i]];?></td>
                                            <?php }?>
                                        <?php }?>
                                        <td>
                                            <input type="file" name="subir archivo" />
                                        </td>
                                        <td>
                                            <button name="subir" class="btn btn-success">SUBIR</button>
                                        </td>
                                    </tr>
                                    <?php }?>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>