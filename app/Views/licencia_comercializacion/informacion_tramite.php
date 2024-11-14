<h4 class="sub-title mb-2">DATOS HOJA DE RUTA</h4>
<table class="table table-bordered">
    <tbody>
        <tr>
            <th class="text-nowrap text-right" width="200px" scope="row">Responsable del trámite:</th>
            <td><?= $fila['responsable'];?></td>
            <th class="text-nowrap text-right" width="200px" scope="row">Estado actual del trámite:</th>
            <td><?= $fila['estado_tramite'];?></td>
            <th class="text-nowrap text-right" width="100px" scope="row">Fecha H.R.:</th>
            <td><?= $fila['fecha_hoja_ruta'];?></td>
        </tr>
    </tbody>
</table>
<h4 class="sub-title mb-2">DATOS DOCUMENTO EXTERNO</h4>
<table class="table table-bordered">
    <tbody>
        <tr>
            <th class="text-nowrap text-right" width="200px" scope="row">CITE:</th>
            <td><?= $fila['cite'];?></td>
            <th class="text-nowrap text-right" width="100px" scope="row">FECHA:</th>
            <td><?= $fila['fecha_cite'];?></td>
            <th class="text-nowrap text-right" width="200px" scope="row">FOJAS:</th>
            <td><?= $fila['fojas'];?></td>
            <th class="text-nowrap text-right" width="100px" scope="row">ADJUNTOS:</th>
            <td><?= $fila['adjuntos'];?></td>
        </tr>
        <tr>
            <th class="text-nowrap text-right" width="200px" scope="row">REMITENTE:</th>
            <td colspan="7"><?= $fila['remitente'];?></td>
        </tr>
        <tr>
            <th class="text-nowrap text-right" width="200px" scope="row">REFERENCIA:</th>
            <td colspan="3"><?= $fila['referencia'];?></td>
            <th class="text-nowrap text-right" width="200px" scope="row">DOCUMENTO DIGITAL:</th>
            <td colspan="3"><a href="<?= base_url($fila['doc_digital']);?>" target="_blank" title="Ver Documento"><i class="feather icon-file"></i> Ver Documento</a></td>
        </tr>
    </tbody>
</table>