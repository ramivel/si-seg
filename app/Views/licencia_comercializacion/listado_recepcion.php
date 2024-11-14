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
                    <div class="card-block">
                        <div class="dt-responsive table-responsive">
                            <?= form_open_multipart($controlador.'recibir_multiple');?>
                            <div class="row">
                                <div class="col-sm-4 text-left mb-2">
                                    <?php echo form_submit('enviar', 'RECIBIR SELECCIONADOS','class="btn btn-danger recibir-form"');?>
                                </div>
                            </div>
                            <table id="tabla-listado" class="table table-striped table-bordered nowrap" style="font-size: small;">
                                <thead>
                                    <tr>
                                        <th class="nosort text-center"><input id="seleccionar-todo" type="checkbox"/></th>
                                        <th class="nosort"></th>
                                        <?php for($i=0;$i<count($campos_listar);$i++){?>
                                        <th class="text-center"><?php echo $campos_listar[$i];?></th>
                                        <?php }?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($datos) && count($datos)>0){?>
                                    <?php foreach ($datos as $fila){?>
                                    <tr id="hr<?= $fila['id']; ?>">
                                        <td class="text-center"><input name="recibir[]" value="<?= $fila['id'];?>" type="checkbox" class="seleccionado"/></td>
                                        <td class="text-center">
                                            <?= anchor($controlador.'recibir/'.$fila['id'], '<i class="fa fa-exchange"></i> RECIBIR',array('class' =>'btn btn-sm btn-primary recibir_tramite'));?>
                                            <?php if($fila['estado_documento_externo'] != 'INGRESADO'){?>
                                                <br><button type="button" class="btn btn-sm btn-warning devolver_correspondencia mt-1" data-direccion="<?= base_url($controlador.'ajax_guardar_devolver')?>" data-idtra="<?=$fila['id'];?>" data-hr="<?=$fila['correlativo'];?>"><i class="fa fa-reply"></i> DEVOLVER</button>
                                            <?php }?>
                                            <?php if(isset($fila['n_correspondencia_externa']) && $fila['n_correspondencia_externa'] > 0){?>
                                                <br><?= anchor($controlador.'correspondencia_externa/'.$fila['id'], 'Correspondencia Externa',array('class' =>'btn btn-sm btn-primary mt-1'));?>
                                            <?php }?>
                                        </td>
                                        <?php for($i=0;$i<count($campos_reales);$i++){?>
                                        <td class="text-center" >
                                            <?= $fila[$campos_reales[$i]];?>
                                        </td>
                                        <?php }?>
                                    </tr>
                                    <?php }?>
                                <?php }?>
                                </tbody>
                            </table>
                            <?= form_close();?>
                        </div>

                        <div class="modal fade" id="devolver-modal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Devolver Trámite</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row d-none">
                                            <div class="col-sm-10">
                                                <?= form_input(array('type'=>'hidden','name'=>'accion','id'=>'accion'));?>
                                                <span class="messages"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row d-none">
                                            <div class="col-sm-10">
                                                <?= form_input(array('type'=>'hidden','name'=>'idtra','id'=>'idtra'));?>
                                                <span class="messages"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Hoja de Ruta:</label>
                                            <div class="col-sm-9">
                                                <span id="hr"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Motivo Devolución:</label>
                                            <div class="col-sm-9">
                                                <?php
                                                $campo = 'motivo_devolucion';
                                                echo form_textarea(array(
                                                    'name' => $campo,
                                                    'id' => $campo,
                                                    'rows' => '4',
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
                                        <button type="button" class="btn btn-warning waves-effect waves-light guardar-devolucion">Devolver</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>