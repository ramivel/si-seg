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
            <?php if(isset($resultados) && count($resultados)>0){?>                
                <?php foreach($resultados as $resultado){?>
                    <div class="col-sm-12 col-md-12">
                        <div class="card">
                            <div class="card-header text-center">
                                <h3><?= $resultado['titulo'];?></h3>
                            </div>
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-md-4">
                                        <a href="<?= $resultado['url_total_estados_bandeja'];?>">
                                            <div class="card bg-c-lite-green update-card text-bold">
                                                <div class="card-block">
                                                    <div class="row align-items-end">
                                                        <div class="col-8">
                                                            <h4 class="text-white"><?= $resultado['total_estados_bandeja'];?></h4>
                                                            <h6 class="text-white m-b-0"><?= $resultado['nombre_total_estados_bandeja'];?></h6>
                                                        </div>
                                                        <div class="col-4 text-right">
                                                            <canvas id="update-chart-2" height="50"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <p class="text-white m-b-0"><i class="feather icon-clock text-white f-14 m-r-10"></i>Ultima Actualización : <?= date('d/m/Y H:i');?></p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>                                    
                                    <div class="col-md-4">
                                        <a href="<?= $resultado['url_total_recepcion'];?>">
                                            <div class="card bg-c-green update-card text-bold">
                                                <div class="card-block">
                                                    <div class="row align-items-end">
                                                        <div class="col-8">
                                                            <h4 class="text-white"><?= $resultado['total_recepcion'];?></h4>
                                                            <h6 class="text-white m-b-0"><?= $resultado['nombre_total_recepcion'];?></h6>
                                                        </div>
                                                        <div class="col-4 text-right">
                                                            <canvas id="update-chart-2" height="50"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <p class="text-white m-b-0"><i class="feather icon-clock text-white f-14 m-r-10"></i>Ultima Actualización : <?= date('d/m/Y H:i');?></p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="<?= $resultado['url_total_mis_tramites'];?>">
                                            <div class="card bg-c-green update-card text-bold">
                                                <div class="card-block">
                                                    <div class="row align-items-end">
                                                        <div class="col-8">
                                                            <h4 class="text-white"><?= $resultado['total_mis_tramites'];?></h4>
                                                            <h6 class="text-white m-b-0"><?= $resultado['nombre_total_mis_tramites'];?></h6>
                                                        </div>
                                                        <div class="col-4 text-right">
                                                            <canvas id="update-chart-2" height="50"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <p class="text-white m-b-0"><i class="feather icon-clock text-white f-14 m-r-10"></i>Ultima Actualización : <?= date('d/m/Y H:i');?></p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    
                                    <?php if(isset($resultado['id_mapa'])){?>
                                    <div class="col-md-12 text-center">                                        
                                        <div id="<?= $resultado['id_mapa'];?>" style="width: 100%; height: 550px;"></div>
                                    </div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>                                    
            <?php }?>                            
        </div>
    </div>
</div>