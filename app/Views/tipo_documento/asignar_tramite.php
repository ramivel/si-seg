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
        <div class="row mb-3">
            <div class="col-sm-12 text-left">
                <a href="<?= base_url($controlador);?>" class="btn btn-success"><i class="feather icon-arrow-left"></i> ATRAS</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header text-center pb-2">
                        <h3 class="mb-1"><?= $fila['nombre'];?></h3>
                        <span>Los campos con <code>*</code> son obligatorios.</span>
                    </div>
                    <div class="card-block">
                        <h4 class="sub-title mb-2">NUEVA ASIGNACIÓN</h4>
                        <span>Los campos con <code>*</code> son obligatorios.</span>
                        <?= form_open_multipart($accion, ['id'=>'formulario']);?>
                            <div class="form-group row d-none">
                                <div class="col-sm-10">
                                    <?= form_hidden('id', set_value('id',$fila['id']));?>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Cambia Estado * :</label>
                                <div class="col-sm-10">
                                    <?php $campo = 'cambia_estado';?>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label"><input class="form-check-input" id="<?= $campo.'_si';?>" type="radio" name="<?= $campo;?>" value="SI"> SI</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label"><input class="form-check-input" id="<?= $campo.'_seleccion';?>" type="radio" name="<?= $campo;?>" value="EL USUARIO SELECCIONA"> EL USUARIO SELECCIONA</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label"><input class="form-check-input" id="<?= $campo.'_no';?>" type="radio" name="<?= $campo;?>" value="NO"> NO</label>
                                    </div>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Trámite*:</label>
                                <div class="col-sm-10">
                                    <?php
                                        $campo = 'fk_tramite';
                                        echo form_dropdown($campo, $tramites, set_value($campo), array('id'=>'fk_tramite_asignar','class' => 'form-control'));
                                    ?>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div id="estado_tramite_padre" class="form-group row">
                                <label class="col-sm-2 col-form-label">Estado del Tramite*:</label>
                                <div class="col-sm-10">
                                    <select id="fk_estado_tramite_padre_asignar" name="fk_estado_tramite" class="form-control">
                                        <option value="">SELECCIONE UNA OPCIÓN</option>
                                    </select>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div id="estado_tramite_hijo" class="form-group row">
                                <label class="col-sm-2 col-form-label">Sub Estado del Tramite*:</label>
                                <div class="col-sm-10">
                                    <select id="fk_estado_tramite_hijo_asignar" name="fk_estado_tramite_hijo" class="form-control">
                                        <option value="">SELECCIONE UNA OPCIÓN</option>
                                    </select>
                                    <span class="messages"></span>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Requiere Justificación:</label>
                                <div class="col-sm-5">
                                    <?php $campo = 'justificacion';?>
                                    <div class="checkbox-fade fade-in-primary">
                                        <label>
                                            <input type="checkbox" value="true" name="<?= $campo;?>" <?= set_checkbox($campo, 'true'); ?> />
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
                                <div class="col-sm-10"><span><b>Nota.</b> Si ya existe la asignación a un trámite se reemplazará a la existente.</span></div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <?php echo form_submit('enviar', 'GUARDAR','class="btn btn-primary m-b-0"');?>
                                </div>
                            </div>
                        <?= form_close();?>
                        <?php if(!empty($datos) && count($datos)>0){?>
                        <?php }?>
                        <h4 class="sub-title mb-2">ASIGNACIÓN(ES) REALIZADA(S)</h4>
                        <div class="dt-responsive table-responsive">
                            <table class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <?php for($i=0;$i<count($campos_listar);$i++){?>
                                        <th class="text-center"><?php echo $campos_listar[$i];?></th>
                                        <?php }?>
                                        <th class="nosort"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datos as $n=>$fila){?>
                                        <tr>
                                            <?php for($i=0;$i<count($campos_reales);$i++){?>
                                            <td class="text-center"><?= $fila[$campos_reales[$i]];?></td>
                                            <?php }?>
                                            <td class="text-center">
                                                <?php echo anchor($controlador.'eliminar_asignacion/'.$fila['id'], '<i class="icofont icofont-ui-delete"></i> Eliminar',array('class' =>'btn btn-danger waves-effect waves-light eliminar'));?>
                                            </td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>