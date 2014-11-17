<?php
    include("../lib/Type.php");
    $atype = Type::GetTypes(0);
    $ctype = Type::GetTypes(-2);
    $counta = count($atype->list);
    $countc = count($ctype->list);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>admin types</title>
    <link rel="stylesheet" href="/styles/style.css" />
    <link rel="stylesheet" href="/styles/admin_style.css" />
</head>
<body>
    <section class="header"><?php require("../require/header.php"); ?></section>
    <section class="topimg"></section>
    <section class="bodypano">
        <div class="left_hand"><?php require("../require/admin_menu.php"); ?></div>
        <div class="right_hand">
            <div class="rh_title">所有类别</div>
            <?php if($counta > 0): ?>
            <?php for($i = 0; $i < $counta; $i++): ?>
            <div class="rh_item <?=$atype->list[$i]->typevalid == 1 ? "c666" : "cCCC" ?>">
                <a href="/admin/newtype.php?id=<?=$atype->list[$i]->typeid ?>" class="<?=$atype->list[$i]->typevalid == 1 ? "c666" : "cCCC" ?>"><?=$atype->list[$i]->typename ?></a> /
                <span><?=$atype->list[$i]->typeid ?></span> /
                <span><?=$atype->list[$i]->typeshow == 1 ? "show" : "hide" ?></span>
            </div>
            <?php for($j = 0; $j < $countc; $j++): ?>
            <?php if($atype->list[$i]->typeid == $ctype->list[$j]->typepid): ?>
            <div class="rh_item_c  <?=$ctype->list[$j]->typevalid == 1 ? "c666" : "cCCC"?>"> 
                <a href="/admin/newtype.php?id=<?=$ctype->list[$j]->typeid ?>"  class="<?=$ctype->list[$j]->typevalid == 1 ? "c666" : "cCCC" ?>"><?=$ctype->list[$j]->typename ?></a> /
                <span><?=$ctype->list[$j]->typeid ?></span> /
                <span><?=$ctype->list[$j]->typeshow == 1 ? "show" : "hide" ?></span>
            </div>
            <?php endif; ?>
            <?php endfor; ?>
            <?php endfor; ?>
            <?php else: ?>
            <div>目前尚未有类型</div>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
        <br />
    </section>
    <section class="footer"><?php require("../require/footer.php"); ?></section>
</body>
</html>