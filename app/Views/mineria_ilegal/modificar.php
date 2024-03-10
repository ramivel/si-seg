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
                                <?= form_hidden('id', set_value('id',$fila['id']));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('id_derivacion', set_value('id_derivacion',$derivacion['id']));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <!-- Row start -->
                        <div class="row">
                            <div class="col-lg-12 col-xl-12">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs  tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#hoja_ruta" role="tab">Hoja de Ruta Madre</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#apm" role="tab">Actor Productivo Minero</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#acto_administrativo" role="tab">Acto Administrativo</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#derivacion" role="tab">Derivación del Tramite</a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content tabs card-block">
                                    <div class="tab-pane active" id="hoja_ruta" role="tabpanel">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label border">Correlativo Hoja Ruta:</label>
                                            <div class="col-sm-10 col-form-label border">
                                                <div class="form-control-static"><?= $fila['correlativo'];?></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-1 col-form-label border">Referencia:</label>
                                            <div class="col-sm-7 col-form-label border">
                                                <div class="form-control-static"><?= $solicitud_licencia['referencia'];?></div>
                                            </div>
                                            <label class="col-sm-2 col-form-label border">Fecha Generación:</label>
                                            <div class="col-sm-2 col-form-label border">
                                                <div class="form-control-static"><?= $solicitud_licencia['fecha_ingreso'];?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="apm" role="tabpanel">
                                        <h5 class="sub-title">Datos del APM</h5>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label border">Denominación:</label>
                                            <div class="col-sm-10 col-form-label border">
                                                <div class="form-control-static"><?= $area_minera['nombre'];?></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label border">Código Único:</label>
                                            <div class="col-sm-2 col-form-label border">
                                                <div class="form-control-static"><?= $area_minera['codigo_unico'];?></div>
                                            </div>
                                            <label class="col-sm-2 col-form-label border">Extensión:</label>
                                            <div class="col-sm-6 col-form-label border">
                                                <div class="form-control-static"><?= $area_minera['extension'].' '.$area_minera['unidad'];?></div>
                                            </div>
                                        </div>
                                        <h5 class="sub-title">Datos del Titular</h5>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label border">Titular Area Minera:</label>
                                            <div class="col-sm-6 col-form-label border">
                                                <div class="form-control-static"><?= $area_minera['titular'];?></div>
                                            </div>
                                            <label class="col-sm-2 col-form-label border">Clasificación del Titular:</label>
                                            <div class="col-sm-2 col-form-label border">
                                                <div class="form-control-static"><?= $area_minera['clasificacion'];?></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Dirección del Titular*:</label>
                                            <div class="col-sm-9">
                                                <?php
                                                    $campo = 'direccion_titular';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'placeholder' => 'Ingrese la Dirección',
                                                        'value' => set_value($campo, $derivacion[$campo])
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Teléfono(s) del Titular*:</label>
                                            <div class="col-sm-9">
                                                <?php
                                                    $campo = 'telefono_titular';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control form-control-uppercase',
                                                        'placeholder' => 'Ingrese el Teléfono',
                                                        'value' => set_value($campo, $derivacion[$campo])
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <h5 class="sub-title">Ubicación</h5>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label border">Departameto(s):</label>
                                            <div class="col-sm-2 col-form-label border">
                                                <div class="form-control-static"><?= $area_minera['departamentos'];?></div>
                                            </div>
                                            <label class="col-sm-2 col-form-label border">Provincia(s):</label>
                                            <div class="col-sm-2 col-form-label border">
                                                <div class="form-control-static"><?= $area_minera['provincias'];?></div>
                                            </div>
                                            <label class="col-sm-2 col-form-label border">Municipio(s):</label>
                                            <div class="col-sm-2 col-form-label border">
                                                <div class="form-control-static"><?= $area_minera['municipios'];?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="acto_administrativo" role="tabpanel">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Estado del Tramite*:</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'fk_estado_tramite';
                                                    echo form_dropdown($campo, $estadosTramites, set_value($campo, $derivacion[$campo]), array('class' => 'form-control'));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Acto Administrativo:</label>
                                            <div class="col-sm-8">
                                                <?= $derivacion['fk_documento'] ? form_hidden('fk_documento_ant', set_value('fk_documento_ant',$derivacion['fk_documento'])):'';?>
                                                <?php $campo = 'fk_documento';?>
                                                <select id="<?= $campo;?>" name="<?= $campo;?>" class="documentos-all-ajax col-sm-12">
                                                    <?php if($acto_administrativo){?>
                                                        <option value="<?= $acto_administrativo['id'];?>"><?= $acto_administrativo['correlativo'];?></option>
                                                    <?php }else{?>
                                                        <option value="">Escriba el Correlativo</option>
                                                    <?php }?>                                                    
                                                </select>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-info documentos-all-limpiar">Limpiar</button>
                                            </div>
                                        </div>
                                        <?php if($derivacion['adjunto_pdf']){?>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label border">Archivo Adjunto Anterior:</label>
                                            <div class="col-sm-10 col-form-label border">
                                                <div class="form-control-static">
                                                    <?= form_hidden('adjunto_pdf_ant', set_value('adjunto_pdf_ant',$derivacion['adjunto_pdf']));?>
                                                    <a href="<?= base_url('archivos/documentos/'.$derivacion['adjunto_pdf']);?>" target="_blank" title="Ver Documento"><i class="feather icon-file"></i> Ver Documento</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label border">Nota: <strong>Si selecciona un archivo se reemplazará el anterior.</strong></label>
                                        </div>
                                        <?php }?>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Archivo Digital (PDF):</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'adjunto_pdf';
                                                    echo form_upload(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control',
                                                        'accept' => 'application/pdf',
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Fecha Emisión:</label>
                                            <div class="col-sm-3">
                                                <?php
                                                    $campo = 'fecha_emision';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control',
                                                        'type' => 'date',
                                                        'value' => set_value($campo, $derivacion[$campo])
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Fecha Notificación:</label>
                                            <div class="col-sm-3">
                                                <?php
                                                    $campo = 'fecha_notificacion';
                                                    echo form_input(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'class' => 'form-control',
                                                        'type' => 'date',
                                                        'value' => set_value($campo, $derivacion[$campo])
                                                    ));
                                                ?>
                                                <span class="messages"></span>
                                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Observaciones:</label>
                                            <div class="col-sm-10">
                                                <?php
                                                    $campo = 'observaciones';
                                                    echo form_textarea(array(
                                                        'name' => $campo,
                                                        'id' => $campo,
                                                        'rows' => '3',
                                                        'class' => 'form-control form-control-uppercase',
                                                        'value' => set_value($campo, $derivacion[$campo])
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
                                            <label class="col-sm-2 col-form-label">Analista Destinatario*:</label>
                                            <div class="col-sm-10">
                                                <?php $campo = 'fk_usuario_destinatario';?>
                                                <select id="<?= $campo;?>" name="<?= $campo;?>" class="analista-destinatario-ajax col-sm-12">
                                                    <option value="<?= $usuario['id'];?>"><?= $usuario['nombre_completo'].' ('.$usuario['cargo'].' - '.$usuario['oficina'].')';?></option>
                                                </select>
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
                                <a href="<?= base_url('acto_administrativo');?>" class="btn btn-success m-b-0">CANCELAR</a>
                            </div>
                        </div>
                        <?= form_close();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>