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
                        <div class="dt-responsive table-responsive" style="min-height: 300px;">
                            <!--div class="bg-color-box color-warning waves-effect waves-light" style="background-color: #FEEDB9;color:black !important;">TRÁMITES A PUNTO DE VENCER SU PLAZO</!--div>
                            <div-- class="bg-color-box color-danger waves-effect waves-light" style="background-color: #F2C4C9; color:black !important;">TRÁMITES QUE VENCIERON SU PLAZO</div-->
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
                                        <?php
                                        $style = '';
                                        /*if($fila['notificar']=='t'){
                                            if($fila['dias'] > $fila['dias_limite'])
                                                $style = 'table-danger';
                                            elseif($fila['dias'] > $fila['dias_intermedio'])
                                                $style = 'table-warning';
                                        }*/
                                        ?>
                                    <tr class="<?=$style?>">
                                        <td class="text-center">
                                            <?= anchor($controlador.'revisar_denuncia_manual/'.$fila['id_hoja_ruta'], '<i class="fa fa-check-square-o"></i> REVISAR',array('class' =>'btn btn-sm btn-primary'));?><br>                                            
                                        </td>
                                        <?php for($i=0;$i<count($campos_reales);$i++){?>
                                        <td class="text-center" >
                                            <?php
                                                if($campos_reales[$i]=='estado_manual'){
                                                    $style = '';
                                                    switch($fila[$campos_reales[$i]]){
                                                        case 'INGRESADO':
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>