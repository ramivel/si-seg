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
                        <div class="col-sm text-left">
                                <a href="<?= base_url($controlador.'agregar');?>" class="btn btn-success"><i class="feather icon-plus-circle"></i> Nuevo Registro</a>
                            </div>
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
                                        <?php for($i=0;$i<count($campos_listar);$i++){?>
                                        <th class="text-center"><?php echo $campos_listar[$i];?></th>
                                        <?php }?>
                                        <th class="nosort"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($datos) && count($datos)>0){?>
                                    <?php foreach ($datos as $fila){?>
                                    <tr>
                                        <?php for($i=0;$i<count($campos_reales);$i++){?>
                                        <td class="text-center">
                                        <?php
                                            if($campos_reales[$i]=='tramites'){
                                                if($fila['tramites']){
                                                    $tmp = explode(',', $fila['tramites']);
                                                    foreach($tmp as $row)
                                                        echo $tramites[$row].'<br>';
                                                }
                                            }elseif($campos_reales[$i]=='perfiles'){
                                                if($fila['perfiles']){
                                                    $tmp = explode(',', $fila['perfiles']);
                                                    foreach($tmp as $row)
                                                        echo $perfiles[$row].'<br>';
                                                }
                                            }elseif($campos_reales[$i]=='notificacion'){
                                                if($fila[$campos_reales[$i]] == 't')
                                                    echo 'SI';
                                                else
                                                    echo 'NO';                                                
                                            }else{
                                                echo $fila[$campos_reales[$i]];
                                            }
                                        ?>
                                        </td>
                                        <?php }?>
                                        <td class="text-center">
                                            <div class="dropdown-info dropdown open">
                                                <button class="btn btn-info dropdown-toggle waves-effect waves-light " type="button" id="dropdown-4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones</button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-4" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                    <?php echo anchor('tipo_documento/descargar/'.$fila['id'], 'Descargar Plantilla',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                    <?php echo anchor('tipo_documento/editar/'.$fila['id'], 'Editar',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                    <?php echo anchor('tipo_documento/eliminar/'.$fila['id'], 'Eliminar',array('class' =>'dropdown-item waves-light waves-effect eliminar'));?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php }?>
                                <?php }?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <?php for($i=0;$i<count($campos_listar);$i++){?>
                                        <th><?php echo $campos_listar[$i];?></th>
                                        <?php }?>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>