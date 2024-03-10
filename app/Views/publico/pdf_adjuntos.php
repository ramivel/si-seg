<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td width="538" bgcolor="<?= $color;?>" align="center"><b>ADJUNTO(S)</b></td>
    </tr>    
    <tr>
        <td><img src="<?=base_url($denuncia['fotografia_uno']);?>" alt="Fotografía 1"/></td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="center"><b>Fotografía 1:</b></td>
    </tr>
    <?php if($denuncia['fotografia_dos']){?>        
        <tr>
            <td><img src="<?=base_url($denuncia['fotografia_dos']);?>" alt="Fotografía 1"/></td>
        </tr>
        <tr>
            <td bgcolor="<?= $color;?>" align="center"><b>Fotografía 2:</b></td>
        </tr>
    <?php }?>
    <?php if($denuncia['fotografia_tres']){?>        
        <tr>
            <td><img src="<?=base_url($denuncia['fotografia_tres']);?>" alt="Fotografía 1"/></td>
        </tr>
        <tr>
            <td bgcolor="<?= $color;?>" align="center"><b>Fotografía 3:</b></td>
        </tr>
    <?php }?>
</table>