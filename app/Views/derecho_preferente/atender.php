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
                                    <?= form_input(array('type'=>'hidden','name'=>'id','value'=>set_value('id', (isset($fila['id']) ? $fila['id'] : ''), false)));?>
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
                                    <?= form_input(array('type'=>'hidden','name'=>'ultimo_fk_usuario_responsable','id'=>'ultimo_fk_usuario_responsable','value'=>set_value('ultimo_fk_usuario_responsable', (isset($fila['ultimo_fk_usuario_responsable']) ? $fila['ultimo_fk_usuario_responsable'] : ''), false)));?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <h5 class="sub-title">Datos de la Hoja de Ruta</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="text-nowrap" width="180px" scope="row">Correlativo:</th>
                                            <td><?= $fila['correlativo'];?></td>
                                            <th class="text-nowrap" width="180px" scope="row">Fecha Solicitud:</th>
                                            <td><?= $fila['fecha_solicitud'];?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h5 class="sub-title mt-3">Datos del Área(s) Minera(s) Referencial</h5>
                            <div class="col-md-12 col-sm-12">
                                <div class="dt-responsive table-responsive">
                                    <table id="tabla_areas_mineras" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Código Único</th>
                                                <th class="text-center">Denominación</th>
                                                <th class="text-center">Tipo Área</th>
                                                <th class="text-center">Titular</th>
                                                <th class="text-center">Clasificación</th>
                                                <th class="text-center">Representante Legal</th>
                                                <th class="text-center">Extensión Solicitada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(isset($areas_mineras) && count($areas_mineras)>0){?>
                                            <?php foreach($areas_mineras as $i=>$row){?>
                                                <tr>
                                                    <td class="text-center"><?= $row['codigo_unico'];?></td>
                                                    <td class="text-center"><?= $row['denominacion'];?></td>
                                                    <td class="text-center"><?= $row['tipo_area'];?></td>
                                                    <td class="text-center"><?= $row['titular'];?></td>
                                                    <td class="text-center"><?= $row['clasificacion_titular'];?></td>
                                                    <td class="text-center"><?= $row['representante_legal'];?></td>
                                                    <td class="text-center"><?= $row['extension_solicitada'];?></td>
                                                </tr>
                                            <?php }?>
                                        <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <h5 class="sub-title">Datos del Documento Externo</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="text-nowrap" scope="row">CITE:</th>
                                            <td><?= $fila['cite'];?></td>
                                            <th class="text-nowrap" scope="row">Fecha:</th>
                                            <td><?= $fila['fecha_cite'];?></td>
                                            <th class="text-nowrap" scope="row">Documento Digital:</th>
                                            <td><a href="<?= base_url($fila['doc_digital']);?>" target="_blank" title="Ver Documento"><i class="feather icon-file"></i> Ver Documento</a></td>
                                        </tr>
                                        <tr>
                                            <th class="text-nowrap" scope="row">Remitente:</th>
                                            <td colspan="5"><?= $fila['remitente'];?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-nowrap" scope="row">Referencia:</th>
                                            <td colspan="5"><?= $fila['referencia'];?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            $validacion_fecha_notificacion = 'NO';
                            $validacion_documentos = 'NO';
                            ?>
                            <?php if(count($documentos)>0){?>
                                <?php $validacion_documentos = 'SI';?>
                                <h5 class="sub-title mt-3">Documento(s) Generado(s)</h5>
                                <div class="col-md-12 col-sm-12">
                                    <div class="dt-responsive table-responsive">
                                        <table id="tabla_areas_mineras" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Correlativo</th>
                                                    <th class="text-center">Fecha</th>
                                                    <th class="text-center">Tipo Documento</th>
                                                    <th class="text-center">Fecha Notificación *</th>
                                                    <th class="text-center">Archivo Digital *</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($documentos as $n=>$row){?>
                                                    <tr>
                                                        <td class="text-center"><?= $row['correlativo'];?></td>
                                                        <td class="text-center"><?= $row['fecha'];?></td>
                                                        <td class="text-center"><?= $row['tipo_documento'];?></td>
                                                        <td class="text-center form-group">
                                                            <?php
                                                            if($row['notificacion'] == 't'){
                                                                $validacion_fecha_notificacion = 'SI';
                                                                $campo = 'fecha_notificacion[]';
                                                                echo form_input(array(
                                                                    'name' => $campo,
                                                                    'class' => 'form-control',
                                                                    'type' => 'date',
                                                                    'value' => set_value($campo)
                                                                ));
                                                                echo '<span class="messages"></span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="text-center form-group">
                                                            <?php
                                                                $campo = 'documentos[]';
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
                            <?php }?>
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'validacion_fecha_notificacion';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'type' => 'hidden',
                                            'value' => set_value($campo, $validacion_fecha_notificacion)
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'validacion_documento';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'type' => 'hidden',
                                            'value' => set_value($campo, $validacion_documentos)
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <h5 class="sub-title mt-3">Derivación del Trámite</h5>
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
                                            'value' => set_value($campo, (isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Destinatario*:</label>
                                <div class="col-sm-10">
                                    <?php $campo = 'fk_usuario_destinatario';?>
                                    <select id="<?= $campo;?>" name="<?= $campo;?>" class="analista-destinatario-dp-ajax col-sm-12">
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