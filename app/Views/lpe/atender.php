<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header text-center pb-2">
                        <h3 class="mb-1"><?= $correlativo;?></h3>
                        <span>Los campos con <code>*</code> son obligatorios.</span>
                    </div>
                    <div class="card-block">
                        <?= $informacion_tramite;?>
                        <?= form_open_multipart($accion, ['id'=>'formulario']);?>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('correlativo', set_value('correlativo', (isset($fila['correlativo']) ? $fila['correlativo'] : '')));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('id_hoja_ruta', set_value('id_hoja_ruta', (isset($fila['id']) ? $fila['id'] : '')));?>
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
                                <?= form_hidden('fk_area_minera', set_value('fk_area_minera', (isset($fila['fk_area_minera']) ? $fila['fk_area_minera'] : '')));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('ultimo_fk_estado_tramite_padre', set_value('ultimo_fk_estado_tramite_padre', (isset($ultima_derivacion['fk_estado_tramite_padre']) ? $ultima_derivacion['fk_estado_tramite_padre'] : '')));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('ultimo_fk_estado_tramite_hijo', set_value('ultimo_fk_estado_tramite_hijo', (isset($ultima_derivacion['fk_estado_tramite_hijo']) ? $ultima_derivacion['fk_estado_tramite_hijo'] : '')));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('ultimo_fk_usuario_responsable', set_value('ultimo_fk_usuario_responsable', (isset($ultima_derivacion['fk_usuario_responsable']) ? $ultima_derivacion['fk_usuario_responsable'] : '')));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('ultimo_fecha_actualizacion_estado', set_value('ultimo_fecha_actualizacion_estado', (isset($fila['ultimo_fecha_actualizacion_estado']) ? $fila['ultimo_fecha_actualizacion_estado'] : '')));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <h4 class="sub-title mb-2">DATOS DEL ÁREA MINERA</h4>
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
                                        'value' => set_value($campo,(isset($area_minera[$campo]) ? $area_minera[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($area_minera[$campo]) ? $area_minera[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($area_minera[$campo]) ? $area_minera[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($area_minera[$campo]) ? $area_minera[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($area_minera[$campo]) ? $area_minera[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($area_minera[$campo]) ? $area_minera[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($area_minera[$campo]) ? $area_minera[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                        </div>
                        <?php $campo = 'area_protegida';?>
                        <?php if(isset($area_minera[$campo]) && !empty($area_minera[$campo])){?>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Área Protegida:</label>
                                <div class="col-sm-10">
                                    <?php
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'readonly' => true,
                                            'value' => set_value($campo,(isset($area_minera[$campo]) ? $area_minera[$campo] : ''),false)
                                        ));
                                    ?>
                                </div>
                            </div>
                        <?php }?>
                        <?php $campo = 'area_protegida_adicional';?>
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
                        <h4 class="sub-title mb-2">ACTOR PRODUCTIVO MINERO SOLICITANTE</h4>
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
                                        'value' => set_value($campo,(isset($area_minera[$campo]) ? $area_minera[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($area_minera[$campo]) ? $area_minera[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-8">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Represenante Legal*:</label>
                                    <div class="col-sm-9">
                                        <?php
                                            $campo = 'representante_legal';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : 'DATOS A CONSIGNAR'),false)
                                            ));
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
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'value' => set_value($campo,((isset($fila[$campo]) && $fila[$campo]) ? $fila[$campo] : 'BOLIVIANA'),false)
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
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Domicilio Legal*:</label>
                            <div class="col-sm-10">
                                <?php
                                    $campo = 'domicilio_legal';
                                    echo form_textarea(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'rows' => '3',
                                        'class' => 'form-control form-control-uppercase',
                                        'value' => set_value($campo,((isset($ultima_derivacion[$campo]) && $ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : 'DATOS A CONSIGNAR'),false)
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
                                        'value' => set_value($campo,((isset($ultima_derivacion[$campo]) && $ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : 'DATOS A CONSIGNAR'),false)
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
                                        'value' => set_value($campo,((isset($ultima_derivacion[$campo]) && $ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : 'DATOS A CONSIGNAR'),false)
                                    ));
                                ?>
                                <span class="messages"></span>
                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                <?php }?>
                            </div>
                        </div>
                        <?php
                        $validacion_fecha_notificacion_anexar = 'NO';
                        $validacion_documentos_anexar = 'NO';
                        ?>
                        <?php if(isset($documentos) && count($documentos)>0){?>
                            <h4 class="sub-title mb-2">DOCUMENTO(S) QUE SE ANEXARAN</h4>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="dt-responsive table-responsive">
                                        <table id="tabla_documentos" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Nº</th>
                                                    <th class="text-center">Fecha</th>
                                                    <th class="text-center">Correlativo</th>
                                                    <th class="text-center">Tipo Documento</th>
                                                    <th class="text-center">Estado Actualización</th>
                                                    <th class="text-center">Fecha Notificación*</th>
                                                    <?php if(in_array(10, session()->get('registroPermisos'))){?>
                                                        <th class="text-center">Seleccionar Archivo(s)*</th>
                                                    <?php }?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($documentos as $n=>$row){?>
                                                    <tr>
                                                        <td class="text-center"><?= ($n+1);?></td>
                                                        <td class="text-center"><?= $row['fecha'];?></td>
                                                        <td class="text-center"><?= $row['correlativo'];?></td>
                                                        <td class="text-center"><?= $row['tipo_documento'];?></td>
                                                        <td class="text-center"><?= $row['estado_tramite'];?></td>
                                                        <td class="text-center form-group">
                                                            <?php
                                                            $campo = 'fecha_notificacion_anexar[]';
                                                            if($row['notificacion'] == 't'){
                                                                $validacion_fecha_notificacion_anexar = 'SI';
                                                                echo form_input(array(
                                                                    'name' => $campo,
                                                                    'class' => 'form-control',
                                                                    'type' => 'date',
                                                                ));
                                                                echo '<span class="messages"></span>';
                                                            }else{
                                                                echo form_input(array(
                                                                    'name' => $campo,
                                                                    'class' => 'form-control',
                                                                    'type' => 'hidden',
                                                                ));
                                                                echo '<span class="messages"></span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php if(in_array(10, session()->get('registroPermisos'))){?>
                                                            <td class="text-center form-group">
                                                                <?php
                                                                $validacion_documentos_anexar = 'SI';
                                                                $campo = 'documentos_anexar[]';
                                                                echo form_upload(array(
                                                                    'name' => $campo,
                                                                    'class' => 'form-control',
                                                                    'accept' => 'application/pdf'
                                                                ));
                                                                echo '<span class="messages"></span>';
                                                                ?>
                                                            </td>
                                                        <?php }?>
                                                    </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?php
                                    $campo = 'validacion_fecha_notificacion_anexar';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'type' => 'hidden',
                                        'value' => set_value($campo, $validacion_fecha_notificacion_anexar)
                                    ));
                                ?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?php
                                    $campo = 'validacion_documento_anexar';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'type' => 'hidden',
                                        'value' => set_value($campo, $validacion_documentos_anexar)
                                    ));
                                ?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <?php
                        $validacion_documentos_cargar = 'NO';
                        ?>
                        <?php if(isset($documentos_cargar) && count($documentos_cargar)>0){?>
                            <h4 class="sub-title mb-2">DOCUMENTO(S) QUE DEBE CARGAR</h4>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="dt-responsive table-responsive">
                                        <table id="tabla_documentos" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">N°</th>
                                                    <th class="text-center">Correlativo</th>
                                                    <th class="text-center">Fecha</th>
                                                    <th class="text-center">Tipo Documento</th>
                                                    <th class="text-center">Usuario Creación</th>
                                                    <th class="text-center">Seleccionar Archivo(s)*</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($documentos_cargar as $n=>$row){?>
                                                    <tr>
                                                        <td class="text-center"><?= ($n+1);?></td>
                                                        <td class="text-center"><?= $row['correlativo'];?></td>
                                                        <td class="text-center"><?= $row['fecha'];?></td>
                                                        <td class="text-center"><?= $row['tipo_documento'];?></td>
                                                        <td class="text-center"><?= $row['usuario'];?></td>
                                                        <td class="text-center form-group">
                                                            <?php
                                                            $validacion_documentos_cargar = 'SI';
                                                            $campo = 'documentos_cargar[]';
                                                            echo form_upload(array(
                                                                'name' => $campo,
                                                                'class' => 'form-control',
                                                                'accept' => 'application/pdf'
                                                            ));
                                                            echo '<span class="messages"></span>';
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?php
                                    $campo = 'validacion_documento_cargar';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'type' => 'hidden',
                                        'value' => set_value($campo, $validacion_documentos_cargar)
                                    ));
                                ?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <?php if(isset($correspondencia_externa) && count($correspondencia_externa)>0){?>
                            <h4 class="sub-title mt-2 mb-2">CORRESPONDENCIA EXTERNA</h4>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="dt-responsive table-responsive">
                                        <table id="tabla_documentos" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Atendido<br>(SI)</th>
                                                    <th class="text-center">Observaciones<br>Atención</th>
                                                    <th class="text-center">Documento Externo</th>
                                                    <th class="text-center">Doc. Digital</th>
                                                    <th class="text-center">Obs. Recepción</th>
                                                    <th class="text-center">Fecha Ingreso</th>
                                                    <th class="text-center">Días Pasados</th>
                                                    <th class="text-center">Ingresado Por</th>
                                                    <th class="text-center">Fecha Recepción</th>
                                                    <th class="text-center">Recepcionado Por</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($correspondencia_externa as $n=>$row){?>
                                                    <tr>
                                                        <td class="text-center form-group">
                                                            <?php $campo = 'atendido'; ?>
                                                            <input type="checkbox" id="checkbox" name="<?= $campo;?>[]" value="SI" class="form-control" <?= (isset($atendido) && $atendido[$n]=='SI')?'checked':'';?> />
                                                            <span class="messages"></span>
                                                            <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                                <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                            <?php }?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php $campo = 'observaciones_ce'; ?>
                                                            <textarea name="<?= $campo;?>[]" rows="3" cols="40" style="text-transform: uppercase;"><?= (isset($observaciones_ce) && $observaciones_ce[$n])?$observaciones_ce[$n]:'SIN OBSERVACIONES';?></textarea>
                                                            <span class="messages"></span>
                                                            <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                                <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                            <?php }?>
                                                        </td>
                                                        <td class="text-center"><?= $row['documento_externo'];?></td>
                                                        <td class="text-center"><a href="<?= base_url($row['doc_digital']);?>" target="_blank" title="Ver Documento"><i class="feather icon-file"></i> Ver Documento</a></td>
                                                        <td class="text-center"><?= $row['observacion_recepcion'];?></td>
                                                        <td class="text-center"><?= $row['fecha_ingreso'];?></td>
                                                        <td class="text-center"><?= $row['dias_pasados'];?></td>
                                                        <td class="text-center"><?= $row['ingreso'];?></td>
                                                        <td class="text-center"><?= $row['fecha_recepcion'];?></td>
                                                        <td class="text-center"><?= $row['recepcion'];?></td>
                                                    </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                        <h4 class="sub-title mb-2">DERIVACIÓN DEL TRÁMITE</h4>
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
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Destinatario*:</label>
                            <div class="col-sm-10">
                                <?php $campo = 'fk_usuario_destinatario';?>
                                <select id="<?= $campo;?>" name="<?= $campo;?>" class="analista-destinatario-lpe-ajax col-sm-12">
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
                                    echo form_dropdown($campo, $instrucciones, set_value($campo), array('id'=>$campo,'class' => 'instrucciones col-sm-12'));
                                ?>
                                <span class="messages"></span>
                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                <?php }?>
                            </div>
                        </div>
                        <div class="form-group row instruccion_otro">
                            <label class="col-sm-2 col-form-label">Otro*:</label>
                            <div class="col-sm-10">
                                <?php
                                    $campo = 'otro';
                                    echo form_textarea(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'rows' => '3',
                                        'class' => 'form-control form-control-uppercase',
                                        'value' => set_value($campo)
                                    ));
                                ?>
                                <span class="messages"></span>
                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                <?php }?>
                            </div>
                        </div>
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