<?php
/*####################################################################################
##
## FRONTEND
##
## sf3 - 3.0.0 Build 0052
##
####################################################################################*/
?>

<?php

if(!$settings){
	return true;
}
if($settings['status']!='Enabled'){
	return true;
}

?>


<?php if($OC2){?>
<link rel="stylesheet" type="text/css" href="catalog/view/simonoop/sf3/themes/default/css/sf3oc2.css?<?php echo time();?>"/>
<?php }else{?>
<link rel="stylesheet" type="text/css" href="catalog/view/simonoop/sf3/themes/default/css/sf3.css?<?php echo time();?>"/>
<?php }?>

<script type="text/javascript" src="catalog/view/simonoop/angular.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/angular-local-storage/0.2.1/angular-local-storage.min.js" type="text/javascript"></script>
<script type="text/javascript" src="catalog/view/simonoop/sf3/ng.sf3.js"></script>
<script type="text/javascript" src="catalog/view/simonoop/sf3/ng.sf3.lib.js"></script>

<link rel="stylesheet" type="text/css" href="catalog/view/simonoop/lib/slider.ion/css/ion.rangeSlider.css"/>
<link rel="stylesheet" type="text/css" href="catalog/view/simonoop/lib/slider.ion/css/ion.rangeSlider.skin<?php echo $settings['sliderskin'];?>.css"/>
<script src='catalog/view/simonoop/lib/slider.ion/js/ion.rangeSlider.js'></script>

<script type="text/javascript" src="catalog/view/simonoop/lib/slimscroll/jquery.slimscroll.js"></script>

<link rel="stylesheet" type="text/css" href="catalog/view/simonoop/lib/tooltipster/tooltipster.css"/>
<link rel="stylesheet" type="text/css" href="catalog/view/simonoop/lib/tooltipster/themes/tooltipster-light.css"/>
<link rel="stylesheet" type="text/css" href="catalog/view/simonoop/lib/tooltipster/themes/tooltipster-noir.css"/>
<link rel="stylesheet" type="text/css" href="catalog/view/simonoop/lib/tooltipster/themes/tooltipster-punk.css"/>
<link rel="stylesheet" type="text/css" href="catalog/view/simonoop/lib/tooltipster/themes/tooltipster-shadow.css"/>
<script type="text/javascript" src="catalog/view/simonoop/lib/tooltipster/jquery.tooltipster.min.js"></script>
<script type="text/javascript" src="catalog/view/simonoop/crypto.md5.js"></script>


<script>
	//$(function(){
	sf3.constant("env",{
		category_id:<?php echo $category_id;?>,
	manufacturer_id:<?php echo $manufacturer_id;?>,
	search:'<?php echo isset($_GET['search'])?$_GET['search']:''?>',
    language_id:<?php echo $language_id; ?>,
	template:'default',
    settings:<?php echo json_encode($settings);?>,
	module:<?php echo json_encode($module);?>,
	layout_id:<?php echo $layout_id;?>,
	isHome:<?php echo $isHome;?>,
	isManufacturerPage:<?php echo $isManufacturerPage;?>,
	bootstrap:<?php echo $bootstrap;?>,
	limit:<?php echo $limit;?>,
	currency:{
		id: '<?php echo $currency['id'];?>',
        code: '<?php echo $currency['code'];?>',
        symbol_left: '<?php echo $currency['symbol_left'];?>',
        symbol_right:'<?php echo $currency['symbol_right'];?>'
	},
	js:<?php echo json_encode($js,1);?>,
	})
	//});
</script>

<?php if($OC2){?>
<section id="sf3" ng-app="sf3" ng-controller="ctrlFilters" ng-show="filters.length">
<div class="panel panel-default">
<div class="panel-heading">{{env.settings.title[env.language_id]}}
<div class="sf3-clear" ng-show="dirty()" ng-click="clearFilters()">{{env.settings.cleartitle[env.language_id]}}</div>
</div>
<div ng-repeat="(findex,f) in filters" class="list-group">
<sf3-radio ng-if="f.render=='radio'" f="f"></sf3-radio>
<sf3-checkbox ng-if="f.render=='checkbox'" f="f"></sf3-checkbox>
<sf3-imagelist ng-if="f.render=='image list'" f="f"></sf3-imagelist>
<sf3-mosaic ng-if="f.render=='image mosaic'" f="f"></sf3-mosaic>
<sf3-dropdown ng-if="f.render=='dropdown'" f="f"></sf3-dropdown>
<sf3-slider-price ng-if="f.render=='slider' && f.source.type=='price'" f="f" currency_id="env.currency.id"></sf3-slider-price>
<sf3-slider ng-if="f.render=='slider' && f.source.type!='price'" f="f" currency_id="env.currency.id"></sf3-slider>
</div>
</div>
</section>
<?php }else{ ?>


<section id="sf3" ng-app="sf3" ng-controller="ctrlFilters" ng-show="filters.length">
<div class="box sf3"  ng-cloak>

<div class="box-heading">{{env.settings.title[env.language_id]}}
<div class="sf3-clear" ng-show="dirty()" ng-click="clearFilters()">{{env.settings.cleartitle[env.language_id]}}</div>
</div>

<div class="box-content">
<div class="sf3-bread-crumbs">
<div class="sf3-crumb" ng-repeat="f in filters">
</div>
</div>
<div ng-repeat="(findex,f) in filters" class="sf3-element-wrapper">
<sf3-radio ng-if="f.render=='radio'" f="f"></sf3-radio>
<sf3-checkbox ng-if="f.render=='checkbox'" f="f"></sf3-checkbox>
<sf3-imagelist ng-if="f.render=='image list'" f="f"></sf3-imagelist>
<sf3-mosaic ng-if="f.render=='image mosaic'" f="f"></sf3-mosaic>
<sf3-dropdown ng-if="f.render=='dropdown'" f="f"></sf3-dropdown>
<sf3-slider-price ng-if="f.render=='slider' && f.source.type=='price'" f="f" currency_id="env.currency.id"></sf3-slider-price>
<sf3-slider ng-if="f.render=='slider' && f.source.type!='price'" f="f" currency_id="env.currency.id"></sf3-slider>
</div>

<div class="sf3-go-wrapper bottom" ng-if="general.auto=='no'">
<button class="sf3-go">Search</button>
</div>
</div>
</div>

</section>
<?php }?>