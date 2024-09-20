<table border="1" cellpadding="1" cellspacing="0">
    <tr>
        <td bgcolor="<?= $color;?>" colspan="<?= $n_campos;?>" align="center"><b>ÁREA(S) MINERA(S) REFERENCIAL</b></td>
    </tr>
    <tr>
        <?php foreach($campos_listar as $i=>$row){?>
            <td bgcolor="<?= $color;?>" align="center"><b><?= $row;?></b></td>
        <?php }?>
    </tr>
    <?php foreach($areas_mineras as $n=>$area_minera){?>
        <tr>
            <?php foreach($campos_reales as $i=>$row){?>
                <td bgcolor="<?= $color;?>" align="center"><?= $area_minera[$row];?></td>
            <?php }?>
        </tr>
    <?php }?>
</table>