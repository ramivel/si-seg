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
                    <div class="card-header text-center pb-2">
                        <h3 class="mb-1"><?= $titulo;?></h3>
                        <span>Los campos con <code>*</code> son obligatorios.</span>
                    </div>
                    <div class="card-block">
                        <div class="row mb-2">
                            <div class="col-sm-12 h3 text-center"><strong>NOTA.</strong> La plantilla generada es únicamente referencial.</div>
                        </div>
                        <?= form_open($accion, ['id'=>'formulario']);?>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Trámite:</label>
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
                            <?php }elseif($tipo_tramite == 'lpe/'){?>
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
                            <?php }?>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Estado Actual:</label>
                                <div class="col-sm-9">
                                    <?php
                                        $campo = 'estado_tramite';
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
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Tipo de Documento*:</label>
                                <div class="col-sm-9">
                                    <select id="fk_tipo_documento" name="fk_tipo_documento" class="form-control">
                                        <option value="">SELECCIONE UNA OPCIÓN</option>
                                        <?php foreach($tiposDocumentos as $row){ ?>
                                            <option value="<?= $row['id'];?>" data-cambia-estado="<?= $row['cambia_estado'];?>" data-justificacion="<?= $row['justificacion'];?>" data-estado-tramite="<?= $row['estado_tramite'];?>"  <?= (isset($fk_tipo_documento) && $fk_tipo_documento == $row['id']) ? 'selected' : ''; ?> ><?= $row['nombre'];?></option>
                                        <?php }?>
                                    </select>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div id="estado_actualizar" class="form-group row">
                                <label class="col-sm-2 col-form-label">Estado al que Actualizará:</label>
                                <div class="col-sm-9">
                                    <?php
                                        $campo = 'estado_actualizarse';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'class' => 'form-control form-control-uppercase',
                                            'readonly' => true,
                                            'value' => set_value($campo)
                                        ));
                                    ?>
                                </div>
                            </div>
                            <div id="estado_tramite_padre" class="form-group row">
                                <label class="col-sm-2 col-form-label">Estado al que Actualizará*:</label>
                                <div class="col-sm-9">
                                    <select id="fk_estado_tramite" name="fk_estado_tramite" class="form-control">
                                        <?php foreach($estadosTramites as $row){ ?>
                                            <option value="<?= $row['id'];?>" data-padre="<?= $row['padre'];?>" <?= (isset($id_estado_padre) && $id_estado_padre == $row['id']) ? 'selected' : ''; ?> ><?= $row['texto'];?></option>
                                        <?php }?>
                                    </select>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div id="estado_tramite_hijo" class="form-group row">
                                <label class="col-sm-2 col-form-label">Sub Estado al que Actualizará*:</label>
                                <div class="col-sm-9">
                                    <select id="fk_estado_tramite_hijo" name="fk_estado_tramite_hijo" class="form-control">
                                        <option value="">SELECCIONE UNA OPCIÓN</option>
                                        <?php if(isset($estadosTramitesHijo)){?>
                                            <?php foreach($estadosTramitesHijo as $row){?>
                                                <option value="<?= $row['id'];?>" <?= (isset($id_estado_hijo) && $id_estado_hijo == $row['id']) ? 'selected' : ''; ?> ><?= $row['texto'];?></option>
                                            <?php }?>
                                        <?php }?>
                                    </select>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
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
                                <label class="col-sm-2 col-form-label">Referencia:</label>
                                <div class="col-sm-9">
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
                            <div id="justificacion_emision" class="form-group row">
                                <label class="col-sm-2 col-form-label">Justificación de la Emisión*:</label>
                                <div class="col-sm-9">
                                    <?php
                                        $campo = 'justificacion';
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
                                <label class="col-sm-2"></label>
                                <div class="col-sm-10">
                                    <?php echo form_submit('enviar', 'GENERAR CITE','class="btn btn-primary m-b-0"');?>
                                    <a href="<?= base_url($retorno);?>" class="btn btn-success m-b-0">CANCELAR</a>
                                    <a href="<?= base_url($atender);?>" class="btn btn-inverse m-b-0">ATENDER TRAMITE</a>
                                </div>
                            </div>
                        <?= form_close();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>