<?php
    $atype = Type::GetTypes(-1, 1, 1, 7);
    $counta = count($atype->list);
?>
        <div class="myname">
            <div class="master"><img src="/images/master.jpg"/>&nbsp;&nbsp;</div>
            <div class="master lh52">The 4th AyleinOter@What a loser</div>
            <div class="menu lh52">
                <a href="/index.php" class="<?=!isset($typeid) || $typeid == 0 ? "c000" : "" ?>">全部</a>
            <?php if($counta > 0): foreach($atype->list as $key => $value): ?>
                <a href="/index.php?type=<?=$value->typeid ?>" class="<?=isset($typeid) && $typeid > 0 && $value->typeid == $typeid ? "c000" : "" ?>"><?=$value->typename ?></a>
            <?php endforeach; endif; ?>
            </div>
            <div class="clear"></div>
        </div>
