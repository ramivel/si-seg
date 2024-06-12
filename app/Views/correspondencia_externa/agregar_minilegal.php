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
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('id'); ?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Correlativo H.R. Minería Ilegal*:</label>
                            <div class="col-sm-10">
                                <?php $campo = 'fk_hoja_ruta'; ?>
                                <select id="<?= $campo; ?>" name="<?= $campo; ?>" class="buscar-hoja-ruta-ajax col-sm-12">
                                    <?php if (isset($hr_minilegal)) { ?>
                                        <option value="<?= $hr_minilegal['id']; ?>"><?= $hr_minilegal['correlativo'].' - '.$hr_minilegal['fecha_hoja_ruta'].' - '.$tipos_denuncias[$hr_minilegal['fk_tipo_denuncia']]; ?></option>
                                    <?php } else { ?>
                                        <option value="">Escriba la Hoja de Ruta Minería Ilegal</option>
                                    <?php } ?>
                                </select>
                                <span class="messages"></span>
                                <?php if (isset($validation) && $validation->hasError($campo)) { ?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo); ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        <h5 class="sub-title">Información de la Denuncia</h5>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Fecha Hoja de Ruta : </label>
                                    <div class="col-sm-6">
                                        <?php
                                            $campo = 'fecha_hr';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => 'true',
                                                'value' => set_value($campo,'')
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
                                    <label class="col-sm-4 col-form-label">Tipo de Minería Ilegal : </label>
                                    <div class="col-sm-7">
                                        <?php
                                            $campo = 'tipo_denuncia';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'readonly' => 'true',
                                                'class' => 'form-control form-control-uppercase',
                                                'value' => set_value($campo,'')
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
                                                'value' => set_value($campo,'')
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
                                                'value' => set_value($campo,'')
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
                                    <label class="col-sm-4 col-form-label">Departamento : </label>
                                    <div class="col-sm-7">
                                        <?php
                                            $campo = 'departamento';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => 'true',
                                                'value' => set_value($campo,'')
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
                                    <label class="col-sm-4 col-form-label">Provincia : </label>
                                    <div class="col-sm-7">
                                        <?php
                                            $campo = 'provincia';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'readonly' => 'true',
                                                'class' => 'form-control form-control-uppercase',
                                                'value' => set_value($campo,'')
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
                                    <label class="col-sm-4 col-form-label">Municipio : </label>
                                    <div class="col-sm-7">
                                        <?php
                                            $campo = 'municipio';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'readonly' => 'true',
                                                'class' => 'form-control form-control-uppercase',
                                                'value' => set_value($campo,'')
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
                        <h5 class="sub-title">ULTIMA DERIVACIÓN</h5>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Remitente:</label>
                            <div class="col-sm-10">
                                <?php
                                $campo = 'remitente';
                                echo form_input(array(
                                    'name' => $campo,
                                    'id' => $campo,
                                    'class' => 'form-control form-control-uppercase',
                                    'readonly' => true,
                                    'value' => set_value($campo,'')
                                ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Destinatario:</label>
                            <div class="col-sm-10">
                                <?php
                                $campo = 'destinatario';
                                echo form_input(array(
                                    'name' => $campo,
                                    'id' => $campo,
                                    'class' => 'form-control form-control-uppercase',
                                    'readonly' => true,
                                    'value' => set_value($campo,'')
                                ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Responsable:</label>
                            <div class="col-sm-10">
                                <?php
                                $campo = 'responsable';
                                echo form_input(array(
                                    'name' => $campo,
                                    'id' => $campo,
                                    'class' => 'form-control form-control-uppercase',
                                    'readonly' => true,
                                    'value' => set_value($campo,'')
                                ));
                                ?>
                            </div>
                        </div>
                        <h5 class="sub-title">Datos del Documento Externo</h5>
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Cite*:</label>
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
                                    <label class="col-sm-2 col-form-label">Fecha*:</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $campo = 'fecha_cite';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
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
                            <label class="col-sm-2 col-form-label">Remitente*:</label>
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
                            <label class="col-sm-2 col-form-label">Referencia*:</label>
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
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Cantidad de Fojas*:</label>
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
                                    <label class="col-sm-2 col-form-label">Adjuntos*:</label>
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
                            <label class="col-sm-2 col-form-label">Doc. Digital (.pdf) (Max. 20MB)* <span class="mytooltip tooltip-effect-5">
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
                                <?php echo form_submit('enviar', 'GUARDAR', 'class="btn btn-primary m-b-0"'); ?>
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