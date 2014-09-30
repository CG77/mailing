{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{include uri="design:mailing/common/header.tpl" page='edit'}

<form method="post" action={concat( '/mailing/users/edit/', $item.id )|ezurl} name="userseditform" class="jqTransform">
	<div id="ezmailing_container">
		<div id="ezmailing_content">
			<h2>{'Edit user'|i18n( 'extension/ezmailing/text' )}</h2>
			<div id="ezmailing_edit" class="ezmailing_edit_user">
			
			
				{def 	$attributes 		= ezini( 'MailingUserAccountSettings', 'Attributes', 'ezmailing.ini' )
    					$block				= 0
    					$templateAttribute 	= false()
    					$requires 			= ezini( 'MailingUserAccountSettings', 'RequiredAttributes', 'ezmailing.ini' )}
    					
                {foreach $attributes as $identifier => $label}
                	{set $templateAttribute = concat( 'design:mailing/users/attributes/', $identifier, '.tpl' )}
                	{if or( $block|eq( 3 ), $block|eq( 6 ) )}
                		</div>
                	{/if}
                	{if $block|eq( 6 )}
                			<div class="clear"></div>
                		</div>
                	{/if}
                	{if or( $block|eq( 0 ), $block|eq( 6 ) )}
                		<div class="inlineBlock">
                		{set $block = 0}
                	{/if}
                	{if or( $block|eq( 3 ), $block|eq( 0 ) )}
                		<div class="ezmailing_edit_{cond( $block|eq( 0 ), 'left', true(), 'right' )}">
                	{/if}
                    
                    {include uri=$templateAttribute edit=true() requires=$requires}
                    
                    
                    {set $block = $block|inc()}
                {/foreach}
                
                {if $attributes|count}
                	</div>
                	<div class="clear"></div>
                </div>
                {/if}
                
                {undef $attributes $block $templateAttribute $requires}
                
				<div id="user_registrations" class="inlineBlock">
					<h3>{'Registrations, select mailing lists'|i18n( 'extension/ezmailing/text' )}:</h3>
					<div id="ezmailing_record_list">			
						{def $mapping = $item.all_mailing_lists_with_registrations}
						<ul>
						{foreach $mapping as $map sequence array('bglight', 'bgdark') as $seq}
							{def 
    							$mailingList = $map.mailing
    							$registration = $map.registration
							}   
							<li class="ezmailing_list_item">
								<div class="registration_description">
									{if and( $registration, $registration.mailinglist_id )}
									<p>{'Registration date'|i18n( 'extension/ezmailing/text' )} : <span>{$registration.state_updated|l10n( 'shortdatetime' )}</span></p>
									{/if}
									<div class="clear"></div>
									<img src="{$mailingList.lang|flag_icon}" width="18" height="12" alt="{$mailingList.lang}" title="{$mailingList.lang}" />
									<p class="mailing_list_name">
										<a href={$mailingList.url|ezurl}>{$mailingList.name|wash}</a>
									</p>
									<div class="clear"></div>
								</div>
								<div class="registration_status {cond( $registration, $registration.state_string_stand|downcase(), true(), '' )}">
									{if and( $registration, $registration.state_string_stand|downcase()|eq('registred') )}
										<img src={"pictos/registred_user.png"|ezimage} alt="Registred" width="70" height="70" />
									{elseif and( $registration, $registration.state_string_stand|downcase()|eq('unregistred') )}
										<img src={"pictos/unregistred_user.png"|ezimage} alt="Unregistred" width="70" height="70" />
    								{/if}
									<p>{if and( $registration, $registration.state_string_stand )}{$registration.state_string}{/if}</p>
								</div>
								<div class="clear"></div>
								<div class="ezmailing_checkbox_button">
			                        <table cellpadding="0" cellspacing="0">
			                            <tr>
			                                <td>
			                                    <input	type="checkbox"
			                                            name="registrations[]"
			                                            value="{$mailingList.id}"
			                                            {if and( $registration, $registration.is_active )} checked="checked" {/if}
			                                            class="jqTransformField" />
			                                </td>
			                            </tr>
			                        </table>
			                    </div>                    
							</li>
							{undef $mailingList $registration}
						{/foreach}
						</ul>  
				        {undef $mapping}
					</div>		
				</div>
			</div>
		</div>
	</div>
	<span id="store_record">
		<button type="submit" name="StoreButton" title="{'Store user'|i18n( 'extension/ezmailing/text' )}">{'Store'|i18n( 'extension/ezmailing/text' )}</button>
	</span>
</form>
{include uri="design:mailing/common/footer.tpl"}