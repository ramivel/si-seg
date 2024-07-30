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
                                <?= form_input(array('type'=>'hidden','name'=>'id','id'=>'id_acto_administrativo','value'=>set_value('id', (isset($id) ? $id : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('fk_hoja_ruta_solicitud', set_value('fk_hoja_ruta_solicitud', (isset($fila['fk_hoja_ruta']) ? $fila['fk_hoja_ruta'] : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('fk_tipo_hoja_ruta', set_value('fk_tipo_hoja_ruta', (isset($fila['fk_tipo_hoja_ruta']) ? $fila['fk_tipo_hoja_ruta'] : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('id_derivacion', set_value('id_derivacion', (isset($ultima_derivacion['id']) ? $ultima_derivacion['id'] : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'fk_area_minera','id'=>'fk_area_minera','value'=>set_value('fk_area_minera', (isset($fila['fk_area_minera']) ? $fila['fk_area_minera'] : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'ultimo_fk_estado_tramite_padre','id'=>'ultimo_fk_estado_tramite_padre','value'=>set_value('ultimo_fk_estado_tramite_padre', (isset($fila['ultimo_fk_estado_tramite_padre']) ? $fila['ultimo_fk_estado_tramite_padre'] : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'ultimo_fk_estado_tramite_hijo','id'=>'ultimo_fk_estado_tramite_hijo','value'=>set_value('ultimo_fk_estado_tramite_hijo', (isset($fila['ultimo_fk_estado_tramite_hijo']) ? $fila['ultimo_fk_estado_tramite_hijo'] : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'ultimo_fk_usuario_responsable','id'=>'ultimo_fk_usuario_responsable','value'=>set_value('ultimo_fk_usuario_responsable', (isset($fila['ultimo_fk_usuario_responsable']) ? $fila['ultimo_fk_usuario_responsable'] : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <!-- Row start -->
                        <div class="row">
                            <div class="col-lg-12 col-xl-12">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs  tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#hoja_ruta" role="tab"><strong>Hoja de Ruta Madre</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#apm" role="tab"><strong>Actor Productivo Minero</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-toggle="tab" href="#acto_administrativo" role="tab"><strong>Estado del Trámite</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#derivacion" role="tab"><strong>Derivación del Tramite</strong></a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content tabs card-block">
                                    <div class="tab-pane active" id="hoja_ruta" role="tabpanel">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Hoja de Ruta Madre:</label>
                                            <div class="col-sm-5">
                                                <?php
                                                    $campo = 'correlativo';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control',
                                                        'readonly' => true,
                                                        'value' => set_value($campo, (isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 col-form-label">Referencia:</label>
                                            <div class="col-sm-7">
                                                <?php
                                                    $campo = 'referencia';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control',
                                                        'readonly' => true,
                                                        'value' => set_value($campo, (isset($datos[$campo]) ? $datos[$campo] : ''), false)
                                                    ));
                                                ?>
                                            </div>
                                            <label class="col-sm-2 col-form-label">Fecha Mecanizada:</label>
                                            <div class="col-sm-2">
                                                <?php
                                                    $campo = 'fecha_mecanizada';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control',
                                                        'readonly' => true,
                                                        'value' => set_value($campo, (isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="apm" role="tabpanel">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Código Único:</label>
                                            <div class="col-sm-3">
                                                <?php
                                                    $campo = 'codigo_unico';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'readonly' => true,
                                                        'value' => set_value($campo,(isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                            <label class="col-sm-2 col-form-label">Denominación:</label>
                                            <div class="col-sm-5">
                                                <?php
                                                    $campo = 'denominacion';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'readonly' => true,
                                                        'value' => set_value($campo,(isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Extensión:</label>
                                            <div class="col-sm-3">
                                                <?php
                                                    $campo = 'extension';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'readonly' => true,
                                                        'value' => set_value($campo,(isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                            <label class="col-sm-2 col-form-label">Dir. Departamental/Regional:</label>
                                            <div class="col-sm-5">
                                                <?php
                                                    $campo = 'regional';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'readonly' => true,
                                                        'value' => set_value($campo,(isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Departameto(s):</label>
                                            <div class="col-sm-2">
                                                <?php
                                                    $campo = 'departamentos';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'readonly' => true,
                                                        'value' => set_value($campo,(isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                            <label class="col-sm-1 col-form-label">Provincia(s):</label>
                                            <div class="col-sm-3">
                                                <?php
                                                    $campo = 'provincias';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'readonly' => true,
                                                        'value' => set_value($campo,(isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                            <label class="col-sm-1 col-form-label">Municipio(s):</label>
                                            <div class="col-sm-3">
                                                <?php
                                                    $campo = 'municipios';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'readonly' => true,
                                                        'value' => set_value($campo,(isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                        </div>
                                        <?php $campo = 'area_protegida';?>
                                        <?php if(isset($datos[$campo]) && !empty($datos[$campo])){?>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Área Protegida:</label>
                                                <div class="col-sm-10">
                                                    <?php
                                                        echo form_input(array(
                                                            'name' => $campo,
                                                            'id' => $campo,
                                                            'class' => 'form-control form-control-uppercase',
                                                            'readonly' => true,
                                                            'value' => set_value($campo,(isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                        ));
                                                    ?>
                                                </div>
                                            </div>
                                        <?php }?>

                                        <?php $campo = 'area_protegida_adicional';?>
                                        <?php if(in_array(9, session()->get('registroPermisos'))){ ?>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Restricción(es) Adicional(es):</label>
                                                <div class="col-sm-10">
                                                    <?php
                                                        $campo = 'area_protegida_adicional';
                                                        echo form_textarea(array(
                                                            'name' => $campo,
                                                            'id' => $campo,
                                                            'rows' => '2',
                                                            'class' => 'form-control form-control-uppercase',
                                                            'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                                        ));
                                                    ?>
                                                    <span class="messages"></span>
                                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                    <?php }?>
                                                </div>
                                            </div>
                                        <?php }elseif(isset($fila[$campo]) && !empty($fila[$campo])){?>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Restricción(es) Adicional(es):</label>
                                                <div class="col-sm-10">
                                                    <?php
                                                        $campo = 'area_protegida_adicional';
                                                        echo form_textarea(array(
                                                            'name' => $campo,
                                                            'id' => $campo,
                                                            'rows' => '2',
                                                            'readonly' => true,
                                                            'class' => 'form-control form-control-uppercase',
                                                            'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                                        ));
                                                    ?>
                                                    <span class="messages"></span>
                                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                    <?php }?>
                                                </div>
                                            </div>
                                        <?php }?>

                                        <div class="row">
                                            <div class="col-sm-12 col-md-8">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">Represenante Legal*:</label>
                                                    <div class="col-sm-9">
                                                        <?php
                                                            $campo = 'representante_legal';
                                                            if($fk_tipo_hoja_ruta == 1){
                                                                echo form_input(array(
                                                                    'name' => $campo,
                                                                    'id' => $campo,
                                                                    'class' => 'form-control form-control-uppercase',
                                                                    'readonly' => true,
                                                                    'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                                                ));
                                                            }else{
                                                                echo form_input(array(
                                                                    'name' => $campo,
                                                                    'id' => $campo,
                                                                    'class' => 'form-control form-control-uppercase',
                                                                    'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                                                ));
                                                            }
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-4">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Nacionalidad*:</label>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            $campo = 'nacionalidad';
                                                            if($fk_tipo_hoja_ruta == 1){
                                                                echo form_input(array(
                                                                    'name' => $campo,
                                                                    'id' => $campo,
                                                                    'class' => 'form-control form-control-uppercase',
                                                                    'readonly' => true,
                                                                    'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                                                ));
                                                            }else{
                                                                echo form_input(array(
                                                                    'name' => $campo,
                                                                    'id' => $campo,
                                                                    'class' => 'form-control form-control-uppercase',
                                                                    'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                                                ));
                                                            }
                                                        ?>
                                                        <span class="messages"></span>
                                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Solicitante:</label>
                                            <div class="col-sm-6">
                                                <?php
                                                    $campo = 'titular';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'readonly' => true,
                                                        'value' => set_value($campo,(isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                            <label class="col-sm-2 col-form-label">Clasificación del APM:</label>
                                            <div class="col-sm-2">
                                                <?php
                                                    $campo = 'clasificacion';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'readonly' => true,
                                                        'value' => set_value($campo,(isset($datos[$campo]) ? $datos[$campo] : ''),false)
                                                    ));
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Domicilio Legal:</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'domicilio_legal';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,(isset($ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : ''),false)
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Domicilio Procesal*:</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'domicilio_procesal';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,(isset($ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : ''),false)
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Teléfono(s) del Solicitante*:</label>
                                            <div class="col-sm-9">
                                                <?php
                                                    $campo = 'telefono_solicitante';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,(isset($ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : ''),false)
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="acto_administrativo" role="tabpanel">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Estado del Tramite*:</label>
                                            <div class="col-sm-10">

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
                                            <label class="col-sm-2 col-form-label">Estado del Tramite Especifico*:</label>
                                            <div class="col-sm-10">
                                                <select id="fk_estado_tramite_hijo" name="fk_estado_tramite_hijo" class="form-control">
                                                    <option value="">SELECCIONE UNA OPCIÓN</option>
                                                    <?php if(isset($estadosTramitesHijo)){?>
                                                        <?php foreach($estadosTramitesHijo as $row){?>
                                                            <option value="<?= $row['id'];?>" <?= (isset($id_estado_hijo) && $id_estado_hijo == $row['id']) ? 'selected' : ''; ?> ><?= $row['texto'];?></option>
                                                        <?php }?>
                                                    <?php }?>
                                                </select>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-12"><span><strong>Nota. Si el estado del trámite no fue modificado, no es obligatorio el llenado de Documento(s) Anexado(s) y Observaciones en el formulario.</strong></span></div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">El APM presento:</label>
                                            <div class="col-sm-2">
                                                <?php $campo = 'recurso_jerarquico';?>
                                                <div class="checkbox-fade fade-in-primary">
                                                    <label>
                                                        <input type="checkbox" id="<?= $campo;?>" name="<?= $campo;?>" value="true" <?= set_checkbox($campo, 'true', (isset($ultima_derivacion[$campo]) && $ultima_derivacion[$campo]=='t')); ?> />
                                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>RECURSO JERÁRQUICO</span>
                                                    </label>
                                                </div>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                            <div class="col-sm-2">
                                                <?php $campo = 'recurso_revocatoria';?>
                                                <div class="checkbox-fade fade-in-primary">
                                                    <label>
                                                        <input type="checkbox" id="<?= $campo;?>" name="<?= $campo;?>" value="true" <?= set_checkbox($campo, 'true', (isset($ultima_derivacion[$campo]) && $ultima_derivacion[$campo]=='t')); ?> />
                                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>RECURSO DE REVOCATORIA</span>
                                                    </label>
                                                </div>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                            <div class="col-sm-2">
                                                <?php $campo = 'oposicion';?>
                                                <div class="checkbox-fade fade-in-primary">
                                                    <label>
                                                        <input type="checkbox" id="<?= $campo;?>" name="<?= $campo;?>" value="true" <?= set_checkbox($campo, 'true', (isset($ultima_derivacion[$campo]) && $ultima_derivacion[$campo]=='t')); ?> />
                                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>OPOSICIÓN</span>
                                                    </label>
                                                </div>
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
                                                                            $campo = 'fecha_notificacion'.$row['id'];
                                                                            echo form_input(array(
                                                                                'name' => $campo,
                                                                                'id' => $campo,
                                                                                'class' => 'form-control',
                                                                                'type' => 'date',
                                                                                'required' => 'true',
                                                                                'value' => set_value($campo)
                                                                            ));
                                                                            echo '<span class="messages"></span>';
                                                                            if(isset($validation) && $validation->hasError($campo))
                                                                                echo '<span class="form-bar text-danger">'.$validation->getError($campo).'</span>';
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            <?php }?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php }?>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Observaciones*:</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'observaciones';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo,'', false)
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
                                            <label class="col-sm-2 col-form-label">Usuario Responsable del Trámite:</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'usuario_responsable';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control',
                                                        'readonly' => true,
                                                        'value' => set_value($campo, (isset($ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : ''),false)
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Destinatario*:</label>
                                            <div class="col-sm-10">
                                                <?php $campo = 'fk_usuario_destinatario';?>
                                                <select id="<?= $campo;?>" name="<?= $campo;?>" class="analista-destinatario-ajax col-sm-12">
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
                                                <label class="col-sm-2 col-form-label">Asignar como Responsable:</label>
                                                <div class="col-sm-5">
                                                    <?php $campo = 'responsable';?>
                                                    <div class="checkbox-fade fade-in-primary">
                                                        <label>
                                                            <input type="checkbox" value="true" name="<?= $campo;?>" <?= set_checkbox($campo, 'true'); ?> />
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
                                            <label class="col-sm-2 col-form-label">Motivo de Anexar:</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'motivo_anexo';
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
                            <label class="col-sm-8"></label>
                            <div class="col-sm-4 text-right">
                                <?php echo form_submit('enviar', 'GUARDAR','class="btn btn-primary m-b-0"');?>
                                <a href="<?= base_url($controlador.'mis_tramites');?>" class="btn btn-success m-b-0">CANCELAR</a>
                            </div>
                        </div>
                        <?= form_close();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>