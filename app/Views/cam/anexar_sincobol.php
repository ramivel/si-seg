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
                        <h4 class="sub-title mt-4 mb-2">ANEXAR HOJA DE RUTA DE SINCOBOL</h4>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Hoja(s) de ruta(s) Interna o Externa del SINCOBOL que se anexará * <span class="mytooltip tooltip-effect-5">
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
                            <label class="col-sm-2 col-form-label">Motivo de Anexar * : </label>
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
                        <div class="form-group row">
                            <label class="col-sm-8"></label>
                            <div class="col-sm-4 text-right">
                                <?php echo form_submit('enviar', 'ANEXAR','class="btn btn-primary m-b-0"');?>
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