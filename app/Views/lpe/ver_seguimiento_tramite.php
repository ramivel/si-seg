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
                        <tr class="<?= (count($datos) == ($n+1) ? "table-warning" : "") ?>">
                            <?php for($i=0;$i<count($campos_listado);$i++){?>
                                <?php if($campos_listado[$i]=='estado'){?>
                                    <td>
                                        <?php
                                        $style = '';
                                        switch($row[$campos_listado[$i]]){
                                            case 'MIGRADO':
                                                $style = MIGRADO;
                                                break;
                                            case 'ATENDIDO':
                                                $style = ATENDIDO;
                                                break;
                                            case 'RECIBIDO':
                                                $style = RECIBIDO;
                                                break;
                                            case 'DERIVADO':
                                                $style = DERIVADO;
                                                break;
                                            case 'DEVUELTO':
                                                $style = DEVUELTO;
                                                break;
                                            case 'FINALIZADO':
                                                $style = FINALIZADO;
                                                break;
                                            case 'EN ESPERA':
                                                $style = EN_ESPERA;
                                                break;
                                        }
                                        echo '<button class="'.$style.'">'.$row[$campos_listado[$i]].'</button>';
                                        ?>
                                    </td>
                                <?php }elseif($campos_listado[$i]=='apm_presento'){?>
                                    <td>
                                        <?php
                                        if($row['recurso_jerarquico']=='t')
                                            echo 'RECURSO JERÁRQUICO<br>';
                                        if($row['recurso_revocatoria']=='t')
                                            echo 'RECURSO DE REVOCATORIA<br>';
                                        if($row['oposicion']=='t')
                                            echo 'OPOSICIÓN';
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