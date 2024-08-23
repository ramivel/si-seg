<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row mb-3">
            <div class="col-sm-12 text-left">
                <a href="<?= $url_atras;?>" class="btn btn-success"><i class="feather icon-arrow-left"></i> ATRAS</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Formulario</h5>
                        <span>Los campos con <code>*</code> son obligatorios.</span>
                    </div>
                    <div class="card-block">
                        <?= form_open_multipart($accion, ['id'=>'formulario']);?>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-5 col-form-label">Tipo de Minería Ilegal : </label>
                                    <div class="col-sm-7">
                                        <?php
                                            $campo = 'fk_tipo_denuncia';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'readonly' => 'true',
                                                'class' => 'form-control form-control-uppercase',
                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $tipo_denuncias[$denuncia[$campo]] : ''),false)
                                            ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-5 col-form-label">Correlativo Hoja de Ruta Minería Ilegal : </label>
                                    <div class="col-sm-7">
                                        <?php
                                            $campo = 'correlativo_hr';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'readonly' => 'true',
                                                'class' => 'form-control form-control-uppercase',
                                                'value' => set_value($campo,(isset($hoja_ruta[$campo]) ? $hoja_ruta[$campo] : ''),false)
                                            ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Fecha : </label>
                                    <div class="col-sm-5">
                                        <?php
                                            $campo = 'fecha_hr';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => 'true',
                                                'value' => set_value($campo,(isset($hoja_ruta[$campo]) ? $hoja_ruta[$campo] : ''),false)
                                            ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-5 col-form-label">Correlativo Formulario de Minería Ilegal : </label>
                                    <div class="col-sm-7">
                                        <?php
                                            $campo = 'correlativo_denuncia';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'readonly' => 'true',
                                                'class' => 'form-control form-control-uppercase',
                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                            ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Fecha : </label>
                                    <div class="col-sm-5">
                                        <?php
                                            $campo = 'fecha_denuncia';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => 'true',
                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                            ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if($denuncia['fk_tipo_denuncia']==3){?>
                            <h4 class="sub-title mt-2 mb-2">Origen</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="text-nowrap text-right" width="320px" scope="row">Tipo de Origen :</th>
                                            <td><?= $denuncia['origen_oficio'];?></td>
                                        </tr>
                                        <?php if($denuncia['enlace']){?>
                                            <tr>
                                                <th class="text-nowrap text-right" scope="row">Enlace :</th>
                                                <td><a href="<?= $denuncia['enlace'];?>" target="_blank"><?= $denuncia['enlace'];?></a></td>
                                            </tr>
                                        <?php }?>
                                        <tr>
                                            <th class="text-nowrap text-right" scope="row">Informe Técnico :</th>
                                            <td>
                                                <?= $denuncia['informe_tecnico_numero'].' DE '.$denuncia['informe_tecnico_fecha'];?>
                                                &nbsp; <a href="<?=base_url($denuncia['informe_tecnico_digital']);?>" class='btn btn-sm btn-inverse' target='_blank' title='Ver Documento'><i class='fa fa-file-pdf-o'></i> Ver Documento</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-nowrap text-right" scope="row">Breve Descripción :</th>
                                            <td><?= $denuncia['descripcion_oficio'];?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php if(isset($hojas_rutas) && count($hojas_rutas)>0){?>
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap text-center" scope="row">Tipo H.R.</th>
                                                <th class="text-nowrap text-center" scope="row">Correlativo</th>
                                                <th class="text-nowrap text-center" scope="row">Fecha</th>
                                                <th class="text-nowrap text-center" scope="row">Referencia</th>
                                                <th class="text-nowrap text-center" scope="row">Remitente Externo/Interno</th>
                                                <th class="text-nowrap text-center" scope="row">Cite Externo/Interno</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($hojas_rutas as $row){?>
                                                <tr>
                                                    <td class="text-center"><?= $row['tipo_hoja_ruta'];?></td>
                                                    <td class="text-center"><?= $row['correlativo'];?></td>
                                                    <td class="text-center"><?= $row['fecha'];?></td>
                                                    <td class="text-center"><?= $row['referencia'];?></td>
                                                    <td class="text-center"><?= $row['remitente'];?></td>
                                                    <td class="text-center"><?= $row['cite'];?></td>
                                                </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                            <?php }?>
                            </div>
                        <?php }?>
                        <?php if(isset($denunciantes) && count($denunciantes)>0){?>
                            <h4 class="sub-title mt-2 mb-2">Datos del Denunciante(s)</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap text-center" scope="row">Nombre Completo</th>
                                            <th class="text-nowrap text-center" scope="row">Documento Identidad</th>
                                            <th class="text-nowrap text-center" scope="row">Celular</th>
                                            <th class="text-nowrap text-center" scope="row">E-Mail</th>
                                            <th class="text-nowrap text-center" scope="row">Dirección</th>
                                            <th class="text-nowrap text-center" scope="row">Documento Digital</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($denunciantes as $row){?>
                                            <tr>
                                                <td class="text-center"><?= $row['nombres'].' '.$row['apellidos'];?></td>
                                                <td class="text-center"><?= $row['documento_identidad'].' '.$row['expedido'];?></td>
                                                <td class="text-center"><?= $row['telefonos'];?></td>
                                                <td class="text-center"><?= $row['email'];?></td>
                                                <td class="text-center"><?= $row['direccion'];?></td>
                                                <td class='text-center'>
                                                    <a href="<?=base_url($row['documento_identidad_digital']);?>" class='btn btn-sm btn-inverse' target='_blank' title='Ver Documento de Identidad'><i class='fa fa-file-pdf-o'></i> Ver Documento</a> &nbsp;
                                                </td>
                                            </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        <?php }?>
                        <h4 class="sub-title mt-4 mb-2">Descripción de la Actividad Minera</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap" width="180px" scope="row">Departameto(s):</th>
                                        <td><?= $denuncia['departamento'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Provincia(s):</th>
                                        <td><?= $denuncia['provincia'];?></td>
                                        <th class="text-nowrap" width="180px" scope="row">Municipio(s):</th>
                                        <td><?= $denuncia['municipio'];?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Comunidad/Localidad :</th>
                                        <td><?= $denuncia['comunidad_localidad'];?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Descripción del lugar o punto de referencia :</th>
                                        <td><?= $denuncia['descripcion_lugar'];?></td>
                                    </tr>
                                    <?php if($denuncia['autores']){ ?>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Nombre(s) del posible(s) autor(es) :</th>
                                        <td><?= $denuncia['autores'];?></td>
                                    </tr>
                                    <?php }?>
                                    <?php if($denuncia['persona_juridica']){ ?>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Nombre de la persona(s) jurídica(s)<br>(empresa, cooperativa u otro) que este vinculado(s) a la actividad :</th>
                                        <td><?= $denuncia['persona_juridica'];?></td>
                                    </tr>
                                    <?php }?>
                                    <?php if($denuncia['descripcion_materiales']){ ?>
                                    <tr>
                                        <th class="text-nowrap text-right" width="320px" scope="row">Descripción de la maquinaria(s) u objeto(s) utilizado(s) en la actividad :</th>
                                        <td><?= $denuncia['descripcion_materiales'];?></td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_input(array('type'=>'hidden','name'=>'id_hoja_ruta','id'=>'id_hoja_ruta','value'=>set_value('id_hoja_ruta', (isset($id_hoja_ruta) ? $id_hoja_ruta : ''), false)));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('id_derivacion', set_value('id_derivacion', (isset($ultima_derivacion['id']) ? $ultima_derivacion['id'] : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <div class="form-group row d-none">
                            <div class="col-sm-10">
                                <?= form_hidden('id_denuncia', set_value('id_denuncia', (isset($denuncia['id']) ? $denuncia['id'] : ''), false));?>
                                <span class="messages"></span>
                            </div>
                        </div>
                        <h4 class="sub-title mt-4 mb-2">FINALIZAR HOJA DE RUTA DE MINERÍA ILEGAL</h4>
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label">Caja Documental Nº <span class="mytooltip tooltip-effect-5">
                                        <span class="tooltip-item"><i class="fa fa-question-circle"></i></span>
                                        <span class="tooltip-content clearfix">
                                            <span class="tooltip-text">Ayuda</span>
                                        </span>
                                    </span> * : </label>
                                    <div class="col-sm-6">
                                        <?php
                                            $campo = 'caja_documental';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                            ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Gestión * :</label>
                                    <div class="col-sm-8">
                                        <?php
                                            $campo = 'gestion_archivo';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'type' => 'number',
                                                'class' => 'form-control',
                                                'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                            ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Fojas * :</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $campo = 'fojas';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'type' => 'number',
                                            'class' => 'form-control',
                                            'value' => set_value($campo,(isset($denuncia[$campo]) ? $denuncia[$campo] : ''),false)
                                        ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if (isset($validation) && $validation->hasError($campo)) { ?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo); ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Motivo * :</label>
                            <div class="col-sm-10">
                                <?php
                                    $campo = 'motivo_finalizar';
                                    echo form_textarea(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'rows' => '3',
                                        'class' => 'form-control form-control-uppercase',
                                        'value' => set_value($campo,'',false)
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
                            <div class="col-sm-4 text-left">
                                <?php echo form_submit('enviar', 'FINALIZAR','class="btn btn-primary m-b-0"');?>
                                <a href="<?= base_url($controlador.'mis_tramites');?>" class="btn btn-success m-b-0">CANCELAR</a>
                            </div>
                        </div>
                        <?= form_close();?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>