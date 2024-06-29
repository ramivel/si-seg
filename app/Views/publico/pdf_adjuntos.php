<table border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td bgcolor="<?= $color;?>" align="center"><b>ADJUNTO(S)</b></td>
    </tr>
    <tr>
        <?php
        list($width, $height) = getimagesize(base_url($denuncia['fotografia_uno']));
        if($height>490)
            $height = 490;
        ?>
        <td align="center"><img src="<?=base_url($denuncia['fotografia_uno']);?>" alt="Fotografía 1" height="<?= $height.'px';?>"/></td>
    </tr>
    <tr>
        <td bgcolor="<?= $color;?>" align="center"><b>Fotografía 1:</b></td>
    </tr>
    <?php if($denuncia['fotografia_dos']){?>
        <tr>
            <?php
            list($width, $height) = getimagesize(base_url($denuncia['fotografia_dos']));
            if($height>490)
                $height = 490;
            ?>
            <td align="center"><img src="<?=base_url($denuncia['fotografia_dos']);?>" alt="Fotografía 2" height="<?= $height.'px';?>"/></td>
        </tr>
        <tr>
            <td bgcolor="<?= $color;?>" align="center"><b>Fotografía 2:</b></td>
        </tr>
    <?php }?>
    <?php if($denuncia['fotografia_tres']){?>
        <tr>
            <?php
            list($width, $height) = getimagesize(base_url($denuncia['fotografia_tres']));
            if($height>490)
                $height = 490;
            ?>
            <td align="center"><img src="<?=base_url($denuncia['fotografia_tres']);?>" alt="Fotografía 3" height="<?= $height.'px';?>"/></td>
        </tr>
        <tr>
            <td bgcolor="<?= $color;?>" align="center"><b>Fotografía 3:</b></td>
        </tr>
    <?php }?>
</table>