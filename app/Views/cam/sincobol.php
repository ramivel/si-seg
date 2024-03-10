<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row mb-3">
            <div class="col-sm-12 text-left">
                <a href="<?= $url_atras;?>" class="btn btn-success"><i class="feather icon-arrow-left"></i> ATRAS</a>
                <a href="<?= $sincobol."correspondencia/hoja_ruta/hr_pdf/".$fila['fk_hoja_ruta'];?>" target="_blank" class="btn btn-warning"><i class="fa fa-print"></i> IMPRIMIR H.R.</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header text-center">                        
                        <h4><?= $fila['correlativo'];?></h4>
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

                        <?php if(count($hr_anexadas) > 0){?>
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
                                        <th class="text-nowrap" scope="row">Solicitante:</th>
                                        <td><?= $fila['titular'];?></td>
                                        <th class="text-nowrap" scope="row">Clasificación APM:</th>
                                        <td><?= $fila['clasificacion_titular'];?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered">
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
                        </div>
                        <div id="tabla_datos" class="row mb-1">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs md-tabs " role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= $url_acto_administrativo;?>#tabla_datos" ><strong>SEGUIMIENTO TRÁMITE</strong></a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= $url_externa;?>#tabla_datos"><strong>CORRESPONDENCIA EXTERNA</strong></a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#tabla_datos"><strong>HISTÓRICO SINCOBOL</strong></a>
                                        <div class="slide"></div>
                                    </li>                                    
                                </ul>                                
                            </div>
                        </div>
                        <!--div class="row mb-4">
                            <div class="col-md-10">
                                <h4 class="sub-title mt-2 mb-2">Actos Administrativos</h4>
                            </div>
                            <div class="col-md-2 text-center">
                                <button class="btn btn-success"><i class="fa fa fa-download"></i> Descargar (Excel)</button>
                            </div>
                        </!--div-->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <?php for($i=0;$i<count($cabecera_derivacion);$i++){?>
                                        <th class="text-center"><?php echo $cabecera_derivacion[$i];?></th>
                                        <?php }?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($derivaciones) && count($derivaciones)>0){?>
                                    <?php foreach ($derivaciones as $n=>$derivacion){?>
                                    <tr>
                                        <?php for($i=0;$i<count($campos_derivacion);$i++){?>
                                        <td><?= $derivacion[$campos_derivacion[$i]];?></td>
                                        <?php }?>
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