app.controller('ctrlLayouts',['$scope','$http','$log','env','$cacheFactory', function($scope,$http,$log,$env,$cacheFactory){

    $scope.modules = {};

    $http.get('index.php',{params:{route:"module/sf3/getLayouts",token:$env['token']}, cache:true}).success(function(data){
        $scope.modules = data;
    });
    
    $scope.save = function(){
        $("td.view").fadeTo('fast',.2);
        var cache = $cacheFactory.get('$http');
        cache.removeAll();
        $http.post('index.php?route=module/sf3/save&token='+ $env['token'] +'&key=sf3_module', $scope.modules.filter(function(o){return o==null?0:o.status=='1'})).success(function(){
            $("td.view").fadeTo('slow',1);
        });          
    }    
   
    
   
}]);

