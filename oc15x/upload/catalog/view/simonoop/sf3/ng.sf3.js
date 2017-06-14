var sf3 = angular.module('sf3', ['ngAnimate', 'LocalStorageModule']);


/*####################################################################################
 ##
 ## helpers
 ##
 ####################################################################################*/
//sf3.run(function($rootScope){
//$rootScope.loadFilters = function() { }
//})
/*####################################################################################
 ##
 ## core!
 ##
 ####################################################################################*/
sf3.controller('ctrlFilters', ['$scope', '$http', '$location', '$log', '$sce', 'env', '$timeout', 'service', 'localStorageService', function ($scope, $http, $location, $log, $sce, $env, $timeout, service, localStorageService) {

    /***** INIT ****/
    $scope.debug = 0;
    $scope.filters = [];

    $scope.env = $env;
    $scope.service = service;
    $scope.canwatch = true;
    $scope.page = 1;
    $scope.limit = $env.limit;
    $scope.sort = null;
    $scope.order = null;
    $scope.absuga = 'aaa';

    $scope.$emit('content.changed');

    var filter = function (f, idx) {
        angular.extend(f, service.getSpecifics());

        if ($env.settings.hash == 'yes') {
            if (window.location.hash) {
                var hash = decodeURIComponent(window.location.hash.replace(/#\//, '')).split(';');//ch1
                hash.map(function (el) {
                    var elari = el.split('=');//ch1
                    if (elari.length == 2) {
                        var name = elari[0];//ch1
                        if (f.source.data[f.source.type].name == name) {
                            var values = elari[1].split(',');//ch1
                            if (f.render != 'slider') {
                                for (var j = 0; j < values.length; j++) {//ch1
                                    var value = values[j];//ch1
                                    for (var i = 0; i < f.filterdata.length; i++) {//ch1
                                        if (f.filterdata[i].text == value) {
                                            f.filterdata[i].active = true;
                                        }
                                    }
                                }
                            } else if (f.source.type == 'price') {
                                f.currentValues = values;
                                f.values = values;
                            } else {
                                f.currentValues = values;
                                f.values = values;
                            }

                        }
                    }
                });
            } else {
                if (typeof f.self.persist != 'undefined' && f.self.persist.status == 'yes') {
                    var persist = $scope.persist.get(f.uid);

                    if (persist) {
                        if (f.render != 'slider') {

                            for (var j = 0; j < persist.filterdata.length; j++) {//ch1
                                var value = persist.filterdata[j];//ch1
                                for (var i = 0; i < f.filterdata.length; i++) {//ch1
                                    $log.log(value.uid, f.filterdata[i].uid, value.uid == f.filterdata[i].uid, f.filterdata[i], value)
                                    if (f.filterdata[i].uid == value.uid && value.active) {
                                        f.filterdata[i].active = true;
                                    }
                                }
                            }
                        } else if (f.source.type == 'price') {
                            f.currentValues = persist.values;
                            f.values = persist.values;
                        } else {
                            //f.currentValues=persist.values;
                        }
                    }
                }
            }
        } else {

        }
        return f;
    };//filter

    /*####################################################################################
     ##
     ## dobinds
     ##
     ####################################################################################*/
    $scope.dobinds = function () {
        $scope.limit = $("option[value*='limit']:eq(0)").length ? $("option[value*='limit']:eq(0)").parent().val().match(/limit=([\d]+)/)[1] : $env.limit;

        $("a[href*='page']").off("click");
        $("a[href*='page']").on("click", function (e) {
            e.preventDefault();
            $scope.page = $(this).attr("href").match(/page=([\d]+)/)[1];
            $scope.reloadFilters({keeppage: true})
        });

        $("select").filter(function () {
            return $(this).find("option[value*='sort']").length
        }).removeAttr("onchange").on("change", function () {
            var url = $(this).val();
            var mSort = url.match(/sort=([\w\.]*)/);//ch1
            var mOrder = url.match(/order=([\w\.]*)/);//ch1
            var mPage = url.match(/page=([\w\.]*)/);//ch1
            var mLimit = url.match(/limit=([\w\.]*)/);//ch1
            $scope.sort = mSort ? mSort[1] : 'p.sort_order';
            $scope.order = mOrder ? mOrder[1] : 'ASC';
            $scope.page = mPage ? mPage[1] : 1;
            $scope.limit = mLimit ? mLimit[1] : $scope.limit;
            $scope.reloadFilters({keeppage: true})
        });

        $("a[href*='#']").off("click");
        $("a[href*='#']").on("click", function () {
            location.reload();
        })
    };//$scope.dobinds()    

    /*####################################################################################
     ##
     ## Am I dirty?
     ##
     ####################################################################################*/
    $scope.dirty = function () {
        return $scope.filters.some(function (o) {
            return o.dirty()
        });
    };//dirty

    /*####################################################################################
     ##
     ## Clear filters!
     ##
     ####################################################################################*/
    $scope.clearFilters = function () {
        $scope.canwatch = false;
        $scope.filters.map(function (o) {
            o.clear()
        });
        $location.path('');
        $scope.persist.clear();
        setTimeout(function () {
            $scope.canwatch = true;
            $scope.reloadFilters({});
        }, 0)
    };//clearFilters

    /*####################################################################################
     ##
     ## Clear filters!
     ##
     ####################################################################################*/
    $scope.persist = {
        set: function (key, val) {
            $log.log('persist:set:', key, val)
            return localStorageService.set(key, val);
        },
        get: function (key) {
            var data = localStorageService.get(key);
            $log.log('persist:get:', key, data)
            return data
        },
        clear: function () {
            localStorageService.clearAll();
        },
        remove: function (key) {
            localStorageService.remove(key);
        }
    }

    /*####################################################################################
     ##
     ## watch manager
     ##
     ####################################################################################*/
    $scope.addWatch = function (i, render, type) {
        $scope.$watch('filters[' + i + '].' + (render == 'slider' && type == 'price' ? 'values' : 'filterdata'), function (n, o) {

            if (n != o && angular.isDefined(o) && $scope.canwatch) {

                //$log.log("Watching...")
                var idx = this.exp.match(/\[(\d+)\]/)[1];//ch1
                var no = $scope.filters[idx];//ch1

                if (typeof no.self.persist != 'undefined' && no.self.persist.status == 'yes') {
                    $scope.persist.set(no.uid, no);
                    $log.log('presisting', no)
                }

                if ((no.settings.delay.custom) != 0) {
                    if (service._timeout) {
                        $timeout.cancel(service._timeout);
                    }
                    service._timeout = $timeout(function () {
                        //$log.log("I'M WATCHING YOU WITH A DELAY! ("+no.settings.delay.custom+")");
                        if ($env.settings.hash == 'yes') {
                            $location.path(service.getHash($scope.filters))
                        }
                        $scope.reloadFilters({});
                        service._timeout = null;
                    }, parseInt(no.settings.delay.custom));
                } else {
                    //$log.log("I'M WATCHING YOU RIGHT NOW!", no.source.type)
                    if ($env.settings.hash == 'yes') {
                        $location.path(service.getHash($scope.filters))
                    }
                    $scope.reloadFilters({});
                }
            } else {
                //$log.log("Didn't watch")
            }
        }, true)
    };//addWatch

    /*####################################################################################
     ##
     ## reload filters
     ##
     ####################################################################################*/
    $scope.reloadFilters = function (options) {

        if (!options.keeppage) {
            $scope.page = 1;
        }

        var promiseGetFilters = service.reloadFilters(service.getQS($scope.filters), $scope.page, $scope.limit, $scope.sort, $scope.order);
        promiseGetFilters.then(function (payload) {
            payload.data.map(function (f, i) {
                $scope.filters[i].totals = f.totals;
                $scope.filters[i].time = f.time;
            });
        });

        var promiseProductLoad = service.loadProducts(service.getQS($scope.filters), $scope.page, $scope.limit, $scope.sort, $scope.order, $scope.dirty());

        var dom = $scope.env.module.filter(function (o) {
            return o.layout_id == $env.layout_id
        });
        var domid = dom[0].domid.split(';');

        domid.map(function (o) {
            $(o).fadeTo('fast', .2);
        });

        promiseProductLoad.then(function (data) {
            var $incoming = angular.element("<SF3WRAP>" + data.data + "</SF3WRAP>");//ch1
            domid.map(function (o) {
                $(o).html($incoming.find(o).html()).fadeTo('slow', 1);
            });

            setTimeout(function () {
                $scope.dobinds();

                //Do we have a display function?
                if (typeof display == 'function') {
                    var view = (typeof $.totalStorage != 'undefined') ? $.totalStorage('display') : $.cookie('display');//ch1
                    if (view) {
                        display(view);
                    } else {
                        display('list');
                    }
                } else {
                    //Journal
                    if (typeof Journal != 'undefined' && typeof Journal.gridView == 'function') {
                        Journal.gridView();
                    }
                }

                //Do we have a bivo slider?
                if ($('#slideshow0').length)$('#slideshow0').nivoSlider();

                //Should we load external js files?
                if ($env.js) {
                    angular.forEach($env.js.files, function (status, src) {
                        if (status == 'Enabled') {
                            $.ajax({
                                dataType: "script",
                                cache: $env.settings.ajaxcache == 'yes',
                                url: src
                            });
                        }
                    });
                }

                if(typeof $env.settings.scrollto != undefined && $env.settings.scrollto!='NONE'){
                    if($env.settings.scrollto=='TOP'){
                        $(window).scrollTop(0);
                    }else if($env.settings.scrollto=='BOTTOM'){
                        $(window).scrollTop($(document).height());
                    }
                }

            }, 0)
        });
    };//reloadFilters

    /*####################################################################################
     ##
     ## load bootstrap filters on pageload
     ##
     ####################################################################################*/
    $scope.loadBootstrapFilters = function () {
        var payload = $env.bootstrap;
        payload.map(function (f, i) {
            $scope.filters.push(new filter(f, i));
            $scope.addWatch(i, f.render, f.source.type);
        });

        $scope.dobinds();

        setTimeout(function () {
            $(".sf3-scrollable").each(function () {

                var maxItems = $(this).data("showmore-items");
                var currentItems = $(this).find(".sf3-table tr").length;
                var totalItems = currentItems > maxItems ? maxItems : currentItems + .5;

                var height = totalItems * parseInt($(this).find(".sf3-table tr:eq(0)").outerHeight()) + parseInt($(this).find(".sf3-table").css("border-spacing").split(/ /) [0]) * 2;
                $(this).slimScroll({
                    height: height
                });
            });
            $(".sf3-readmore").each(function () {
                var $_this = $(this);
                if ($_this.find("table tr").length > parseInt($_this.data("showmore-items"))) {
                    $_this.find("table tr:gt(" + (parseInt($_this.data("showmore-items")) - 1) + ")").hide();
                    $_this.find("div.sf3-showmore").click(function (e) {
                        e.preventDefault;
                        $_this.data("visible", !$_this.data("visible"));
                        $_this.data("visible") ?
                            $_this.find("table tr").fadeIn('fast') :
                            $_this.find("table tr:gt(" + (parseInt($_this.data("showmore-items")) - 1) + ")").fadeOut('fast');
                        $_this.data("visible") ?
                            $(this).text($_this.data("captionless")) : $(this).text($_this.data("captionmore"))
                    });
                }
            });
            $('.sf3-help-text').tooltipster({theme: 'tooltipster-shadow'});
        }, 0);

        if($scope.dirty()){
            $scope.reloadFilters({});
        }

    };//loadFilters

    /*####################################################################################
     ##
     ## GOGOGO!
     ##
     ####################################################################################*/
    $scope.loadBootstrapFilters()

}]);//controller:ctrlFilters




