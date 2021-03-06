{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{include uri="design:mailing/common/header.tpl" page='edit'}

<form method="post" action={concat( '/mailing/campaigns/edit/', $item.id )|ezurl} name="campaigneditform" class="jqTransform">
	<div id="ezmailing_container">
		<div id="ezmailing_content">
			<h2>{'Edit campaign'|i18n( 'extension/ezmailing/text' )}</h2>
			<div id="ezmailing_edit">
				<div class="ezmailing_edit_left">
					<div class="block">
						<label for="subject">{'Subject'|i18n( 'extension/ezmailing/text' )}<span class="required">*</span></label>
		    			<input type="text" id="subject" name="subject" value="{$item.subject|wash}" maxlength="60"/>
		    			<div class="clear"></div>
	    			</div>
	    			<div class="block">
		    			<label for="sender_name">{'Sender name'|i18n( 'extension/ezmailing/text' )}<span class="required">*</span></label>
	        			<input type="text" id="sender_name" name="sender_name" value="{$item.sender_name|wash}" />
	        			<div class="clear"></div>
	    			</div>
	    			<div class="block">
		    			<label for="sender_email">{'Sender email'|i18n( 'extension/ezmailing/text' )}<span class="required">*</span></label>
	        			<input type="text" name="sender_email" name="sender_email" value="{$item.sender_email|wash}" />
	        			<div class="clear"></div>
	    			</div>
				</div>
				<div class="ezmailing_edit_right">
			        <div class="block">
			        	<label>{'Sending date'|i18n( 'extension/ezmailing/text' )}<span class="required">*</span></label>
					    {def 
					       $base = ezini('eZJSCore', 'LocalScriptBasePath', 'ezjscore.ini')
					       $prefix = "mailing"
					       $suffix = "mailing"
					       $day = $item.sending_date|datetime( 'custom', '%d' )
					       $month = $item.sending_date|datetime( 'custom', '%m' )
					       $year = $item.sending_date|datetime( 'custom', '%Y' )
					       $second = $item.sending_date|datetime( 'custom', '%s' )
					       $minute = $item.sending_date|datetime( 'custom', '%i' )
					       $hour = $item.sending_date|datetime( 'custom', '%H' )
					    }
				        {ezscript_require( 'ezjsc::yui2' )}
				        {ezcss_require( concat( '/', $base.yui2, 'calendar/assets/calendar.css' ) )}
						<script type="text/javascript">
						(function() {ldelim}
						    YUILoader.addModule({ldelim}
						        name: 'datepicker',
						        type: 'js',
						        fullpath: '{"javascript/ezdatepicker.js"|ezdesign( 'no' )}',
						        requires: ["calendar"],
						        after: ["calendar"],
						        skinnable: false
						    {rdelim});
						    YUILoader.require(["datepicker"]);
						    // Load the files using the insert() method.
						    var options = [];
						    YUILoader.insert(options, "js");
						{rdelim})();
						</script>
						<div id="campaign_sending_date" class="blocElement">
							<img class="datepicker-icon" src={"pictos/datepicker.png"|ezimage} id="{$prefix}_datetime_cal_{$suffix}" width="22" height="22" onclick="showDatePicker( '{$prefix}', '{$suffix}', 'datetime' );" />
							<input id="{$prefix}_datetime_year" type="text" name="{$prefix}_datetime_year_{$suffix}" size="5" value="{$year}" />
							<span class="date_separator">/</span>
			                <input id="{$prefix}_datetime_month" type="text" name="{$prefix}_datetime_month_{$suffix}" size="5" value="{$month}" />
			                <span class="date_separator">/</span>
			                <input id="{$prefix}_datetime_day" type="text" name="{$prefix}_datetime_day_{$suffix}" size="5" value="{$day}" />
			                <div id="{$prefix}_datetime_cal_container_{$suffix}" style="display: none; position: absolute;"></div>
			                <div class="clear"></div>
						</div>
						<div class="clear"></div>
			        </div>
					<div class="block">
						<div id="campaign_sending_time" class="blocElement">			                
			                <input id="{$prefix}_datetime_second" type="text" name="{$prefix}_datetime_second_{$suffix}" size="5" value="{$second}" />
			                <span class="date_separator">:</span>
			                <input id="{$prefix}_datetime_minute" type="text" name="{$prefix}_datetime_minute_{$suffix}" size="5" value="{$minute}" />
			                <span class="date_separator">:</span>
			                <input id="{$prefix}_datetime_hour" type="text" name="{$prefix}_datetime_hour_{$suffix}" size="5" value="{$hour}" />
			                <div class="clear"></div>
						</div>
		                <div class="clear"></div>
					</div>
					<div class="block block2">
						<label>{'Last updated state date'|i18n( 'extension/ezmailing/text' )} : </label>
        				<p>{$item.state_updated|l10n( 'shortdatetime' )}</p>
		                <div class="clear"></div>
					</div>
					<div class="block block2">
						<label>{'Created'|i18n( 'extension/ezmailing/text' )} : </label>
        				<p>{$item.created|l10n( 'shortdatetime' )}</p>
		                <div class="clear"></div>
					</div>
					<div class="block block2">
						<label>{'Updated'|i18n( 'extension/ezmailing/text' )} : </label>
        				<p>{$item.updated|l10n( 'shortdatetime' )}</p>
		                <div class="clear"></div>
					</div>
					<div class="block block2" id="compaign_id">
						<label>{'ID'|i18n( 'extension/ezmailing/text' )} : </label>
						<p>{$item.id}</p>
						<div class="clear"></div>
					</div>
			        {undef $base $prefix $suffix $day $month $year $second $minute $hour}
				</div>
				<div class="clear"></div>
				<div class="inlineBlock">
					<div class="left_inlineBlock">
						<label>{'Content'|i18n( 'extension/ezmailing/text' )} :<span class="required">*</span></label>
					</div>
					<div class="right_inlineBlock">
						<table id="campaign_content" cellspacing="0">
							<tbody>
								<tr class="dyn_content">
									<td class="type">
										<input id="content_type_1" type="radio" name="content_type" {if $item.content_type|eq(0)} checked="checked" {/if} value="0" class="jqTransformField" />
										<label>{'eZ Publish'|i18n( 'extension/ezmailing/text' )}</label>
									</td>
									<td class="content">
										{def 
											$contentNode = fetch( 'content', 'node', hash( 'node_id',$item.node_id ))
											$topNodeIDs = ezini('MailingSettings', 'TopNodes', 'ezmailing.ini')
										}
										<select name="node_id" class="jqTransformField customCss customCss2"{if $item.content_type|eq(1)} disabled="disabled" {/if}>     
											{foreach $topNodeIDs as $id}
												{def $children = fetch('content','list',hash('parent_node_id',$id))}
													<optgroup label="{$children.0.parent.name|wash}">
														{foreach $children as $child}
															<option value="{$child.node_id}" {if $child.node_id|eq($item.node_id)} selected="selected" {/if}>{$child.name|wash}</option>            
														{/foreach}
													</optgroup>
												{undef $children}
											{/foreach}
										</select>
								   </td>
								</tr>
								<tr class="bgdark">
									<td class="site">
										<input id="content_type_2" type="radio" name="content_type" {if $item.content_type|eq(1)} checked="checked" {/if} value="1" class="jqTransformField" />
										<label>{'Manual'|i18n( 'extension/ezmailing/text' )}</label>
									</td>
									<td class="url">
										<div>
											<textarea name="manual_content" rows="5" cols="50"{if $item.content_type|eq(0)} disabled="disabled" {/if}>{$item.get_content}</textarea>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="clear"></div>
				</div>
				<div id="ezmailing_record_list">
					<div id="campain_site_access">
						<label>{'Site Access'|i18n( 'extension/ezmailing/text' )}<span class="required">*</span></label>
						<div class="clear"></div>
						<ul>
							{def $count=0}
							{foreach $newsSiteAccessList as $oSiteAccess sequence array('bglight', 'bgdark') as $seq}
							<li class="ezmailing_list_item">
								<div class="site">
									<h3>{$oSiteAccess.siteAccessName|wash}</h3>
								</div>
								<div class="url">
									<a href="{$oSiteAccess.siteAccessUrl|wash}" target="_blank">{$oSiteAccess.siteAccessUrl|wash}</a>
								</div>
								<div class="flag">
									<img src="{$oSiteAccess.siteAccessLocale|flag_icon}" width="18" height="12" alt="{$oSiteAccess.siteAccessLocale}" />&nbsp;{$oSiteAccess.siteAccessLocale}
								</div>
								<div class="ezmailing_checkbox_button">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<input id="siteaccess_{$count}"
                                                        type="radio"
                                                        name="siteaccess"
                                                        {if $oSiteAccess.siteAccessName|eq($item.siteaccess)} checked="checked" {/if}
                                                        value="{$oSiteAccess.siteAccessName|wash}" class="jqTransformField" />
											</td>
										</tr>
									</table>
								</div>
								<div class="clear"></div>		
							</li>
								{set $count=$count|inc}
							{/foreach}
						</ul>
					</div>
					<div id="campain_destinations">
						<label>{'Destination'|i18n( 'extension/ezmailing/text' )}<span class="required">*</span></label>
						<div class="clear"></div>
						<ul>
						{def 
							$destination = $item.destination_mailing_list|explode(':')
							$mainling_lists = fetch('mailing','fetch',hash(
																	   'type','mailinglist',
																	   'sort_by', hash( 'created', 'desc')
																   )
											  )
						}
						{set $count=0}
						{foreach $mainling_lists as $mailingList}
							<li class="ezmailing_list_item">
								<div class="destination">
									<h3>{$mailingList.name|wash}</h3>
								</div>
								<div class="ezmailing_mailing_lists_registration">
									<p class="mailing_list_registrations_count">
										<span>{$mailingList.registrations_count}</span>{'registrations'|i18n( 'extension/ezmailing/text' )}
									</p>
								</div>
								{if is_provided()}
								<div class="ezmailing_mailing_lists_registration">
									<p class="mailing_list_registrations_count">
										<span>{cond($mailingList.remote_id|ne(''), $mailingList.count_remote_registration, 'NA')}</span>{'remote registrations'|i18n( 'extension/ezmailing/text' )}
									</p>
								</div>
								{/if}
								<div class="flag">
									<img src="{$mailingList.lang|flag_icon}" width="18" height="12" alt="{$mailingList.lang}" />&nbsp;{$mailingList.lang}
								</div>
								<div class="ezmailing_checkbox_button">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<input	id="mailinglists_destination_{$count}"
														type="checkbox"
														name="mailinglists_destination[]" 
														{if $destination|contains($mailingList.id)} checked="checked" {/if}
														value="{$mailingList.id}"
														class="jqTransformField" />
											</td>
										</tr>
									</table>
								</div>
								<div class="clear"></div>
							</li>
							{set $count=$count|inc}
			               	{/foreach}
				           	{undef $mainling_lists $destination $count}							
							</ul>
					</div>
				</div>

				<div class="clear"></div>
				<div id="ezmailing_campaign_edit_left" class="ezmailing_edit_left">
                    {if is_provided()}
                        <div class="block">
                            <label for="report_email">{'Report email'|i18n( 'extension/ezmailing/text' )}:<span class="required">*</span></label>
                            <input type="text" id="report_email" name="report_email" value="{$item.report_email|wash}" />
                            <div class="clear"></div>
                        </div>
                    {/if}
					<div class="block">
						<label for="description">{'Description'|i18n( 'extension/ezmailing/text' )}:</label>
						<textarea id="description" name="description" rows="5" cols="50">{$item.description|wash}</textarea>
						<div class="clear"></div>
					</div>
					<div class="block">
						<label for="recurrency">{'Recurrency'|i18n( 'extension/ezmailing/text' )}:</label>
						<input type="text" id="recurrency" name="recurrency" size="2" value="{$item.recurrency.value|wash}"{if $item.content_type|eq(1)} disabled="disabled" {/if} />			            
						<div class="clear"></div>
							<select name="recurrency_period"{if $item.content_type|eq(1)} disabled="disabled" {/if} class="jqTransformField customCss customCss2">
								<option value="day" {if $item.recurrency.period|eq('day')} selected="selected" {/if}>{'days'|i18n( 'extension/ezmailing/text' )}</option>
								<option value="week" {if $item.recurrency.period|eq('week')} selected="selected" {/if}>{'weeks'|i18n( 'extension/ezmailing/text' )}</option>
							</select>
						<div class="clear"></div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<span id="store_record">
		<button type="submit" name="StoreCampaignButton" title="{'Edit this campaign.'|i18n( 'extension/ezmailing/text' )}">{'Store'|i18n( 'extension/ezmailing/text' )}</button>
	</span>
</form>

{include uri="design:mailing/common/footer.tpl"}