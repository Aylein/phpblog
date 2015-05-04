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
                    <div ng-repeat="type in types" class="ml5 mt5 {{type.typevalid == 1 ? 'c000' : 'c666'}}">
                        <div>
                            <span class="cp" ng-bind="type.show == 1 ? '>' : '+'" ng-click="show(type.key)"></span>
                            &nbsp;&nbsp;<span ng-bind="type.typename" ao-input="" key="{{type.key}}"></span>
                        </div>
                        <div class="ml35" ng-show="type.show == 1">
                            <div ng-repeat="tp in type.list" ng-bind="tp.typename"></div>
                        </div>
                    </div>
                </div>