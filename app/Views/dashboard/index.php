<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Contratos Administrativos Mineros</h3>
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="<?= base_url('cam/mis_tramites');?>">
                                    <div class="card bg-c-lite-green update-card text-bold">
                                        <div class="card-block">
                                            <div class="row align-items-end">
                                                <div class="col-8">
                                                    <h4 class="text-white"><?= $total_estados_bandeja;?></h4>
                                                    <h6 class="text-white m-b-0">TOTAL TRÁMITE(S)</h6>
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
                            <?php foreach($estados_bandeja as $estado){?>
                                <div class="col-md-4">
                                    <a href="<?= (isset($url_estados_bandeja[$estado]) ? base_url($url_estados_bandeja[$estado]) : '#');?>">
                                        <div class="card bg-c-green update-card text-bold">
                                            <div class="card-block">
                                                <div class="row align-items-end">
                                                    <div class="col-8">
                                                        <h4 class="text-white"><?= (isset($resumen_estados_bandeja[$estado]) ? $resumen_estados_bandeja[$estado] : 0);?></h4>
                                                        <h6 class="text-white m-b-0"><?=$estado?></h6>
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
                            <?php }?>
                            <?php if($total > 0){?>
                            <div class="col-md-12 text-center">
                                <h5 class="mb-3">Estados de Mis Trámites</h5>
                                <!--div class="row">
                                    <div class="col-md-6">
                                        <div class="alert alert-warning icons-alert">
                                            <p><span class="parpadea"><i class="fa fa-circle"></i></span> &nbsp; <strong>TIENE <code>4 TRÁMITES</code> QUE ESTAN A PUNTO DE VENCER EL PLAZO DE ATENCIÓN</strong></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-danger icons-alert">
                                            <p><span class="parpadea"><i class="fa fa-circle"></i></span> &nbsp; <strong>TIENE <code>4 TRÁMITES</code> QUE VENCIERON EL PLAZO DE ATENCIÓN</strong></p>
                                        </div>
                                    </div>
                                </!--div-->
                                <div id="dashcam" style="width: 100%; height: 550px;"></div>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>