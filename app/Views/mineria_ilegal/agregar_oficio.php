<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Formulario</h5>
                        <span>Los campos con <code>*</code> son obligatorios.</span>
                    </div>
                    <div class="card-block">
                        <?= form_open_multipart($accion, ['id'=>'formulario']);?>

                        <!-- Row start -->
                        <div class="row">
                            <div class="col-lg-12 col-xl-12">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs  tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#origen" role="tab"><strong>Origen</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#descripcion_explotacion" role="tab"><strong>Descripción de la Actividad Minera</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#areas_mineras_identificadas" role="tab"><strong>Área(s) Minera(s) Identificada(s)</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#coordenadas_geograficas" onclick="redimensionar()" role="tab"><strong>Coordenada(s) Geográfica(s)</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#adjuntos" role="tab"><strong>Adjunto(s)</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#hoja_ruta" role="tab"><strong>Hoja de Ruta</strong></a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content tabs card-block">
                                    <div class="tab-pane active" id="origen" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Tipo de Origen * :</label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'origen_oficio';
                                                            echo form_dropdown($campo, $tipos_origen_oficio, set_value($campo, set_value($campo,'')), array('id' => $campo, 'class' => 'form-control'));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Enlace <span class="mytooltip tooltip-effect-5">
                                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                                        <span class="tooltip-content clearfix">
                                                            <span class="tooltip-text">Debe escribir la dirección URL de la página que hace referencia a la actividad ilegal, Ejemplo: https://www.la-razon.com/.</span>
                                                        </span>
                                                    </span> : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'enlace';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                        
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Informe Técnico <span class="mytooltip tooltip-effect-5">
                                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                                        <span class="tooltip-content clearfix">
                                                            <span class="tooltip-text">Debe escribir el correlativo, ejemplo: AJAM/DCCM/INF-TEC/XX/XXXX</span>
                                                        </span>
                                                    </span> * : </label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'informe_tecnico_numero';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Fecha * :</label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'informe_tecnico_fecha';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'type' => 'date',
                                                                'class' => 'form-control',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-6 col-form-label">Documento de Digital (Max. 20MB) * :</label>
                                                    <div class="col-sm-6">
                                                        <?php
                                                        $campo = 'informe_tecnico_digital';
                                                        echo form_upload(array(
                                                            'name' => $campo,
                                                            'id' => $campo,
                                                            'class' => 'form-control',
                                                            'accept' => '.pdf',
                                                        ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if (isset($validation) && $validation->hasError($campo)) { ?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo); ?></span>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Breve Descripción * :</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'descripcion_oficio';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,'',false)
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Anexar H.R. <span class="mytooltip tooltip-effect-5">
                                                <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                                <span class="tooltip-content clearfix">
                                                    <span class="tooltip-text">Debe escribir el correlativo de la Hoja de Ruta Interna o Externa que desea anexar.</span>
                                                </span>
                                            </span> : </label>
                                            <div class="col-sm-8">
                                                <?php $campo = 'fk_hoja_ruta';?>
                                                <select id="<?= $campo;?>" name="<?= $campo;?>" class="hr-in-ex-ajax col-sm-12">
                                                    <option value="">Escriba el correlativo de la Hoja de Ruta Interna o Externa...</option>
                                                </select>
                                                <span class="messages"></span>
                                                <span class="form-bar"><b>Nota.</b> La H.R. no deben estar archivadas o anexadas en el SINCOBOL caso contrario no aparecera para su selección.</span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-info agregar_hr_in_ex"><i class="fa fa-paperclip"></i> Anexar Hoja de Ruta</button>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <table id="tabla_hojas_rutas" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Tipo H.R.</th>
                                                            <th class="text-center">Correlativo</th>
                                                            <th class="text-center">Fecha</th>
                                                            <th class="text-center">Referencia</th>
                                                            <th class="text-center">Remitente Externo/Interno</th>
                                                            <th class="text-center">Cite Externo/Interno</th>                                                            
                                                            <th class="text-center"> </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if(isset($hojas_rutas) && count($hojas_rutas)>0){?>
                                                        <?php foreach($hojas_rutas as $row){?>
                                                            <tr id="am<?= $row['id'];?>">
                                                                <td class="text-center form-group"><input type="hidden" name="id_areas_mineras[]" value="<?= $row['id'];?>" /><?= $row['codigo_unico'];?></td>
                                                                <td class="text-center"><?= $row['area_minera'];?></td>
                                                                <td class="text-center"><?= $row['tipo_area_minera'];?></td>
                                                                <td class="text-center"><?= $row['extension'];?></td>
                                                                <td class="text-center"><?= $row['titular'];?></td>
                                                                <td class="text-center"><?= $row['clasificacion'];?></td>
                                                                <td class="text-center"><?= $row['departamentos'];?></td>
                                                                <td class="text-center"><?= $row['provincias'];?></td>
                                                                <td class="text-center"><?= $row['municipios'];?></td>
                                                                <td class='text-center'>
                                                                    <button type="button" class="btn btn-sm btn-danger waves-effect waves-light" title="Desanexar Área Minera" onclick="desanexar_area_minera_mineria_ilegal(<?= $row['id'];?>);"><span class="icofont icofont-ui-delete"></span></button>
                                                                </td>
                                                            </tr>
                                                        <?php }?>
                                                    <?php }?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'hr_anexados';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'type' => 'hidden',
                                                                'value' => set_value($campo, '')
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="descripcion_explotacion" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Departamento * :</label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'departamento';
                                                            echo form_dropdown($campo, $departamentos, set_value($campo, set_value($campo,(isset($municipio[$campo]) ? $municipio[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Provincia * :</label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'provincia';
                                                            echo form_dropdown($campo, $provincias, set_value($campo, set_value($campo,(isset($municipio[$campo]) ? $municipio[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Municipio * :</label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'fk_municipio';
                                                            echo form_dropdown($campo, $municipios, set_value($campo, set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Comunidad/Localidad * : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'comunidad_localidad';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Descripción del lugar o punto de referencia * : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'descripcion_lugar';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '3',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Nombre(s) del posible(s) autor(es) : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'autores';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '3',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Nombre de la persona(s) jurídica(s) (empresa, cooperativa u otro) que este vinculado(s) a la actividad : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'persona_juridica';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '3',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Descripción de la maquinaria(s) u objeto(s) utilizado(s) en la actividad : </label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $campo = 'descripcion_materiales';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '3',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="areas_mineras_identificadas" role="tabpanel">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Buscador Área Minera : </label>
                                            <div class="col-sm-8">
                                                <?php $campo = 'fk_area_minera';?>
                                                <select id="<?= $campo;?>" name="<?= $campo;?>" class="area-minera-mineria-ilegal-ajax col-sm-12">
                                                    <option value="">Escriba el Código Único o Denominación</option>
                                                </select>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-info agregar_area_minera_mineria_ilegal"><i class="fa fa-paperclip"></i> Anexar Área Minera</button>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-sm-12">
                                            <table id="tabla_areas_mineras" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Código Único</th>
                                                        <th class="text-center">Área Minera</th>
                                                        <th class="text-center">Tipo Área</th>
                                                        <th class="text-center">Extensión</th>
                                                        <th class="text-center">Titular</th>
                                                        <th class="text-center">Clasificación</th>
                                                        <th class="text-center">Departamento(s)</th>
                                                        <th class="text-center">Provincia(s)</th>
                                                        <th class="text-center">Municipio(s)</th>
                                                        <th class="text-center"> </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if(isset($areas_mineras) && count($areas_mineras)>0){?>
                                                    <?php foreach($areas_mineras as $row){?>
                                                        <tr id="am<?= $row['id'];?>">
                                                            <td class="text-center form-group"><input type="hidden" name="id_areas_mineras[]" value="<?= $row['id'];?>" /><?= $row['codigo_unico'];?></td>
                                                            <td class="text-center"><?= $row['area_minera'];?></td>
                                                            <td class="text-center"><?= $row['tipo_area_minera'];?></td>
                                                            <td class="text-center"><?= $row['extension'];?></td>
                                                            <td class="text-center"><?= $row['titular'];?></td>
                                                            <td class="text-center"><?= $row['clasificacion'];?></td>
                                                            <td class="text-center"><?= $row['departamentos'];?></td>
                                                            <td class="text-center"><?= $row['provincias'];?></td>
                                                            <td class="text-center"><?= $row['municipios'];?></td>
                                                            <td class='text-center'>
                                                                <button type="button" class="btn btn-sm btn-danger waves-effect waves-light" title="Desanexar Área Minera" onclick="desanexar_area_minera_mineria_ilegal(<?= $row['id'];?>);"><span class="icofont icofont-ui-delete"></span></button>
                                                            </td>
                                                        </tr>
                                                    <?php }?>
                                                <?php }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="coordenadas_geograficas" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <div id="mi-map" class="set-map"></div>
                                            </div>
                                            <div class="col-md-12 col-sm-12 mt-4">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Coordenada(s) : </label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'coordenadas';
                                                            echo form_textarea(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'rows' => '3',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($coordenadas) ? $coordenadas : ''),false)
                                                            ));
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button type="button" class="btn btn-warning" onclick="limpiarCoordenadas();"><i class="fa fa-trash-o"></i> Borrar Coordenada(s) </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="adjuntos" role="tabpanel">
                                        <p>Puede agregar imágenes, documentos, vídeos y audios que permitan una mayor claridad sobre los hechos denunciados.</p>
                                        <p>Extensiones permitidas de Imágen: jpg, gif, bmp, png; de Documento: txt, doc, docx, pdf; de Vídeo: avi, mp4, mpeg, mwv; de Audio: mp3, wav, wma con un tamaño máximo de 20 MB.</p>
                                        <div class="row">
                                            <div class="col-sm-12 text-center mb-3">
                                                <button type="button" class="btn btn-inverse agregar_adjunto"><i class="fa fa-plus-square"></i> Agregar Nuevo Adjunto</button>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                            <table id="tabla_adjuntos" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Tipo</th>
                                                            <th class="text-center">Nombre</th>
                                                            <th class="text-center">Adjunto</th>
                                                            <th class="text-center"> </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="hoja_ruta" role="tabpanel">
                                    <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Dirección Departamental/Regional * :</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'fk_oficina';
                                                    echo form_dropdown($campo, $oficinas, set_value($campo, set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Destinatario*:</label>
                                            <div class="col-sm-10">
                                                <?php $campo = 'fk_usuario_destinatario';?>
                                                <select id="<?= $campo;?>" name="<?= $campo;?>" class="analista-destinatario-mineria-ilegal-ajax col-sm-12">
                                                    <?php if(isset($usu_destinatario)){ ?>
                                                        <option value="<?= $usu_destinatario['id'];?>"><?= $usu_destinatario['nombre'];?></option>
                                                    <?php }else{ ?>
                                                        <option value="">Escriba el Nombre o Cargo...</option>
                                                    <?php } ?>
                                                </select>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Instrucción*:</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'instruccion';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,'',false)
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row end -->
                        <div class="form-group row">
                            <label class="col-sm-4"></label>
                            <div class="col-sm-8 text-right">
                                <?php echo form_submit('enviar', 'GUARDAR','class="btn btn-primary m-b-0"');?>
                                <?php echo anchor($controlador.'mis_ingresos', 'CANCELAR',array('class' =>'btn btn-success m-b-0'));?>
                            </div>
                        </div>
                        <?= form_close();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
