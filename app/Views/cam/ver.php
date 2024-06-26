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
                                        <th class="text-nowrap" scope="row">Representante Legal:</th>
                                        <td><?= $fila['representante_legal'];?></td>
                                        <th class="text-nowrap" scope="row">Nacionalidad:</th>
                                        <td><?= $fila['nacionalidad'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap" scope="row">Solicitante:</th>
                                        <td><?= $fila['titular'];?></td>
                                        <th class="text-nowrap" scope="row">Clasificación APM:</th>
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
                        <div id="tabla_datos" class="row mb-1">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs md-tabs " role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#tabla_datos" ><strong>SEGUIMIENTO TRÁMITE</strong></a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= $url_externa;?>#tabla_datos"><strong>CORRESPONDENCIA EXTERNA</strong></a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= $url_sincobol;?>#tabla_datos"><strong>HISTÓRICO SINCOBOL</strong></a>
                                        <div class="slide"></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!--div class="row mt-4 mb-4">
                            <div class="col-md-10">
                                <h4 class="sub-title mt-2 mb-2">Actos Administrativos</h4>
                            </div>
                            <div class="col-md-2 text-center">
                                <button class="btn btn-success"><i class="fa fa fa-download"></i> Descargar (Excel)</button>
                            </div>
                        </!--div-->
                        <div class="table-responsive">
                            <table class="table table-bordered">
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
                                    <tr class="<?= (count($derivaciones) == ($n+1) ? "table-warning" : "") ?>">
                                        <?php for($i=0;$i<count($campos_derivacion);$i++){?>
                                            <?php if($campos_derivacion[$i]=='adjunto_pdf'){ ?>
                                                <?php if($derivacion[$campos_derivacion[$i]]){?>
                                                    <td><a href="<?= base_url($ruta_archivos.$derivacion[$campos_derivacion[$i]]);?>" target="_blank" title="Ver Documento"><i class="feather icon-file"></i> Ver Documento</a></td>
                                                <?php }else{?>
                                                    <td></td>
                                                <?php }?>
                                            <?php }elseif($campos_derivacion[$i]=='estado'){?>
                                                <td>
                                                    <?php
                                                    $style = '';
                                                    switch($derivacion[$campos_derivacion[$i]]){
                                                        case 'MIGRADO':
                                                            $style = 'btn btn-sm btn-danger btn-round';
                                                            break;
                                                        case 'ATENDIDO':
                                                            $style = 'btn btn-sm btn-success btn-round';
                                                            break;
                                                        case 'RECIBIDO':
                                                            $style = 'btn btn-sm btn-primary btn-round';
                                                            break;
                                                        case 'DERIVADO':
                                                            $style = 'btn btn-sm btn-primary btn-round';
                                                            break;
                                                        case 'DEVUELTO':
                                                            $style = 'btn btn-sm btn-warning btn-round';
                                                            break;
                                                        case 'FINALIZADO':
                                                            $style = 'btn btn-sm btn-danger btn-round';
                                                            break;
                                                        case 'EN ESPERA':
                                                            $style = 'btn btn-sm btn-info btn-round';
                                                            break;
                                                    }
                                                    echo '<button class="'.$style.'">'.$derivacion[$campos_derivacion[$i]].'</button>';
                                                    ?>
                                                </td>
                                            <?php }elseif($campos_derivacion[$i]=='apm_presento'){?>
                                                <td>
                                                    <?php
                                                    if($derivacion['recurso_jerarquico']=='t')
                                                        echo 'RECURSO JERÁRQUICO<br>';
                                                    if($derivacion['recurso_revocatoria']=='t')
                                                        echo 'RECURSO DE REVOCATORIA<br>';
                                                    if($derivacion['oposicion']=='t')
                                                        echo 'OPOSICIÓN';
                                                    ?>
                                                </td>
                                            <?php }else{?>
                                                <td><?= $derivacion[$campos_derivacion[$i]];?></td>
                                            <?php }?>
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