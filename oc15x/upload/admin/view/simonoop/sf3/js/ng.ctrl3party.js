app.controller('ctrl3party',['$scope','$http','$log','env','$cacheFactory', function($scope,$http,$log,$env,$cacheFactory){

    $scope.general = [];

    $http.get('index.php?route=module/sf3/getSettings&token='+ $env['token'], {cache:true}).success(function(data){
        $scope.general = data;
    });

    $scope.save = function(){
        $("td.view").fadeTo('fast',.2);
        var cache = $cacheFactory.get('$http');
        cache.removeAll();
        $http.post('index.php?route=module/sf3/save&token='+ $env['token'] +'&key=sf3_general', $scope.general).success(function(){
            $("td.view").fadeTo('slow',1);
        });
    };

    $scope.testmemcache = function(){
        $http.post('index.php?route=module/sf3/testmemcache&token='+ $env['token'], $scope.general.cache.memcache).success(function(data){
            alert(data.status?'Connection successful.\n\nMemcache version:'+ data.status:'Could not connect');
        });
    };

    $scope.clearcache = function(){
        $http.post('index.php?route=module/sf3/clearcache&token='+ $env['token'], $scope.general.cache.memcache).success(function(data){
        });
    }
}]);

