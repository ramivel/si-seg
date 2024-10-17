<div id="tabla_datos" class="col-sm-12">
    <?= $tabs;?>
    <!-- Tab panes -->
    <div class="tab-content tabs card-block" style="background-color:#fff; border-left: 1px solid #ddd; border-right: 1px solid #ddd; border-bottom: 1px solid #ddd;">
        <div class="tab-pane active">
            <div class="row mb-3">
                <div class="col-sm-12 text-right">
                    <a href="<?= base_url($descargar);?>" class="btn btn-sm btn-inverse" target="_blank" title="Descargar Seguimiento"><i class="fa fa-file-excel-o"></i> Descargar Seguimiento</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <?php for($i=0;$i<count($cabecera_listado);$i++){?>
                            <th class="text-center"><?php echo $cabecera_listado[$i];?></th>
                            <?php }?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($datos) && count($datos)>0){?>
                        <?php foreach ($datos as $n=>$row){?>
                        <tr class="<?= (count($datos) == ($n+1) ? "table-warning" : "") ?>">
                            <?php for($i=0;$i<count($campos_listado);$i++){?>
                                <?php if($campos_listado[$i]=='estado'){?>
                                    <td>
                                        <?php
                                        $style = '';
                                        switch($row[$campos_listado[$i]]){
                                            case 'REGULARIZACIÓN':
                                                $style = 'btn btn-sm btn-danger btn-round';
                                                break;
                                            case 'ATENDIDO':
                                                $style = 'btn btn-sm btn-success btn-round';
                                                break;
                                            case 'RECIBIDO':
                                                $style = 'btn btn-sm btn-primary btn-round';
                                                break;
                                            case 'DERIVADO':
                                                $style = 'btn btn-sm btn-primary btn-round';
                                                break;
                                            case 'DEVUELTO':
                                                $style = 'btn btn-sm btn-warning btn-round';
                                                break;
                                            case 'FINALIZADO':
                                                $style = 'btn btn-sm btn-danger btn-round';
                                                break;
                                            case 'ANEXADO':
                                                $style = 'btn btn-sm btn-info btn-round';
                                                break;
                                        }
                                        echo '<button class="'.$style.'">'.$row[$campos_listado[$i]].'</button>';
                                        ?>
                                    </td>
                                <?php }elseif($campos_listado[$i]=='tipo_documento_derivado'){?>
                                    <td class="text-center">
                                        <?php
                                        $style = '';
                                        switch($row[$campos_listado[$i]]){
                                            case 'ORIGINAL':
                                                $style = 'btn btn-out btn-sm btn-success btn-square';
                                                break;
                                            case 'COPIA':
                                                $style = 'btn btn-out btn-sm btn-info btn-square';
                                                break;
                                        }
                                        echo '<button class="'.$style.'">'.$row[$campos_listado[$i]].'</button>';
                                        ?>
                                    </td>
                                <?php }elseif($campos_listado[$i]=='hr_anexada'){?>
                                    <td><a href="<?= base_url($controlador.'ver/'.$back.'/'.$row['id_hr_anexada']);?>" target="_blank"><?= $row[$campos_listado[$i]];?></a></td>
                                <?php }else{?>
                                    <td><?= $row[$campos_listado[$i]];?></td>
                                <?php }?>
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