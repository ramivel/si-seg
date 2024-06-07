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
                                        $campo = 'oficina';
                                        echo form_dropdown($campo, $oficinas, set_value($campo), array('id' => 'oficina-reporte', 'class' => 'form-control'));
                                    ?>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                                <div class="col-sm-5">
                                    <?php
                                        $campo = 'usuario';
                                        echo form_dropdown($campo, $usuarios, set_value($campo), array('id' => 'usuario-reporte', 'class' => 'form-control'));
                                    ?>
                                    <?php if(isset($validation) && $validation->hasError($campo)){?>
                                        <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                    <?php }?>
                                </div>
                                <div class="col-sm-3">
                                    <button name="enviar" class="btn btn-info" type="submit" value="buscar"><i class="fa fa-list"></i> Generar Reporte</button>
                                    <button name="enviar" class="btn btn-inverse" type="submit" value="excel"><i class="fa fa-file-excel-o"></i> Exportar Excel</button>
                                </div>
                            </div>
                        <?= form_close();?>

                        <?php if(isset($datos) && count($datos)>0){?>
                            <div class="dt-responsive table-responsive">
                                <table id="tabla-buscador" class="table table-striped table-bordered nowrap" style="font-size: small;">
                                    <thead>
                                        <tr>
                                            <?php for($i=0;$i<count($campos_listar);$i++){?>
                                            <th class="text-center"><?php echo $campos_listar[$i];?></th>
                                            <?php }?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($datos as $fila){?>
                                        <tr>
                                            <?php for($i=0;$i<count($campos_reales);$i++){?>
                                            <td class="text-center">
                                            <?php
                                                if($campos_reales[$i]=='doc_digital'){
                                                    if(isset($fila['doc_digital']) && $fila['doc_digital'])
                                                        echo "<a href='".base_url($ruta_archivos.$fila['fk_area_minera'].'/'.$fila['doc_digital'])."' target='_blank' title='Ver Documento'><i class='feather icon-file'></i> Ver Documento</a>";
                                                    else
                                                        echo "";
                                                }else{
                                                    echo $fila[$campos_reales[$i]];
                                                }
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