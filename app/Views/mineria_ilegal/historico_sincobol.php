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
                                                <a href="https://www.autoridadminera.gob.bo/denuncias/vistadmin.php?iDenuncia=<?= $fila['id_denuncia'];?>" class="btn btn-sm btn-inverse" target="_blank">VER</a>
                                            </td>
                                            <?php for($i=0;$i<count($campos_reales);$i++){?>
                                            <td class="text-center" >
                                                <?php
                                                    if($campos_reales[$i]=='correlativo'){
                                                        printf('FD-%04d', $fila['id_denuncia']);
                                                    }elseif($campos_reales[$i]=='estado'){
                                                        $style = 'btn btn-sm btn-warning btn-round';
                                                        $texto = 'DESCARTADO';
                                                        if($fila['hoja_ruta']){
                                                            $style = 'btn btn-sm btn-success btn-round';
                                                            $texto = 'PROCESADO';
                                                        }
                                                        echo '<button class="'.$style.'">'.$texto.'</button>';
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