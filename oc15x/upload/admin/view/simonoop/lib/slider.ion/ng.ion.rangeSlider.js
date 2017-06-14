angular.module("ng-slider-ion",[])
.directive("sf3Slider", ['$log','env',function($log,env){    
    return{
        replace:true,
        restrict :'E',
        scope :{
            model:'=',
            options:'='            
        },
        template : '<div class="slider-wrapper"><div class="sf3-slider"></div></div>',
        link: function (scope, element, attrs) {
            options = angular.extend(scope.options,{
                keyboard: true,
                onChange : function (data) {scope.model =data.from}, 
                onFinish : function (data) {scope.$apply(function(){scope.model=data.from})},                    
                from:angular.isDefined(scope.model)?scope.model:0                        
            });     
            element.find(".sf3-slider").ionRangeSlider(options);            
        }            
    }  
}])