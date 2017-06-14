app.controller('ctrlFilters',['$scope','$http','$log','env','$routeParams','$filter','$cacheFactory', function($scope,$http,$log,$env,$routeParams,$filter,$cacheFactory){
    
    //$scope.aux = [];
    $scope.current={};
    $scope.filter_idx = null;
    $scope.aux_placement = {};
    $scope.current.caption=null;
    $scope.current.captionmoreless=null;

	$scope.sortableOptions = {
    	update: function(e, ui) { 
    		$scope.setFilterIdx(-1);
    	},
    
  	};

    /**
     * init stuff
    */
    $http.get('index.php',{params:{route:"module/sf3/getLanguages",token:$env['token']}, cache:true}).success(function(data){
        $scope.aux_languages = data;
    });

    $http.get('index.php',{params:{route:"module/sf3/getStockStatus",token:$env['token']}, cache:true}).success(function(data){$scope.aux_stock_status = data;});

    $http.get('index.php',{params:{route:"module/sf3/getLayouts",token:$env['token']}, cache:true}).success(function(data){
        $scope.modules = data;
    });    

    $http.get('index.php',{params:{route:"module/sf3/getCurrencies",token:$env['token']}, cache:true}).success(function(data){
        $scope.aux_currencies = data;
    });
    
    $http.get('index.php?route=module/sf3/get&token='+ $env['token'] +'&key=sf3_filterdata', {cache:true}).success(function(data){
        $scope.filters = data;
    });    

    $http.get('index.php?route=module/sf3/getCategories&token='+ $env['token'], {cache:true}).success(function(data){
        $scope.aux_categories = data;
    });
    
    $http.get('index.php?route=module/sf3/getManufacturers&token='+ $env['token'],{cache:true}).success(function(data){
        $scope.aux_manufacturers = data;
    });    
    
    $http.get('index.php?route=module/sf3/getOptions&token='+ $env['token'], {cache:true}).success(function(data){
        $scope.aux_options = data;
    });      

    $http.get('index.php?route=module/sf3/getAttributeGroups&token='+ $env['token'], {cache:true}).success(function(data){
        $scope.aux_attributes_groups = data;
    });

    $http.get('index.php?route=module/sf3/getAttributes&token='+ $env['token'], {cache:true}).success(function(data){
        $scope.aux_attributes = data;
    });
    
    $http.get('index.php?route=module/sf3/getStores&token='+ $env['token'], {cache:true}).success(function(data){
        $scope.aux_stores = data;
    });
    
    $http.get('index.php?route=module/sf3/getAllTables&token='+ $env['token'], {cache:true}).success(function(data){
        $scope.aux_oc_tables= data;
    });

    $http.get('index.php?route=module/sf3/getTaxes&token='+ $env['token'], {cache:true}).success(function(data){
        $scope.aux_taxes= data;
    });

   	$scope.getDisplayName = function(f){

   		if(angular.isDefined(f.caption) && angular.isDefined(f.caption[$env.language_id]) && f.caption[$env.language_id]!=''){
   			return f.caption[$env.language_id];
   		}
   		
   		if(angular.isDefined(f.source) && angular.isDefined(f.source.data[f.source.type].name)){
   			return f.source.data[f.source.type].name;
   		}

        if(angular.isDefined(f.source)) {
            return f.source.type;
        }

        return "new filter";
   	};
	
    /*
    * placement stuff
    */
    $scope.addCategory = function(d){
        switch(d.job){
            case 'addone':
                $scope.f.placement.categories.data = $scope.f.placement.categories.data || [];
                $scope.f.placement.categories.data.push({'category_id':d.p.category_id,'name':d.p.name});
                break;
            case 'addall':
                $scope.f.placement.categories.data=[];
                for(var i=0;i<$scope.aux_categories.length;i++){
                    p = $scope.aux_categories[i];
                    $scope.f.placement.categories.data.push({'category_id':p.category_id,'name':p.name})    
                }
                break;
            case 'clearall':
                $scope.f.placement.categories.data=[];
                break;
        }
    };

    $scope.addManufacturer = function(d){
        switch(d.job){
            case 'addone':
                $scope.f.placement.manufacturers.data = $scope.f.placement.manufacturers.data || [];
                $scope.f.placement.manufacturers.data.push({'manufacturer_id':d.p.manufacturer_id,'name':d.p.name});
                break;
            case 'addall':
                $scope.f.placement.manufacturers.data=[];
                for(var i=0;i<$scope.aux_manufacturers.length;i++){
                    p = $scope.aux_manufacturers[i];
                    $scope.f.placement.manufacturers.data.push({'manufacturer_id':p.manufacturer_id,'name':p.name})    
                }
                break;
            case 'clearall':
                $scope.f.placement.manufacturers.data=[];
                break;
        }
    };

    $scope.addLayout = function(d){
        switch(d.job){
            case 'addone':
                $scope.f.placement.layouts.data = $scope.f.placement.layouts.data || [];
                $scope.f.placement.layouts.data.push({'layout_id':d.p.layout_id,'name':d.p.name});
                break;
            case 'addall':
                $scope.f.placement.layouts.data=[];
                for(var i=0;i<$scope.modules.length;i++){
                    p = $scope.modules[i];
                    $scope.f.placement.layouts.data.push({'layout_id':p.layout_id,'name':p.name})
                }
                break;
            case 'clearall':
                $scope.f.placement.layouts.data=[];
                break;
        }
    };

    /**
     * methods
     */
    /*TODO
    $scope.getTableFields = function(table){
    	$http.post('index.php?route=module/sf3/getTableFields&token='+ $env['token'],{tablename:table}).success(function(data){
        	$scope.aux_oc_table_fields= data;
    	});
    }
    */
     
    $scope.setFilterIdx = function(idx){
        $scope.filter_idx = idx;
        $scope.f = $scope.filters[$scope.filter_idx];
        
	
		/*TODO
        if(angular.isDefined($scope.f.self.attributes.external)){
			$http.post('index.php?route=module/sf3/getTableFields&token='+ $env['token'],{tablename:$scope.f.self.attributes.external.table}).success(function(data){
        		$scope.f.aux_oc_table_fields= data;
    		});
		}
		*/
    };
    
    $scope.remove = function(idx){
        $scope.filters.splice(idx,1);
    };
    
    $scope.new = function(){
        $scope.filters.push({});
        $scope.setFilterIdx($scope.filters.length-1)
    };
    
    $scope.save = function(){
        $("td.view").fadeTo('fast',.2);
        var cache = $cacheFactory.get('$http');
        cache.removeAll();
        $http.post('index.php?route=module/sf3/save&token='+ $env['token'] +'&key=sf3_filterdata', $scope.filters).success(function(){
            $("td.view").fadeTo('slow',1);
        });          
    };
    
    /* to del*/
    /*
    $scope.getSourceName = function(f){
        $log.log(f.source.type)
        if(f.source.type =='attributes'){
            return ($filter('filter')($scope.aux_attributes, {id:f.source.id}))[0].name
        }else if(f.source.type =='options'){
            return ($filter('filter')($scope.aux_options, {id:f.source.id}))[0].name
        }
        return f.source.type;        
    }
    */

    
}]);
