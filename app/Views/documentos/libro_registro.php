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
                    <div class="card-header text-center">
                        <h4>Libro de Registro</h4>
                        <span>Debe seleccionar las hojas de ruta para generar el Libro de Registro.</span>
                    </div>
                    <div class="card-block">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <?php
                                    $campo = 'id_tramite';
                                    echo form_dropdown($campo, $tramites, set_value($campo), array('id' => $campo, 'class' => 'form-control'));
                                ?>
                            </div>
                            <div class="col-sm-5">
                                <?php
                                    $campo = 'id_hoja_ruta';
                                    echo form_dropdown($campo, $hojas_ruta, set_value($campo), array('id' => $campo, 'class' => 'libro-registro-ajax col-sm-12'));
                                ?>
                            </div>
                            <div class="col-sm-3">
                                <button type="button" class="btn btn-info agregar-hr"><i class="fa fa-plus"></i> Agregar al Libro</button>
                            </div>
                        </div>                    
                        <?= form_open($accion, array('target'=>'_blank'));?>
                        <div class="row mb-2">
                            <div class="col-sm-12 text-right">                                
                                <button type="submit" class="btn btn-success"><i class="fa fa-print"></i> Imprimir Libro de Registro</button>
                            </div>
                        </div>
                        <div class="dt-responsive table-responsive">
                            <table id="tabla-libro-registro" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th class="text-center">Correlativo</th>
                                        <th class="text-center">Tipo Trámite</th>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                        <?= form_close();?>                        

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>