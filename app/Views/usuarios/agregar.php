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
                        <?= form_open($accion, ['id'=>'form-usuario']);?>
                            <div class="form-group row d-none">
                                <div class="col-sm-10">
                                    <?= form_hidden('id');?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nombre Completo*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'nombre_completo';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'placeholder' => 'Ingrese el Nombre Completo',
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
                                <label class="col-sm-2 col-form-label">Oficina*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'fk_oficina';
                                        echo form_dropdown($campo, $oficinas, set_value($campo), array('class' => 'form-control'));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Cargo*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'fk_perfil';
                                        echo form_dropdown($campo, $perfiles, set_value($campo), array('class' => 'form-control'));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Correo Electronico*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'email';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
                                            'placeholder' => 'Ingrese su E-Mail',
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
                                <label class="col-sm-2 col-form-label">Horarios de Atención:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'atencion';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',                                            
                                            'value' => set_value($campo,'LUNES A VIERNES EN HORARIOS DE OFICINA',false)
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Usuario*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'usuario';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
                                            'placeholder' => 'Ingrese su Usuario',
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
                                <label class="col-sm-2 col-form-label">Contraseña*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'pass';
                                        echo form_password(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
                                            'placeholder' => 'Ingrese la Contraseña'
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2">Tramites*:</label>
                                <div class="col-sm-10">
                                    <?php $campo = 'tramites';?>
                                    <?php foreach($tramites as $i => $row){?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" id="checkbox" name="<?= $campo;?>[]" value="<?= $i;?>" <?= set_checkbox($campo.'[]', $i); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?= $row;?></span>
                                            </label>
                                        </div>
                                    <?php }?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2">Permisos:</label>
                                <div class="col-sm-10">
                                    <?php $campo = 'permisos';?>
                                    <?php foreach($permisos as $i => $row){?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" id="checkbox" name="<?= $campo;?>[]" value="<?= $i;?>" <?= set_checkbox($campo.'[]', $i); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?= $row;?></span>
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