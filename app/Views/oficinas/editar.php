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
                        <?= form_open($accion, ['id'=>'formulario']);?>
                            <div class="form-group row d-none">
                                <div class="col-sm-10">
                                    <?= form_hidden('id', set_value('id',$fila['id']));?>
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
                                            'value' => set_value($campo, $fila[$campo])
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Departamento*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'departamento';
                                        echo form_dropdown($campo, $departamentos, set_value($campo, $fila[$campo]), array('class' => 'form-control'));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Correlativo* <span class="mytooltip tooltip-effect-5">
                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                        <span class="tooltip-content clearfix">
                                            <span class="tooltip-text">Este campo debera terminar en /</span>
                                        </span>
                                    </span> : </label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'correlativo';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'value' => set_value($campo, $fila[$campo])
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Dirección*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'direccion';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'value' => set_value($campo, $fila[$campo])
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Teléfono(s):</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'telefonos';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'value' => set_value($campo, $fila[$campo])
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Desconcentrado:</label>
                                <div class="col-sm-5">
                                    <?php $campo = 'desconcentrado';?>
                                    <div class="checkbox-fade fade-in-primary">
                                        <label>
                                            <input type="checkbox" value="true" name="<?= $campo;?>" <?= set_checkbox($campo, 'true', ($fila[$campo]=='t') ); ?> />
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
                                <label class="col-sm-2 col-form-label">H.R. Busqueda*<span class="mytooltip tooltip-effect-5">
                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                        <span class="tooltip-content clearfix">
                                            <span class="tooltip-text">Se debe seleccionar las Regional(es) a las cuales puede buscar esta DIRECCIÓN.</span>
                                        </span>
                                    </span> : </label>
                                <div class="col-sm-2">
                                    <?php $campo = 'regional_busqueda';?>
                                    <?php foreach($regionales as $row){?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" value="<?= $row;?>" name="<?= $campo;?>[]" <?= set_checkbox($campo.'[]', $row, in_array($row, $regionalesBusqueda)); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                <span><?= $row;?></span>
                                            </label>
                                        </div><br>
                                    <?php }?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                                <label class="col-sm-2 col-form-label">H.R. Anexar del SINCOBOL*<span class="mytooltip tooltip-effect-5">
                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                        <span class="tooltip-content clearfix">
                                            <span class="tooltip-text">Se debe seleccionar la(s) Oficina(s) de las cuales se puede anexar del sistema SINCOBOL.</span>
                                        </span>
                                    </span> : </label>
                                <div class="col-sm-5">
                                    <?php $campo = 'fk_oficina_sincobol';?>
                                    <?php foreach($oficinas_sincobol as $i=>$row){ ?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" value="<?= $i;?>" name="<?= $campo;?>[]" <?= set_checkbox($campo.'[]', $i, in_array($i, $oficinasSincobol)); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                <span><?= $row;?></span>
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
                                <label class="col-sm-2 col-form-label">Derivación(es) adicional(es)*:<span class="mytooltip tooltip-effect-5">
                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                        <span class="tooltip-content clearfix">
                                            <span class="tooltip-text">Se debe seleccionar la(s) Dirección(es) a las cuales se puede derivar los tramites.</span>
                                        </span>
                                    </span> : </label>
                                <div class="col-sm-5">
                                    <?php $campo = 'fk_oficina_derivacion';?>
                                    <?php foreach($oficinas_derivacion as $i=>$row){ ?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" value="<?= $i;?>" name="<?= $campo;?>[]" <?= set_checkbox($campo.'[]', $i, in_array($i, $oficinasDerivacion)); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                <span><?= $row;?></span>
                                            </label>
                                        </div><br>
                                    <?php }?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                                <label class="col-sm-2 col-form-label">Departamentos de Atención<span class="mytooltip tooltip-effect-5">
                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                        <span class="tooltip-content clearfix">
                                            <span class="tooltip-text">Se debe seleccionar el Departamento(s) que realiza la atención esta Oficina.</span>
                                        </span>
                                    </span> : </label>
                                <div class="col-sm-3">
                                    <?php $campo = 'departamentos_atencion';?>
                                    <?php foreach($departamentos as $i=>$row){ ?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" value="<?= $i;?>" name="<?= $campo;?>[]" <?= set_checkbox($campo.'[]', $i, in_array($i, $departamentosAtencion)); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                <span><?= $row;?></span>
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