/*####################################################################################
 ##
 ## directive! NOW!
 ##
 ####################################################################################*/

sf3
/*####################################################################################
 ##
 ## tool service for the directives
 ##
 ####################################################################################*/
    .service('tools', ['$log', function ($log) {

        /**
         *
         *  Base64 encode / decode
         *  http://www.webtoolkit.info/
         *
         **/

        this.setSliderGeneralOptions = function (f, currency_id) {
            options = {
                keyboard: true,
                grid: f.settings.slider.grid.status == 'yes',
                hide_min_max: f.settings.slider.hide_min_max.status == 'yes',
                hide_from_to: f.settings.slider.hide_from_to.status == 'yes',
            }

            if (f.source.type == 'price') {
                options = angular.extend(options, {
                    step: f.self.price.slider.step[currency_id]
                })
            }
            return options;
        }
        this.setGeneralOptions = function (f) {
            return {
                searchbox: f.settings.searchbox.status == 'yes',
                totals: f.settings.totals.status == 'yes',
                totalsnumbers: f.settings.totals.status == 'yes' && (f.settings.totals.type == 'numbers' || f.settings.totals.type == 'both'),
                totalsbars: f.settings.totals.status == 'yes' && (f.settings.totals.type == 'bars' || f.settings.totals.type == 'both'),
                zeroCount: f.settings.zeroCount.status,
                collapsible: f.settings.collapsible.status == 'yes',
                collapsibleDefault: f.settings.collapsible.default,
                showmore: f.settings.showmore,
                barwidth: f.settings.totals.barwidth,
                help: f.settings.help,
                help_text: f.settings.help_text,
                barWidthBox: function () {
                    return {width: this.barwidth + 'px'};
                },
                barWidthImg: function (curr) {
                    var total = 0;
                    f.totals.map(function (o) {
                        total = total + parseInt(o.total)
                    });
                    if (!total)total = 100;
                    return {width: parseInt(parseInt(this.barwidth) * parseInt(curr) / total) + 'px'};
                },
                zero: function (curr) {
                    if (this.zeroCount == 'normal')return '';
                    if (parseInt(curr) == 0)return this.zeroCount == 'hide' ? 'hide poshide' : 'greyout';
                    return '';
                }
            }
        }
    }])
    /*####################################################################################
     ##
     ## radio directive
     ##
     ####################################################################################*/
    .directive("sf3Radio", ['$log', 'env', 'tools', function ($log, env, tools) {
        return {
            replace: true,
            restrict: 'E',
            scope: {
                f: '=',
            },
            templateUrl: 'catalog/view/simonoop/sf3/parts/checkbox_radio.html',
            controller: function ($scope, $element) {
                $scope.o = tools.setGeneralOptions($scope.f);
                $scope.activate = function (idx) {

                    $scope.f.filterdata.map(function (o, i) {

                        if ($scope.f.totals[i].total != '0') {
                            o.active = (i == idx) ? !o.active : false;
                        } else {
                            o.active = false;
                        }
                    });
                }
            },
            link: function (scope, element, attrs) {
                scope.env = env;
            }
        }
    }])
    /*####################################################################################
     ##
     ## checkbox directive
     ##
     ####################################################################################*/
    .directive("sf3Checkbox", ['$log', 'env', 'tools', function ($log, env, tools) {
        return {
            replace: true,
            restrict: 'E',
            scope: {
                f: '=',
            },
            templateUrl: 'catalog/view/simonoop/sf3/parts/checkbox_radio.html?' + Math.random(),
            controller: function ($scope, $element) {
                $scope.o = tools.setGeneralOptions($scope.f)

                $scope.activate = function (idx) {
                    $log.log("activate")
                    if ($scope.f.totals[idx].total != '0') {
                        $scope.f.filterdata[idx].active = !$scope.f.filterdata[idx].active;
                    } else {
                        $scope.f.filterdata[idx].active = false;
                    }
                }
            },
            link: function (scope, element, attrs) {
                scope.env = env;
            }
        }
    }])
    /*####################################################################################
     ##
     ## image list directive
     ##
     ####################################################################################*/
    .directive("sf3Imagelist", ['$log', 'env', 'tools', function ($log, env, tools) {
        return {
            replace: true,
            restrict: 'E',
            scope: {
                f: '=',
            },
            templateUrl: 'catalog/view/simonoop/sf3/parts/checkbox_radio.html?' + Math.random(),
            controller: function ($scope, $element) {
                $scope.o = tools.setGeneralOptions($scope.f)

                $scope.activate = function (idx) {
                    $scope.f.filterdata[idx].active = !$scope.f.filterdata[idx].active;
                }
            },
            link: function (scope, element, attrs) {
                scope.env = env;
            }
        }
    }])
    .filter("filterdropdownitems", ['$log', function ($log) {
        return function (items, extra) {
            var filtered = [];
            items.map(function (i) {
                if ((extra.zeroCount == 'normal') || (extra.zeroCount != 'normal' && (extra.totals[i.idx].total == -1 || extra.totals[i.idx].total > 0))) {
                    filtered.push(i)
                }
            });

            return filtered;
        }
    }])
    /*####################################################################################
     ##
     ## mosaic
     ##
     ####################################################################################*/
    .directive("sf3Mosaic", ['$log', 'env', 'tools', function ($log, env, tools) {
        return {
            replace: true,
            restrict: 'E',
            scope: {
                f: '=',
            },
            templateUrl: 'catalog/view/simonoop/sf3/parts/mosaic.html?' + Math.random(),
            controller: function ($scope, $element) {
                $scope.o = tools.setGeneralOptions($scope.f)

                $scope.activate = function (idx) {
                    $scope.f.filterdata[idx].active = !$scope.f.filterdata[idx].active;
                }
            },
            link: function (scope, element, attrs) {
                scope.env = env;
            }
        }
    }])
    /*####################################################################################
     ##
     ## dropdown directive
     ##
     ####################################################################################*/
    .directive("sf3Dropdown", ['$log', 'env', 'tools', function ($log, env, tools) {
        return {
            replace: true,
            restrict: 'E',
            scope: {
                f: '=',
            },
            templateUrl: 'catalog/view/simonoop/sf3/parts/dropdown.html',
            controller: function ($scope, $element) {
                $scope.o = tools.setGeneralOptions($scope.f)
                $scope.f.dropDownObject = $element.find("select");


                $scope.build = function (idx, text) {
                    return $scope.o.totals && idx > 0 && typeof $scope.f.totals[idx]!='undefined'? text + '(' + $scope.f.totals[idx].total + ')' : text;
                }
                $scope.activate = function (f) {
                    $scope.f.filterdata.map(function (o) {
                        o.active = f ? (o.text == f.text ? !o.active : false) : false;
                    });
                }
                $scope.f.filterdata.map(function (o) {
                    if (o.active) {
                        setTimeout(function () {
                            $element.find("select").val(o.idx);
                        }, 0)
                    }
                });
            },
            link: function (scope, element, attrs) {
                scope.env = env;
            }
        }
    }])
    /*####################################################################################
     ##
     ## price slider directive
     ##
     ####################################################################################*/
    .directive("sf3SliderPrice", ['$log', 'env', 'tools', function ($log, env, tools) {
        return {
            replace: true,
            restrict: 'E',
            scope: {
                f: '=',
                currencyId: '='
            },
            templateUrl: 'catalog/view/simonoop/sf3/parts/slider.html',
            controller: function ($scope, $element, $attrs) {
                $scope.o = tools.setGeneralOptions($scope.f)
                $scope.f.values = $scope.f.getCurrentValues();
                $log.log('sf3SliderPrice', $scope.f)
                options = {
                    onFinish: function (data) {
                        $scope.$apply(function () {
                            $scope.f.values = [data.from, data.to]
                        })
                    },
                    type: "double",
                    min: $scope.f.getValues()[0],
                    max: $scope.f.getValues()[1],
                    from: $scope.f.values[0],
                    to: $scope.f.values[1],
                    prettify: function (num) {
                        if(typeof $scope.f.self.price.thousands != 'undefined' && $scope.f.self.price.thousands.status=='yes') {
                            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, $scope.f.self.price.thousands.char);
                        }else{
                            return num;
                        }
                    }
                }

                if ($scope.f.self.price.slider.prefix.status == 'currency prefix') {
                    angular.extend(options, {prefix: env.currency.symbol_left})
                }
                if ($scope.f.self.price.slider.postfix.status == 'currency postfix') {
                    angular.extend(options, {postfix: env.currency.symbol_right})
                }

                $scope.f.sliderObject = $element.find(".sf3-slider").ionRangeSlider(
                    angular.extend(options, tools.setSliderGeneralOptions($scope.f, $scope.currencyId))
                );
            },
            link: function (scope, element, attrs) {
                scope.env = env;
            }

        }
    }])
    /*####################################################################################
     ##
     ## generic slider directive
     ##
     ####################################################################################*/
    .directive("sf3Slider", ['$log', 'env', 'tools', function ($log, env, tools) {
        return {
            replace: true,
            restrict: 'E',
            scope: {
                f: '='
            },
            templateUrl: 'catalog/view/simonoop/sf3/parts/slider.html',
            controller: function ($scope, $element, $attrs) {
                $scope.o = tools.setGeneralOptions($scope.f)
                //$scope.f.values=[0];
                $scope.f.values = $scope.f.getCurrentValues();
                options = {
                    onFinish: function (data) {
                        $scope.$apply(function () {
                            $scope.f.values = [data.from];
                            $scope.f.filterdata.map(function (o, i) {
                                o.active = (i == data.from) ? true : false;
                            });
                        })
                    },
                    values: $scope.f.getValues()
                };
                $scope.f.sliderObject = $element.find(".sf3-slider").ionRangeSlider(
                    angular.extend(options, tools.setSliderGeneralOptions($scope.f, null))
                );
            },
            link: function (scope, element, attrs) {
                scope.env = env;
            }
        }
    }])

/*####################################################################################
 ##
 ## helper service
 ##
 ####################################################################################*/
sf3.service('service', ['$http', '$log', 'env', 'tools', function ($http, $log, env, tools) {

    var Base64 = {

        // private property
        _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

        // public method for encoding
        encode: function (input) {
            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;

            input = Base64._utf8_encode(input);

            while (i < input.length) {

                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output +
                    this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                    this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

            }

            return output;
        },

        // public method for decoding
        decode: function (input) {
            var output = "";
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;

            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

            while (i < input.length) {

                enc1 = this._keyStr.indexOf(input.charAt(i++));
                enc2 = this._keyStr.indexOf(input.charAt(i++));
                enc3 = this._keyStr.indexOf(input.charAt(i++));
                enc4 = this._keyStr.indexOf(input.charAt(i++));

                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;

                output = output + String.fromCharCode(chr1);

                if (enc3 != 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 != 64) {
                    output = output + String.fromCharCode(chr3);
                }

            }

            output = Base64._utf8_decode(output);

            return output;

        },

        // private method for UTF-8 encoding
        _utf8_encode: function (string) {
            string = string.replace(/\r\n/g, "\n");
            var utftext = "";

            for (var n = 0; n < string.length; n++) {

                var c = string.charCodeAt(n);

                if (c < 128) {
                    utftext += String.fromCharCode(c);
                }
                else if ((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }

            }

            return utftext;
        },

        // private method for UTF-8 decoding
        _utf8_decode: function (utftext) {
            var string = "";
            var i = 0;
            var c = c1 = c2 = 0;

            while (i < utftext.length) {

                c = utftext.charCodeAt(i);

                if (c < 128) {
                    string += String.fromCharCode(c);
                    i++;
                }
                else if ((c > 191) && (c < 224)) {
                    c2 = utftext.charCodeAt(i + 1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }
                else {
                    c2 = utftext.charCodeAt(i + 1);
                    c3 = utftext.charCodeAt(i + 2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }

            }

            return string;
        }

    }


    /*####################################################################################
     ##
     ## first filter load
     ##
     ####################################################################################*/
    this.loadFilters = function () {
        if (window.location.hash) {
            hash = {hash: decodeURIComponent(window.location.hash.replace(/#\//, ''))}
        } else {
            hash = null;
        }

        if (typeof env.settings.method != 'undefined' && env.settings.method == 'GET') {

            return $http.get('index.php?route=module/sf3/loadfilters', {
                params: {
                    payload:{},
                    hash: hash,
                    cat_id: env.category_id,
                    man_id: env.manufacturer_id,
                    search: env.search,
                    ishome: env.isHome

                }, cache: env.settings.ajaxcache == 'yes'
            });
        } else {
            return $http.post('index.php?route=module/sf3/loadfilters', {
                payload:{},
                hash: hash,
                cat_id: env.category_id,
                man_id: env.manufacturer_id,
                search: env.search,
                ishome: env.isHome
            });
        }
    }

    /*####################################################################################
     ##
     ## filter reloads
     ##
     ####################################################################################*/
    this.reloadFilters = function (qs, page, limit, sort, order) {
        if (typeof env.settings.method != 'undefined' && env.settings.method == 'GET') {
            return $http.get('index.php?route=module/sf3/loadfilters', {
                params: {
                    payload: Base64.encode(encodeURIComponent(JSON.stringify(qs))),
                    cat_id: env.category_id,
                    man_id: env.manufacturer_id,
                    search: env.search,
                    page: page,
                    limit: limit,
                    sort: sort,
                    order: order,
                    ishome: env.isHome
                }, cache: env.settings.ajaxcache == 'yes'
            });
        } else {
            return $http.post('index.php?route=module/sf3/loadfilters', {
                payload: Base64.encode(encodeURIComponent(JSON.stringify(qs))),
                cat_id: env.category_id,
                man_id: env.manufacturer_id,
                search: env.search,
                page: page,
                limit: limit,
                sort: sort,
                order: order,
                ishome: env.isHome
            });
        }
    }

    /*####################################################################################
     ##
     ## load filtered products
     ##
     ####################################################################################*/

    this.loadProducts = function (qs, page, limit, sort, order, dirty) {
        var current = document.location.href.split(/#/)[0];

        if (env.isHome) {
            current = dirty ? 'index.php?route=product/search&search=%' : current;
        }

        if (env.isManufacturerPage) {
            if(!env.manufacturer_id) {
                current = dirty ? 'index.php?route=product/search&search=%' : current;
            }else{
                current = dirty ? 'index.php?route=product/manufacturer/info&manufacturer_id='+ env.manufacturer_id : current;
            }
        }



        if (typeof env.settings.method != 'undefined' && env.settings.method == 'GET') {
            return $http.get(current, {
                params: {
                    payload: Base64.encode(encodeURIComponent(JSON.stringify(qs))),
                    cat_id: env.category_id,
                    man_id: env.manufacturer_id,
                    search: env.search,
                    page: page,
                    limit: limit,
                    sort: sort,
                    order: order,
                }, cache: env.settings.ajaxcache == 'yes'
            });
        } else {

            if(!current.match(/\?/)){
                current+='?';
            }else{
                current+='&';
            }
            var getParams = {
                search: env.search,
                page: page,
                limit: limit,
                sort: sort,
                order: order
            }
            current += $.param(getParams);
            return $http.post(current, {
                payload: Base64.encode(encodeURIComponent(JSON.stringify(qs))),
                cat_id: env.category_id,
                man_id: env.manufacturer_id,
                search: env.search,
                page: page,
                limit: limit,
                sort: sort,
                order: order,
            });
        }
    }


    /*####################################################################################
     ##
     ## get current filterdata in hash format
     ##
     ####################################################################################*/
    this.getHash = function (filters) {
        return filters.map(function (o) {
            hash = o.toHash();
            return hash ? o.name() + '=' + hash : null
        }).filter(function (o) {
            return o != null
        }).join(';');
    }

    /*####################################################################################
     ##
     ## get current filterdata in Querystring format
     ##
     ####################################################################################*/
    this.getQS = function (filters) {
        qs = filters.map(function (o) {
            hash = o.toQS();
            var _data = o.source.data[o.source.type];
            //delete _data.name;
            return hash ? {
                type: o.source.type,
                //source:o.source.data[o.source.type],
                source: _data,
                data: hash,
            } : null
        }).filter(function (o) {
            return o != null
        });
        return qs.length ? qs : null;
    }

    /*####################################################################################
     ##
     ## helper function to add to filter object
     ##
     ####################################################################################*/
    this.getSpecifics = function () {
        return {
            dirty: function () {
                if (this.render == 'slider' && this.source.type == 'price') {
                    if (!angular.isDefined(this.values))return false;
                    return this.values[0] != parseFloat(this.filterdata[0].text) || this.values[1] != parseFloat(this.filterdata[1].text) ? true : false;
                } else {
                    out = this.filterdata.filter(function (o) {
                        return o.active && o.value != 'sf3none'
                    });
                    return out.length ? true : false;
                }
            },
            redraw: function () {
                this.values[0] = parseInt(this.values[0] || 0);
                this.values[1] = parseInt(this.values[1] || 0);
                this.sliderObject.data("ionRangeSlider").update({
                    from: parseInt(this.values[0]),
                    to: parseInt(this.values[1])
                });
            },
            clear: function () {
                if (this.render == 'slider' && this.source.type == 'price') {
                    this.values[0] = parseFloat(this.filterdata[0].text);
                    this.values[1] = parseFloat(this.filterdata[1].text);
                    this.redraw()
                } else {
                    out = this.filterdata.map(function (o) {
                        o.active = false
                    });
                }
                if (this.render == 'slider') {
                    this.sliderObject.data("ionRangeSlider").reset();
                }
                if (this.render == 'dropdown') {
                    this.current = this.filterdata[0];
                }
            },
            getCurrentValues: function () {
                return typeof this.currentValues != 'undefined' ? this.currentValues : this.getValues();
            },
            getValues: function () {
                if (this.source.type == 'price') {
                    return this.filterdata.map(function (o) {
                        return parseInt(o.text);
                    });
                } else {
                    return this.filterdata.map(function (o) {
                        return o.text;
                    });
                }
            },
            setValues: function () {

            },
            name: function () {
                return this.source.data[this.source.type].name;
            },
            cleanName: function () {
                return this.name();
            },
            toHash: function () {
                if (this.render == 'slider' && this.source.type == 'price') {
                    return this.values[0] != parseFloat(this.filterdata[0].text) || this.values[1] != parseFloat(this.filterdata[1].text) ? this.values : null;
                } else {
                    out = this.filterdata.filter(function (o) {
                        return o.active && o.value != 'sf3none'
                    });
                    return out.length ? out.map(function (o) {
                        return o.text
                    }) : null;
                }
            },
            toQS: function () {
                if (this.render == 'slider' && this.source.type == 'price') {
                    if (typeof this.values == "undefined")return null;
                    return this.values[0] != parseFloat(this.filterdata[0].text) || this.values[1] != parseFloat(this.filterdata[1].text) ? new Array(this.values) : null;
                } else if (this.source.type == 'price') {
                    out = this.filterdata.filter(function (o) {
                        return o.active && o.value != 'sf3none'
                    });
                    return out.length ? out.map(function (o) {
                        return o.value.split('-')
                    }) : null;
                } else {
                    out = this.filterdata.filter(function (o) {
                        return o.active && o.value != 'sf3none'
                    });
                    return out.length ? out.map(function (o) {
                        return o.value
                    }) : null;
                }
            },
        }

    }
}])