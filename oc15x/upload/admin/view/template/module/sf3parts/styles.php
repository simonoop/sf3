
<div class="main-title container-fluid">
    <div class="row">
        <div class="col-md-6">Styles</div>
        <div class="col-md-6 align-right">
        <a class="btn btn-info" ng-click="save()">Save</a>
        </div>
    </div>
</div>
<!--
TODO:
http://codemirror.net/#
-->
<table style="width: 100%;">
    <tr>
        <td style="width:200px"><iframe id="preview" src="{{previewURL}}"></iframe></td>
        <td><textarea style="height: 100%;width:100%;">{{css}}</textarea></td>
    </tr>
</table>

</div>
