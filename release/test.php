<!DOCTYPE html>
<html ng-app="app">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" type="image/ico" href="/favicon.ico">
        <title>AyleinOter IV test</title>
        <link rel="stylesheet" href="styles/style.min.css" />
        <link rel="stylesheet" href="styles/index.min.css" />
        <link rel="stylesheet" href="styles/admin.min.css" />
        <link rel="stylesheet" href="styles/extra.min.css" />
    </head>
    <body>          
        <div ng-controller="adminStageController">
        <ao-nodes></ao-nodes>
        <div class="l_title">Stages</div>
        <div class="l_s">
            <div>展开 添加新章节</div>
            <div class="l_new">
                
            </div>
            <br>
            <div class="list">

            </div>
        </div>
        </div>
    </body>
    <script src="scripts/angular.min.js"></script>
    <script src="scripts/angular-route.min.js"></script>
    <script src="scripts/modules.min.js"></script>
    <script src="scripts/directives.min.js"></script>
    <script src="scripts/controllers.min.js"></script>
</html>