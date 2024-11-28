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
                    <div class="card-header text-center pb-0">
                        <a href="<?= base_url($controlador.'mis_tramites_excel');?>" class="btn btn-inverse" target="_blank"><i class="fa fa-file-excel-o"></i> Descargar Bandeja</a>
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
                                                <div class="dropdown-info dropdown open">
                                                    <button class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light " type="button" id="dropdown-4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones</button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdown-4" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                        <?php echo anchor($controlador.'ver/2/'.$fila['id'], 'Ver',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                        <?php
                                                        switch($fila['ultimo_estado']){
                                                            case 'RECIBIDO':
                                                            case 'DEVUELTO':
                                                                echo anchor('documentos/generar_documento/'.$id_tramite.'/'.$fila['id'], 'Generar Documento',array('class' =>'dropdown-item waves-light waves-effect'));
                                                                echo anchor($controlador.'atender/'.$fila['id'], 'Atender',array('class' =>'dropdown-item waves-light waves-effect'));
                                                                echo anchor($controlador.'espera/'.$fila['id'], 'En Espera',array('class' =>'dropdown-item waves-light waves-effect'));
                                                                if(in_array(5, session()->get('registroPermisos')) && $fila['finalizar'] == 'SI')
                                                                    echo anchor($controlador.'finalizar/'.$fila['id'], 'Finalizar',array('class' =>'dropdown-item waves-light waves-effect'));
                                                                break;
                                                            case 'DERIVADO':
                                                            case 'MIGRADO':
                                                                if($fila['editar']=='t')
                                                                    echo anchor($controlador.'editar/'.$fila['id'], 'Editar Derivación',array('class' =>'dropdown-item waves-light waves-effect'));
                                                                break;
                                                            case 'EN ESPERA':
                                                                echo anchor('documentos/generar_documento/'.$id_tramite.'/'.$fila['id'], 'Generar Documento',array('class' =>'dropdown-item waves-light waves-effect'));
                                                                echo anchor($controlador.'atender/'.$fila['id'], 'Atender',array('class' =>'dropdown-item waves-light waves-effect'));
                                                                break;
                                                        }
                                                        ?>
                                                        <?php echo anchor($controlador.'anexar_sincobol/'.$fila['id'], 'Anexar H.R. SINCOBOL',array('class' =>'dropdown-item waves-light waves-effect'));?>
                                                        <?php echo anchor($controlador.'hoja_ruta_pdf/'.$fila['id'], 'Imprimir Hoja de Ruta',array('class' =>'dropdown-item waves-light waves-effect', 'target'=>'_blank'));?>
                                                    </div>
                                                </div>
                                                <?php if($fila['n_correspondencia_externa'] > 0){?>
                                                    <br>
                                                    <?= anchor($controlador.'correspondencia_externa/'.$fila['id'], 'Correspondencia Externa',array('class' =>'btn btn-sm btn-primary mt-1'));?>
                                                <?php }?>
                                            </td>
                                            <?php for($i=0;$i<count($campos_reales);$i++){?>
                                            <td class="text-center" >
                                                <?php
                                                    if($campos_reales[$i]=='ultimo_estado'){
                                                        $style = '';
                                                        switch($fila[$campos_reales[$i]]){
                                                            case 'MIGRADO':
                                                                $style = MIGRADO;
                                                                break;
                                                            case 'RECIBIDO':
                                                                $style = RECIBIDO;
                                                                break;
                                                            case 'DEVUELTO':
                                                                $style = DEVUELTO;
                                                                break;
                                                            case 'DERIVADO':
                                                                $style = DERIVADO;
                                                                break;
                                                            case 'EN ESPERA':
                                                                $style = EN_ESPERA;
                                                                break;
                                                        }
                                                        echo '<button class="'.$style.'">'.$fila[$campos_reales[$i]].'</button>';
                                                    }elseif($campos_reales[$i]=='apm_presento'){
                                                        if($fila['ultimo_recurso_jerarquico']=='t')
                                                            echo 'RECURSO JERÁRQUICO<br>';
                                                        if($fila['ultimo_recurso_revocatoria']=='t')
                                                            echo 'RECURSO DE REVOCATORIA<br>';
                                                        if($fila['ultimo_oposicion']=='t')
                                                            echo 'OPOSICIÓN';
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