<div class="modal fade" id="persona-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Remitente</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-12 col-md-6">
                        <?php $campo = 'm_nombres';?>
                        <label for="<?= $campo;?>">Nombre(s)*:</label>
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
                        <?php $campo = 'm_apellidos';?>
                        <label for="<?= $campo;?>">Apellido(s)*:</label>
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
                        <?php $campo = 'm_documento_identidad';?>
                        <label for="<?= $campo;?>">Documento de Identidad*:</label>
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
                        <?php $campo = 'm_expedido';?>
                        <label for="<?= $campo;?>">Expedido*:</label>
                        <?php
                            echo form_dropdown($campo, $expedidos, set_value($campo), array('id'=>$campo,'class' => 'form-control'));
                        ?>
                        <span id="<?= 'error_'.$campo;?>"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12 col-md-6">
                        <?php $campo = 'm_telefonos';?>
                        <label for="<?= $campo;?>">Celular*:</label>
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
                        <?php $campo = 'm_email';?>
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
                        <?php $campo = 'm_direccion';?>
                        <label for="<?= $campo;?>">Dirección*:</label>
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
                    <div class="form-group col-sm-12 col-md-6">
                        <?php $campo = 'm_institucion';?>
                        <label for="<?= $campo;?>">Institución:</label>
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
                        <?php $campo = 'm_cargo';?>
                        <label for="<?= $campo;?>">Cargo:</label>
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

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary waves-effect waves-light guardar-persona">Guardar Remitente</button>
            </div>
        </div>
    </div>
</div>