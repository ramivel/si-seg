<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td bgcolor="<?= $color;?>" colspan="2" align="center"><b>DATOS DEL TRÁMITE</b></td>
    </tr>    
    <tr>
        <td width="138" bgcolor="<?= $color;?>" align="rigth"><b>Tipo de Trámite:</b></td>
        <td width="400">Contrato Administrativo Minero</td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="rigth"><b>N° de Trámite:</b></td>
        <td><?= $fila['correlativo'];?></td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="rigth"><b>Código Único:</b></td>
        <td><?= $fila['codigo_unico'];?></td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="rigth"><b>Denominación:</b></td>
        <td><?= $fila['denominacion'];?></td>
    </tr>
</table>
<br /><br />
<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td bgcolor="<?= $color;?>" colspan="2" align="center"><b>CÓDIGO DE SEGUIMIENTO</b></td>
    </tr>
    <tr>
        <td align="center" colspan="2"><h1><?= $codigo_seguimiento['codigo_seguimiento'];?></h1></td>
    </tr>
    <tr>
        <td width="138" bgcolor="<?= $color;?>" align="rigth"><b>Fecha:</b></td>
        <td width="400"><?= $codigo_seguimiento['fecha'];?></td>
    </tr>
</table>