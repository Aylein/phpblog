<?php    
    /*
    if(!isset($_SESSION["admin"])){
        header("location:ng403.php?err=nologin");
        die();
    }
    */
?>
            <div class="pano_left" ng-controller="adminController">
                <div class="l_title">Admin Managemant</div>
                <div class="l_s">
                    <div class="l_urlpano {{os.width}}" ng-repeat="os in nodes">
                        <div>
                            <a href="{{os.url == '' ? 'javascript:void(0);' : os.url}}" ng-bind="os.name" class="{{os.cur == 1 ? 'c000' :'c666'}}"></a>
                        </div>
                        <div class="mt15">
                            <ul>
                                <li ng-repeat="oa in os.list">
                                    <a href="{{oa.url == '' ? 'javascript:void(0);' : oa.url}}" ng-bind="oa.name" class="{{oa.cur == 1 ? 'c000' :'c666'}}">sss</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>