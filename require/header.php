<?php
    $atype = Type::GetTypes(-1, 1, 1, 7);
    $counta = count($atype->list);
?>
        <div class="myname">
            <div class="master"><img src="/images/master.jpg"/>&nbsp;&nbsp;</div>
            <div class="master lh52">The 4th AyleinOter@What a loser</div>
            <div class="menu lh52">
                <a href="/index.php" class="<?=!isset($typeid) || $typeid == 0 ? "c000" : "" ?>">全部</a>
            <?php if($counta > 0): for($i = 0; $i < $counta; $i++): ?>
                <a href="/index.php?type=<?=$atype->list[$i]->typeid ?>" class="<?=isset($typeid) && $typeid > 0 && $atype->list[$i]->typeid == $typeid ? "c000" : "" ?>"><?=$atype->list[$i]->typename ?></a>
            <?php endfor; endif; ?>
            </div>
            <div class="clear"></div>
        </div>
