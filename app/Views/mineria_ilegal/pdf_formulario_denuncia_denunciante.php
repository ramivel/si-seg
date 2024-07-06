<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td width="150" bgcolor="<?= $color;?>" align="right"><b>Fecha y hora:</b></td>
        <td width="405"><?= $denuncia['fecha_denuncia'];?></td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="right"><b>Tipo de Denuncia:</b></td>
        <td><?= $tipo_denuncias[$denuncia['fk_tipo_denuncia']];?></td>
    </tr>
</table>
<br /><br />
<?php if(isset($hojas_ruta_sincobol)){?>
    <table border="1" cellpadding="3" cellspacing="0">
        <tr>
            <td bgcolor="<?= $color;?>" colspan="2" align="center"><b>ORIGEN</b></td>
        </tr>
        <tr>
            <td width="150" bgcolor="<?= $color;?>" align="rigth"><b>Hoja(s) de Ruta(s):</b></td>
            <td width="405"><?= $hojas_ruta_sincobol;?></td>
        </tr>
    </table>
    <br /><br />
<?php }?>
<?php if(isset($denunciante)){?>
    <table border="1" cellpadding="3" cellspacing="0">
        <tr>
            <td bgcolor="<?= $color;?>" colspan="2" align="center"><b>DATOS DEL DENUNCIANTE</b></td>
        </tr>
        <tr>
            <td width="150" bgcolor="<?= $color;?>" align="rigth"><b>Nombre(s):</b></td>
            <td width="405"><?= $denunciante['nombres'];?></td>
        </tr>
        <tr>
            <td bgcolor="<?= $color;?>" align="rigth"><b>Apellido(s):</b></td>
            <td><?= $denunciante['apellidos'];?></td>
        </tr>
        <tr>
            <td bgcolor="<?= $color;?>" align="rigth"><b>Documento de Identidad:</b></td>
            <td><?= $denunciante['documento_identidad'].' '.$denunciante['expedido'];?></td>
        </tr>
        <tr>
            <td bgcolor="<?= $color;?>" align="rigth"><b>Celular:</b></td>
            <td><?= $denunciante['telefonos'];?></td>
        </tr>
        <?php if($denunciante['email']){?>
            <tr>
                <td bgcolor="<?= $color;?>" align="rigth"><b>E-Mail:</b></td>
                <td><?= $denunciante['email'];?></td>
            </tr>
        <?php }?>
        <tr>
            <td bgcolor="<?= $color;?>" align="rigth"><b>Dirección:</b></td>
            <td><?= $denunciante['direccion'];?></td>
        </tr>
    </table>
    <br /><br />
<?php }?>
<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td bgcolor="<?= $color;?>" colspan="2" align="center"><b>DESCRIPCIÓN DE LA ACTIVIDAD MINERA</b></td>
    </tr>
    <tr>
        <td width="150" bgcolor="<?= $color;?>" align="rigth"><b>Departamento:</b></td>
        <td width="405"><?= $denuncia['departamento'];?></td>
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
            <td bgcolor="<?= $color;?>" align="rigth"><b>Nombre de la persona jurídica (empresa, cooperativa u otro) que este vinculado a la actividad:</b></td>
            <td><?= $denuncia['persona_juridica'];?></td>
        </tr>
    <?php }?>
    <?php if($denuncia['descripcion_materiales']){?>
        <tr>
            <td bgcolor="<?= $color;?>" align="rigth"><b>Descripción de la maquinaria u objeto(s) utilizado(s) en la actividad:</b></td>
            <td><?= $denuncia['descripcion_materiales'];?></td>
        </tr>
    <?php }?>
    <?php if($denuncia['areas_denunciadas']){?>
        <tr>
            <td bgcolor="<?= $color;?>" align="rigth"><b>Nombre del área o correlativo de la solicitud en caso de estar en trámite en la AJAM:</b></td>
            <td><?= $denuncia['areas_denunciadas'];?></td>
        </tr>
    <?php }?>
</table>
<?php if(isset($areas_mineras) && count($areas_mineras)>0){ ?>
    <br /><br />
    <table border="1" cellpadding="3" cellspacing="0">
        <tr>
            <td bgcolor="<?= $color;?>" colspan="10" align="center"><b>ÁREA(S) MINERA(S) IDENTIFICADA(S)</b></td>
        </tr>
        <tr>
            <td bgcolor="<?= $color;?>" align="center"><b>Nº</b></td>
            <td bgcolor="<?= $color;?>" align="center"><b>CÓDIGO ÚNICO</b></td>
            <td bgcolor="<?= $color;?>" align="center"><b>ÁREA MINERA</b></td>
            <td bgcolor="<?= $color;?>" align="center"><b>TIPO ÁREA</b></td>
            <td bgcolor="<?= $color;?>" align="center"><b>EXTENSIÓN</b></td>
            <td bgcolor="<?= $color;?>" align="center"><b>TITULAR</b></td>
            <td bgcolor="<?= $color;?>" align="center"><b>CLASIFICACIÓN</b></td>
            <td bgcolor="<?= $color;?>" align="center"><b>DEPARTAMENTO(S)</b></td>
            <td bgcolor="<?= $color;?>" align="center"><b>PROVINCIA(S)</b></td>
            <td bgcolor="<?= $color;?>" align="center"><b>MUNICIPIO(S)</b></td>
        </tr>
        <?php foreach($areas_mineras as $n=>$area_minera){?>
            <tr>
                <td align="center"><?= $n+1;?></td>
                <td align="center"><?= $area_minera['codigo_unico'];?></td>
                <td align="center"><?= $area_minera['area_minera'];?></td>
                <td align="center"><?= $area_minera['tipo_area_minera'];?></td>
                <td align="center"><?= $area_minera['extension'];?></td>
                <td align="center"><?= $area_minera['titular'];?></td>
                <td align="center"><?= $area_minera['clasificacion'];?></td>
                <td align="center"><?= $area_minera['departamentos'];?></td>
                <td align="center"><?= $area_minera['provincias'];?></td>
                <td align="center"><?= $area_minera['municipios'];?></td>
            </tr>
        <?php }?>
    </table>
<?php }?>
<?php if(isset($coordenadas) && count($coordenadas)>0){ ?>
    <br /><br />
    <table border="1" cellpadding="3" cellspacing="0">
        <tr>
            <td bgcolor="<?= $color;?>" colspan="3" align="center"><b>COORDENADA(S) GEOGRÁFICA(S)</b></td>
        </tr>
        <tr>
            <td width="178" bgcolor="<?= $color;?>" align="center"><b>N°</b></td>
            <td width="189" bgcolor="<?= $color;?>" align="center"><b>LONGITUD</b></td>
            <td width="189" bgcolor="<?= $color;?>" align="center"><b>LATITUD</b></td>
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