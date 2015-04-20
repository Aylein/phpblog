<?php
    $atype = Type::GetTypes(-1, 1, 1, 7);
    $counta = count($atype->list);
    $self = strpos($_SERVER['PHP_SELF'], "says.php");
?>
        <div class="myname">
            <div class="master"><img src="/images/master.jpg"/>&nbsp;&nbsp;</div>
            <div class="master lh52">The 4th AyleinOter@What a loser</div>
            <div class="menu lh52">
                <a href="/index.php" class="<?=!$self && (!isset($typeid) || $typeid == 0) ? "c000" : "" ?>">全部</a>
            <?php if($counta > 0): foreach($atype->list as $key => $value): ?>
                <a href="/index.php?type=<?=$value->typeid ?>" class="<?=!$self && (isset($typeid) && $typeid) > 0 && $value->typeid == $typeid ? "c000" : "" ?>"><?=$value->typename ?></a>
            <?php endforeach; endif; ?>
                <a href="/says.php" class="<?=$self ? "c000" : "" ?>">Says</a>
            </div>
            <div class="clear"></div>
        </div>
