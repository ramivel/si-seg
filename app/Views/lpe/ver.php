<div class="page-wrapper">
    <?= $title?>
    <div class="page-body">
        <div class="row mb-3">
            <div class="col-sm-12 text-left">
                <a href="<?= $url_atras;?>" class="btn btn-success"><i class="feather icon-arrow-left"></i> ATRAS</a>
                <a href="<?= base_url($controlador.'hoja_ruta_pdf/'.$fila['id']);?>" target="_blank" class="btn btn-warning"><i class="fa fa-print"></i> IMPRIMIR H.R.</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header text-center pb-0">
                        <h3><?= $fila['correlativo'];?></h3>
                    </div>
                    <div class="card-block">
                        <?= $informacion_tramite;?>
                    </div>
                </div>
            </div>
            <?= $seccion;?>
        </div>
    </div>
</div>