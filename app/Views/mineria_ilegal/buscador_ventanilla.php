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
                        <h5>Filtros</h5>
                        <span>Debe seleccionar los campos requeridos para la busqueda.</span>
                    </div>
                    <div class="card-block">
                        <?= form_open($accion);?>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <?php
                                    $campo = 'texto';
                                    echo form_input(array(
                                        'name' => $campo,
                                        'id' => $campo,
                                        'type' => (isset($campo_buscar) && $campo_buscar == 'fecha_hoja_ruta') ? 'date':'text',
                                        'placeholder' => 'Escriba el Correlativo',
                                        'class' => 'form-control form-control-uppercase',
                                        'value' => set_value($campo,'',false)
                                    ));
                                ?>
                                <?php if(isset($validation) && $validation->hasError($campo)){?>
                                    <span class="form-bar text-danger"><?= $validation->getError($campo);?></span>
                                <?php }?>
                            </div>
                            <div class="col-sm-3">
                                <?php
                                    $campo = 'campo';
                                    echo form_dropdown($campo, $campos_buscar, set_value($campo), array('id' => 'filtro-buscador', 'class' => 'form-control'));
                                ?>
                            </div>
                            <!--
                            <div class="col-sm-2">
                                <?php
                                    $campo = 'tipo_denuncia';
                                    echo form_dropdown($campo, $tipos_denuncias, set_value($campo), array('class' => 'form-control'));
                                ?>
                            </div>
                            -->
                            <div class="col-sm-1">
                                <button class="btn btn-info"><i class="fa fa-search"></i>Buscar</button>
                            </div>
                        </div>
                        <?= form_close();?>
                        <?php if(isset($datos)){ ?>
                            <div class="dt-responsive table-responsive">
                                <table id="tabla-buscador" class="table table-striped table-bordered nowrap" style="font-size: small;">
                                    <thead>
                                        <tr>
                                            <?php for($i=0;$i<count($campos_listar);$i++){?>
                                                <th class="text-center"><?php echo $campos_listar[$i];?></th>
                                            <?php }?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(!empty($datos) && count($datos)>0){?>
                                        <?php foreach ($datos as $fila){?>
                                        <tr>
                                            <?php for($i=0;$i<count($campos_reales);$i++){?>
                                                <td class="text-center">
                                                <?php
                                                    if($campos_reales[$i]=='ultimo_estado'){
                                                        $style = '';
                                                        switch($fila[$campos_reales[$i]]){
                                                            case 'REGULARIZACIÓN':
                                                                $style = 'btn btn-sm btn-danger btn-round';
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
                                                            case 'ANEXADO':
                                                                $style = 'btn btn-sm btn-info btn-round';
                                                                break;
                                                        }
                                                        echo '<button class="'.$style.'">'.$fila[$campos_reales[$i]].'</button>';
                                                    }elseif($campos_reales[$i]=='denunciante' || $campos_reales[$i]=='remitente' || $campos_reales[$i]=='destinatario' || $campos_reales[$i]=='responsable'){
                                                        echo str_replace(' || ', '<br>', $fila[$campos_reales[$i]]);
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
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>