<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row mb-3">
            <div class="col-sm-12 text-left">
                <a href="<?= $url_atras;?>" class="btn btn-success"><i class="feather icon-arrow-left"></i> ATRAS</a>
                <a href="<?= base_url($controlador.'hoja_ruta_pdf/'.$hoja_ruta['id']);?>" target="_blank" class="btn btn-warning"><i class="fa fa-print"></i> IMPRIMIR H.R.</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h3><?= $hoja_ruta['correlativo'];?></h3>
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
                        <?php if($denuncia['fk_tipo_denuncia']==3){?>
                            <h4 class="sub-title mt-2 mb-2">Origen</h4>
                            <div class="table-responsive">
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
                        <?php if(isset($areas_mineras) && count($areas_mineras)>0){?>
                        <h4 class="sub-title mt-4 mb-2">Área(s) Minera(s) Identificada(s)</h4>
                        <div class="table-responsive">
                            <?php if($denuncia['areas_denunciadas']){ ?>
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="text-nowrap text-right" width="320px" scope="row">Área(s) denunciada(s) que se encuentran en trámite en la AJAM :</th>
                                            <td><?= $denuncia['areas_denunciadas'];?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php }?>
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap text-center" scope="row">Código Único</th>
                                        <th class="text-nowrap text-center" scope="row">Área Minera</th>
                                        <th class="text-nowrap text-center" scope="row">Tipo Área</th>
                                        <th class="text-nowrap text-center" scope="row">Extensión</th>
                                        <th class="text-nowrap text-center" scope="row">Titular</th>
                                        <th class="text-nowrap text-center" scope="row">Clasificación</th>
                                        <th class="text-nowrap text-center" scope="row">Departamento(s)</th>
                                        <th class="text-nowrap text-center" scope="row">Provincia(s)</th>
                                        <th class="text-nowrap text-center" scope="row">Municipio(s)</th>
                                    </tr>
                                </thead>
                                <tbody>                                
                                    <?php foreach($areas_mineras as $row){?>
                                        <tr>
                                            <td class="text-center"><?= $row['codigo_unico'];?></td>
                                            <td class="text-center"><?= $row['area_minera'];?></td>
                                            <td class="text-center"><?= $row['tipo_area_minera'];?></td>
                                            <td class="text-center"><?= $row['extension'];?></td>
                                            <td class="text-center"><?= $row['titular'];?></td>
                                            <td class="text-center"><?= $row['clasificacion'];?></td>
                                            <td class="text-center"><?= $row['departamentos'];?></td>
                                            <td class="text-center"><?= $row['provincias'];?></td>
                                            <td class="text-center"><?= $row['municipios'];?></td>
                                        </tr>
                                    <?php }?>                                
                                </tbody>
                            </table>
                        </div>
                        <?php }?>
                        <h4 class="sub-title mt-4 mb-2">Coordenada(s) Geográfica(s)</h4>
                        <div class="table-responsive">
                            <div id="mi-map" class="set-map"></div>
                            <table class="table table-bordered mt-2 mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap text-center" scope="row">N°</th>
                                        <th class="text-nowrap text-center" scope="row">Longitud</th>
                                        <th class="text-nowrap text-center" scope="row">Latitud</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($coordenadas as $n=>$coordenada){?>
                                    <tr>
                                        <td class="text-center"><?= $n+1;?></td>
                                        <td class="text-center"><?= $coordenada['longitud'];?></td>
                                        <td class="text-center"><?= $coordenada['latitud'];?></td>
                                    </tr>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>
                        <?php if(isset($adjuntos) && count($adjuntos)>0){?>
                        <h4 class="sub-title mt-4 mb-2">Adjunto(s)</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap text-center" scope="row">Tipo</th>
                                        <th class="text-nowrap text-center" scope="row">Nombre</th>
                                        <th class="text-nowrap text-center" scope="row">Cite</th>
                                        <th class="text-nowrap text-center" scope="row">Fecha</th>
                                        <th class="text-nowrap text-center" scope="row">Adjunto</th>
                                    </tr>
                                </thead>
                                <tbody>                                    
                                    <?php foreach($adjuntos as $i=>$row){?>
                                        <tr>
                                            <td class="text-center"><?= $row['tipo'];?></td>
                                            <td class="text-center"><?= $row['nombre'];?></td>
                                            <td class="text-center"><?= $row['cite'];?></td>
                                            <td class="text-center"><?= $row['fecha_cite'];?></td>
                                            <td class="text-center"><a href="<?= base_url($row['adjunto']);?>" class="btn btn-sm btn-inverse" target="_blank"><i class="icofont icofont-download-alt"></i> Ver Adjunto</a></td>
                                        </tr>
                                    <?php }?>                                    
                                </tbody>
                            </table>
                        </div>
                        <?php }?>
                        <div class="row mt-4 mb-4">
                            <div class="col-md-12">
                                <h4 class="sub-title mt-2 mb-2">Seguimiento de la Hoja de Ruta</h4>
                            </div>
                            <!--
                            <div class="col-md-2 text-center">
                                <button class="btn btn-success"><i class="fa fa fa-download"></i> Descargar (Excel)</button>
                            </div>
                            -->
                        </div>
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
                                                        case 'REGULARIZACIÓN':
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