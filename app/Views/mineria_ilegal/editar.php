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
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'id_hoja_ruta','id'=>'id_hoja_ruta','value'=>set_value('id_hoja_ruta', (isset($id_hoja_ruta) ? $id_hoja_ruta : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('id_derivacion', set_value('id_derivacion', (isset($derivacion['id']) ? $derivacion['id'] : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('id_denuncia', set_value('id_denuncia', (isset($denuncia['id']) ? $denuncia['id'] : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('tipo_denuncia', set_value('tipo_denuncia', (isset($tipo_denuncia) ? $tipo_denuncia : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('informe_tecnico_digital', set_value('informe_tecnico_digital', (isset($informe_tecnico_digital) ? $informe_tecnico_digital : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'id_denunciantes_ant','id'=>'id_denunciantes_ant','value'=>set_value('id_denunciantes_ant', (isset($id_denunciantes_ant) ? $id_denunciantes_ant : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'id_hojas_rutas_ant','id'=>'id_hojas_rutas_ant','value'=>set_value('id_hojas_rutas_ant', (isset($id_hojas_rutas_ant) ? $id_hojas_rutas_ant : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'id_areas_mineras_ant','id'=>'id_areas_mineras_ant','value'=>set_value('id_areas_mineras_ant', (isset($id_areas_mineras_ant) ? $id_areas_mineras_ant : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('coordenadas_ant', set_value('coordenadas_ant', (isset($coordenadas) ? $coordenadas : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('ultimo_fk_usuario_responsable', set_value('ultimo_fk_usuario_responsable', (isset($hoja_ruta['ultimo_fk_usuario_responsable']) ? $hoja_ruta['ultimo_fk_usuario_responsable'] : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'tmp_adjuntos','id'=>'tmp_adjuntos','value'=>set_value('tmp_adjuntos', count($adjuntos))));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'id_hojas_ruta_anexadas_ant','id'=>'id_hojas_ruta_anexadas_ant','value'=>set_value('id_hojas_ruta_anexadas_ant', (isset($id_hojas_ruta_anexadas_ant) ? $id_hojas_ruta_anexadas_ant : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>


                        <!-- Row start -->
                        <div class="row">
                            <div class="col-lg-12 col-xl-12">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs  tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#ínformacion" role="tab"><strong>Información</strong></a>
                                    </li>
                                    <?php if($tipo_denuncia == 3){?>
                                        <li class="nav-item">
                                            <a class="nav-link " data-toggle="tab" href="#origen" role="tab"><strong>Origen</strong></a>
                                        </li>
                                    <?php }else{?>
                                        <li class="nav-item">
                                            <a class="nav-link " data-toggle="tab" href="#datos_personales" role="tab"><strong>Datos del Denunciante(s)</strong></a>
                                        </li>
                                    <?php }?>
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
                                        <a class="nav-link " data-toggle="tab" href="#estado_mineria_ilegal" role="tab"><strong>Estado</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#derivacion" role="tab"><strong>Derivación</strong></a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content tabs card-block">
                                    <div class="tab-pane active" id="ínformacion" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-5 col-form-label">Tipo de Minería Ilegal : </label>
                                                    <div class="col-sm-7">
                                                        <?php
                                                            $campo = 'fk_tipo_denuncia';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'readonly' => 'true',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $tipo_denuncias[$denuncia[$campo]] : ''),false)
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
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-5 col-form-label">Correlativo Hoja de Ruta Minería Ilegal : </label>
                                                    <div class="col-sm-7">
                                                        <?php
                                                            $campo = 'correlativo_hr';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'readonly' => 'true',
                                                                'class' => 'form-control form-control-uppercase',
                                                                'value' => set_value($campo,(isset($hoja_ruta[$campo]) ? $hoja_ruta[$campo] : ''),false)
                                                            ));
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
                                                    <label class="col-sm-2 col-form-label">Fecha : </label>
                                                    <div class="col-sm-5">
                                                        <?php
                                                            $campo = 'fecha_hr';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control form-control-uppercase',
                                                                'readonly' => 'true',
                                                                'value' => set_value($campo,(isset($hoja_ruta[$campo]) ? $hoja_ruta[$campo] : ''),false)
                                                            ));
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
                                                    <label class="col-sm-5 col-form-label">Correlativo Formulario de Minería Ilegal : </label>
                                                    <div class="col-sm-7">
                                                        <?php
                                                            $campo = 'correlativo_denuncia';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'readonly' => 'true',
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
                                            <div class="col-md-6 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">Fecha : </label>
                                                    <div class="col-sm-5">
                                                        <?php
                                                            $campo = 'fecha_denuncia';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control form-control-uppercase',
                                                                'readonly' => 'true',
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
                                    <?php if($tipo_denuncia == 3){?>
                                        <div class="tab-pane " id="origen" role="tabpanel">
                                            <div class="row form-group">
                                                <label class="col-sm-2 col-form-label">Tipo de Origen * :</label>
                                                <div class="col-sm-4">
                                                    <?php
                                                        $campo = 'origen_oficio';
                                                        echo form_dropdown($campo, $tipos_origen_oficio, set_value($campo, set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
                                                    ?>
                                                    <span class="messages"></span>
                                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                    <?php }?>
                                                </div>
                                            </div>
                                            <div class="row form-group" id="origen_enlace" style="display: none;">
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
                                                        <label class="col-sm-6 col-form-label">Documento de Digital :</label>
                                                        <div class="col-sm-6">
                                                            <a href="<?=base_url($informe_tecnico_digital);?>" class='btn btn-inverse' target='_blank' title='Ver Informe Técnico'><i class='fa fa-file-pdf-o'></i> Descargar</a>
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
                                                            'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                                        ));
                                                    ?>
                                                    <span class="messages"></span>
                                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                    <?php }?>
                                                </div>
                                            </div>
                                            <div class="form-group row" id="origen_hr" style="display: none;">
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
                                                    <div class="dt-responsive table-responsive">
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
                                                                    <tr id="hr<?= $row['id'];?>">
                                                                        <td class="text-center form-group"><input type="hidden" name="id_hojas_rutas[]" value="<?= $row['id'];?>" /><?= $row['tipo_hoja_ruta'];?></td>
                                                                        <td class="text-center"><?= $row['correlativo'];?></td>
                                                                        <td class="text-center"><?= $row['fecha'];?></td>
                                                                        <td class="text-center"><?= $row['referencia'];?></td>
                                                                        <td class="text-center"><?= $row['remitente'];?></td>
                                                                        <td class="text-center"><?= $row['cite'];?></td>
                                                                        <td class='text-center'>
                                                                            <button type="button" class="btn btn-sm btn-danger waves-effect waves-light" title="Desanexar Área Minera" onclick="desanexar_hoja_ruta(<?= $row['id'];?>);"><span class="icofont icofont-ui-delete"></span></button>
                                                                        </td>
                                                                    </tr>
                                                                <?php }?>
                                                            <?php }?>
                                                            </tbody>
                                                        </table>
                                                    </div>
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
                                                                    'value' => set_value($campo, 'SI')
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
                                    <?php }else{?>
                                        <div class="tab-pane " id="datos_personales" role="tabpanel">
                                            <p><strong>Nota.</strong> Los datos a consignar deben corresponder a la persona que esta haciendo la denuncia, no asi de la persona que está entregando la documentación.</p>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">Buscador de Personas : </label>
                                                        <div class="col-sm-6">
                                                            <?php $campo = 'id_denunciante'; ?>
                                                            <select id="<?= $campo; ?>" name="<?= $campo; ?>" class="denunciante-ajax col-sm-12">
                                                                <option value="">Escriba el Documento de Identidad o Nombre de la Persona</option>
                                                            </select>
                                                            <span class="messages"></span>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <button type="button" class="btn btn-info agregar_denunciante"><i class="fa fa-paperclip"></i> Anexar Denunciante</button>
                                                            <button type="button" class="btn btn-primary nuevo_denunciante"><i class="fa fa-plus"></i> Nuevo Denunciante</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-sm-12">
                                                    <table id="tabla_denunciantes" class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Nombre Completo</th>
                                                                <th class="text-center">Documento Identidad</th>
                                                                <th class="text-center">Celular</th>
                                                                <th class="text-center">E-Mail</th>
                                                                <th class="text-center">Dirección</th>
                                                                <th class="text-center"> </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php if(isset($denunciantes) && count($denunciantes)>0){?>
                                                            <?php foreach($denunciantes as $row){?>
                                                                <tr id="den<?= $row['id'];?>">
                                                                    <td class="text-center form-group"><input type="hidden" name="id_denunciantes[]" value="<?= $row['id'];?>" /><?= $row['nombres'].' '.$row['apellidos'];?></td>
                                                                    <td class="text-center"><?= $row['documento_identidad'].' '.$row['expedido'];?></td>
                                                                    <td class="text-center"><?= $row['telefonos'];?></td>
                                                                    <td class="text-center"><?= $row['email'];?></td>
                                                                    <td class="text-center"><?= $row['direccion'];?></td>
                                                                    <td class='text-center'>
                                                                        <a href="<?=base_url($row['documento_identidad_digital']);?>" class='btn btn-sm btn-inverse' target='_blank' title='Ver Documento de Identidad'><i class='fa fa-file-pdf-o'></i></a> &nbsp;
                                                                        <button type="button" class="btn btn-sm btn-danger waves-effect waves-light" title="Desanexar Denunciante" onclick="desanexar_denunciante(<?= $row['id'];?>);"><span class="icofont icofont-ui-delete"></span></button>
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
                                                                $campo = 'denunciantes_anexados';
                                                                echo form_input(array(
                                                                    'name' => $campo,
                                                                    'id' => $campo,
                                                                    'type' => 'hidden',
                                                                    'value' => set_value($campo, 'SI')
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
                                    <?php }?>
                                    <div class="tab-pane " id="descripcion_explotacion" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Departamento * :</label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'departamento';
                                                            echo form_dropdown($campo, $departamentos, set_value($campo, set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
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
                                                            echo form_dropdown($campo, $provincias, set_value($campo, set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''))), array('id' => $campo, 'class' => 'form-control'));
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
                                                    <label class="col-sm-4 col-form-label">Municipio * : </label>
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
                                                    <label class="col-sm-2 col-form-label">Descripción del lugar o punto de referencia <span class="mytooltip tooltip-effect-5">
                                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                                        <span class="tooltip-content clearfix">
                                                            <span class="tooltip-text">Brindar información que permita ubicar el área denunciada (Área Protegida, Rio u otros).</span>
                                                        </span>
                                                    </span> * : </label>
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
                                            <?php if($tipo_denuncia == 1){?>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12">
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">Área(s) denunciada(s) que se encuentran en trámite en la AJAM : </label>
                                                        <div class="col-sm-10">
                                                            <?php
                                                                $campo = 'areas_denunciadas';
                                                                echo form_textarea(array(
                                                                    'name' => $campo,
                                                                    'id' => $campo,
                                                                    'rows' => '3',
                                                                    'readonly' => 'true',
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
                                        <?php }?>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Buscador de Áreas Mineras : </label>
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
                                        <p>Nota. Haga <strong>DOBLE CLICK</strong> en el mapa para poner uno o mas puntos.</p>
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Latitud <span class="mytooltip tooltip-effect-5">
                                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                                        <span class="tooltip-content clearfix">
                                                            <span class="tooltip-text">Debe ingresar el dato en Grados Decimales (GD) Ejemplo: -16.517438</span>
                                                        </span>
                                                    </span> : </label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'latitude';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control',
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
                                            <div class="col-md-4 col-sm-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Longitud <span class="mytooltip tooltip-effect-5">
                                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                                        <span class="tooltip-content clearfix">
                                                            <span class="tooltip-text">Debe ingresar el dato en Grados Decimales (GD) Ejemplo: -68.118976</span>
                                                        </span>
                                                    </span> : </label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'longitude';
                                                            echo form_input(array(
                                                                'name' => $campo,
                                                                'id' => $campo,
                                                                'class' => 'form-control',
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
                                            <div class="col-sm-12 col-md-2">
                                                <button type="button" class="btn btn-inverse" onclick="agregarPunto();"><i class="fa fa-map-marker"></i> Agregar Punto</button>
                                            </div>
                                        </div>
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
                                                                'readonly' => 'true',
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
                                                <button type="button" class="btn btn-inverse agregar_adjunto" data-tipo="IMAGEN"><i class="fa fa-plus-square"></i> Agregar Imagen</button>
                                                <button type="button" class="btn btn-inverse agregar_adjunto" data-tipo="DOCUMENTO"><i class="fa fa-plus-square"></i> Agregar Documento</button>
                                                <button type="button" class="btn btn-inverse agregar_adjunto" data-tipo="VIDEO"><i class="fa fa-plus-square"></i> Agregar Video</button>
                                                <button type="button" class="btn btn-inverse agregar_adjunto" data-tipo="AUDIO"><i class="fa fa-plus-square"></i> Agregar Audio</button>
                                            </div>
                                            <div class="col-md-12 col-sm-12">
                                                <table id="tabla_adjuntos" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">Tipo</th>
                                                            <th class="text-center">Nombre</th>
                                                            <th class="text-center">Cite</th>
                                                            <th class="text-center">Fecha</th>
                                                            <th class="text-center">Adjunto</th>
                                                            <th class="text-center"> </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(isset($adjuntos) && count($adjuntos)>0){?>
                                                            <?php foreach($adjuntos as $i=>$row){?>
                                                                <tr id="adj<?= ($i+1);?>">
                                                                    <td class="text-center form-group">
                                                                        <input type="hidden" name="id_adjuntos[]" value="<?= $row['id'];?>" /><?= mb_strtoupper($row['tipo']);?><span class='messages'></span>
                                                                    </td>
                                                                    <td class="text-center"><?= $row['nombre'];?></td>
                                                                    <td class="text-center"><?= $row['cite'];?></td>
                                                                    <td class="text-center"><?= $row['fecha_cite'];?></td>
                                                                    <td class="text-center"><a href="<?= base_url($row['adjunto']);?>" class="btn btn-inverse" target="_blank"><i class="icofont icofont-download-alt"></i>Descargar</a></td>
                                                                    <td class="text-center">
                                                                        <!--
                                                                        <button type="button" class="btn btn-sm btn-danger" title="Eliminar Adjunto" onclick="eliminar_adjunto(<?= $i;?>);">
                                                                            <span class='icofont icofont-ui-delete'></span>
                                                                        </button>
                                                                        -->
                                                                    </td>
                                                                </tr>
                                                            <?php }?>
                                                        <?php }?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="estado_mineria_ilegal" role="tabpanel">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Estado de la Denuncia * :</label>
                                            <div class="col-sm-10">
                                                <?= form_input(array('type'=>'hidden','name'=>'anexar_documentos','id'=>'anexar_documentos','value'=>set_value('anexar_documentos', ( (isset($anexar_documentos) && $anexar_documentos=='t') ? 'SI' : 'NO'), false)));?>
                                                <select id="fk_estado_tramite" name="fk_estado_tramite" class="form-control">
                                                    <?php foreach($estadosTramites as $row){ ?>
                                                        <option value="<?= $row['id'];?>" data-padre="<?= $row['padre'];?>" data-anexar="<?= $row['anexar'];?>" <?= (isset($id_estado_padre) && $id_estado_padre == $row['id']) ? 'selected' : ''; ?> ><?= $row['texto'];?></option>
                                                    <?php }?>
                                                </select>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div id="estado_tramite_hijo" class="form-group row">
                                            <label class="col-sm-2 col-form-label">Estado de la Denuncia Especifico * :</label>
                                            <div class="col-sm-10">
                                                <select id="fk_estado_tramite_hijo" name="fk_estado_tramite_hijo" class="form-control">
                                                    <option value="">SELECCIONE UNA OPCIÓN</option>
                                                    <?php if(isset($estadosTramitesHijo)){?>
                                                        <?php foreach($estadosTramitesHijo as $row){?>
                                                            <option value="<?= $row['id'];?>" data-anexar="<?= $row['anexar_documentos'] ?>" <?= (isset($id_estado_hijo) && $id_estado_hijo == $row['id']) ? 'selected' : ''; ?> ><?= $row['texto'];?></option>
                                                        <?php }?>
                                                    <?php }?>
                                                </select>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <?php if(count($documentos)>0){?>
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Documento(s) Generado(s) : </label>
                                                <div class="col-sm-12">
                                                    <table id="tabla_documentos" class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">N°</th>
                                                                <th class="text-center">Correlativo</th>
                                                                <th class="text-center">Fecha</th>
                                                                <th class="text-center">Tipo Documento</th>
                                                                <th class="text-center">Fecha Notificación</th>
                                                                <th class="text-center">Archivo Digital</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($documentos as $n=>$row){?>
                                                                <tr>
                                                                    <td class="text-center"><?= ($n+1);?></td>
                                                                    <td class="text-center"><?= $row['correlativo'];?></td>
                                                                    <td class="text-center"><?= $row['fecha'];?></td>
                                                                    <td class="text-center"><?= $row['tipo_documento'];?></td>
                                                                    <td class="text-center form-group">
                                                                        <?php
                                                                        if($row['notificacion'] == 't'){
                                                                            $campo = 'fecha_notificacion';
                                                                            echo form_input(array(
                                                                                'name' => $campo.$row['id'],
                                                                                'id' => $campo.$row['id'],
                                                                                'class' => 'form-control',
                                                                                'type' => 'date',
                                                                                'required' => 'true',
                                                                                'value' => set_value($campo(isset($row[$campo]) ? $row[$campo] : ''))
                                                                            ));
                                                                            echo '<span class="messages"></span>';
                                                                            if(isset($validation) && $validation->hasError($campo))
                                                                                echo '<span class="form-bar text-danger">'.$validation->getError($campo).'</span>';
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center form-group">
                                                                        <?php if($row['doc_digital']){?>
                                                                            <a href="<?=base_url($row['doc_digital']);?>" class='btn btn-sm btn-inverse' target='_blank' title='Ver Documento'><i class='fa fa-file-pdf-o'></i> Ver Documento</a>
                                                                        <?php }?>
                                                                        <?php
                                                                            $campo = 'adjunto';
                                                                            echo form_upload(array(
                                                                                'name' => $campo.$row['id'],
                                                                                'id' => $campo.$row['id'],
                                                                                'class' => 'form-control',                                                                                
                                                                                'accept' => 'application/pdf'
                                                                            ));
                                                                            echo '<span class="messages"></span>';
                                                                            if(isset($validation) && $validation->hasError($campo.$row['id']))
                                                                                echo '<span class="form-bar text-danger">'.$validation->getError($campo.$row['id']).'</span>';
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            <?php }?>
                                                        </tbody>
                                                    </table>
                                                    <p class="mb-1"><strong>Nota.</strong> Si selecciona otro archivo y se guarda el formulario este reemplazará al anterior archivo.</p>
                                                </div>
                                            </div>
                                        <?php }?>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Observaciones : </label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'observaciones';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,(isset($derivacion[$campo]) ? $derivacion[$campo] : ''),false)
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="derivacion" role="tabpanel">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Destinatario * : </label>
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
                                        <?php if(in_array(4, session()->get('registroPermisos'))){ ?>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Asignar como Responsable : </label>
                                                <div class="col-sm-5">
                                                    <?php $campo = 'responsable';?>
                                                    <div class="checkbox-fade fade-in-primary">
                                                        <label>
                                                            <input type="checkbox" value="true" name="<?= $campo;?>" <?= set_checkbox($campo, 'true', (isset($derivacion['fk_usuario_responsable']) && $derivacion['fk_usuario_responsable'] == $derivacion['fk_usuario_destinatario'])); ?> />
                                                            <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                            <span>SI</span>
                                                        </label>
                                                    </div>
                                                    <span class="messages"></span>
                                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                    <?php }?>
                                                </div>
                                            </div>
                                        <?php }?>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Instrucción * : </label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'instruccion';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,(isset($derivacion[$campo]) ? $derivacion[$campo] : ''),false)
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Anexar H.R. SINCOBOL <span class="mytooltip tooltip-effect-5">
                                                <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                                <span class="tooltip-content clearfix">
                                                    <span class="tooltip-text">Debe escribir el(los) correlativo(s) de la Hoja de Ruta Interna o Externa que desea anexar al Tramite.</span>
                                                </span>
                                            </span> : </label>
                                            <div class="col-sm-10">
                                                <?php $campo = 'anexar_hr';?>
                                                <select id="<?= $campo;?>" name="<?= $campo;?>[]" data-controlador="<?= $controlador;?>" class="hr-in-ex-mejorado-ajax col-sm-12" multiple="multiple">
                                                    <?php if(isset($hojas_ruta_anexadas)){ ?>
                                                        <?php foreach($hojas_ruta_anexadas as $hoja_ruta_anexada){?>
                                                            <option value="<?= $hoja_ruta_anexada['id'];?>" selected><?= $hoja_ruta_anexada['nombre'];?></option>
                                                        <?php }?>
                                                    <?php }else{ ?>
                                                        <option value="">Escriba el correlativo de la Hoja de Ruta Interna o Externa...</option>
                                                    <?php } ?>
                                                </select>
                                                <span class="messages"></span>
                                                <span class="form-bar"><b>Nota.</b> La(s) H.R. no deben estar archivadas o anexadas en el SINCOBOL caso contrario no aparecera para su selección.</span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Motivo de Anexar : </label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'motivo_anexo';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,(isset($derivacion[$campo]) ? $derivacion[$campo] : ''),false)
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
                            <label class="col-sm-8"></label>
                            <div class="col-sm-4 text-right">
                                <?php echo form_submit('enviar', 'GUARDAR','class="btn btn-primary m-b-0"');?>
                                <a href="<?= base_url($controlador.'mis_tramites');?>" class="btn btn-success m-b-0">CANCELAR</a>
                            </div>
                        </div>
                        <?= form_close();?>

                        <?= $nuevo_denunciante;?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>