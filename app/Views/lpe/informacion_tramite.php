<table class="table table-bordered mb-0">
    <tbody>
        <tr>
        <th class="text-nowrap text-right" width="200px" scope="row">Código Único:</th>
            <td><?= $fila['codigo_unico'];?></td>
            <th class="text-nowrap text-right" width="200px" scope="row">Denominación:</th>
            <td><?= $fila['denominacion'];?></td>
            <th class="text-nowrap text-right" width="200px" scope="row">Fecha Mecanizada:</th>
            <td><?= $fila['fecha_mecanizada'];?></td>
        </tr>
    </tbody>
</table>
<table class="table table-bordered">
    <tbody>
        <tr>
        <th class="text-nowrap text-right" width="200px" scope="row">Responsable del Trámite:</th>
            <td><?= $fila['responsable'];?></td>
            <th class="text-nowrap text-right" width="200px" scope="row">Estado actual del Trámite:</th>
            <td><?= $fila['estado_tramite'];?></td>
        </tr>
    </tbody>
</table>