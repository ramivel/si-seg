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
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li><i class="feather icon-maximize full-card"></i></li>
                            </ul>
                        </div>
                    </div>
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
                                    <tr id="hr<?= $fila['id_derivacion']; ?>">
                                    <td class="text-center"><input name="recibir[]" value="<?= $fila['id_derivacion'];?>" type="checkbox" class="seleccionado"/></td>
                                        <td class="text-center">
                                            <?= anchor($controlador.'recibir/'.$fila['id_derivacion'], '<i class="fa fa-exchange"></i> RECIBIR',array('class' =>'btn btn-sm btn-primary recibir_tramite'));?><br>
                                            <button type="button" class="btn btn-sm btn-warning devolver_correspondencia mt-1" data-direccion="<?= base_url($controlador.'ajax_guardar_devolver')?>" data-idtra="<?=$fila['id_derivacion'];?>" data-hr="<?=$fila['correlativo_hr'];?>"><i class="fa fa-reply"></i> DEVOLVER</button><br>
                                            <?= anchor($controlador.'formulario_denuncia_pdf/'.$fila['id_denuncia'], '<i class="fa fa-print"></i> IMPRIMIR FORMULARIO',array('class' =>'btn btn-sm btn-info mt-1', 'target'=>'_blank'));?><br>
                                            <?= anchor($controlador.'hoja_ruta_pdf/'.$fila['id_hoja_ruta'], '<i class="fa fa-print"></i> IMPRIMIR H.R.',array('class' =>'btn btn-sm btn-info mt-1', 'target'=>'_blank'));?>
                                        </td>
                                        <?php for($i=0;$i<count($campos_reales);$i++){?>
                                        <td class="text-center" >
                                            <?php
                                                if($campos_reales[$i]=='tipo_documento_derivado'){
                                                    $style = '';
                                                    switch($fila[$campos_reales[$i]]){
                                                        case 'ORIGINAL':
                                                            $style = 'btn btn-out btn-sm btn-success btn-square';
                                                            break;
                                                        case 'COPIA':
                                                            $style = 'btn btn-out btn-sm btn-info btn-square';
                                                            break;
                                                    }
                                                    echo '<button class="'.$style.'">'.$fila[$campos_reales[$i]].'</button>';
                                                }elseif($campos_reales[$i]=='fk_tipo_denuncia'){
                                                    echo $tipo_denuncias[$fila[$campos_reales[$i]]];
                                                }else{
                                                    echo $fila[$campos_reales[$i]];
                                                }
                                            ?>
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
                                        <h4 class="modal-title">Devolver Hoja de Ruta</h4>
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