var app = angular.module('app',['ngRoute','s-elements','ng-slider-ion','ui.sortable']);

app.config(['$routeProvider', function ($routeProvider) {
        $routeProvider
            .when('/general', {
                templateUrl: 'view/template/module/sf3parts/general.php',
                controller:'ctrlGeneral'
            })
            .when('/layouts', {
                templateUrl: 'view/template/module/sf3parts/layouts.php',
                controller:'ctrlLayouts'
            })
            .when('/styles', {
                templateUrl: 'view/template/module/sf3parts/styles.php',
                controller:'ctrlStyles'
            })
            .when('/filters', {
                templateUrl: 'view/template/module/sf3parts/filters.php',
                controller:'ctrlFilters'
            })
            .when('/filters/:filter_idx', {
                templateUrl: 'view/template/module/sf3parts/filters.php',
                controller:'ctrlFilters'
            })
            .when('/js', {
                templateUrl: 'view/template/module/sf3parts/js.php',
                controller:'ctrlJS'
            })
            .when('/about', {
                templateUrl: 'view/template/module/sf3parts/about.php',
                controller:'ctrlAbout'
            })
            .when('/hireme', {
                templateUrl: 'view/template/module/sf3parts/hireme.php',
                controller:'ctrlHireme'
            })
            .when('/cache', {
                templateUrl: 'view/template/module/sf3parts/cache.php',
                controller:'ctrlCache'
            })
            .when('/3party', {
                templateUrl: 'view/template/module/sf3parts/3party.php',
                controller:'ctrl3party'
            })

            .otherwise({
                redirectTo: '/general'
            });
}]);

app.filter('toLabel', [function(){
    return function(input) {
        return input.replace(/_/,' ').toLowerCase().replace( /\b./g, function(a){ return a.toUpperCase(); } );
    };
}]);

app.filter('to0or1', [function(){
    return function(input) {
        return input=='on'?'1':'0';
    };
}]);
