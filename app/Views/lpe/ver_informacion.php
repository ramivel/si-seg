<div class="table-responsive">
    <h4 class="sub-title mb-2">DATOS HOJA RUTA MADRE</h4>
    <table class="table table-bordered mb-0">
        <tbody>
            <tr>
                <th class="text-nowrap" width="180px" scope="row" rowspan="2">Remitente:</th>
                <td rowspan="2">
                    <?= $hr_remitente['nombre_completo'];?>
                    <br><b><?= $hr_remitente['cargo'];?></b>
                    <br><b><?= $hr_remitente['institucion'];?></b>
                </td>
                <th class="text-nowrap" scope="row">Fecha Mecanizada:</th>
                <td><?= $hoja_ruta['fecha_mecanizada'];?></td>
            </tr>
            <tr>
                <th class="text-nowrap" width="180px" scope="row">Cantidad de fojas:</th>
                <td><?= $hr_remitente['cantidad_fojas'];?></td>
            </tr>
            <tr>
                <th class="text-nowrap" width="180px" scope="row">Responsable Actual:</th>
                <td><?= $ultima_derivacion['usuario_responsable'];?></td>
                <th class="text-nowrap" width="180px" scope="row">Estado Actual del Trámite:</th>
                <td><?= $ultima_derivacion['estado_actual_tramite'];?></td>
            </tr>
            <?php if($ultima_derivacion['recurso_jerarquico']=='t' || $ultima_derivacion['recurso_revocatoria']=='t' || $ultima_derivacion['oposicion']=='t'){?>
                <tr>
                    <th class="text-nowrap" width="180px" scope="row">El APM Presento:</th>
                    <td colspan="3">
                        <?php
                        if($ultima_derivacion['recurso_jerarquico']=='t')
                            echo "RECURSO JERÁRQUICO ";
                        if($ultima_derivacion['recurso_revocatoria']=='t')
                            echo "RECURSO DE REVOCATORIA ";
                        if($ultima_derivacion['oposicion']=='t')
                            echo "OPOSICIÓN ";
                        ?>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</div>
<h4 class="sub-title mt-2 mb-2">Datos del Área Minera</h4>
<div class="table-responsive">
    <table class="table table-bordered mb-0">
        <tbody>
            <tr>
                <th class="text-nowrap" width="180px" scope="row">Código Único:</th>
                <td><?= $hoja_ruta['codigo_unico'];?></td>
                <th class="text-nowrap" width="180px" scope="row">Extensión:</th>
                <td><?= $hoja_ruta['extension'];?></td>
            </tr>
            <tr>
                <th class="text-nowrap" scope="row">Denominación:</th>
                <td><?= $hoja_ruta['denominacion'];?></td>
                <th class="text-nowrap" scope="row">Regional:</th>
                <td><?= $hoja_ruta['regional'];?></td>
            </tr>
            <tr>
                <th class="text-nowrap" scope="row">Representante Legal:</th>
                <td><?= $hoja_ruta['representante_legal'];?></td>
                <th class="text-nowrap" scope="row">Nacionalidad:</th>
                <td><?= $hoja_ruta['nacionalidad'];?></td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered mb-0">
        <tbody>
            <tr>
                <th class="text-nowrap" width="180px" scope="row">Solicitante:</th>
                <td><?= $hoja_ruta['titular'];?></td>
                <th class="text-nowrap" width="180px" scope="row">Clasificación APM:</th>
                <td><?= $hoja_ruta['clasificacion_titular'];?></td>
                <th class="text-nowrap" width="180px" scope="row">Teléfono(s):</th>
                <td><?= $ultima_derivacion['telefono_solicitante'];?></td>
            </tr>
            <tr>
                <th class="text-nowrap" width="180px" scope="row">Domicilio Legal:</th>
                <td colspan="5"><?= $ultima_derivacion['domicilio_legal'];?></td>
            </tr>
            <tr>
                <th class="text-nowrap" width="180px" scope="row">Domicilio Procesal:</th>
                <td colspan="5"><?= $ultima_derivacion['domicilio_procesal'];?></td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered mb-0">
        <tbody>
            <tr>
                <th class="text-nowrap" width="180px" scope="row">Departameto(s):</th>
                <td><?= $hoja_ruta['departamentos'];?></td>
                <th class="text-nowrap" width="180px" scope="row">Provincia(s):</th>
                <td><?= $hoja_ruta['provincias'];?></td>
                <th class="text-nowrap" width="180px" scope="row">Municipio(s):</th>
                <td><?= $hoja_ruta['municipios'];?></td>
            </tr>
        </tbody>
    </table>
    <?php if($hoja_ruta['area_protegida_adicional']){ ?>
        <table class="table table-bordered mb-0">
            <tbody>
                <tr>
                    <th class="text-nowrap" width="180px" scope="row">Restricción(es) Adicional(es):</th>
                    <td><?= $hoja_ruta['area_protegida_adicional'];?></td>
                </tr>
            </tbody>
        </table>
    <?php }?>
</div>
<?php if($registro_minero && $deuda){?>
    <h4 class="sub-title mt-3">Datos Catastro Minero</h4>
    <div class="table-responsive">
        <table class="table table-bordered mb-0">
            <tbody>
                <?php if($registro_minero){?>
                    <tr>
                        <th class="text-nowrap" width="350px" scope="row">Fecha de Registro Minero:</th>
                        <td><?= $registro_minero['fecha_inscripcion_minera'];?></td>
                        <th class="text-nowrap" width="180px" scope="row">Fecha de Vencimiento:</th>
                        <td><?= $registro_minero['fecha_vencimiento'];?></td>
                    </tr>
                    <tr>
                        <th class="text-nowrap" scope="row">Número de Gaceta de Publicación de Ley:</th>
                        <td><?= $registro_minero['gaceta_numero'];?></td>
                        <th class="text-nowrap" scope="row">Fecha de Publicación de Gaceta:</th>
                        <td><?= $registro_minero['gaceta_fecha'];?></td>
                    </tr>
                <?php }?>
                <?php if($deuda){?>
                    <tr>
                        <th class="text-nowrap" width="350px" scope="row">Deuda de Patente Minera (Años):</th>
                        <td><?= $deuda['anio_deuda'];?></td>
                        <th class="text-nowrap" width="180px" scope="row">Total (Bs.):</th>
                        <td><?= $deuda['deuda_total'];?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
<?php }?>