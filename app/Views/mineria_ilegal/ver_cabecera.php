<ul class="nav nav-tabs tabs">
    <li class="nav-item">
        <a class="nav-link <?= $activo=='SEGUIMIENTO TRÁMITE' ? 'active':'';?>" href="<?= $url_seguimiento_tramite;?>#tabla_datos" title="Ver la Información"><strong>SEGUIMIENTO TRÁMITE</strong></a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $activo=='CORRESPONDENCIA EXTERNA' ? 'active':'';?>" href="<?= $url_correspondencia_externa;?>#tabla_datos" title="Ver la Información"><strong>CORRESPONDENCIA EXTERNA</strong></a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $activo=='DOCUMENTO(S) GENERADO(S)' ? 'active':'';?>" href="<?= $url_documentos_generados;?>#tabla_datos" title="Ver la Información"><strong>DOCUMENTO(S) GENERADO(S)</strong></a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $activo=='HOJA(S) DE RUTA ANEXADA(S)' ? 'active':'';?>" href="<?= $url_hojas_ruta_anexadas;?>#tabla_datos" title="Ver la Información"><strong>HOJA(S) DE RUTA SINCOBOL ANEXADA(S)</strong></a>
    </li>
</ul>