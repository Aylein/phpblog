<ao-nodes></ao-nodes>
<div class="l_title">Users</div>
<div class="l_s">
    <div>展开 添加新会员</div>
    <div class="l_new">
        <div class="l_new_img" style="background: url(./images/ac/ac_13.png) no-repeat 100%; background-size: 100%"></div>
        <div class="l_new_info">
            <form name="new_user" ng-submit="newTest.submit()">
                <input type="text" ng-class="{'bA50000': newTest.usernameRes == 'error'}" name="username" ng-model="new.username" ng-blur="newTest.usernameTest()">
                <span class="cA50000" ng-bind="newTest.usernameText"></span><br>
                <input type="password" ng-class="{'bA50000': newTest.userpassRes == 'error'}" name="userpass" ng-model="new.userpass" ng-blur="newTest.userpassTest()">
                <span class="cA50000" ng-bind="newTest.userpassText"></span><br>
                <select name="usertype" ng-model="new.usertype" ng-options="type as type for type in newTest.usertypes"></select>
                <select name="uservalid" ng-model="new.uservalid" ng-options="type.val as type.text for type in newTest.uservalid"></select>
                <input type="submit" value="确定">
                <input type="reset" ng-click="newTest.reset()" value="重置">
            </form>
        </div>
        <div class="clear"></div>
    </div>
    <br>
    <div class="list">
        <div ng-repeat="item in list" class="mt15">
            <div ng-show="updateTest.updateid != item.n.userid" ng-class="{allBBB: item.n.uservalid == 0}">
                <div class="l_new_img" style="background: url({{item.n.userimg}}) no-repeat 100%; background-size: 100%"></div>
                <div class="l_new_info">
                    <a href="javascript: void(0);" ng-bind="item.n.username" ng-click="updateTest.setUpdate(item.n.userid)"></a><br>
                    <span ng-bind="item.n.usercreatetime"></span><br>
                    <span ng-bind="item.n.usertype"></span> <span ng-bind="item.n.uservalid == 1 ? '可用' : '不可用'"></span>
                </div>
                <div class="clear"></div>
            </div>
            <div ng-show="updateTest.updateid == item.u.userid">
                <div class="l_new_img" style="background: url({{item.n.userimg}}) no-repeat 100%; background-size: 100%"></div>
                <div class="l_new_info">
                    <form name="update_user_{{item.u.userid}}" ng-submit="updateTest.submit('u_' + item.u.userid)">
                        <input name="username" ng-class="{'bA50000': updateTest.usernameRes == 'error'}" type="text" ng-model="item.u.username">
                        <span class="cA50000" ng-bind="updateTest.usernameText"></span><br>
                        <input name="userpass" ng-class="{'bA50000': updateTest.usernameRes == 'error'}" type="password" ng-model="item.u.userpass">
                        <span class="cA50000" ng-bind="updateTest.userpassText"></span><br>
                        <select name="usertype" ng-model="item.u.usertype" ng-options="type as type for type in newTest.usertypes"></select>
                        <select name="uservalid" ng-model="item.u.uservalid" ng-options="type.val as type.text for type in newTest.uservalid"></select>
                        <input type="submit" value="确定">
                        <input type="reset" ng-click="updateTest.reset('u_' + item.u.userid, $event)" value="重置">
                        <input type="button" ng-click="updateTest.cancel('u_' + item.u.userid)" value="取消">
                    </form>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <ao-pg model="pager"></ao-pg>
    </div>
</div>