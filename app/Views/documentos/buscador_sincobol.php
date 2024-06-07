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
                                <div class="col-sm-4">
                                    <?php
                                        $campo = 'texto';
                                        echo form_input(array(
                                            'name' => $campo,
                                            'id' => $campo,
                                            'placeholder' => 'Escriba el texto a buscar',
                                            'class' => 'form-control form-control-uppercase',
                                            'value' => set_value($campo,'',false)
                                        ));
                                    ?>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                        $campo = 'campo';
                                        echo form_dropdown($campo, $campos_buscar, set_value($campo), array('class' => 'form-control'));
                                    ?>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                                <div class="col-sm-3">
                                    <button name="enviar" class="btn btn-info" type="submit" value="buscar"><i class="fa fa-list"></i> Buscar</button>
                                </div>
                            </div>
                        <?= form_close();?>

                        <?php if(isset($datos) && count($datos)>0){?>
                            <div class="dt-responsive table-responsive">
                                <table id="tabla-buscador" class="table table-striped table-bordered nowrap" style="font-size: small;">
                                    <thead>
                                        <tr>
                                            <th class="nosort" width="90px"></th>
                                            <?php for($i=0;$i<count($campos_listar);$i++){?>
                                                <th class="text-center"><?php echo $campos_listar[$i];?></th>
                                            <?php }?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($datos as $fila){?>
                                        <tr>
                                            <td class="text-center">
                                                <?php echo anchor('documentos/desanexar_sincobol/'.$fila['id_hoja_ruta_seguimiento'].'/'.$fila['id_tramite'].'/'.$fila['id_siseg'].'/'.$fila['id_hoja_ruta'], 'Desanexar',array('class' =>'btn btn-danger desanexar'));?>                                                
                                            </td>
                                            <?php for($i=0;$i<count($campos_reales);$i++){?>
                                                <td class="text-center">
                                                    <?php
                                                        if($campos_reales[$i]=='id_tramite')
                                                            echo $tramites[$fila['id_tramite']];
                                                        else
                                                            echo $fila[$campos_reales[$i]];
                                                    ?>
                                                </td>
                                            <?php }?>
                                        </tr>
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