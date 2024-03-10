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
                                    <?= form_hidden('id');?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nombre*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'nombre';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'placeholder' => 'Ingrese el Nombre',
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
                                <label class="col-sm-2 col-form-label">Sigla* <span class="mytooltip tooltip-effect-5">
                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                        <span class="tooltip-content clearfix">
                                            <span class="tooltip-text">Este campo debera terminar en /</span>
                                        </span>
                                    </span> : </label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'sigla';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'placeholder' => 'Ingrese la Sigla',
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
                                <label class="col-sm-2 col-form-label">Descripción:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'descripcion';
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
                                <label class="col-sm-2 col-form-label">Plantilla (.doc;.docx)* <span class="mytooltip tooltip-effect-5">
                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                        <span class="tooltip-content clearfix">
                                            <span class="tooltip-text">La Plantilla debe contener las siguientes variables: ${correlativo} ${fecha} ${referencia} </span>
                                        </span>
                                    </span> : </label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'adjunto';
                                        echo form_upload(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
                                            'accept' => '.doc,.docx',
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Requieré Fecha de Notificación:</label>
                                <div class="col-sm-5">
                                    <?php $campo = 'notificacion';?>
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
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Cargos*:</label>
                                <div class="col-sm-4">
                                    <?php $campo = 'perfiles';?>
                                    <?php foreach($perfiles as $row){?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" value="<?= $row['id'];?>" name="<?= $campo;?>[]" <?= set_checkbox($campo.'[]', $row['id']); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                <span><?= $row['nombre'];?></span>
                                            </label>
                                        </div><br>
                                    <?php }?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                                <label class="col-sm-1 col-form-label">Tramites*:</label>
                                <div class="col-sm-4">
                                    <?php $campo = 'tramites';?>
                                    <?php foreach($tramites as $row){?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" value="<?= $row['id'];?>" name="<?= $campo;?>[]" <?= set_checkbox($campo.'[]', $row['id']); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                <span><?= $row['nombre'];?></span>
                                            </label>
                                        </div><br>
                                    <?php }?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2"></label>
                                <div class="col-sm-10">
                                    <?php echo form_submit('enviar', 'GUARDAR','class="btn btn-primary m-b-0"');?>
                                    <a href="<?= base_url($controlador);?>" class="btn btn-success m-b-0">CANCELAR</a>
                                </div>
                            </div>
                        <?= form_close();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>