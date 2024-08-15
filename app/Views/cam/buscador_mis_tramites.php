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
                        <h5>Filtros</h5>
                        <span>Debe seleccionar los campos requeridos para la busqueda.</span>
                    </div>
                    <div class="card-block">
                        <?= form_open($accion);?>
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label class="col-form-label">Fecha Mecanizada Inicio:</label>
                                <?php
                                    $campo = 'fecha_inicio';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'type' => 'date',
                                        'class' => 'form-control',
                                        'value' => set_value($campo,'',false)
                                    ));
                                ?>
                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                <?php }?>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-form-label">Fecha Mecanizada Fin:</label>
                                <?php
                                    $campo = 'fecha_fin';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'type' => 'date',
                                        'class' => 'form-control',
                                        'value' => set_value($campo,'',false)
                                    ));
                                ?>
                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                <?php }?>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-form-label">Estado:</label>
                                <?php
                                    $campo = 'estado';
                                    echo form_dropdown($campo, $estados, set_value($campo), array('id' => 'estado-reporte','class' => 'form-control'));
                                ?>
                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                <?php }?>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-form-label">SubEstado:</label>
                                <?php
                                    $campo = 'subestado';
                                    echo form_dropdown($campo, $subestados, set_value($campo), array('id' => 'subestado-reporte','class' => 'form-control'));
                                ?>
                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                <?php }?>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <button name="enviar" class="btn btn-info" type="submit" value="buscar"><i class="fa fa-search"></i> Generar</button>
                                <button name="enviar" class="btn btn-inverse" type="submit" value="excel"><i class="fa fa-file-excel-o"></i> Excel</button>
                            </div>
                        </div>
                        <?= form_close();?>

                        <?php if(isset($datos) && count($datos) > 0){ ?>
                            <div class="dt-responsive table-responsive">
                                <table id="tabla-listado" class="table table-striped table-bordered nowrap" style="font-size: small;">
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
                                        <tr>
                                            <td class="text-center">
                                                <?php echo anchor($controlador.'ver/3/'.$fila['id'], '<i class="fa fa-eye"></i> Ver',array('class' =>'btn btn-sm btn-info'));?>
                                            </td>
                                            <?php for($i=0;$i<count($campos_reales);$i++){?>
                                            <td class="text-center">
                                            <?php
                                                if($campos_reales[$i]=='ultimo_estado'){
                                                    $style = '';
                                                    switch($fila[$campos_reales[$i]]){
                                                        case 'MIGRADO':
                                                            $style = 'btn btn-sm btn-danger btn-round';
                                                            break;
                                                        case 'ATENDIDO':
                                                            $style = 'btn btn-sm btn-success btn-round';
                                                            break;
                                                        case 'RECIBIDO':
                                                            $style = 'btn btn-sm btn-primary btn-round';
                                                            break;
                                                        case 'DEVUELTO':
                                                            $style = 'btn btn-sm btn-warning btn-round';
                                                            break;
                                                        case 'DERIVADO':
                                                            $style = 'btn btn-sm btn-inverse btn-round';
                                                            break;
                                                        case 'EN ESPERA':
                                                            $style = 'btn btn-sm btn-info btn-round';
                                                            break;
                                                    }
                                                    echo '<button class="'.$style.'">'.$fila[$campos_reales[$i]].'</button>';
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
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>