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
                            <a href="<?= base_url($controlador);?>" class="btn btn-primary"><i class="feather icon-arrow-left"></i> Atrás</a>
                            <a href="<?= base_url($controlador.'agregar_categoria/'.$tramite['id']);?>" class="btn btn-success"><i class="feather icon-plus-circle"></i> Nuevo Registro</a>
                        </div>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li><i class="feather icon-maximize full-card"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="dt-responsive table-responsive">                        
                            <table id="tabla-listado" class="table table-striped table-hover table-bordered nowrap">                            
                                <thead>
                                    <tr>
                                        <th class="nosort"></th>
                                        <?php for($i=0;$i<count($campos_listar);$i++){?>
                                        <th class="text-center" style="width: 50px !important;"><?php echo $campos_listar[$i];?></th>
                                        <?php }?>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($datos) && count($datos)>0){?>
                                    <?php foreach ($datos as $fila){?>
                                    <tr>
                                        <td class="text-center">
                                            <div class="dropdown-info dropdown open">
                                                <button class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light " type="button" id="dropdown-4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones</button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-4" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">                                                    
                                                    <?php echo anchor($controlador.'subcategoria/'.$fila['id'], 'Listado Sub Estados',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                    <?php echo anchor($controlador.'editar_categoria/'.$fila['id'], 'Editar',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                    <?php echo anchor($controlador.'eliminar_categoria/'.$fila['id'], 'Eliminar',array('class' =>'dropdown-item waves-light waves-effect eliminar'));?>                                                                                                      
                                                </div>
                                            </div>
                                            
                                        </td>
                                        <?php for($i=0;$i<count($campos_reales);$i++){?>
                                        <td class="text-center">
                                        <?php
                                            if($campos_reales[$i]=='activo'){
                                                if($fila[$campos_reales[$i]]=='t')
                                                    echo '<span class="text-success"><i class="ti-check"></i> Activo</span>';
                                                else
                                                    echo '<span class="text-danger"><i class="ti-close"></i> Inactivo</span>';
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