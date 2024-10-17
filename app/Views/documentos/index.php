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
        <div class="row mb-3">
            <div class="col-sm-12 text-right">
                <a href="<?= base_url($controlador.'mis_documentos_excel/'.$id_tramite);?>" class="btn btn-inverse" target="_blank"><i class="fa fa-file-excel-o"></i> Descargar Mis Documentos</a>
            </div>
        </div>
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
                                        <th class="nosort" width="90px"></th>
                                        <?php for($i=0;$i<count($campos_listar);$i++){?>
                                        <th class="text-center"><?php echo $campos_listar[$i];?></th>
                                        <?php }?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($datos) && count($datos)>0){?>
                                    <?php foreach ($datos as $n=>$fila){?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if($fila['estado'] !== 'ANULADO' && $fila['estado'] !== 'SOLICITUD ANULACIÓN'){?>
                                                <div class="dropdown-info dropdown open">
                                                    <button class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light " type="button" id="dropdown-4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones</button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdown-4" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                        <?php echo anchor('documentos/descargar/'.$fila['id'], 'Descargar Word',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                        <?php if($fila['estado'] == 'SUELTO'){?>
                                                            <?php echo anchor('documentos/editar/'.$id_tramite.'/'.$fila['id'], 'Editar',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                            <?php echo anchor('documentos/anular/'.$id_tramite.'/'.$fila['id'], 'Solicitar Anulación',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                        <?php }elseif($fila['fk_usuario_actual'] == $id_usuario && !$fila['doc_digital']){?>
                                                            <?php echo anchor('documentos/anular/'.$id_tramite.'/'.$fila['id'], 'Solicitar Anulación',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                        <?php }?>
                                                        <?php if(!$fila['doc_digital']){?>
                                                            <?php echo anchor('documentos/subir/'.$id_tramite.'/'.$fila['id'], 'Subir Documento',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            <?php }?>
                                        </td>
                                        <?php for($i=0;$i<count($campos_reales);$i++){?>
                                        <td class="text-center">
                                            <?php
                                            if($campos_reales[$i] == 'doc_digital'){
                                                if(isset($fila['doc_digital']) && $fila['doc_digital'])
                                                    echo "<a href='".base_url($fila['doc_digital'])."' target='_blank' title='Ver Documento'><i class='feather icon-file'></i> Ver Documento</a>";
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