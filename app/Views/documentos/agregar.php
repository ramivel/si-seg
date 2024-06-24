<div class="page-wrapper">
    <?= $title?>
    <?php if(!empty(session()->getFlashdata('fail'))){?>
        <div class="alert alert-danger background-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="icofont icofont-close-line-circled text-white"></i>
            </button>
            <?= session()->getFlashdata('fail');?>
        </div>
    <?php }?>
    <?php if(!empty(session()->getFlashdata('success'))){?>
    <div class="alert alert-success background-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="icofont icofont-close-line-circled text-white"></i>
        </button>
        <?= session()->getFlashdata('success');?>
    </div>
    <?php }?>
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
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Hoja Ruta Madre:</label>
                                <div class="col-sm-8">
                                    <?php
                                        $campo = 'correlativo';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
                                            'readonly' => 'true',
                                            'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                        ));
                                    ?>
                                </div>
                            </div>
                            <?php if($tipo_tramite == 'cam/'){?>
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
                                    <label class="col-sm-1 col-form-label">Denominación:</label>
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
                            <?php }elseif($tipo_tramite == 'mineria_ilegal/'){?>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Formulario Minería Ilegal:</label>
                                    <div class="col-sm-3">
                                        <?php
                                            $campo = 'correlativo_formulario';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => true,
                                                'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                            ));
                                        ?>
                                    </div>
                                    <label class="col-sm-1 col-form-label">Fecha:</label>
                                    <div class="col-sm-2">
                                        <?php
                                            $campo = 'fecha_denuncia';
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

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Fecha Actual:</label>
                                <div class="col-sm-2">
                                    <?php
                                        $campo = 'departamento';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
                                            'readonly' => 'true',
                                            'value' => set_value($campo,(isset($datosUsuario[$campo]) ? ucwords(strtolower($datosUsuario[$campo])) : ''),false)
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                        $campo = 'fecha';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
                                            'readonly' => 'true',
                                            'value' => set_value($campo,date('d/m/Y')),
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Tipo de Documento*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'fk_tipo_documento';
                                        echo form_dropdown($campo, $tiposDocumentos, set_value($campo), array('id'=>$campo, 'class' => 'form-control'));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Referencia:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'referencia';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
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
                                <label class="col-sm-2"></label>
                                <div class="col-sm-10">
                                    <?php echo form_submit('enviar', 'GUARDAR','class="btn btn-primary m-b-0"');?>
                                    <a href="<?= base_url($retorno);?>" class="btn btn-success m-b-0">CANCELAR</a>
                                </div>
                            </div>
                        <?= form_close();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>