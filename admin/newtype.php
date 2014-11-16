<?php
    include("../lib/type.php");
    $id = 0;
    if(isset($_GET["id"]) && is_int($_GET["id"])) $id = (int)$_GET["id"];
    $type = null;
    if($id > 0) $type = Type::GetType($id);
    $update = $type == null ? false : true;
    $res = Type::GetTypes();
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
            <form method="post" action="/var/newtype.php">
                <input type="hidden" name="typeid" id="typeid" value="<?=$update ? $type->typeid : "0" ?>"/>
                <div class="rh_title">添加类别</div>
                <div class="rh_item">
                    <div class="form_title">关联类型：</div>
                    <div class="form_value">
                        <select name="typepid" id="typepid" class="">
                            <option value="0">主要分类</option>
                            <?php if(count($res->list) > 0): ?>
                            <?php for ($i=0, $z = count($res->list); $i < $z ; $i++): ?>
                            <option value="<?=$res->list[$i]->typeid ?>" <?=$update && $type->typeid == $res->list[$i]->typeid ? "selected=\"sekected\"" : "" ?>><?=$res->list[$i]->typename ?></option>
                            <?php endfor ?>
                            <?php endif ?>
                        </select>
                    </div>
                    <div class="form_title">顶部显示：</div>
                    <div class="form_value">
                        <select name="typeshow" id="typeshow" class="">
                            <option value="1" <?=$update && $type->typeshow == 1 ? "selected=\"selected\"" : "" ?>>显示</option>
                            <option value="0" <?=$update && $type->typeshow == 0 ? "selected=\"selected\"" : "" ?>>不显示</option>
                        </select>
                    </div>
                    <div class="form_hid" id="typepid_hid"></div>
                    <div class="clear"></div>
                </div>
                <div class="rh_item">
                    <div class="form_title">类型名称：</div>
                    <div class="form_value">
                        <input type="text" name="typename" id="typename" class="" value="<?=$update ? $type->typename : "" ?>"/>
                    </div>
                    <div class="form_hid" id="typename_hid"></div>
                    <div class="clear"></div>
                </div>
                <div class="rh_item">
                    <div class="form_title">排列顺序：</div>
                    <div class="form_value">
                        <input type="text" name="typesort" id="typesort" class="w80" value="<?=$update ? $type->typesort : "0" ?>"/>
                    </div>
                    <div class="form_title">是否可用：</div>
                    <div class="form_value">
                        <select name="typevalid" id="typevalid" class="">
                            <option value="1" <?=$update && $type->typevalid == 1 ? "selected=\"selected\"" : "" ?>>可用</option>
                            <option value="0" <?=$update && $type->typevalid == 0 ? "selected=\"selected\"" : "" ?>>不可用</option>
                        </select>
                    </div>
                    <div class="form_hid" id="typesort_hid"></div>
                    <div class="clear"></div>
                </div>
                <div class="rh_item">
                    <div class="form_title">&nbsp;</div>
                    <div class="form_value">
                        <input type="submit" id="yes_bt" class="w80"/>
                        <input type="reset" id="reset_bt" class="w80"/>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
        <div class="clear"></div>
        <br />
    </section>
    <section class="footer"><?php require("../require/footer.php"); ?></section>
</body>
<script src="/scripts/jquery-1.11.1.min.js"></script>
<script src="/scripts/form.js"></script>
</html>