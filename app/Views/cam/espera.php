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
                                <?= form_hidden('id', set_value('id',(isset($fila['id']) ? $fila['id'] : ''), false));?>
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
                                <?= form_hidden('ultimo_fk_usuario_remitente', set_value('ultimo_fk_usuario_remitente', (isset($fila['ultimo_fk_usuario_remitente']) ? $fila['ultimo_fk_usuario_remitente'] : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'ultimo_fk_usuario_responsable','id'=>'ultimo_fk_usuario_responsable','value'=>set_value('ultimo_fk_usuario_responsable', (isset($fila['ultimo_fk_usuario_responsable']) ? $fila['ultimo_fk_usuario_responsable'] : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
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
                                        'value' => set_value($campo, (isset($hr_remitente[$campo]) ? $hr_remitente[$campo] : ''),false)
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
                                        'value' => set_value($campo, (isset($hr_remitente[$campo]) ? $hr_remitente[$campo] : ''), false)
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
                                        'value' => set_value($campo, (isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                        </div>
                        <h5 class="sub-title">Datos del Área Minera</h5>
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
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                            <label class="col-sm-2 col-form-label">Regional:</label>
                            <div class="col-sm-5">
                                <?php
                                    $campo = 'regional';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control form-control-uppercase',
                                        'readonly' => true,
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
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
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                        </div>
                        <?php $campo = 'area_protegida';?>
                        <?php if(isset($fila[$campo]) && !empty($fila[$campo])){?>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Área Protegida:</label>
                                <div class="col-sm-10">
                                    <?php
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'readonly' => true,
                                            'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                        ));
                                    ?>
                                </div>
                            </div>
                        <?php }?>
                        <?php $campo = 'area_protegida_adicional'; ?>
                        <?php if(isset($fila[$campo]) && !empty($fila[$campo])){?>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Restricción(es) Adicional(es):</label>
                                <div class="col-sm-10">
                                    <?php

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
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Representante Legal:</label>
                            <div class="col-sm-6">
                                <?php
                                    $campo = 'representante_legal';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control form-control-uppercase',
                                        'readonly' => true,
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                            <label class="col-sm-2 col-form-label">Nacionalidad:</label>
                            <div class="col-sm-2">
                                <?php
                                    $campo = 'nacionalidad';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control form-control-uppercase',
                                        'readonly' => true,
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                    ));
                                ?>
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
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                            <label class="col-sm-2 col-form-label">Clasificación del APM:</label>
                            <div class="col-sm-2">
                                <?php
                                    $campo = 'clasificacion_titular';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control form-control-uppercase',
                                        'readonly' => true,
                                        'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                        </div>
                        <h5 class="sub-title">Ultimo Acto Administrativo</h5>
                        <div class="form-group row">
                            <label class="col-sm-1 col-form-label">Remitente:</label>
                            <div class="col-sm-5">
                                <?php
                                    $campo = 'ultimo_remitente';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'value' => set_value($campo, (isset($ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                            <label class="col-sm-1 col-form-label">Cargo:</label>
                            <div class="col-sm-5">
                                <?php
                                    $campo = 'ultimo_cargo';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'value' => set_value($campo, (isset($ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Fecha Derivación:</label>
                            <div class="col-sm-2">
                                <?php
                                    $campo = 'ultimo_fecha_derivacion';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'value' => set_value($campo,(isset($ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                            <label class="col-sm-1 col-form-label">Instrucción:</label>
                            <div class="col-sm-7">
                                <?php
                                    $campo = 'ultimo_instruccion';
                                    echo form_textarea(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'rows' => '3',
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'value' => set_value($campo,(isset($ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Estado Tramite:</label>
                            <div class="col-sm-10">
                                <?php
                                    $campo = 'ultimo_estado_tramite_padre';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'value' => set_value($campo,(isset($ultima_derivacion[$campo]) ? $ultima_derivacion[$campo] : ''),false)
                                    ));
                                ?>
                            </div>
                            <?php $campo = 'ultimo_estado_tramite_hijo'; ?>
                            <?php if(isset($ultima_derivacion[$campo]) && $ultima_derivacion[$campo]){?>
                                <label class="col-sm-2 col-form-label">Estado Tramite Especifico:</label>
                                <div class="col-sm-4">
                                    <?php
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
                                            'readonly' => true,
                                            'value' => set_value($campo,$ultima_derivacion[$campo],false)
                                        ));
                                    ?>
                                </div>
                            <?php }?>
                        </div>
                        <?php if(isset($documentos_anexados) && count($documentos_anexados) > 0){?>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Documento(s) Anexado(s):</label>
                                <div class="col-sm-10">
                                <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Correlativo</th>
                                                <th>Fecha</th>
                                                <th>Tipo Documento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($documentos_anexados as $row){?>
                                                <tr>
                                                    <td><?= $row['correlativo'];?></td>
                                                    <td><?= $row['fecha'];?></td>
                                                    <td><?= $row['tipo_documento'];?></td>
                                                </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php }?>
                        <?php $campo = 'ultima_fecha_notificacion'; ?>
                        <?php if(isset($ultima_derivacion[$campo]) && $ultima_derivacion[$campo]){?>
                        <div class="form-group row">
                            <?php if(isset($ultima_derivacion[$campo]) && $ultima_derivacion[$campo]){?>
                            <label class="col-sm-2 col-form-label">Fecha Notificación:</label>
                            <div class="col-sm-2">
                                <?php
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'value' => set_value($campo,$ultima_derivacion[$campo],false)
                                    ));
                                ?>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php $campo = 'ultima_observacion'; ?>
                        <?php if(isset($ultima_derivacion[$campo]) && $ultima_derivacion[$campo]){?>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Observaciones:</label>
                            <div class="col-sm-10">
                                <?php
                                    echo form_textarea(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'rows' => '2',
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'value' => set_value($campo, $ultima_derivacion[$campo],false)
                                    ));
                                ?>
                            </div>
                        </div>
                        <?php } ?>
                        <h5 class="sub-title">Motivo Espera * </h5>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <?php
                                    $campo = 'motivo_espera';
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
                        <!--
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Respaldo Digital * : </label>
                            <div class="col-sm-10">
                                <?php
                                    $campo = 'adjunto';
                                    echo form_upload(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'class' => 'form-control',
                                        'accept' => '.pdf',
                                    ));
                                ?>
                                <span class="messages"></span>
                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                <?php }?>
                            </div>
                        </div>
                        -->
                        <div class="form-group row">
                            <label class="col-sm-8"></label>
                            <div class="col-sm-4 text-right">
                                <?php echo form_submit('enviar', 'EN ESPERA','class="btn btn-primary m-b-0"');?>
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