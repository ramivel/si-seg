<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td width="148" bgcolor="<?= $color;?>" align="right"><b>Fecha y hora:</b></td>
        <td width="390"><?= $denuncia['fecha_denuncia'];?></td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="right"><b>Tipo de Denuncia:</b></td>
        <td><?= $tipo_denuncias[$denuncia['fk_tipo_denuncia']];?></td>
    </tr>
</table>
<br /><br />
<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td bgcolor="<?= $color;?>" colspan="2" align="center"><b>ORIGEN</b></td>
    </tr>
    <tr>
        <td width="148" bgcolor="<?= $color;?>" align="rigth"><b>Tipo de Origen:</b></td>
        <td width="390"><?= $denuncia['origen_oficio'];?></td>
    </tr>
    <?php if($denuncia['enlace']){?>
    <tr>
        <td bgcolor="<?= $color;?>" align="rigth"><b>Enlace:</b></td>
        <td><?= $denuncia['enlace'];?></td>
    </tr>
    <?php }?>
    <tr>
        <td bgcolor="<?= $color;?>" align="rigth"><b>Breve Descripción:</b></td>
        <td><?= $denuncia['descripcion_oficio'];?></td>
    </tr>
</table>
<br /><br />
<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td bgcolor="<?= $color;?>" colspan="2" align="center"><b>DESCRIPCIÓN DE LA EXPLOTACIÓN ILEGAL</b></td>
    </tr>
    <tr>
        <td width="148" bgcolor="<?= $color;?>" align="rigth"><b>Departamento:</b></td>
        <td width="390"><?= $denuncia['departamento'];?></td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="rigth"><b>Provincia:</b></td>
        <td><?= $denuncia['provincia'];?></td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="rigth"><b>Municipio:</b></td>
        <td><?= $denuncia['municipio'];?></td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="rigth"><b>Comunidad/Localidad:</b></td>
        <td><?= $denuncia['comunidad_localidad'];?></td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="rigth"><b>Descripción del lugar o punto de referencia:</b></td>
        <td><?= $denuncia['descripcion_lugar'];?></td>
    </tr>
    <?php if($denuncia['autores']){?>
        <tr>
            <td bgcolor="<?= $color;?>" align="rigth"><b>Nombre(s) del posible(s) autor(es):</b></td>
            <td><?= $denuncia['autores'];?></td>
        </tr>
    <?php }?>
    <?php if($denuncia['persona_juridica']){?>
        <tr>
            <td bgcolor="<?= $color;?>" align="rigth"><b>Nombre de la persona(s) jurídica(s) (empresa, cooperativa u otro) que este vinculado(s) a la actividad:</b></td>
            <td><?= $denuncia['persona_juridica'];?></td>
        </tr>
    <?php }?>
    <?php if($denuncia['descripcion_materiales']){?>
        <tr>
            <td bgcolor="<?= $color;?>" align="rigth"><b>Descripción de la maquinaria(s) u objeto(s) utilizado(s) en la actividad:</b></td>
            <td><?= $denuncia['descripcion_materiales'];?></td>
        </tr>
    <?php }?>
    <?php if($denuncia['areas_denunciadas']){?>
        <tr>
            <td bgcolor="<?= $color;?>" align="rigth"><b>Área(s) denunciada(s) que se encuentran en trámite en la AJAM:</b></td>
            <td><?= $denuncia['areas_denunciadas'];?></td>
        </tr>
    <?php }?>
</table>
<?php if(isset($coordenadas) && count($coordenadas)>0){ ?>
    <br /><br />
    <table border="1" cellpadding="3" cellspacing="0">
        <tr>
            <td bgcolor="<?= $color;?>" colspan="3" align="center"><b>COORDENADA(S) GEOGRÁFICA(S)</b></td>
        </tr>
        <tr>
            <td width="178" bgcolor="<?= $color;?>" align="center"><b>N°</b></td>
            <td width="180" bgcolor="<?= $color;?>" align="center"><b>LONGITUD</b></td>
            <td width="180" bgcolor="<?= $color;?>" align="center"><b>LATITUD</b></td>
        </tr>
        <?php foreach($coordenadas as $n=>$coordenada){?>
            <tr>
                <td align="center"><?= $n+1;?></td>
                <td align="center"><?= $coordenada['longitud'];?></td>
                <td align="center"><?= $coordenada['latitud'];?></td>
            </tr>
        <?php }?>
    </table>
<?php }?>