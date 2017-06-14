<!--
SIMONVERSION
-->
<div class="view-title">General Settings
    <div class="generic-buttons">
        <a class="generic-button save" ng-click="save()">save</a>
    </div>
</div>
<table class="form">
    <tr>
        <td>Status:</td>
        <td>
            <sbuttons options="['Enabled','Disabled']" model="general.status"></sbuttons>
        </td>
    </tr>
<?php

/*
    <tr>
        <td>Ajax enabled:</td>
        <td>
            <sbuttons options="['yes','no']" model="general.ajax"></sbuttons>
        </td>
    </tr>
    <tr>
        <td>Search on filter click</td>
        <td>
            <sbuttons options="['yes','no']" model="general.auto"></sbuttons>
        </td>
    </tr>
*/
?>
    <tr>
        <td>Change url hash:</td>
        <td>
            <sbuttons options="['yes','no']" model="general.hash"></sbuttons>
        </td>
    </tr>
    <tr>
        <td>Slider skin:</td>
        <td>
            <sbuttons options="['Flat','HTML5','Modern','Nice','Simple']" model="general.sliderskin"></sbuttons>
        </td>
    </tr>
	<!--
    <tr>
        <td>Filter logic:</td>
        <td>
            <sbuttons options="['AND','OR']" model="general.logic"></sbuttons>
        </td>        
    </tr>
    -->
	<tr>
		<td>Category children:<span class="help">Present filters belonging to the current category's children</span></td>
		<td>
			<sbuttons options="['yes','no']" model="general.children"></sbuttons>
		</td>
	</tr>

    <!--#################################################################################################
    ##
    ## Caption
    ##
    ##################################################################################################-->
    <tr>
        <td>Module Title</td>
        <td>
            <div>
                <input class="sinput"
                       ng-repeat="l in aux_languages"
                       ng-init="current.caption=current.caption||1;general.title[l.language_id]=general.title[l.language_id]||'Filters'"
                       ng-model="general.title[l.language_id]"
                       ng-show="current.caption==l.language_id"/>
            </div>
            <div>
                <img class="language_selector" src="../image/flags/{{l.image}}"
                     ng-repeat="l in aux_languages"
                     ng-click="current.caption=l.language_id"
                     ng-class="current.caption==l.language_id?'':'off'"/>
            </div>
        </td>
    </tr>
    <tr>
        <td>Clear Filters text</td>
        <td>
            <div>
                <input class="sinput"
                       ng-repeat="l2 in aux_languages"
                       ng-init="current.caption2=current.caption2||1;general.cleartitle[l2.language_id]=general.cleartitle[l2.language_id]||'Clear Filters'"
                       ng-model="general.cleartitle[l2.language_id]"
                       ng-show="current.caption2==l2.language_id"/>
            </div>
            <div>
                <img class="language_selector" src="../image/flags/{{l2.image}}"
                     ng-repeat="l2 in aux_languages"
                     ng-click="current.caption2=l2.language_id"
                     ng-class="current.caption2==l2.language_id?'':'off'"/>
            </div>
        </td>
    </tr>

    <tr>
        <td>AJAX method:</td>
        <td>
            <sbuttons options="['GET','POST']" model="general.method"></sbuttons>
        </td>
    </tr>

    <tr>
        <td>After AJAX call, scroll to:</td>
        <td>
            <sbuttons options="['TOP','BOTTOM','NONE']" model="general.scrollto"></sbuttons>
        </td>
    </tr>

    <tr ng-show="Suhosin.present && general.method=='GET'">
        <td>Suhosin char limit</td>
        <td>
            <div><img src="view/simonoop/sf3/image/danger.png"></div>
            <div style="display: inline-block;">You have installed the <a href='https://suhosin.org/stories/index.html' target='_blank'>Suhosin PHP Patch</a>. The max_value_length is currently set at {{Suhosin.max_value_length}} chars. Please set this value to at least 2048 chars.<br>
                Contact your host provider if you're not sure how to change this value.</div>
        </td>
    </tr>
</table>
