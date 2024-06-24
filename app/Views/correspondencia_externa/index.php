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
                            <table id="tabla-listado" class="table table-striped table-bordered nowrap">
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
                                            <?php
                                            if($fila['editar'] == 't' && $fila['estado'] == 'INGRESADO'){
                                                echo anchor($controlador.'editar/'.$fila['id'], '<i class="fa fa-edit"></i> Editar',array('class' =>'btn btn-sm btn-info mb-2')).'<br>';
                                            }
                                            ?>
                                            <a href="<?= $sincobol."correspondencia/hoja_ruta/hr_pdf/".$fila['fk_hoja_ruta'];?>" target="_blank" class="btn btn-sm btn-warning"><i class="fa fa-print"></i> IMPRIMIR H.R.</a>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>