<?php    
    /*
    if(!isset($_SESSION["admin"])){
        header("location:ng403.php?err=nologin");
        die();
    }
    */
?>
                <ao-nodes></ao-nodes>
                <div class="l_title">Types</div>
                <div class="l_s">
                    <div ng-repeat="type in types" class="ml5 mt5 {{type.node.typevalid == 1 ? 'c000' : 'c666'}}">
                        <div>
                            <span class="cp" ng-bind="type.show ? '>' : '+'" ng-click="show(type.key)"></span>&nbsp;&nbsp;
                            <span ng-bind="type.node.typename" ng-show="!type.update" ng-click="showupdate(type.key)"></span>
                            <span ng-show="type.update">
                                <input type="text" ng-model="type.unode.typename">&nbsp;&nbsp;
                                <input type="button" value="~" ng-click="showupdate(type.key)">&nbsp;&nbsp;
                                <input type="button" value="+" ng-click="update(type.key)">&nbsp;&nbsp;
                                <input type="button" value="-" ng-click="drop(type.key)">&nbsp;&nbsp;
                                <input type="button" value="#" ng-click="shown(type.key)">
                            </span>
                        </div>
                        <div class="ml35" ng-show="type.show">
                            <div ng-repeat="tp in type.list">
                                <span ng-bind="tp.node.typename" ng-show="!tp.update" ng-click="showupdate(tp.key)"></span>
                                <span ng-show="tp.update">
                                    <input type="text" ng-model="tp.unode.typename">&nbsp;&nbsp;
                                    <input type="button" value="~" ng-click="showupdate(tp.key)">&nbsp;&nbsp;
                                    <input type="button" value="+" ng-click="update(tp.key)">&nbsp;&nbsp;
                                    <input type="button" value="-" ng-click="drop(tp.key)">&nbsp;&nbsp;
                                    <input type="button" value="#" ng-click="shown(tp.key)">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>