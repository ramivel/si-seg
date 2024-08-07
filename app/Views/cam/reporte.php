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
                        <span>Debe seleccionar los campos requeridos para generar el Reporte.</span>
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
                                <div class="col-sm-4">
                                    <label class="col-form-label">Oficina:</label>
                                    <?php
                                        $campo = 'oficina';
                                        echo form_dropdown($campo, $oficinas, set_value($campo), array('class' => 'form-control'));
                                    ?>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                                <div class="col-sm-12 mt-3">
                                    <button name="enviar" class="btn btn-info" type="submit" value="buscar"><i class="fa fa-list"></i> Generar Reporte</button>
                                    <button name="enviar" class="btn btn-inverse" type="submit" value="excel"><i class="fa fa-file-excel-o"></i> Exportar Excel</button>
                                </div>
                            </div>
                        <?= form_close();?>
                    </div>
                </div>
            </div>

            <?php if(isset($resultado_general)){?>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-block">
                        <h5 class="mb-4 text-center">Reporte General</h5>
                        <div class="row">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-xs table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ESTADO TRAMITE</th>
                                            <?php foreach($oficinas as $idOficina=>$oficina){?>
                                                <?php if($idOficina>0){?>
                                                    <th class="text-center"><?= $oficina?></th>
                                                <?php }?>
                                            <?php }?>
                                            <th class="text-center">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($estados_tramites as $estado){?>
                                            <?php $total_estado = 0; ?>
                                            <?php if($estado['id']>0){?>
                                                <tr>
                                                    <td><?= $estado['texto'];?></td>
                                                    <?php foreach($oficinas as $idOficina=>$oficina){?>
                                                        <?php if($idOficina>0){?>
                                                            <td class="text-center"><?= (isset($resultado_general[$oficina][$estado['texto']]) && $resultado_general[$oficina][$estado['texto']] > 0) ? $resultado_general[$oficina][$estado['texto']] : 0; ?></td>
                                                            <?php $total_estado += (isset($resultado_general[$oficina][$estado['texto']]) && $resultado_general[$oficina][$estado['texto']] > 0) ? $resultado_general[$oficina][$estado['texto']] : 0; ?>
                                                        <?php }?>
                                                    <?php }?>
                                                    <th class="text-center"><?= $total_estado;?></th>
                                                </tr>
                                            <?php }?>
                                        <?php }?>
                                        <tr>
                                            <th>TOTAL</th>
                                            <?php $total = 0;?>
                                            <?php foreach($oficinas as $idOficina=>$oficina){?>
                                                <?php if($idOficina>0){?>
                                                    <th class="text-center"><?= (isset($total_oficinas[$oficina]) && $total_oficinas[$oficina] > 0) ? $total_oficinas[$oficina] : 0; ?></th>
                                                    <?php $total += (isset($total_oficinas[$oficina]) && $total_oficinas[$oficina] > 0) ? $total_oficinas[$oficina] : 0; ?>
                                                <?php }?>
                                            <?php }?>
                                            <th class="text-center"><?= $total;?></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-block">
                        <h5 class="mb-4 text-center">Gráfico de Avance</h5>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <button id="imprimir-chart-general" class="btn btn-success"><i class="fa fa fa-download"></i> Descargar (Imagen)</button>
                            </div>
                        </div>
                        <div id="avance" style="width: 100%; height: 750px;"></div>
                    </div>
                </div>
            </div>
            <?php }?>

            <?php if(isset($resultado_oficina)){?>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-block">
                        <h5 class="mb-4 text-center"><?= $oficinas[$oficina];?></h5>
                        <div class="row">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-xs table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ESTADO</th>
                                            <?php foreach($clasificaciones as $clasificacion){?>
                                                <th class="text-center"><?= $clasificacion?></th>
                                            <?php }?>
                                            <th class="text-center">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($estados_tramites as $estado){?>
                                            <?php $total_estado = 0; ?>
                                            <?php if($estado['id']>0){?>
                                                <tr>
                                                    <td><?= $estado['texto'];?></td>
                                                    <?php foreach($clasificaciones as $clasificacion){?>
                                                        <td class="text-center"><?= (isset($resultado_oficina[$clasificacion][$estado['texto']]) && $resultado_oficina[$clasificacion][$estado['texto']] > 0) ? $resultado_oficina[$clasificacion][$estado['texto']] : 0; ?></td>
                                                        <?php $total_estado += (isset($resultado_oficina[$clasificacion][$estado['texto']]) && $resultado_oficina[$clasificacion][$estado['texto']] > 0) ? $resultado_oficina[$clasificacion][$estado['texto']] : 0; ?>
                                                    <?php }?>
                                                    <th class="text-center"><?= $total_estado;?></th>
                                                </tr>
                                            <?php }?>
                                        <?php }?>
                                        <tr>
                                            <th>TOTAL</th>
                                            <?php $total = 0;?>
                                            <?php foreach($clasificaciones as $clasificacion){?>
                                                <th class="text-center"><?= (isset($total_clasificaciones[$clasificacion]) && $total_clasificaciones[$clasificacion] > 0) ? $total_clasificaciones[$clasificacion] : 0; ?></th>
                                                <?php $total += (isset($total_clasificaciones[$clasificacion]) && $total_clasificaciones[$clasificacion] > 0) ? $total_clasificaciones[$clasificacion] : 0; ?>
                                            <?php }?>
                                            <th class="text-center"><?= $total;?></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-block">
                        <h5 class="mb-4 text-center">Gráfico de Avance</h5>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <button id="imprimir-chart-oficina" class="btn btn-success"><i class="fa fa fa-download"></i> Descargar (Imagen)</button>
                            </div>
                        </div>
                        <div id="avance_oficina" style="width: 100%; height: 750px;"></div>
                    </div>
                </div>
            </div>
            <?php }?>

        </div>
    </div>
</div>