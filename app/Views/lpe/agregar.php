<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header text-center pb-2">
                        <h3 class="mb-1"><?= $titulo_formulario;?></h3>
                        <span>Los campos con <code>*</code> son obligatorios.</span>
                    </div>
                    <div class="card-block">
                        <?= form_open_multipart($accion, ['id'=>'formulario']);?>
                        <!-- Row start -->
                        <div class="row">
                            <div class="col-lg-11 col-xl-11">
                                <h4 class="sub-title mb-2">HOJA DE RUTA MADRE</h4>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Correlativo*:</label>
                                    <div class="col-sm-10">
                                        <?php $campo = 'fk_solicitud_licencia_contrato';?>
                                        <select id="<?= $campo;?>" name="<?= $campo;?>" class="hoja-ruta-madre-lpe-ajax col-sm-12">
                                            <?php if(isset($hr_madre)){ ?>
                                                <option value="<?= $hr_madre['id'];?>"><?= $hr_madre['nombre'];?></option>
                                            <?php }else{ ?>
                                                <option value="">Escriba la Hoja de Ruta Madre o el Código Único del Área Minera</option>
                                            <?php } ?>
                                        </select>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Referencia:</label>
                                    <div class="col-sm-6">
                                        <?php
                                            $campo = 'referencia';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => true,
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                    </div>
                                    <label class="col-sm-2 col-form-label">Fecha Mecanizada:</label>
                                    <div class="col-sm-2">
                                        <?php
                                            $campo = 'fecha_mecanizada';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => true,
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                    </div>
                                </div>
                                <h4 class="sub-title mb-2">DATOS DEL ÁREA MINERA</h4>
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
                                                'value' => set_value($campo,'',false)
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
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Extensión:</label>
                                    <div class="col-sm-3">
                                        <?php
                                            $campo = 'extension';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => true,
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                    </div>
                                    <label class="col-sm-2 col-form-label">Dir. Departamental/Regional:</label>
                                    <div class="col-sm-5">
                                        <?php
                                            $campo = 'regional';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => true,
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Departameto(s):</label>
                                    <div class="col-sm-2">
                                        <?php
                                            $campo = 'departamentos';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => true,
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                    </div>
                                    <label class="col-sm-1 col-form-label">Provincia(s):</label>
                                    <div class="col-sm-3">
                                        <?php
                                            $campo = 'provincias';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => true,
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                    </div>
                                    <label class="col-sm-1 col-form-label">Municipio(s):</label>
                                    <div class="col-sm-3">
                                        <?php
                                            $campo = 'municipios';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => true,
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Área Protegida:</label>
                                    <div class="col-sm-10">
                                        <?php
                                            $campo = 'area_protegida';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'class' => 'form-control form-control-uppercase',
                                                'readonly' => true,
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                    </div>
                                </div>
                                <h4 class="sub-title mb-2">ACTOR PRODUCTIVO MINERO SOLICITANTE</h4>
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
                                                'value' => set_value($campo,'',false)
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
                                                'value' => set_value($campo,'',false)
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
                                                'value' => set_value($campo,'',false)
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
                                                'value' => set_value($campo,'',false)
                                            ));
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Domicilio Legal*:</label>
                                    <div class="col-sm-10">
                                        <?php
                                            $campo = 'domicilio_legal';
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
                                    <label class="col-sm-2 col-form-label">Domicilio Procesal*:</label>
                                    <div class="col-sm-10">
                                        <?php
                                            $campo = 'domicilio_procesal';
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
                                    <label class="col-sm-2 col-form-label">Teléfono(s) del Solicitante*:</label>
                                    <div class="col-sm-9">
                                        <?php
                                            $campo = 'telefono_solicitante';
                                            echo form_input(array(
                                                'name' => $campo,
                                                'id' => $campo,
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
                                <h4 class="sub-title mb-2">ESTADO DEL TRÁMITE</h4>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Estado del Trámite*:</label>
                                    <div class="col-sm-10">
                                        <select id="fk_estado_tramite" name="fk_estado_tramite" class="form-control">
                                            <?php foreach($estadosTramites as $row){?>
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
                                    <label class="col-sm-2 col-form-label">Sub Estado del Trámite*:</label>
                                    <div class="col-sm-10">
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
                                    <label class="col-sm-2 col-form-label">El APM presento:</label>
                                    <div class="col-sm-2">
                                        <?php $campo = 'recurso_jerarquico';?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" id="<?= $campo;?>" name="<?= $campo;?>" value="true" <?= set_checkbox($campo, 'true', false); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>RECURSO JERÁRQUICO</span>
                                            </label>
                                        </div>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                    <div class="col-sm-2">
                                        <?php $campo = 'recurso_revocatoria';?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" id="<?= $campo;?>" name="<?= $campo;?>" value="true" <?= set_checkbox($campo, 'true', false); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>RECURSO DE REVOCATORIA</span>
                                            </label>
                                        </div>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                    <div class="col-sm-2">
                                        <?php $campo = 'oposicion';?>
                                        <div class="checkbox-fade fade-in-primary">
                                            <label>
                                                <input type="checkbox" id="<?= $campo;?>" name="<?= $campo;?>" value="true" <?= set_checkbox($campo, 'true', false); ?> />
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span>OPOSICIÓN</span>
                                            </label>
                                        </div>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Observaciones*:</label>
                                    <div class="col-sm-10">
                                        <?php
                                            $campo = 'observaciones';
                                            echo form_textarea(array(
                                                'name' => $campo,
                                                'id' => $campo,
                                                'rows' => '3',
                                                'class' => 'form-control form-control-uppercase',
                                                'value' => set_value($campo,'SIN OBSERVACIONES',false)
                                            ));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                                <h4 class="sub-title mb-2">DERIVACIÓN DEL TRÁMITE</h4>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Responsable del Trámite*:</label>
                                    <div class="col-sm-10">
                                        <?php $campo = 'fk_usuario_destinatario';?>
                                        <select id="<?= $campo;?>" name="<?= $campo;?>" class="analista-destinatario-lpe-ajax col-sm-12">
                                            <?php if(isset($usu_destinatario)){ ?>
                                                <option value="<?= $usu_destinatario['id'];?>"><?= $usu_destinatario['nombre'];?></option>
                                            <?php }else{ ?>
                                                <option value="">Escriba el Nombre o Cargo...</option>
                                            <?php } ?>
                                        </select>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Instrucción*:</label>
                                    <div class="col-sm-10">
                                        <?php
                                            $campo = 'instruccion';
                                            echo form_dropdown($campo, $instrucciones, set_value($campo), array('id'=>$campo,'class' => 'instrucciones col-sm-12'));
                                        ?>
                                        <span class="messages"></span>
                                        <?php if(isset($validation) && $validation->hasError($campo)){?>
                                            <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="form-group row instruccion_otro">
                                    <label class="col-sm-2 col-form-label">Otro*:</label>
                                    <div class="col-sm-10">
                                        <?php
                                            $campo = 'otro';
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
                            </div>
                        </div>
                        <!-- Row end -->
                        <div class="form-group row">
                            <div class="col-sm-11 text-center mt-3">
                                <?php echo form_submit('enviar', 'GUARDAR','class="btn btn-primary m-b-0"');?>
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
