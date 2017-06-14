<?php
	/*####################################################################################
	##
	## ADMIN
	##
	## sf3 - 3.0.0 Build 0052
	##
	####################################################################################*/
?>

<?php



?>

<?php
/*####################################################################################
##
## OC 2>=0
##
####################################################################################*/
if($OC2){?>

<?php echo $header; ?>

<link rel="stylesheet" href="view/simonoop/sf3/css/sf3.css"/>
<link rel="stylesheet" href="view/simonoop/sf3/css/sf3oc2addon.css"/>

<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular.min.js"></script>
<script type="text/javascript" src="view/simonoop/lib/angular.route.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.app.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlMain.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlLayouts.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlGeneral.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlStyles.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlFilters.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlJS.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlAbout.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlCache.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrl3party.js"></script>

<script type="text/javascript" src="view/simonoop/sf3/jquery/ui/1.11.4/jquery-ui.min.js"></script>

<script type="text/javascript" src="view/simonoop/lib/s-elements/ng.s-elements.js"></script>
<link rel="stylesheet" href="view/simonoop/lib/s-elements/ng.s-elements.css"/>

<link rel="stylesheet" type="text/css" href="view/simonoop/lib/slider.ion/css/ion.rangeSlider.css"/>
<link rel="stylesheet" type="text/css" href="view/simonoop/lib/slider.ion/css/ion.rangeSlider.skinModern.css"/>
<script src='view/simonoop/lib/slider.ion/js/ion.rangeSlider.js'></script>
<script src='view/simonoop/lib/slider.ion/ng.ion.rangeSlider.js'></script>
<script type="text/javascript" src="view/simonoop/lib/angular.sortable.js"></script>

<script>
	app.constant("env",{
		modulename:'sf3',
		token:'<?php echo $token; ?>',
		location:'<?php echo preg_replace('/&amp;/', '&', $action); ?>',
		memcache:<?php echo $memcache;?>,
		language_id:<?php echo $language_id;?>
	})
	$(function(){
		$(".content").prepend($(".loss"));$(".loss").fadeIn('slow');
	});
</script>


<?php echo $column_left; ?>

<div id="content" ng-app="app" ng-controller="ctrlMain">
  <div class="page-header">
	<div class="container-fluid">
	  <div class="pull-right">
		<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
	  <h1><?php echo $heading_title; ?></h1>
	  <ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	  </ul>
	</div>
  </div>
  <div class="container-fluid">
	<?php if ($error_warning) { ?>
	<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	</div>
	<?php } ?>
	<div class="panel panel-default">
	  <div class="panel-body">

		<div class="panel-top">
			<img src="http://images.simonoop.com/simon/simonfilters3/logosf3.png" alt="" />
		</div>

		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-filter" class="form-horizontal">

			<div class="content">
				<table class="stuff">
					<tr>
						<td class="nav">
							<div class="top-line"></div>
								<ul>
									<li ng-click="go('/general');sel=1" ng-class="sel==1?'on':''">General Settings</li>
									<li ng-click="go('/layouts');sel=2" ng-class="sel==2?'on':''">Layouts</li>
									<!--<li ng-click="go('/styles');sel=3" ng-class="sel==3?'on':''">Styles</li>-->
									<li ng-click="go('/filters');sel=4" ng-class="sel==4?'on':''">Filters</li>
									<div class="top-line"></div>
				<!--
									<li ng-click="go('/themes');sel=10" ng-class="sel==10?'on':''">Themes</li>
				-->
									<li ng-click="go('/cache');sel=8" ng-class="sel==8?'on':''">Cache</li>
									<li ng-click="go('/js');sel=5" ng-class="sel==5?'on':''">JavaScript</li>
									<li ng-click="go('/3party');sel=9" ng-class="sel==9?'on':''">Third party compatibility</li>
									<div class="top-line"></div>
									<li ng-click="go('/about');sel=6" ng-class="sel==6?'on':''">About</li>
									<!--<li ng-click="go('/hireme');sel=7" ng-class="sel==7?'on':''">Hire me</li>-->
								</ul>
						</td>
						<td class="separator"></td>
						<td class="view">
							<div class="top-line"></div>
							<div ng-view></div>
						</td>
					</tr>
				</table>

			</div>

		</form>
	  </div>
	</div>
  </div>

</div>
<?php echo $footer; ?>

<?php }else{
/*###################################################################################
##
## OC <2
##
####################################################################################*/
?>

<?php echo $header; ?>

<link rel="stylesheet" href="view/simonoop/sf3/css/sf3.css"/>

<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular.min.js"></script>
<script type="text/javascript" src="view/simonoop/lib/angular.route.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.app.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlMain.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlLayouts.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlGeneral.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlStyles.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlFilters.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlJS.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlAbout.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrlCache.js"></script>
<script type="text/javascript" src="view/simonoop/sf3/js/ng.ctrl3party.js"></script>

<script type="text/javascript" src="view/simonoop/lib/s-elements/ng.s-elements.js"></script>
<link rel="stylesheet" href="view/simonoop/lib/s-elements/ng.s-elements.css"/>

<link rel="stylesheet" type="text/css" href="view/simonoop/lib/slider.ion/css/ion.rangeSlider.css"/>
<link rel="stylesheet" type="text/css" href="view/simonoop/lib/slider.ion/css/ion.rangeSlider.skinModern.css"/>
<script src='view/simonoop/lib/slider.ion/js/ion.rangeSlider.js'></script>
<script src='view/simonoop/lib/slider.ion/ng.ion.rangeSlider.js'></script>
<script type="text/javascript" src="view/simonoop/lib/angular.sortable.js"></script>

<script>
	app.constant("env",{
		modulename:'sf3',
		token:'<?php echo $token; ?>',
		location:'<?php echo preg_replace('/&amp;/', '&', $action); ?>',
		memcache:<?php echo $memcache;?>,
		language_id:<?php echo $language_id;?>
	})
	$(function(){
		$(".content").prepend($(".loss"));$(".loss").fadeIn('slow');
	});

</script>
<?php #if(!$valid)return true;?>
<div id="content" ng-app="app" ng-controller="ctrlMain">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo
		$breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
	<?php } ?>
	<div class="box">
		<div class="heading">
			<img src="http://images.simonoop.com/simon/simonfilters3/logosf3.png" alt="" />
			<div class="buttons">

			<a href="<?php echo $cancel; ?>" class="btn btn-primary"><?php echo $button_cancel; ?></a>
			</div>
		</div>
		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<div class="content">
				<table class="stuff">
					<tr>
						<td class="nav">
							<div class="top-line"></div>
							<ul>
								<li ng-click="go('/general');sel=1" ng-class="sel==1?'on':''">General Settings</li>
								<li ng-click="go('/layouts');sel=2" ng-class="sel==2?'on':''">Layouts</li>
								<!--<li ng-click="go('/styles');sel=3" ng-class="sel==3?'on':''">Styles</li>-->
								<li ng-click="go('/filters');sel=4" ng-class="sel==4?'on':''">Filters</li>
								<div class="top-line"></div>
								<!--
								<li ng-click="go('/themes');sel=10" ng-class="sel==10?'on':''">Themes</li>
								-->
								<li ng-click="go('/cache');sel=8" ng-class="sel==8?'on':''">Cache</li>
								<li ng-click="go('/js');sel=5" ng-class="sel==5?'on':''">JavaScript</li>
								<li ng-click="go('/3party');sel=9" ng-class="sel==9?'on':''">Third party compatibility</li>
								<div class="top-line"></div>
								<li ng-click="go('/about');sel=6" ng-class="sel==6?'on':''">About</li>
								<!--<li ng-click="go('/hireme');sel=7" ng-class="sel==7?'on':''">Hire me</li>-->
							</ul>
						</td>
						<td class="separator"></td>
						<td class="view">
							<div class="top-line"></div>
							<div ng-view></div>
						</td>
					</tr>
				</table>

			</div>
		</form>
	</div>
</div>
<?php echo $footer; ?>

<?php }?>

