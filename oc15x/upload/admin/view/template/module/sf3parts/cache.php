<div class="view-title">Cache
	<div class="generic-buttons">
		<a class="generic-button save" ng-click="save()">save</a>
	</div>
</div>
<table class="form">
	<tr>
		<td>Database Cache:</td>
		<td>
			<sbuttons options="cacheoptions" model="general.cache.status"></sbuttons>
			<div ng-show="general.cache.status=='memcache'">
                <span ng-show="env.memcache==1">
                    host: <input ng-model="general.cache.memcache.host" class="sinput"/>
                    port: <input ng-model="general.cache.memcache.port" class="sinput"/>
                    <input type="button" value="test connection" class="generic-button" ng-click="testmemcache()"/>

                </span>
			</div>

				<input type="button" value="purge cache" class="generic-button" ng-click="clearcache()" ng-hide="general.cache.status=='none'"/>

		</td>
	</tr>
	<tr>
		<td>AJAX cache:</td>
		<td>
			<sbuttons options="['yes','no']" model="general.ajaxcache"></sbuttons>
		</td>
	</tr>
</table>