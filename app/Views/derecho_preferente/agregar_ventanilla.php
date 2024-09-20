<div class="page-wrapper">
    <?= $title ?>
    <div class="page-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Formulario</h5>
                        <span>Los campos con <code>*</code> son obligatorios.</span>
                    </div>
                    <div class="card-block">
                        <?= form_open_multipart($accion, ['id' => 'formulario']); ?>
                        <h5 class="sub-title">Datos del Área(s) Minera(s) Referencial</h5>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Buscador de Áreas Mineras : </label>
                            <div class="col-sm-8">
                                <?php $campo = 'fk_area_minera';?>
                                <select id="<?= $campo;?>" name="<?= $campo;?>" class="area-minera-cam-dp-ajax col-sm-12">
                                    <option value="">Escriba el Código Único o Denominación</option>
                                </select>
                                <span class="messages"></span>
                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                <?php }?>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-info agregar_area_minera_cam_dp"><i class="fa fa-paperclip"></i> Anexar Área Minera</button>
                            </div>
                        </div>
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
                                            <th class="text-center"> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(isset($areas_mineras) && count($areas_mineras)>0){?>
                                        <?php foreach($areas_mineras as $i=>$row){?>
                                            <tr id="am<?= $row['id_area_minera'];?>">
                                                <td class="text-center form-group"><input type="hidden" name="id_areas_mineras[]" value="<?= $row['id_area_minera'];?>" /><?= $row['codigo_unico'];?></td>
                                                <td class="text-center"><?= $row['denominacion'];?></td>
                                                <td class="text-center">LICENCIA DE PROSPECCIÓN Y EXPLORACIÓN</td>
                                                <td class="text-center"><?= $row['titular'];?></td>
                                                <td class="text-center"><?= $row['clasificacion'];?></td>
                                                <td class="text-center"><?= $row['representante_legal'];?></td>
                                                <td class="text-center">
                                                    <select name='extensiones[]' class='form-control'>
                                                        <option value='TOTAL' <?= $extensiones[$i]=='TOTAL'?'selected':'';?> >TOTAL</option>
                                                        <option value='PARCIAL' <?= $extensiones[$i]=='PARCIAL'?'selected':'';?> >PARCIAL</option>
                                                    </select>
                                                </td>
                                                <td class='text-center'>
                                                    <button type="button" class="btn btn-sm btn-danger waves-effect waves-light" title="Desanexar Área Minera" onclick="desanexar_area_minera_mineria_ilegal(<?= $row['id_area_minera'];?>);"><span class="icofont icofont-ui-delete"></span></button>
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
                                        $campo = 'areas_mineras_anexadas';
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
                        <h5 class="sub-title">Datos del Documento Externo</h5>
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Cite * :</label>
                                    <div class="col-sm-9">
                                        <?php
                                        $campo = 'cite';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'value' => set_value($campo)
                                        ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if (isset($validation) && $validation->hasError($campo)) { ?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo); ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Fecha * :</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $campo = 'fecha_cite';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
                                            'type' => 'date',
                                            'value' => set_value($campo)
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
                            <label class="col-sm-2 col-form-label">Remitente * :</label>
                            <div class="col-sm-8">
                                <?php $campo = 'fk_persona_externa'; ?>
                                <select id="<?= $campo; ?>" name="<?= $campo; ?>" class="persona-externa-ajax col-sm-12">
                                    <?php if (isset($persona_externa)) { ?>
                                        <option value="<?= $persona_externa['id']; ?>"><?= $persona_externa['nombre']; ?></option>
                                    <?php } else { ?>
                                        <option value="">Escriba el Documento de Identidad o Nombre de la Persona</option>
                                    <?php } ?>
                                </select>
                                <span class="messages"></span>
                                <?php if (isset($validation) && $validation->hasError($campo)) { ?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo); ?></span>
                                <?php } ?>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-info waves-effect" data-toggle="modal" data-target="#persona-modal"><i class="fa fa-user-plus"></i> Nueva Persona</button>
                            </div>
                        </div>
                        <?= $modal_remitente?>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Referencia * :</label>
                            <div class="col-sm-10">
                                <?php
                                $campo = 'referencia';
                                echo form_textarea(array(
                                    'name' => $campo,
                                    'id' => $campo,
                                    'rows' => '2',
                                    'class' => 'form-control form-control-uppercase',
                                    'value' => set_value($campo)
                                ));
                                ?>
                                <span class="messages"></span>
                                <?php if (isset($validation) && $validation->hasError($campo)) { ?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo); ?></span>
                                <?php } ?>
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
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Cantidad de Fojas * :</label>
                                    <div class="col-sm-6">
                                        <?php
                                        $campo = 'fojas';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'type' => 'number',
                                            'class' => 'form-control form-control-uppercase',
                                            'value' => set_value($campo)
                                        ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if (isset($validation) && $validation->hasError($campo)) { ?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo); ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Adjuntos * :</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $campo = 'adjuntos';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'value' => set_value($campo)
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
                            <label class="col-sm-2 col-form-label">Doc. Digital (.pdf) (Max. 35MB)* <span class="mytooltip tooltip-effect-5">
                                    <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                    <span class="tooltip-content clearfix">
                                        <span class="tooltip-text">Debe subir al menos la nota externa.</span>
                                    </span>
                                </span> : </label>
                            <div class="col-sm-10">
                                <?php
                                $campo = 'doc_digital';
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
                        <div class="form-group row">
                            <label class="col-sm-2"></label>
                            <div class="col-sm-10">
                                <?php echo form_submit('enviar', 'GUARDAR', 'class="btn btn-primary m-b-0 guardar-form"'); ?>
                                <a href="<?= base_url($controlador.'mis_ingresos'); ?>" class="btn btn-success m-b-0">CANCELAR</a>
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>