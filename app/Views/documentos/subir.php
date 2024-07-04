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
                                    <?= form_hidden('id', set_value('id', (isset($fila['id']) ? $fila['id'] : '') ));?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row d-none">
                                <div class="col-sm-10">
                                    <?= form_hidden('id_tramite', set_value('id_tramite', (isset($id_tramite) ? $id_tramite : '') ));?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row d-none">
                                <div class="col-sm-10">
                                    <?= form_hidden('correlativo', set_value('correlativo', (isset($correlativo) ? $correlativo : '') ));?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-center mb-4"><h5><?= $correlativo;?></h5></div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Referencia:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'referencia';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'readonly' => 'true',
                                            'class' => 'form-control',
                                            'value' => set_value($campo, (isset($fila[$campo]) ? $fila[$campo] : ''), false)
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Fecha:</label>
                                <div class="col-sm-1">
                                    <?php
                                        $campo = 'ciudad';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control',
                                            'readonly' => 'true',
                                            'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
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
                                            'value' => set_value($campo,(isset($fila[$campo]) ? $fila[$campo] : ''),false)
                                        ));
                                    ?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Tipo Documento:</label>
                                <div class="col-sm-8">
                                    <?php
                                        $campo = 'tipo_documento';
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
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Hoja de Ruta Madre:</label>
                                <div class="col-sm-8">
                                    <?php
                                        $campo = 'hoja_ruta';
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
                            <?php if($id_tramite == 1){?>
                                <div class="form-group row d-none">
                                    <div class="col-sm-10">
                                        <?= form_hidden('fk_acto_administrativo', set_value('fk_acto_administrativo', (isset($fila['id_acto_administrativo']) ? $fila['id_acto_administrativo'] : '') ));?>
                                        <span class="messages"></span>
                                    </div>
                                </div>
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
                            <?php }elseif($id_tramite == 2){?>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Formulario Minería Ilegal:</label>
                                    <div class="col-sm-3">
                                        <?php
                                            $campo = 'formulario_mineria_ilegal';
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
                                <label class="col-sm-2 col-form-label">Doc. Digital (.pdf) (Max. 35MB) * : </label>
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