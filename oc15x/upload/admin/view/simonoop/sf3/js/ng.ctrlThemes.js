app.controller('ctrlthemes',['$scope','$http','$log','env','$cacheFactory', function($scope,$http,$log,$env,$cacheFactory){

    $scope.general = [];


    $scope.save = function(){
        $("td.view").fadeTo('fast',.2);
        var cache = $cacheFactory.get('$http');
        cache.removeAll();
        $http.post('index.php?route=module/sf3/save&token='+ $env['token'] +'&key=sf3_themes', $scope.general).success(function(){
            $("td.view").fadeTo('slow',1);
        });
    };

}]);

