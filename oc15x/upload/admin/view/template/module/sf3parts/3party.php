<div class="view-title">Third party compatibility
	<div class="generic-buttons">
		<a class="generic-button save" ng-click="save()">save</a>
	</div>
</div>
<table class="form">
	<tr>
		<td style="width:250px">Support YMM if present:</td>
		<td>
			<sbuttons options="['yes','no']" model="general.ymm"></sbuttons>
		</td>
	</tr>

	<tr>
		<td style="width:250px">Support iSearch (4.1.6):</td>
		<td>
			<sbuttons options="['yes','no']" model="general.isearch"></sbuttons>
		</td>
	</tr>
</table>