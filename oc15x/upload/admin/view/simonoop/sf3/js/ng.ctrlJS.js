app.controller('ctrlJS',['$scope','$http','$log','env','$cacheFactory', function($scope,$http,$log,$env,$cacheFactory){

    $scope.editorWindow='files';
    $scope.js={
        files:{}
    };

    $scope.save = function(){
        $("td.view").fadeTo('fast',.2);
        var cache = $cacheFactory.get('$http');
        cache.removeAll();
        $http.post('index.php?route=module/sf3/save&token='+ $env['token'] +'&key=sf3_js', $scope.js).success(function(){
            $("td.view").fadeTo('slow',1);
        });
    };

    
    $http.get('index.php?route=module/sf3/getjs&token='+ $env['token'],{cache:true}).success(function(data){
        if(typeof data.files != 'undefined') {
            $scope.js = data;
        }
    });

    $http.get('index.php?route=module/sf3/getjsfiles&token='+ $env['token'],{cache:true}).success(function(data){
        $scope.jsfiles = data;
    });

}]);

