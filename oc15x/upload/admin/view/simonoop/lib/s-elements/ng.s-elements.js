angular.module("s-elements",[])
    .directive("sbuttons", ['$log',function($log){
        
        var settings = {
            templatePath : 'view/simonoop/lib/s-elements/'
        }
        
        return{
            replace:true,
            restrict :'E',
            scope :{
                options:'=',
                model:'='
            },
            templateUrl :settings.templatePath + 'sbuttons.html',
            controller: function($scope,$element){
                $scope.select = function(ele){
                    $scope.model=angular.isDefined($scope.key)?ele[$scope.key]:ele;
                }  
                $scope.getText = function(ele){
                    return angular.isDefined($scope.text)?ele[$scope.text]:ele;
                }
                $scope.isActive = function(ele){
                    return angular.isDefined($scope.key)?ele[$scope.key]==$scope.model:ele==$scope.model;
                }
            },
            link: function (scope, element, attrs) {
                scope.key = attrs.key;	
                scope.text = attrs.text;
                scope.label = attrs.label;
                
                if(angular.isDefined(attrs.default) && !angular.isDefined(scope.model))scope.model=attrs.default;
                
            }            
        }  
    }])