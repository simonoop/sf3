app.controller('ctrlMain',['$scope','$log','env','$location','$http', function($scope,$log,$env,$location,$http){
    $scope.debug=0;
    $scope.env = $env;
    $scope.sel = 0;

    $scope.isEmpty = function(s){
        return angular.isDefined(s) && s!='';
    };
    $scope.escapeHTML = function(t){
        return t?t.replace(/&gt;/g,'>'):'';
    };
    $scope.go = function (path) {
        $location.path(path);
    };
    
    $scope.in_array = function(needle, haystack){
        return haystack.indexOf(needle)>-1;
    };
    
    $scope.genericRemove = function(haystack,needle){
        haystack.splice(needle,1)
    };

    $scope.sel=1;
    if(window.location.hash.match(/general/))$scope.sel=1;
    if(window.location.hash.match(/layouts/))$scope.sel=2;
    if(window.location.hash.match(/styles/))$scope.sel=3;
    if(window.location.hash.match(/filters/))$scope.sel=4;
    if(window.location.hash.match(/js/))$scope.sel=5;
    if(window.location.hash.match(/about/))$scope.sel=6;
    if(window.location.hash.match(/hireme/))$scope.sel=7;
    if(window.location.hash.match(/cache/))$scope.sel=8;
    if(window.location.hash.match(/3party/))$scope.sel=9;

    

    $scope.deb = function(){
        $scope.debug=1;
        setTimeout(function(){$("#sf3-debug").draggable({ handle: "h1" }).resizable();},0);
        //$scope.$apply();        
        return true;
    }    
}]);
