<div class="modal fade" id="nuevo-denunciante-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Denunciante</h4>
            </div>            
            <div class="modal-body">
                <?= form_open_multipart('', ['id'=>'formulario_denunciante']);?>
                <div class="row">
                    <div class="form-group col-sm-12 col-md-6">
                        <?php $campo = 'd_nombres';?>
                        <label for="<?= $campo;?>">Nombre(s) * :</label>
                        <?php
                        echo form_input(array(
                            'name' => $campo,
                            'id' => $campo,
                            'class' => 'form-control form-control-uppercase',
                            'value' => set_value($campo, '', false)
                        ));
                        ?>
                        <span id="<?= 'error_'.$campo;?>"></span>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        <?php $campo = 'd_apellidos';?>
                        <label for="<?= $campo;?>">Apellido(s) * :</label>
                        <?php
                        echo form_input(array(
                            'name' => $campo,
                            'id' => $campo,
                            'class' => 'form-control form-control-uppercase',
                            'value' => set_value($campo, '', false)
                        ));
                        ?>
                        <span id="<?= 'error_'.$campo;?>"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12 col-md-6">
                        <?php $campo = 'd_documento_identidad';?>
                        <label for="<?= $campo;?>">Documento de Identidad * :</label>
                        <?php
                        echo form_input(array(
                            'name' => $campo,
                            'id' => $campo,
                            'class' => 'form-control',
                            'type' => 'number',
                            'placeholder' => 'SOLAMENTE NÚMEROS',
                            'value' => set_value($campo, '', false)
                        ));
                        ?>
                        <span id="<?= 'error_'.$campo;?>"></span>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        <?php $campo = 'd_expedido';?>
                        <label for="<?= $campo;?>">Expedido * :</label>
                        <?php
                            echo form_dropdown($campo, $expedidos, set_value($campo), array('id'=>$campo,'class' => 'form-control'));
                        ?>
                        <span id="<?= 'error_'.$campo;?>"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12 col-md-6">
                        <?php $campo = 'd_telefonos';?>
                        <label for="<?= $campo;?>">Celular * :</label>
                        <?php
                        echo form_input(array(
                            'name' => $campo,
                            'id' => $campo,
                            'class' => 'form-control form-control-uppercase',
                            'value' => set_value($campo, '', false)
                        ));
                        ?>
                        <span id="<?= 'error_'.$campo;?>"></span>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        <?php $campo = 'd_email';?>
                        <label for="<?= $campo;?>">E-Mail:</label>
                        <?php
                        echo form_input(array(
                            'name' => $campo,
                            'id' => $campo,
                            'type' => 'email',
                            'class' => 'form-control',
                            'value' => set_value($campo, '', false)
                        ));
                        ?>
                        <span id="<?= 'error_'.$campo;?>"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <?php $campo = 'd_direccion';?>
                        <label for="<?= $campo;?>">Dirección * :</label>
                        <?php
                        echo form_textarea(array(
                            'name' => $campo,
                            'id' => $campo,
                            'rows' => '2',
                            'class' => 'form-control form-control-uppercase',
                            'value' => set_value($campo, '', false)
                        ));
                        ?>
                        <span id="<?= 'error_'.$campo;?>"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <?php $campo = 'd_documento_identidad_digital';?>
                        <label for="<?= $campo;?>">Documento de Identidad Digital (.pdf) (Max. 20MB) * :</label>
                        <?php
                        echo form_upload(array(
                            'name' => $campo,
                            'id' => $campo,
                            'class' => 'form-control',
                            'accept' => '.pdf',
                        ));
                        ?>
                        <span id="<?= 'error_'.$campo;?>"></span>
                    </div>
                </div>
                <?= form_close();?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary waves-effect waves-light guardar-denunciante">Guardar Persona</button>
            </div>
        </div>
    </div>
</div>