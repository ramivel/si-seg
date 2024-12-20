<div class="table-responsive">
    <table id="tabla-verificacion" class="table table-xs table-hover">
        <thead>
            <tr>
                <th class="nosort"></th>
                <?php for($i=0;$i<count($cabecera_listado);$i++){?>
                    <th class="text-center"><?php echo $cabecera_listado[$i];?></th>
                <?php }?>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($datos) && count($datos)>0){?>
                <?php foreach ($datos as $fila){?>
                    <tr>
                        <td class="text-center">
                            <?php echo anchor($controlador.'ver/4/'.$fila['id_hoja_ruta'], '<i class="fa fa-eye"></i> Ver',array('class' =>'btn btn-sm btn-info', "title"=>"Ver la Denuncia", "target"=>"_blank"));?>
                            <br>
                            <button class="btn btn-sm btn-danger mt-2" onclick="agregar_reiterativa(<?= $fila['id_hoja_ruta'];?>,'<?= $fila['correlativo_hoja_ruta'];?>');" title="Reiterativa"><i class="fa fa-retweet"></i> Reiterativa</button>
                        </td>
                        <?php for($i=0;$i<count($campos_listado);$i++){?>
                            <td class="text-center" >
                                <?php
                                    if($campos_listado[$i]=='ultimo_estado'){
                                        $style = '';
                                        switch($fila[$campos_listado[$i]]){
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
                                            case 'FINALIZADO':
                                                $style = 'btn btn-sm btn-success btn-round';
                                                break;
                                        }
                                        echo '<button class="'.$style.'">'.$fila[$campos_listado[$i]].'</button>';
                                    }elseif($campos_listado[$i]=='denunciante' || $campos_listado[$i]=='remitente' || $campos_listado[$i]=='destinatario' || $campos_listado[$i]=='responsable' || $campos_listado[$i]=='areas_mineras'){
                                        echo str_replace(' || ', '<br>', $fila[$campos_listado[$i]]);
                                    }else{
                                        echo $fila[$campos_listado[$i]];
                                    }
                                ?>
                            </td>
                        <?php }?>
                    </tr>
                <?php }?>
            <?php }else{?>
                <tr>
                    <td colspan="<?= count($cabecera_listado)+1;?>">No se han encontrado datos.</td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</div>