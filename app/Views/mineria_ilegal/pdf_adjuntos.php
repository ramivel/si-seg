<?php if(isset($adjuntos) && count($adjuntos)>0){ ?>
    <table border="1" cellpadding="3" cellspacing="0">
        <tr>
            <td width="538" bgcolor="<?= $color;?>" align="center"><b>ADJUNTO(S)</b></td>
        </tr>
        <?php foreach($adjuntos as $adjunto){?>
            <?php if($adjunto['tipo'] == 'IMAGEN'){?>
                <tr><td><img src="<?=base_url($adjunto['adjunto']);?>" alt="<?= $adjunto['nombre'];?>" /></td></tr>
            <?php }else{?>
                <tr><td><?= $adjunto['adjunto'];?></td></tr>
            <?php }?>
            <tr><td bgcolor="<?= $color;?>" align="center"><b><?= $adjunto['nombre'];?></b></td></tr>
        <?php }?>
    </table>
<?php }?>