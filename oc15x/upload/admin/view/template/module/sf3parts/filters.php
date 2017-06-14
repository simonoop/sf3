<div class="view-title">Filters
	<div class="generic-buttons">
		<a class="generic-button new" ng-click="new()">new</a> |
		<a class="generic-button save" ng-click="save()">save</a>
	</div>
</div>
<table class="stuff no-spacing">
  <tr>
	<td class="nav">
		<div class="top-line"></div>
		<ul ui-sortable="sortableOptions" ng-model="filters">
			<li ng-repeat="f_item in filters" ng-click="setFilterIdx($index)" ng-class="filter_idx==$index?'on':''">
				{{getDisplayName(f_item)}} ({{f_item.source.type}})
				<div class="f_status {{f_item.status}}"></div>

			</li>
		</ul>
	</td>
	<td class="separator"></td>
	<td class="view">
		<div ng-hide="filter_idx"></div>
		<span ng-repeat="f_item in filters" ng-if="filter_idx==$index" ng-init="editorWindow='core settings'">
			<div class="top-line"></div>
			<div class="view-title">{{getDisplayName(f_item)}}-{{f.source.type||'n/a'}} <span ng-show="in_array(f.type,['option','attribute'])">({{f.type}})</span>
				<div class="generic-buttons">

					<a class="generic-button remove" ng-click="remove(filter_idx)">remove</a>
				</div>
			</div>

			<div class="sf3-tabs">
				<div ng-click="editorWindow='core settings'" ng-class="{'active':editorWindow=='core settings'}">Core settings</div>
				<div ng-click="editorWindow='shared settings'" ng-class="{'active':editorWindow=='shared settings'}">Secondary settings</div>
				<!--<div ng-click="editorWindow='specific settings'" ng-class="{'active':editorWindow=='specific settings'}">Specific settings</div>-->
				<div ng-click="editorWindow='placement'" ng-class="{'active':editorWindow=='placement'}">Placement</div>
				<div ng-show="f.source.type!='price'" ng-click="editorWindow='sorting'" ng-class="{'active':editorWindow=='sorting'}">Sorting</div>
			</div>

<!--#################################################################################################
#####################################################################################################
#####################################################################################################
##
## core settings
##
#####################################################################################################
#####################################################################################################
##################################################################################################-->

			<div class="editor" ng-show="editorWindow=='core settings'">
			<table class="form">
				<!--#################################################################################################
				##
				## status
				##
				##################################################################################################-->
				<tr>
					<td>Status</td>
					<td>
						<sbuttons default="yes" options="['yes', 'no']" model="f.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Source
				##
				##################################################################################################-->
				<tr>
					<td>Source</td>
					<td>
						<sbuttons
							ng-click="f.source.data[f.source.type].name=( (f.source.type=='options' || f.source.type=='attributes')?(f.source.data[f.source.type].name||'n/a'):(f.source.type) )" default="options" options="['options','attributes','categories','tags','manufacturers','stock','ocfilters','price']" model="f.source.type"></sbuttons>
					</td>
				</tr>

				<!--#################################################################################################
				##
				## attribute groups
				##
				##################################################################################################-->
				<tr ng-if="f.source.type=='attributes'">
					<td>Attribute Group</td>
					<td>
							<select class="sselect"
								ng-model="f.source.data[f.source.type].attribute_group_id" 
								ng-options="ag.attribute_group_id as ag.name for ag in aux_attributes_groups" 
								ang-change="f.source.data[f.source.type].name=(aux_attributes|filter:{id:f.source.data[f.source.type].id})[0].name"></select>
					</td>
				</tr>

				<!--#################################################################################################
				##
				## Source name
				##
				##################################################################################################-->
				<tr ng-if="f.source.type=='attributes' || f.source.type=='options'">
					<td>
						<section ng-if="f.source.type=='attributes'">Attribute Name</section>
						<section ng-if="f.source.type=='options'">Option Name</section>
					</td>
					<td>
							<select ng-if="f.source.type=='attributes'" class="sselect"
								ng-model="f.source.data[f.source.type].id" 
								ng-options="a.id as a.name for a in aux_attributes|filter:{attribute_group_id:f.source.data[f.source.type].attribute_group_id}" ng-change="f.source.data[f.source.type].name=(aux_attributes|filter:{id:f.source.data[f.source.type].id})[0].name"></select>
							<select ng-if="f.source.type=='options'" class="sselect" ng-model="f.source.data[f.source.type].id"
								ng-options="o.id as o.name for o in aux_options" 
								ng-change="f.source.data[f.source.type].name=(aux_options|filter:{id:f.source.data[f.source.type].id})[0].name"></select>
					</td>
				</tr>

				<!--#################################################################################################
				##
				## Source name
				##
				##################################################################################################-->

				<tr ng-if="f.source.type=='stock'">
					<td>Default "in stock" status</td>
					<td>
						<select class="sselect"
								ng-model="f.source.data[f.source.type].in_stock_stock_status_id"
								ng-options="ss.stock_status_id as ss.name for ss in aux_stock_status"></select>
					</td>
				</tr>

				<!--#################################################################################################
				##
				## Caption
				##
				##################################################################################################-->
				<tr>
					<td>Caption</td>
					<td>
						<div>
							<input class="sinput"
								ng-repeat="l in aux_languages"
								ng-init="current.caption=current.caption||1;f.caption[l.language_id]=f.caption[l.language_id]||'n/a'"
								ng-model="f.caption[l.language_id]"
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

				<!--#################################################################################################
				##
				## Render
				##
				##################################################################################################-->
				<tr>
					<td>Render Type</td>
					<td>
						<sbuttons default="radio" ng-if="f.source.type=='price'" options="['radio', 'checkbox', 'dropdown', 'slider']" model="f.render"></sbuttons>
						<sbuttons default="radio" ng-if="f.source.type!='price'" options="['radio', 'checkbox', 'dropdown', 'slider', 'image list', 'image mosaic']" model="f.render"></sbuttons>
					</td>
				</tr>

				<!--#################################################################################################
				##
				## Thousands separator
				##
				##################################################################################################-->
				<tr ng-show="f.source.type=='price' && f.render=='slider'">
					<td>Thousands separator</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.self.price.thousands.status"></sbuttons>
						<div ng-if="f.self.price.thousands.status!='no'">
							<label>Char:</label>
							<input ng-init="f.self.price.thousands.char= f.self.price.thousands.char|| ','" ng-model="f.self.price.thousands.char" class="sinput small"/>
						</div>
					</td>
				</tr>

				<!--#################################################################################################
				##
				## Image source
				##
				##################################################################################################-->
				<tr ng-show="(f.source.type=='attributes' || f.source.type=='options')&&(f.render=='image list' || f.render=='image mosaic')">
					<td>Image source</td>
					<td>
						<sbuttons ng-if="f.source.type=='options'" default="option image" options="['option image', 'value as CSS color']" model="f.images.source"></sbuttons>
						<sbuttons ng-if="f.source.type=='attributes'" default="value as CSS color" options="['value as CSS color']" model="f.images.source"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Collapsible
				##
				##################################################################################################-->
				<tr>
					<td>Collapsible</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.settings.collapsible.status"></sbuttons>
						<sbuttons ng-if="f.settings.collapsible.status=='yes'" label="default:" default="expanded" options="['expanded', 'collapsed']" model="f.settings.collapsible.default"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## loading delay
				##
				##################################################################################################-->
				<tr>
					<td>Loading delay (milliseconds)</td>
					<td>
						<sf3-slider ng-init="f.settings.delay.custom=f.settings.delay.custom||0" model="f.settings.delay.custom" options="{min:0,max:2500,step:100}"></sf3-slider>
					</td>
				</tr>
			</table>
			</div>

<!--#################################################################################################
#####################################################################################################
#####################################################################################################
##
## Shared settings
##
#####################################################################################################
#####################################################################################################
##################################################################################################-->            
			<div class="editor" ng-show="editorWindow=='shared settings'">
			<table class="form">
				<!--#################################################################################################
				##
				## Help
				##
				##################################################################################################-->
				<tr>
					<td>Show Help</td>
					<td>
						<sbuttons default="no" options="['no','text','link']" model="f.settings.help.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Help data - text
				##
				##################################################################################################-->
				<tr ng-show="f.settings.help.status!='no' && f.settings.help.status=='text'">
					<td>Help Data</td>
					<td>
						<div>
							<input class="sinput"
								ng-repeat="l in aux_languages"
								ng-init="current.caption_help_text=current.caption_help_text||1;f.settings.help_text[l.language_id]=f.settings.help_text[l.language_id]||''"
								ng-model="f.settings.help_text[l.language_id]"
								ng-show="current.caption_help_text==l.language_id"/>
						</div>
						<div>
							<img class="language_selector" src="../image/flags/{{l.image}}"
								ng-repeat="l in aux_languages"
								ng-click="current.caption_help_text=l.language_id"
								ng-class="current.caption_help_text==l.language_id?'':'off'"/>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Help data - link
				##
				##################################################################################################-->
				<tr ng-show="f.settings.help.status!='no' && f.settings.help.status=='link'">
					<td>Help Link</td>
					<td>
						<div ng-if="f.settings.help.status=='link'">
							<input ng-init="f.settings.help.link= f.settings.help.link|| ''" ng-model="f.settings.help.link" class="sinput big"/>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Show product totals
				##
				##################################################################################################-->
				<tr ng-if="f.source.type!='price'">
					<td>Show product totals</td>
					<td>
						<sbuttons default="yes" options="['yes', 'no']" model="f.settings.totals.status"></sbuttons>
						<sbuttons default="both" ng-if="f.settings.totals.status!='no' && f.render!='dropdown'" options="['numbers', 'bars','both']" model="f.settings.totals.type"></sbuttons>
						<div ng-if="f.settings.totals.status=='yes' && (f.render=='radio' || f.render=='checkbox') && (f.settings.totals.type=='bars' || f.settings.totals.type=='both')">
							<label>Bar width in px:</label>
							<input ng-init="f.settings.totals.barwidth = f.settings.totals.barwidth || '30'" ng-model="f.settings.totals.barwidth" class="sinput"/>
						</div>
					</td>
				</tr>
  
				<!--#################################################################################################
				##
				## Show Search Box
				##
				##################################################################################################-->
				<tr ng-show="f.render=='radio' || f.render=='checkbox' || f.render=='slider'">
					<td>Show search box</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.settings.searchbox.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## When product total is zero
				##
				##################################################################################################-->
				<tr ng-show="f.render!='slider'">
					<td>Filters with zero count</td>
					<td>
						<sbuttons ng-if="f.render!='dropdown'" default="normal" options="['normal', 'hide', 'greyout']" model="f.settings.zeroCount.status"></sbuttons>
						<sbuttons ng-if="f.render=='dropdown'" default="normal" options="['normal', 'hide']" model="f.settings.zeroCount.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Show more less/scrollbars
				##
				##################################################################################################-->
				<tr ng-show="f.render=='radio' || f.render=='checkbox' || f.render=='image list'">
					<td>Show more/less or scrollbars</td>
					<td>
						<sbuttons default="no" options="['no','more/less','scrollbar']" model="f.settings.showmore.status"></sbuttons>
						<div ng-if="f.settings.showmore.status!='no'">
							<label>Number of items to show:</label>
							<input ng-init="f.settings.showmore.height = f.settings.showmore.height || '5'" ng-model="f.settings.showmore.height" class="sinput"/>
						</div>
					</td>
				</tr>

				<!--#################################################################################################
				##
				## More/less captions
				##
				##################################################################################################-->
				<tr data-ng-show="f.settings.showmore.status=='more/less'">
					<td>More text</td>
					<td>
						<div>
							<input class="sinput"
								   ng-repeat="lmore in aux_languages"
								   ng-init="current.captionmore=current.caption||1;f.captionmore[lmore.language_id]=f.captionmore[lmore.language_id]||'more'"
								   ng-model="f.captionmore[lmore.language_id]"
								   ng-show="current.captionmore==lmore.language_id"/>
						</div>
						<div>
							<img class="language_selector" src="../image/flags/{{lmore.image}}"
								 ng-repeat="lmore in aux_languages"
								 ng-click="current.captionmore=lmore.language_id"
								 ng-class="current.captionmore==lmore.language_id?'':'off'"/>
						</div>
					</td>
				</tr>


				<!--#################################################################################################
				##
				## More/less captions
				##
				##################################################################################################-->
				<tr data-ng-show="f.settings.showmore.status=='more/less'">
					<td>Less text</td>
					<td>
						<div>
							<input class="sinput"
								   ng-repeat="lless in aux_languages"
								   ng-init="current.captionless=current.caption||1;f.captionless[lless.language_id]=f.captionless[lless.language_id]||'less'"
								   ng-model="f.captionless[lless.language_id]"
								   ng-show="current.captionless==lless.language_id"/>
						</div>
						<div>
							<img class="language_selector" src="../image/flags/{{lless.image}}"
								 ng-repeat="lless in aux_languages"
								 ng-click="current.captionless=lless.language_id"
								 ng-class="current.captionless==lless.language_id?'':'off'"/>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Split By Char
				##
				##################################################################################################-->
				<tr ng_show="f.source.type=='attributes'">
					<td>Split by Char</td>
					<td>
						<sbuttons default="no" options="['yes','no']" model="f.self.attributes.split.status"></sbuttons>
						<div ng-if="f.self.attributes.split.status=='yes'"><input ng-init="f.self.attributes.split.char=f.self.attributes.split.char||'|'" ng-model="f.self.attributes.split.char" class="sinput"/></div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Slider "Select All"
				##
				##################################################################################################-->
				<tr ng-if="f.render=='slider' && f.source.type!='price'">
					<td>Slider "Select all"</td>
					<td>

						<div>
							<input class="sinput"
								ng-repeat="l in aux_languages"
								ng-init="current.sliderSelectAll=current.sliderSelectAll||1;f.settings.sliderSelectAll[l.language_id]=f.settings.sliderSelectAll[l.language_id]||'all'"
								ng-model="f.settings.sliderSelectAll[l.language_id]"
								ng-show="current.sliderSelectAll==l.language_id"/>
						</div>
						<div>
							<img class="language_selector" src="../image/flags/{{l.image}}"
								ng-repeat="l in aux_languages"
								ng-click="current.sliderSelectAll=l.language_id"
								ng-class="current.sliderSelectAll==l.language_id?'':'off'"/>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## priceslider prefix
				##
				##################################################################################################-->
				<tr ng-if="f.source.type=='price'">
					<td>Show price slider data prefix</td>
					<td>
						<sbuttons default="none" options="['none','currency prefix']" model="f.self.price.slider.prefix.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## priceslider postfix
				##
				##################################################################################################-->
				<tr ng-if="f.source.type=='price'">
					<td>Show price slider data postfix</td>
					<td>
						<sbuttons default="none" options="['none','currency postfix']" model="f.self.price.slider.postfix.status"></sbuttons>
					</td>
				</tr>
   
				<!--#################################################################################################
				##
				## Dropdown initial value
				##
				##################################################################################################-->
				<tr ng-show="f.render=='dropdown'">
					<td>Dropdown initial text</td>
					<td>

						<div>
							<input class="sinput"
								ng-repeat="l in aux_languages"
								ng-init="current.dropDownInitialValue=current.dropDownInitialValue||1;f.self.dropdown.defaultText[l.language_id]=f.self.dropdown.defaultText[l.language_id]||''"
								ng-model="f.self.dropdown.defaultText[l.language_id]"
								ng-show="current.dropDownInitialValue==l.language_id"/>
						</div>
						<div>
							<img class="language_selector" src="../image/flags/{{l.image}}"
								ng-repeat="l in aux_languages"
								ng-click="current.dropDownInitialValue=l.language_id"
								ng-class="current.dropDownInitialValue==l.language_id?'':'off'"/>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Price slider step
				##
				##################################################################################################-->
				<tr ng-show="f.render=='slider' && f.source.type=='price'">
					<td>Price slider step</td>
					<td>
						<div>
							<input class="sinput"
								ng-repeat="l in aux_currencies"
								ng-init="current.priceStep=current.priceStep||1;f.self.price.slider.step[l.currency_id]=f.self.price.slider.step[l.currency_id]||'1'"
								ng-model="f.self.price.slider.step[l.currency_id]"
								ng-show="current.priceStep==l.currency_id"/>
						</div>
						<div>
							<div style="display: inline-block;" class="language_selector" ng-repeat="l in aux_currencies" ng-click="current.priceStep=l.currency_id" ng-class="current.priceStep==l.currency_id?'':'off'">{{l.symbol_left}}{{l.code}}{{l.symbol_right}}</div>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Price intervals
				##
				##################################################################################################-->
				<tr ng-show="f.render!='slider' && f.source.type=='price'">
					<td>Number of price intervals</td>
					<td>
						<div>
							<input class="sinput"
								ng-repeat="l in aux_currencies"
								ng-init="current.priceIntervals=current.priceIntervals||1;f.self.price.intervals[l.currency_id]=f.self.price.intervals[l.currency_id]||'5'"
								ng-model="f.self.price.intervals[l.currency_id]"
								ng-show="current.priceIntervals==l.currency_id"/>
						</div>
						<div>
							<div style="display: inline-block;" class="language_selector" ng-repeat="l in aux_currencies" ng-click="current.priceIntervals=l.currency_id" ng-class="current.priceIntervals==l.currency_id?'':'off'">{{l.symbol_left}}{{l.code}}{{l.symbol_right}}</div>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Price slider decimal places
				##
				##################################################################################################-->
				<tr ng-show="f.source.type=='price'">
					<td>Price slider decimal places</td>
					<td>
						<div>
							<input class="sinput"
								ng-repeat="l in aux_currencies"
								ng-init="current.priceDecimalPlaces=current.priceDecimalPlaces||1;f.self.price.decimalPlaces[l.currency_id]=f.self.price.decimalPlaces[l.currency_id]||'0'"
								ng-model="f.self.price.decimalPlaces[l.currency_id]"
								ng-show="current.priceDecimalPlaces==l.currency_id"/>
						</div>
						<div>
							<div style="display: inline-block;" class="language_selector" ng-repeat="l in aux_currencies" ng-click="current.priceDecimalPlaces=l.currency_id" ng-class="current.priceDecimalPlaces==l.currency_id?'':'off'">{{l.symbol_left}}{{l.code}}{{l.symbol_right}}</div>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Show slider grid
				##
				##################################################################################################-->
				<tr ng-show="f.render=='slider'">
					<td>Show slider grid</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.settings.slider.grid.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Hide slider min & max
				##
				##################################################################################################-->
				<tr ng-show="f.render=='slider'">
					<td>Hide slider min & max</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.settings.slider.hide_min_max.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Hide slider from_to
				##
				##################################################################################################-->
				<tr ng-show="f.render=='slider'">
					<td>Hide slider from & to</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.settings.slider.hide_from_to.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Price specials
				##
				##################################################################################################-->
				<tr ng-show="f.source.type=='price'">
					<td>Enable special prices</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.self.price.specials.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Price discounts
				##
				##################################################################################################-->
				<tr ng-show="f.source.type=='price'">
					<td>Enable discount prices</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.self.price.discount.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Price tax
				##
				##################################################################################################-->
				<tr ng-show="f.source.type=='price'">
					<td>Apply taxes</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.self.price.tax.status"></sbuttons>
						<div ng-if="f.self.price.tax.status!='no'">
							<label>Tax Class:</label>
							<select class="sselect"
									ng-model="f.self.price.tax.tax_id"
									ng-options="t.tax_class_id as t.title for t in aux_taxes"></select>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Mutually Exclusive
				##
				##################################################################################################-->
				<tr ng-show="f.source.type!='price'">
					<td>Mutually Exclusive</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.settings.exclusive.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Price is affected by other filters
				##
				##################################################################################################-->
				<!--
				<tr ng-show="f.source.type=='price'">
					<td>Price filter is affected by other filters</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.self.price.dynamic.status"></sbuttons>
					</td>
				</tr>
				-->
				<!--#################################################################################################
				##
				## Show current category
				##
				##################################################################################################-->
				<tr ng-show="f.source.type=='categories'">
					<td>Show current category</td>
					<td>
						<sbuttons default="yes" options="['yes', 'no']" model="f.settings.showcurrentcategory.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Group child categories
				##
				##################################################################################################-->
				<tr ng-show="f.source.type=='categories'">
					<td>Group child categories</td>
					<td>
						<sbuttons default="yes" options="['yes', 'no']" model="f.settings.groupchildcategories.status"></sbuttons>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Show brands in brand page
				##
				##################################################################################################-->
				<tr ng-show="f.source.type=='manufacturers'">
					<td>Show brands in brand page</td>
					<td>
						<sbuttons default="yes" options="['yes', 'no']" model="f.settings.showmaninmanpage.status"></sbuttons>
					</td>
				</tr>

				<!--#################################################################################################
				##
				## Image/Mosaic dimensions
				##
				##################################################################################################-->
				<tr ng-if="f.render=='image list' || f.render=='image mosaic'">
					<td>Image / mosaic item dimensions</td>
					<td>

						<div>
							<input class="sinput" ng-init="f.images.width=f.images.width||'20px'" ng-model="f.images.width"/>width (px, %, em)
						</div>
						<div>
							<input class="sinput" ng-init="f.images.height=f.images.height||'20px'" ng-model="f.images.height"/>height (px, %, em)
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## External reference
				##
				##################################################################################################-->
				<tr ng_show="f.source.type=='attributes'">
					<td>External reference table</td>
					<td>
						<sbuttons default="no" options="['yes','no']" model="f.self.attributes.external.status"></sbuttons>
						<div ng-show="f.self.attributes.external.status=='yes'">
							<select ng-options="t.name as t.name for t in aux_oc_tables" ng-model="f.self.attributes.external.table" TODOng-change="getTableFields(f.self.attributes.external.table)" class="sselect"></select>
							<label>Look up field:</label>
							<input class="sinput" ng-model="f.self.attributes.external.link_field">
							<label>Data field:</label>
							<input class="sinput" ng-model="f.self.attributes.external.link_data">
						</div>

					</td>
				</tr>

				<!--#################################################################################################
				##
				## persistence
				##
				##################################################################################################-->
				<tr ng-show="f.render!='slider'">
					<td>Persist selection while browsing</td>
					<td>
						<sbuttons default="no" options="['yes', 'no']" model="f.self.persist.status"></sbuttons>
					</td>
				</tr>

			</table>
			</div>
<!--#################################################################################################
#####################################################################################################
#####################################################################################################
##
## placement settings
##
#####################################################################################################
#####################################################################################################
##################################################################################################-->     
			<div class="editor" ng-show="editorWindow=='placement'">
			<table class="form">
			   <!--#################################################################################################
				##
				## Placement - Stores
				##
				##################################################################################################-->
				<tr>
					<td>Placement - Stores</td>
					<td>{{x}}
						<sbuttons default="all" options="['all','custom']" model="f.placement.stores.status"></sbuttons>
						<div ng-show="f.placement.stores.status=='custom'" class="placement-selector">
							<div class="placement-list">
								<ul>
									<li ng-repeat="s in aux_stores">
										<label><input type="checkbox" ng-model="f.placement.stores.data[s.store_id]"/>{{s.name}}</label>
									</li>
								</ul>
									
							</div>
						</div>
					</td>
				</tr>
			   <!--#################################################################################################
				##
				## Placement - Categories
				##
				##################################################################################################-->
				<tr>
					<td>Placement - Categories</td>
					<td>
						<sbuttons default="all" options="['all','custom']" model="f.placement.categories.status"></sbuttons>

						<div ng-show="f.placement.categories.status=='custom'" class="placement-selector">
							<select class="sselect" ng-options="c as c.name for c in aux_categories" ng-model="cp"></select>
							<a ng-click="addCategory({job:'addone', p:cp})" class="generic-button">Add</a>
							<a ng-click="addCategory({job:'addall'})" class="generic-button">Add All</a>
							<a ng-click="addCategory({job:'clearall'})"class="generic-button">Clear All</a>
							<div class="placement-list">
								<ul>
									<li ng-repeat="c in f.placement.categories.data">{{c.name}} <span class="remover" ng-click="genericRemove(f.placement.categories.data,$index)">[remove]</span></li>
								</ul>
							</div>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Placement - Manufacturers
				##
				##################################################################################################-->
				<tr>
					<td>Placement - Manufacturers</td>
					<td>{{x}}
						<sbuttons default="all" options="['all','custom']" model="f.placement.manufacturers.status"></sbuttons>
						<div ng-show="f.placement.manufacturers.status=='custom'" class="placement-selector">
							<select class="sselect" ng-options="c as c.name for c in aux_manufacturers" ng-model="mp"></select>
							<input type="button" ng-click="addManufacturer({job:'addone', p:mp})" value="add"/>
							<input type="button" ng-click="addManufacturer({job:'addall'})" value="add all"/>
							<input type="button" ng-click="addManufacturer({job:'clearall'})" value="clear all"/>
							<div class="placement-list">
								<ul>
									<li ng-repeat="c in f.placement.manufacturers.data">{{c.name}} <span class="remover" ng-click="genericRemove(f.placement.manufacturers.data,$index)">[remove]</span></li>
								</ul>
							</div>
						</div>
					</td>
				</tr>
				<!--#################################################################################################
				##
				## Placement - Layouts
				##
				##################################################################################################-->
				<tr>
					<td>Placement - Layouts</td>
					<td>{{x}}
						<sbuttons default="all" options="['all','custom']" model="f.placement.layouts.status"></sbuttons>
						<div ng-show="f.placement.layouts.status=='custom'" class="placement-selector">
							<select class="sselect" ng-options="c as c.name for c in modules" ng-model="lp"></select>
							<input type="button" ng-click="addLayout({job:'addone', p:lp})" value="add"/>
							<input type="button" ng-click="addLayout({job:'addall'})" value="add all"/>
							<input type="button" ng-click="addLayout({job:'clearall'})" value="clear all"/>
							<div class="placement-list">
								<ul>
									<li ng-repeat="c in f.placement.layouts.data">{{c.name}} <span class="remover" ng-click="genericRemove(f.placement.layouts.data,$index)">[remove]</span></li>
								</ul>
							</div>
						</div>
					</td>
				</tr>
			</table>
			</div>
<!--#################################################################################################
#####################################################################################################
#####################################################################################################
##
## sorting settings
##
#####################################################################################################
#####################################################################################################
##################################################################################################-->     
			<div class="editor" ng-show="editorWindow=='sorting'">
			<table class="form">
				<!--#################################################################################################
				##
				## Sorting
				##
				##################################################################################################-->
				<tr>
					<td>Sorting</td>
					<td>
						<sbuttons default="none" options="['none', 'alphabetically', 'numeric']" model="f.settings.sorting.status"></sbuttons>
						<sbuttons default="ASC" ng-if="f.settings.sorting.status!='none'" options="['ASC', 'DESC']" model="f.settings.sorting.order"></sbuttons>
					</td>
				</tr>

			</table>
			</div>
		</span>
	</td>
  </tr>
</table>
