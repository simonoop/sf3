<div class="view-title">Layouts
    <div class="generic-buttons">
        <a class="generic-button save" ng-click="save()">save</a>
    </div>
</div>
<table class="form">
    <tr ng-repeat="(idx,m) in modules">
        <td>{{m.name}}</td>
        <td>
            <sbuttons ng-init="m.status=m.status||'Disabled'" options='[{"id":"0","text":"Disabled"},{"id":"1","text":"Enabled"}]' key="id" text="text" model="m.status"></sbuttons>
            <div ng-show="m.status=='1'">
                <label>Position</label>
                <select class="sselect"
                    ng-init="m.position=m.position||'column_left'"
                    ng-options="p as p|toLabel for p in ['content_top','content_bottom', 'column_left', 'column_right']"
                    ng-model="m.position">
                </select>          
            </div>  
            <div ng-show="m.status=='1'">
                <label>Sort Order:</label>
                <input class="sinput" ng-model="m.sort_order" ng-init="m.sort_order=m.sort_order||0"/>
            </div>
            <div ng-show="m.status=='1'">
                <label>DOM object:</label>
                <input class="sinput" ng-model="m.domid" ng-init="m.domid=m.domid||'#content'"/> ( Use ";" to separate multiples )
            </div>            
        </td>
    </tr>
</table>
