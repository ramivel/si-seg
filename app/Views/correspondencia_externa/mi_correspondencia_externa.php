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
                            <div class="bg-color-box color-warning waves-effect waves-light" style="background-color: #FEEDB9;color:black !important;">CORRESPONDENCIA A PUNTO DE VENCER SU PLAZO DE ATENCIÓN</div>
                            <div class="bg-color-box color-danger waves-effect waves-light" style="background-color: #F2C4C9; color:black !important;">CORRESPONDENCIA QUE VENCIO EL PLAZO DE ATENCIÓN</div>
                            <table id="tabla-listado" class="table table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th class="nosort"></th>
                                        <?php for($i=0;$i<count($campos_listar);$i++){?>
                                        <th class="text-center"><?php echo $campos_listar[$i];?></th>
                                        <?php }?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($datos) && count($datos)>0){?>
                                    <?php foreach ($datos as $fila){?>
                                        <?php
                                        $style = '';
                                        if($fila['notificar']=='t' && $fila['estado']=='RECIBIDO'){
                                            if($fila['dias_pasados'] > $fila['dias_limite'])
                                                $style = 'table-danger';
                                            elseif($fila['dias_pasados'] > $fila['dias_intermedio'])
                                                $style = 'table-warning';
                                        }
                                        
                                        ?>
                                    <tr class="<?=$style?>">
                                        <td class="text-center">
                                            <?php if($fila['estado']=='RECIBIDO'){?>
                                                <button type="button" class="btn btn-sm btn-primary atender-correspondencia-externa"
                                                data-id="<?=$fila['id'];?>" data-hr="<?=$fila['correlativo'];?>" data-fecha="<?=$fila['fecha_ingreso'];?>" data-dias="<?=$fila['dias_pasados'];?>" data-de='<?=$fila['documento_externo'];?>'><i class="fa fa-check"></i> ATENDER</button>
                                            <?php }?>
                                        </td>
                                        <?php for($i=0;$i<count($campos_reales);$i++){?>
                                        <td>
                                        <?php
                                            if($campos_reales[$i]=='estado'){
                                                $style = '';
                                                switch($fila[$campos_reales[$i]]){
                                                    case 'RECIBIDO':
                                                        $style = 'btn btn-sm btn-success btn-round';
                                                        break;
                                                    case 'INGRESADO':
                                                        $style = 'btn btn-sm btn-primary btn-round';
                                                        break;
                                                    case 'ATENDIDO':
                                                        $style = 'btn btn-sm btn-inverse btn-round';
                                                        break;
                                                }
                                                echo '<button class="'.$style.'">'.$fila[$campos_reales[$i]].'</button>';
                                            }elseif($campos_reales[$i]=='doc_digital'){
                                                echo '<a href="'.base_url($fila[$campos_reales[$i]]).'" target="_blank" title="Ver Documento"><i class="feather icon-file"></i> Ver Documento</a>';
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
                        </div>

                        <div class="modal fade" id="atender-correspondencia-externa-modal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Atender Correspondencia Externa</h4>
                                    </div>
                                    <?= form_open($accion, ['id' => 'formulario-correspondencia-externa']); ?>
                                    <div class="modal-body">
                                        <div class="form-group row d-none">
                                            <div class="col-sm-10">
                                                <?= form_input(array('type'=>'hidden','name'=>'id_documento','id'=>'id_documento'));?>
                                            </div>
                                        </div>
                                        <div class="form-group row d-none">
                                            <div class="col-sm-10">
                                                <?= form_input(array('type'=>'hidden','name'=>'id_tramite','id'=>'id_tramite', 'value'=>$id_tramite));?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Hoja de Ruta Madre:</label>
                                            <div class="col-sm-9">
                                                <span id="hoja_ruta"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Documento Externo:</label>
                                            <div class="col-sm-9">
                                                <span id="documento_externo"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Fecha Ingreso:</label>
                                            <div class="col-sm-9">
                                                <span id="fecha_ingreso"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Días Pasados:</label>
                                            <div class="col-sm-9">
                                                <span id="dias_pasados"></span>
                                            </div>
                                        </div>                                        
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Observaciones:</label>
                                            <div class="col-sm-9">
                                                <?php
                                                $campo = 'observacion_atencion';
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
                                        <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-warning waves-effect waves-light guardar-atender-correspondencia-externa">Atendido</button>
                                    </div>
                                    <?= form_close(); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>