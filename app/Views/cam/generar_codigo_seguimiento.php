<div class="page-wrapper">
    <?= $title ?>
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
                        <?= form_open_multipart($accion, ['id' => 'formulario']); ?>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('id'); ?>
                                <span class="messages"></span>
                            </div>
                        </div>                        
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Correlativo H.R. Madre*:</label>
                            <div class="col-sm-10">
                                <?php $campo = 'fk_acto_administrativo'; ?>
                                <select id="<?= $campo; ?>" name="<?= $campo; ?>" class="buscar-tramite-ajax col-sm-12">
                                    <?php if (isset($hr_madre)) { ?>
                                        <option value="<?= $hr_madre['id']; ?>"><?= $hr_madre['hr']; ?></option>
                                    <?php } else { ?>
                                        <option value="">Escriba la Hoja de Ruta Madre o el Código Único del Área Minera</option>
                                    <?php } ?>
                                </select>
                                <span class="messages"></span>
                                <?php if (isset($validation) && $validation->hasError($campo)) { ?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo); ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        <h5 class="sub-title">Datos del Área Minera</h5>
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
                                    'value' => set_value($campo, (isset($datos[$campo]) ? $datos[$campo] : ''), false)
                                ));
                                ?>
                            </div>
                            <label class="col-sm-2 col-form-label">Denominación:</label>
                            <div class="col-sm-5">
                                <?php
                                $campo = 'denominacion';
                                echo form_input(array(
                                    'name' => $campo,
                                    'id' => $campo,
                                    'class' => 'form-control form-control-uppercase',
                                    'readonly' => true,
                                    'value' => set_value($campo, (isset($datos[$campo]) ? $datos[$campo] : ''), false)
                                ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Represenante Legal:</label>
                            <div class="col-sm-6">
                                <?php
                                $campo = 'representante_legal';
                                echo form_input(array(
                                    'name' => $campo,
                                    'id' => $campo,
                                    'class' => 'form-control form-control-uppercase',
                                    'readonly' => true,
                                    'value' => set_value($campo, (isset($datos[$campo]) ? $datos[$campo] : ''), false)
                                ));
                                ?>
                            </div>
                            <label class="col-sm-2 col-form-label">Nacionalidad:</label>
                            <div class="col-sm-2">
                                <?php
                                $campo = 'nacionalidad';
                                echo form_input(array(
                                    'name' => $campo,
                                    'id' => $campo,
                                    'class' => 'form-control form-control-uppercase',
                                    'readonly' => true,
                                    'value' => set_value($campo, (isset($datos[$campo]) ? $datos[$campo] : ''), false)
                                ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Solicitante:</label>
                            <div class="col-sm-6">
                                <?php
                                $campo = 'titular';
                                echo form_input(array(
                                    'name' => $campo,
                                    'id' => $campo,
                                    'class' => 'form-control form-control-uppercase',
                                    'readonly' => true,
                                    'value' => set_value($campo, (isset($datos[$campo]) ? $datos[$campo] : ''), false)
                                ));
                                ?>
                            </div>
                            <label class="col-sm-2 col-form-label">Clasificación del APM:</label>
                            <div class="col-sm-2">
                                <?php
                                $campo = 'clasificacion';
                                echo form_input(array(
                                    'name' => $campo,
                                    'id' => $campo,
                                    'class' => 'form-control form-control-uppercase',
                                    'readonly' => true,
                                    'value' => set_value($campo, (isset($datos[$campo]) ? $datos[$campo] : ''), false)
                                ));
                                ?>
                            </div>
                        </div>                        
                        <div class="form-group row">
                            <label class="col-sm-2"></label>
                            <div class="col-sm-10">
                                <?php echo form_submit('enviar', 'GENERAR CÓDIGO DE SEGUIMIENTO', 'class="btn btn-primary m-b-0"'); ?>                                
                            </div>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>