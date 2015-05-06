<?php    
    /*
    if(!isset($_SESSION["admin"])){
        header("location:ng403.php?err=nologin");
        die();
    }
    */
?>
                <ao-nodes></ao-nodes>
                <div class="l_title">Types <a href="javascript: void(0);" ng-click="flush()">o(>_<)o</a></div>
                <div class="l_s">
                    <div class="ml5">
                        <span class="cp">></span>&nbsp;&nbsp;
                        <input type="text" ng-model="newtype.typename">&nbsp;&nbsp;
                        <select ng-model="newtype.typepid" ng-options="v.node.typeid as v.node.typename for (k, v) in types">
                            <option value="">主要</option>
                        </select>&nbsp;&nbsp;
                        <input type="button" class="a_button {{newtype.typeshow == 1 ? 'c000' : 'cBBB'}}" value="#" ng-click="shown(newtype.key)">&nbsp;&nbsp;
                        <input type="button" class="a_button" value="~" ng-click="showupdate(newtype.key)">&nbsp;&nbsp;
                        <input type="button" class="a_button" value="+" ng-click="update(newtype.key)">
                    </div>
                </div>
                <div class="l_s">
                    <div ng-repeat="type in types" class="ml5 mt5 {{type.node.typevalid == 1 ? 'c000' : 'cBBB'}}">
                        <div>
                            <span class="cp" ng-bind="type.show ? '>' : '+'" ng-click="show(type.key)"></span>&nbsp;&nbsp;
                            <span ng-bind="(type.node.typeshow == 1 ? '#&nbsp;' : '&nbsp;') + type.node.typename" ng-show="!type.update" ng-click="showupdate(type.key)"></span>
                            <span ng-show="type.update">
                                <input type="text" ng-model="type.unode.typename">&nbsp;&nbsp;
                                <select ng-model="type.unode.typepid" ng-options="v.node.typeid as v.node.typename for (k, v) in types">
                                    <option value="">主要</option>
                                </select>&nbsp;&nbsp;
                                <input type="button" class="a_button {{type.node.typeshow == 1 ? 'c000' : 'cBBB'}}" value="#" ng-click="shown(type.key)">&nbsp;&nbsp;
                                <input type="button" class="a_button" value="~" ng-click="showupdate(type.key)">&nbsp;&nbsp;
                                <input type="button" class="a_button" value="-" ng-click="drop(type.key)">&nbsp;&nbsp;
                                <input type="button" class="a_button" value="+" ng-click="update(type.key)">
                            </span>
                        </div>
                        <div class="ml35" ng-show="type.show">
                            <div ng-repeat="tp in type.list" class="{{tp.node.typevalid == 1 && type.node.typevalid ? 'c000' : 'cBBB'}}">
                                <span ng-bind="(tp.node.typeshow == 1 ? '#&nbsp;' : '&nbsp;') + tp.node.typename" ng-show="!tp.update" ng-click="showupdate(tp.key)"></span>
                                <span ng-show="tp.update">
                                    <input type="text" ng-model="tp.unode.typename">&nbsp;&nbsp;
                                    <select ng-model="tp.unode.typepid" ng-options="v.node.typeid as v.node.typename for (k, v) in types">
                                        <option value="">主要</option>
                                    </select>&nbsp;&nbsp;
                                    <input type="button" class="a_button {{tp.node.typeshow == 1 ? 'c000' : 'cBBB'}}" value="#" ng-click="shown(tp.key)">&nbsp;&nbsp;
                                    <input type="button" class="a_button" value="~" ng-click="showupdate(tp.key)">&nbsp;&nbsp;
                                    <input type="button" class="a_button" value="-" ng-click="drop(tp.key)">&nbsp;&nbsp;
                                    <input type="button" class="a_button" value="+" ng-click="update(tp.key)">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>