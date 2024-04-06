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
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nombre Completo:</label>
                                <div class="col-sm-8">
                                    <?php
                                        $campo = 'nombre_completo';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'readonly' => 'true',
                                            'value' => set_value($campo, set_value($campo, isset($fila[$campo])?$fila[$campo]:''))
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Oficina:</label>
                                <div class="col-sm-8">
                                    <?php
                                        $campo = 'fk_oficina';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'readonly' => 'true',
                                            'value' => set_value($campo, set_value($campo,isset($fila[$campo])?$oficinas[$fila[$campo]]:''))
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Perfil:</label>
                                <div class="col-sm-8">
                                    <?php
                                        $campo = 'fk_perfil';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'readonly' => 'true',
                                            'value' => set_value($campo, set_value($campo, isset($fila[$campo])?$perfiles[$fila[$campo]]:''))
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Contraseña Actual * :</label>
                                <div class="col-sm-8">
                                    <div class="input-group mb-0">
                                        <span class="input-group-addon">
                                            <i class="icofont icofont-eye contrasena_ver"></i>
                                            <i class="icofont icofont-eye-blocked contrasena_ocultar"></i>
                                        </span>
                                        <?php
                                            $campo = 'contrasena_actual';
                                            echo form_password(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control',
                                            ));
                                        ?>                                        
                                    </div>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nueva Contraseña * :</label>
                                <div class="col-sm-8">
                                    <div class="input-group mb-0">
                                        <span class="input-group-addon">
                                            <i class="icofont icofont-eye contrasena_ver"></i>
                                            <i class="icofont icofont-eye-blocked contrasena_ocultar"></i>
                                        </span>
                                        <?php
                                            $campo = 'nueva_contrasena';
                                            echo form_password(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control',
                                            ));
                                        ?>                                        
                                    </div>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Confirmar Nueva Contraseña * :</label>
                                <div class="col-sm-8">
                                    <div class="input-group mb-0">
                                        <span class="input-group-addon">
                                            <i class="icofont icofont-eye contrasena_ver"></i>
                                            <i class="icofont icofont-eye-blocked contrasena_ocultar"></i>
                                        </span>
                                        <?php
                                            $campo = 'confirmar_nueva_contrasena';
                                            echo form_password(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control',
                                            ));
                                        ?>                                        
                                    </div>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">                                
                                <div class="col-sm-10">
                                    <?php echo form_submit('enviar', 'GUARDAR','class="btn btn-primary m-b-0"');?>
                                    <a href="<?= base_url('dashboard');?>" class="btn btn-success m-b-0">CANCELAR</a>
                                </div>
                            </div>
                        <?= form_close();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>