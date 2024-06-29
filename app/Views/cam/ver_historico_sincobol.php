<div id="tabla_datos" class="col-sm-12">
    <?= $tabs;?>
    <!-- Tab panes -->
    <div class="tab-content tabs card-block" style="background-color:#fff; border-left: 1px solid #ddd; border-right: 1px solid #ddd; border-bottom: 1px solid #ddd;">
        <div class="tab-pane active">
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
                        <tr>
                            <?php for($i=0;$i<count($campos_listado);$i++){?>
                                <?php if($campos_listado[$i]=='estado'){?>
                                    <td>
                                        <?php
                                        $style = '';
                                        switch($row[$campos_listado[$i]]){
                                            case 'ENVIADO':
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
                                            case 'EN ESPERA':
                                                $style = 'btn btn-sm btn-info btn-round';
                                                break;
                                        }
                                        echo '<button class="'.$style.'">'.$row[$campos_listado[$i]].'</button>';
                                        ?>
                                    </td>
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