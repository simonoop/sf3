app.controller('ctrlGeneral',['$scope','$http','$log','env','$cacheFactory', function($scope,$http,$log,$env,$cacheFactory){

    $scope.current={};
    $scope.current.caption=null;
    $scope.current.caption2=null;
    $scope.general = [];

    $http.get('index.php?route=module/sf3/getStores&token='+ $env['token'], {cache:true}).success(function(data){
        $scope.stores = data;
    });  
   
    $http.get('index.php?route=module/sf3/getSettings&token='+ $env['token'], {cache:true}).success(function(data){
        $scope.general = data;
    });

    /**
     * init stuff
     */
    $http.get('index.php',{params:{route:"module/sf3/getLanguages",token:$env['token']}, cache:true}).success(function(data){
        $scope.aux_languages = data;
    });

    $scope.save = function(){
        $("td.view").fadeTo('fast',.2);
        var cache = $cacheFactory.get('$http');
        cache.removeAll();
        $http.post('index.php?route=module/sf3/save&token='+ $env['token'] +'&key=sf3_general', $scope.general).success(function(){
            $("td.view").fadeTo('slow',1);
        });          
    }   
    

   
}]);

