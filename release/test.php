<!DOCTYPE html>
<html ng-app="app">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>AyleinOter IV test</title>
        <link rel="stylesheet" href="styles/style.min.css" />
        <link rel="stylesheet" href="styles/index.min.css" />
        <link rel="stylesheet" href="styles/admin.min.css" />
        <link rel="stylesheet" href="styles/extra.min.css" />
    </head>
    <body>          
        <div ng-controller="saysController">
            <div class="l_title">This is the Says page</div>
            <div class="says" style="margin-top: 15px;">
                <div class="says_left bc666"></div>
                <div class="says_right">
                    <div>
                        <div class="say_title">填充 :</div>
                        <div class="say_item"><ao-cs model="comment" /></div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <br />
            <div class="says_pano" ng-repeat="com in list">
                <div class="says_p_left">
                    <div class="says_p_head" style="background: url({{com.user.userimg}}) no-repeat 100%; background-size: 82px;"></div>
                </div>
                <div class="says_p_right">
                    <div>
                        <div class="says_p_hov">
                            <div class="c666">
                                <a href="javascript: void(0);" title="raply" ng-bind="com.user.username"></a> 
                                - <span ng-bind="com.comdate"></span>
                                <a href="javascript: void(0);" ng-click="repeat.showRepeat(com.comid)" ng-bind="repeat.comid == com.comid ? '取消' : '回复'"></a></div>
                            <div class="mt5" ng-bind-html="com.comment | to_trusted"></div>
                        </div>
                        <div ng-show="repeat.comid == com.comid"><ao-cs model="repeat"></ao-cs></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <ao-pg model="pager"></ao-pg>
        </div>
    </body>
    <script src="scripts/angular.min.js"></script>
    <script src="scripts/angular-route.min.js"></script>
    <script src="scripts/modules.min.js"></script>
    <script src="scripts/directives.min.js"></script>
    <script src="scripts/controllers.min.js"></script>
</html>