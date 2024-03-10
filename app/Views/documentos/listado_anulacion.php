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
                                        <?php for($i=0;$i<count($campos_listar);$i++){?>
                                        <th class="text-center"><?php echo $campos_listar[$i];?></th>
                                        <?php }?>
                                        <th class="nosort"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($datos) && count($datos)>0){?>
                                    <?php foreach ($datos as $n=>$fila){?>
                                    <tr>                                        
                                        <?php for($i=0;$i<count($campos_reales);$i++){?>
                                        <td class="text-center">
                                        <?= $fila[$campos_reales[$i]];?>
                                        </td>
                                        <?php }?>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <?php echo anchor('documentos/aprobar_anulacion/'.$id_tramite.'/'.$fila['id'], 'APROBAR',array('class' =>'btn btn-primary waves-effect waves-light mr-2 aprobar_anulacion'));?>
                                                <?php echo anchor('documentos/rechazar_anulacion/'.$id_tramite.'/'.$fila['id'], 'RECHAZAR',array('class' =>'btn btn-warning waves-effect waves-light rechazar_anulacion'));?>                                                
                                            </div>                                            
                                        </td>
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