app.controller('ctrlAbout',['$scope','$http','$log','env', function($scope,$http,$log,$env){

    $http.get('index.php?route=module/sf3/about&token='+ $env['token'], {cache:true}).success(function(data){
            $scope.about = data;   
    });

}]);

