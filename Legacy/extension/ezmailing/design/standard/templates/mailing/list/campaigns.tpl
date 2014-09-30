{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{def $called_in_dashboard=false()}
{if and(is_set($embed),$embed)}
	{set $called_in_dashboard=true()}
{/if}
{if is_unset($show_search_form)}
	{def $show_search_form = true()}
{/if}
{if $called_in_dashboard|not()}
<div id="ezmailing_container">
    <div id="ezmailing_content">
{/if}
    {if $show_search_form}
        <h2>{"Campaign list"|i18n('extension/ezmailing/text')}</h2>
		{*start states*}
        <div id="ezmailing_item_states">        
            <h3>{"Show only campaigns with this selected state :"|i18n('extension/ezmailing/text')}</h3>
            <ul>
                <li id="draft">
                    <a  href={"mailing/campaigns/drafts"|ezurl} 
                        alt="{"Drafts"|i18n('extension/ezmailing/text')}">
                        {if and(is_set($subaction),$subaction|eq('drafts'))}
                            <img    src={"pictos/draft_state_on.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Draft"|i18n('extension/ezmailing/text')}" />
                            <span>{"Drafts"|i18n('extension/ezmailing/text')}</span>
                        {else}
                            <img    src={"pictos/draft_state_off.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Draft"|i18n('extension/ezmailing/text')}" />
                            <span class="off">{"Drafts"|i18n('extension/ezmailing/text')}</span>
                        {/if}
                    </a>
                </li>
                <li id="tested">
                    <a href={"mailing/campaigns/tested"|ezurl}>
                        {if and(is_set($subaction),$subaction|eq('tested'))}
                            <img    src={"pictos/tested_state_on.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Tested"|i18n('extension/ezmailing/text')}" />
                            <span>{"Tested"|i18n('extension/ezmailing/text')}</span>
                        {else}
                            <img    src={"pictos/tested_state_off.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Tested"|i18n('extension/ezmailing/text')}" />
                            <span class="off">{"Tested"|i18n('extension/ezmailing/text')}</span>
                        {/if}
                    </a>
                </li>
                <li id="confirmed">
                    <a href={"mailing/campaigns/confirmed"|ezurl}>
                        {if and(is_set($subaction),$subaction|eq('confirmed'))}
                            <img    src={"pictos/confirmed_state_on.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Confirmed"|i18n('extension/ezmailing/text')}" />
                            <span>{"Confirmed"|i18n('extension/ezmailing/text')}</span>
                        {else}
                            <img    src={"pictos/confirmed_state_off.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Confirmed"|i18n('extension/ezmailing/text')}" />
                            <span class="off">{"Confirmed"|i18n('extension/ezmailing/text')}</span>
                        {/if}
                    </a>
                </li>
                <li id="waiting">
                    <a href={"mailing/campaigns/waiting"|ezurl}>
                        {if and(is_set($subaction),$subaction|eq('waiting'))}
                            <img    src={"pictos/waiting_state_on.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Waiting"|i18n('extension/ezmailing/text')}" />
                            <span>{"Waiting"|i18n('extension/ezmailing/text')}</span>
                        {else}
                            <img    src={"pictos/waiting_state_off.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Waiting"|i18n('extension/ezmailing/text')}" />
                            <span class="off">{"Waiting"|i18n('extension/ezmailing/text')}</span>
                        {/if}
                    </a>
                </li>
                <li id="sending">
                    <a href={"mailing/campaigns/sending"|ezurl}>
                        {if and(is_set($subaction),$subaction|eq('sending'))}
                            <img    src={"pictos/sending_state_on.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Sending"|i18n('extension/ezmailing/text')}" />
                            <span>{"Sending"|i18n('extension/ezmailing/text')}</span>
                        {else}
                            <img    src={"pictos/sending_state_off.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Sending"|i18n('extension/ezmailing/text')}" />
                            <span class="off">{"Sending"|i18n('extension/ezmailing/text')}</span>
                        {/if}
                    </a>
                </li>
                <li id="sent">
                    <a href={"mailing/campaigns/sent"|ezurl}>
                        {if and(is_set($subaction),$subaction|eq('sent'))}
                            <img    src={"pictos/sent_state_on.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Sent"|i18n('extension/ezmailing/text')}" />
                            <span>{"Sent"|i18n('extension/ezmailing/text')}</span>
                        {else}
                            <img    src={"pictos/sent_state_off.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Sent"|i18n('extension/ezmailing/text')}" />
                            <span class="off">{"Sent"|i18n('extension/ezmailing/text')}</span>
                        {/if}
                    </a>
                </li>
                <li id="cancelled">                
                    <a href={"mailing/campaigns/cancelled"|ezurl}>
                        {if and(is_set($subaction),$subaction|eq('cancelled'))}
                            <img    src={"pictos/cancelled_state_on.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Cancelled"|i18n('extension/ezmailing/text')}" />
                            <span>{"Cancelled"|i18n('extension/ezmailing/text')}</span>
                        {else}
                            <img    src={"pictos/cancelled_state_off.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Cancelled"|i18n('extension/ezmailing/text')}" />
                            <span class="off">{"Cancelled"|i18n('extension/ezmailing/text')}</span>
                        {/if}
                    </a>
                </li>
                <li id="deleted">
                    <a href={"mailing/campaigns/deleted"|ezurl}>
                        {if and(is_set($subaction),$subaction|eq('deleted'))}
                            <img    src={"pictos/deleted_state_on.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Deleted"|i18n('extension/ezmailing/text')}" />
                            <span>{"Deleted"|i18n('extension/ezmailing/text')}</span>
                        {else}
                            <img    src={"pictos/deleted_state_off.png"|ezimage()} 
                                    width="55" 
                                    height="55" 
                                    alt="{"Deleted"|i18n('extension/ezmailing/text')}" />
                            <span class="off">{"Deleted"|i18n('extension/ezmailing/text')}</span>
                        {/if}
                    </a>
                </li>
            </ul>
            <div class="clear"></div>
        </div>
		{*end states*}
		{*start search form*}
        <div id="ezmailing_search_form">
            <form   method="post" 
                    name="campaignlistfeatures" 
                    id="campaignlistfeatures"
                    class="jqTransform">
                <div id="ezmailing_sort">
                    <select name="order_field" 
                            id="order_field" 
                            info="{'Please select order field'|i18n( 'extension/ezmailing/text' )}"
                            class="jqTransformField customCss">
                        <option value="#">
                            {'ORDER BY'|i18n( 'extension/ezmailing/text' )}
                        </option>
                        <option value="subject"
                                dir="asc"
                                {if $view_parameters.order_field|eq("subject")}
                                    selected="selected"
                                {/if}>
                            {'SUBJECT'|i18n( 'extension/ezmailing/text' )}
                        </option>
                        <option value="sender_name"
                                dir="asc"
                                {if $view_parameters.order_field|eq("sender_name")}
                                    selected="selected"
                                {/if}>
                            {'SENDER NAME'|i18n( 'extension/ezmailing/text' )}
                        </option>
                        <option value="sender_email"
                                dir="asc"
                                {if $view_parameters.order_field|eq("sender_email")}
                                    selected="selected"
                                {/if}>
                            {'SENDER EMAIL'|i18n( 'extension/ezmailing/text' )}
                        </option>
                        <option value="sending_date"
                                dir="desc"
                                {if $view_parameters.order_field|eq("sending_date")}
                                    selected="selected"
                                {/if}>
                            {'SENDING DATE'|i18n( 'extension/ezmailing/text' )}
                        </option>
                        <option value="state_updated"
                                dir="desc"
                                {if $view_parameters.order_field|eq("state_updated")}
                                    selected="selected"
                                {/if}>
                            {'LAST STATE CHANGED'|i18n( 'extension/ezmailing/text' )}
                        </option>
                        <option value="created"
                                dir="desc"
                                {if $view_parameters.order_field|eq("created")}
                                    selected="selected"
                                {/if}>
                            {'CREATED'|i18n( 'extension/ezmailing/text' )}
                        </option>
                        <option value="updated"
                                dir="desc"
                                {if $view_parameters.order_field|eq("updated")}
                                    selected="selected"
                                {/if}>
                            {'UPDATED'|i18n( 'extension/ezmailing/text' )}
                        </option>
                    </select>
                </div>
                <div id="ezmailing_search">
                    <p>
                        <input  type="text" 
                                {if is_set($searched_text)}
                                    value="{$searched_text}" 
                                {else}
                                    value="{'SEARCH'|i18n( 'extension/ezmailing/text' )}" 
                                {/if}
                                alt="{'SEARCH'|i18n( 'extension/ezmailing/text' )}" 
                                name="SearchCampaignText" 
                                id="ezmailing_search_text" 
                                class="switchValue" />
                        <input  type="submit" 
                                value="" 
                                name="SearchCampaignButton" 
                                id="ezmailing_search_ok" />
                    </p>
                </div>
            </form>
        </div>
		{*end search form*}
        {/if}
		{*start record list*}
        {if $called_in_dashboard|not()}
        <div id="ezmailing_record_list">
		{/if}
            <form   {if is_set($action)} action={$action|ezurl} {/if} 
                    method="post" 
                    name="campaignlistform{if is_set($blockid)}_{$blockid}{/if}" 
                    class="jqTransform">
                {if is_set($searched_text)}<h2>{$lists_count} {"Results for « %search_text »"|i18n( 'extension/ezmailing/text', '', hash( '%search_text', $searched_text ) )}</h2>{/if}
                <ul>
                    {foreach $lists as $item sequence array('bglight', 'bgdark') as $seq}
                        {def $mailing_lists = $item.mailing_lists}
						{if $called_in_dashboard|not()}
                        <li class="ezmailing_list_item ezmailing_{$item.state_string_stand|explode(' ')|implode('_')|downcase()}_news">
                            <p class="ezmailing_sending_date">
                                <strong>{'Sending Date'|i18n( 'extension/ezmailing/text' )} : </strong> {$item.sending_date|l10n( 'shortdatetime' )}
                                <a href="#" title="{'Sending Date'|i18n( 'extension/ezmailing/text' )}">
                                    <img    src={"pictos/clock.png"|ezimage()} 
                                            width="23" 
                                            height="22"  
                                            alt="{'Sending Date'|i18n( 'extension/ezmailing/text' )}" />
                                </a>
                                <span class="ezmailing_tooltip">
                                    <span>{'Created'|i18n( 'extension/ezmailing/text' )} {$item.created|l10n( 'shortdatetime' )}</span>
                                    <span>{'Updated'|i18n( 'extension/ezmailing/text' )} {$item.updated|l10n( 'shortdatetime' )}</span>
                                </span>
                            </p>
                            <h3>
                                <a href={$item.url|ezurl}>{$item.subject|wash}</a>
                            </h3>
                            <h4>{$item.description|wash}&nbsp;</h4>
                            <div class="clear"></div>
                            <div class="ezmailing_sender">
                                <h5>{"Sender"|i18n('extension/ezmailing/text')}</h5>
                                <p class="ezmailing_sender_name">{$item.sender_name|wash}</p>
                                <p class="ezmailing_sender_email">{$item.sender_email|wash}</p>
                            </div>
                            <div class="ezmailing_mailing_list">
                                <h5>{"Mailing Lists"|i18n('extension/ezmailing/text')}</h5>
                                {foreach $mailing_lists as $mailing_list}
                                    <div class="ezmailing_mailing_list_item">
                                        <img    src="{$mailing_list.lang|flag_icon}" 
                                                width="18" 
                                                height="12" 
                                                alt="{$mailing_list.lang}" />
                                        <p>
                                            <a href={$mailing_list.url|ezurl}>{$mailing_list.name|wash}</a>
                                        </p>
                                    </div>
                                {/foreach}
                            </div>
                            <div class="ezmailing_state">
                                <img    src={concat("pictos/",$item.state_string_stand|append(' ')|explode(' ')|extract_left(1).0|downcase(),"_news.png")|ezimage()} 
                                        alt="{$item.state_string}" />
                                <p>{$item.state_string}</p>
                            </div>
                            <div class="ezmailing_open_actions">
                                <a href="#">
                                    <span>{'Actions'|i18n( 'extension/ezmailing/text' )}</span>
                                    <img    src={"pictos/actions_down_arrow.png"|ezimage()} 
                                            width="44" 
                                            height="45" 
                                            alt="Actions" />
                                </a>
                            </div>
                            <div class="ezmailing_checkbox_button">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <input  type="checkbox" 
                                                    name="itemsActionCheckbox[]" 
                                                    value="{$item.id}" 
                                                    class="jqTransformField" />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="ezmailing_actions_buttons">
                                <ul>
                                    <li class="edit_record">
                                        <a href={concat( '/mailing/campaigns/edit/', $item.id )|ezurl}>{'Edit'|i18n( 'extension/ezmailing/text' )}</a>
                                    </li>
                                    <li class="send_news">
                                        <a href={concat( '/mailing/campaigns/send/', $item.id )|ezurl}>{'Send'|i18n( 'extension/ezmailing/text' )}</a>
                                    </li>
                                    <li class="preview_news">
                                        {def $contentNode = fetch( 'content', 'node', hash( 'node_id',$item.node_id ))}
                                        <a href="{$item.campaign_url}" target="_blank">{"Preview"|i18n( 'extension/ezmailing/text' )}</a>
                                        {undef $contentNode}
                                    </li>
                                    <li class="remove_record">
                                        <a href={concat( '/mailing/campaigns/delete/', $item.id )|ezurl}>
                                            {'Remove'|i18n( 'extension/ezmailing/text' )}
                                        </a>
                                    </li>
                                </ul>
                                <div class="ezmailing_close_actions">
                                    <a href="#">
                                        <span>{'Close'|i18n( 'extension/ezmailing/text' )}</span>
                                        <img src={"pictos/actions_up_arrow.png"|ezimage()} width="44" height="45" alt="FERMER" />
                                    </a>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </li>
						{else}
						<li>
                        	<div class="ezmailing_user_email">
                        		<h3>
	                                <a href={$item.url|ezurl}>{$item.subject|wash}</a>
	                            </h3>
                        		<h4>{$item.sender_name|wash}</h4>
                        	</div>
                        	<div class="ezmailing_mailing_list">
	                        	<p class="registration_date">
	                        		<a href="#" title="{'Registration date'|i18n( 'extension/ezmailing/text' )}">
		                                <img    src={"pictos/clock.png"|ezimage()} 
		                                        width="23" 
		                                        height="22"  
		                                        alt="{'Registration date'|i18n( 'extension/ezmailing/text' )}" />
		                            </a>
		                            <span class="ezmailing_tooltip">
		                                <span>{'Created'|i18n( 'extension/ezmailing/text' )} {$item.created|l10n( 'shortdatetime' )}</span>
                                    <span>{'Updated'|i18n( 'extension/ezmailing/text' )} {$item.updated|l10n( 'shortdatetime' )}</span>
		                            </span>
	                        		<strong>{'Sending Date'|i18n( 'extension/ezmailing/text' )} : </strong> {$item.sending_date|l10n( 'shortdatetime' )}
	                    		</p>
	                    		<div class="clear"></div>
	                    		{foreach $mailing_lists as $mailing_list}
                                    <div class="ezmailing_mailing_list_item">
                                        <img    src="{$mailing_list.lang|flag_icon}" 
                                                width="18" 
                                                height="12" 
                                                alt="{$mailing_list.lang}" />
                                        <p>
                                            <a href={$mailing_list.url|ezurl}>{$mailing_list.name|wash}</a>
                                        </p>
                                    </div>
                                {/foreach}
	                        </div>
							<div class="clear"></div>
						</li>
                        {/if}
                        {undef $mailing_lists}
                    {/foreach}
                </ul>
                {if $show_search_form}
                    {def
                    	$page_limit_b = ezini('PaginationSettings','MailingLimit','ezmailing.ini')
    			        $page_uri_b = concat( '/mailing/campaigns', 	cond( $subaction|ne(''), concat('/', $subaction),true(),'' ),
    			        											cond( is_set($searched_text), concat( '/', $searched_text ), true(), '' ) )
    			        $lists_count_b = fetch('mailing','fetch_count',hash(
    			                                                       'type','campaigns',
    			                                                       'filter',cond( is_set($state_filter), hash( 'state',$state_filter), is_set($filter), $filter, true(), hash() ),
    			                                                      )
    			                              )
                    }
                    {include name='navigator'
                         uri='design:navigator/google.tpl'
                         page_uri=$page_uri_b
                         item_count=$lists_count_b
                         view_parameters=$view_parameters
                         item_limit=$page_limit_b}
                    {undef $page_limit_b $page_uri_b $lists_count_b}
                {/if}
				{if $called_in_dashboard|not()}
                <div id="ezmailing_actions_submit">
                    <ul>
                        <li id="remove_selected">
                            <button     type="submit" 
                                        name="RemoveCampaignButton" 
                                        title="{'Remove selected campaign.'|i18n( 'extension/ezmailing/text' )}"
                                        disabled="disabled">
                                {'Remove'|i18n( 'extension/ezmailing/text' )}<br />{'selected'|i18n( 'extension/ezmailing/text' )}
                            </button>
                        </li>
                        <li id="new_record">
                            <button     type="submit" 
                                        name="CreateCampaignButton" 
                                        title="{'Create new campaign'|i18n( 'extension/ezmailing/text' )}">
                                {'Create new'|i18n( 'extension/ezmailing/text' )}<br />{'campaign'|i18n( 'extension/ezmailing/text' )}
                            </button>
                        </li>
                        {if $lists|count|gt(0)}
                        <li id="export_campaign">
                            <a href={concat(    "mailing/export/Campaign/", 
                                                cond(   is_set( $state_filter ), $state_filter, 
                                                        is_set( $search_text ), concat('search/', $search_text), 
                                                        true(), '' 
                                                )
                                        )|ezurl} title="{'CSV export'|i18n( 'extension/ezmailing/text' )}">
                                {'Export All'|i18n( 'extension/ezmailing/text' )}
                            </a>
                        </li>
                        {/if}
                    </ul>
                    <div class="clear"></div>
                </div>
				{/if}
				<div class="clear"></div>
            </form>
		{if $called_in_dashboard|not()}
		</div>
		{/if}
		{*end record list*}
{if $called_in_dashboard|not()}
    </div>
</div>
{/if}
{undef $called_in_dashboard}