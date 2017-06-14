<div class="view-title">Javascript Settings
	<div class="generic-buttons">
		<a class="generic-button save" ng-click="save()">save</a>
	</div>
</div>
<table class="stuff no-spacing">
	<tr>
		<td class="nav">
			<div class="top-line"></div>
			<ul ui-sortable="sortableOptions" ng-model="filters">
				<li ng-click="editorWindow='files'" ng-class="editorWindow=='files'?'on':''">Javascript files</li>
				<!--<li ng-click="editorWindow='inlinescripts'" ng-class="editorWindow=='inlinescripts'?'on':''">Inline scripts</li>-->
			</ul>
		</td>
		<td class="separator"></td>
		<td class="view" ng-show="editorWindow=='files'">
				<div class="top-line"></div>
				<div class="view-title">Javascript files detected</div>
				<table class="form">
					<tr ng-repeat="f in jsfiles.scripts">
						<td>{{f}}</td>
						<td>
							<sbuttons options="['Enabled','Disabled']" default="Disabled" model="js.files[f]"></sbuttons>
						</td>
					</tr>
				</table>

				<div ng-show="jsfiles.error!=''">{{jsfiles.error}}</div>
		</td>
		<?php /*
		<td class="view" ng-show="editorWindow=='inlinescripts'">
			<div class="top-line"></div>
			<div class="view-title">Inline scripts detected</div>
			<table class="form">
				<tr ng-repeat="f in jsfiles.scripts">
					<td>{{f}}</td>
					<td>
						<sbuttons options="['Enabled','Disabled']" default="Disabled" model="js.files[f]"></sbuttons>
					</td>
				</tr>
			</table>
		</td>
 		*/ ?>
	</tr>
</table>

<?php
/*
<!--
SIMONVERSION
-->
<div class="view-title">Javascript Settings
	<div class="generic-buttons">
		<a class="generic-button save" ng-click="save()">save</a>
	</div>
</div>

<div class="sf3-tabs">
	<div ng-click="editorWindow='files'" ng-class="{'active':editorWindow=='files'}" class="active">Javascript files</div>
	<div ng-click="editorWindow='inlinescripts'" ng-class="{'active':editorWindow=='inlinescripts'}">Inline scripts</div>
</div>

<div class="editor" ng-show="editorWindow=='files'">
	<div class="view-title">Javascript files detected</div>
		<table class="form">
		<tr ng-repeat="f in jsfiles.scripts">
			<td>{{f}}</td>
			<td>
				<sbuttons options="['Enabled','Disabled']" default="Disabled" model="js.files[f]"></sbuttons>
			</td>
		</tr>
	</table>
</div>

<div class="editor" ng-show="editorWindow=='inlinescripts'">
	<h2>Inline Javascript code detected:</h2>
	<table class="form">
		<tr ng-repeat="f in jsfiles.scripts">
			<td>{{f}}</td>
			<td>
				<sbuttons options="['Enabled','Disabled']" default="Disabled" model="js.files[f]"></sbuttons>
			</td>
		</tr>
	</table>
</div>
*/
?>