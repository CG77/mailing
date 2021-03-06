{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{def 	$percentReceived 	= $countMailSent|sub( $bounce )|div( $countMailSent )|mul( 100 )
		$percentOpened		= $opened|div( $countMailSent|sub( $bounce ) )|mul( 100 )
		$percentClicked		= $clicked|div( $opened )|mul( 100 )
		$percentBounced		= $bounce|div( $countMailSent )|mul( 100 )
		$percentOpenedDelay = $openedDelay|div( $opened )|mul( 100 )
}
{include uri="design:mailing/common/header.tpl" page="content"}
	{if is_provided()|not()}
	<div id="last_sending_states" class="emailing_dashboard_item">
		<h2>{'MAILINGS STATES DURING THE LAST 2 WEEKS'|i18n( 'extension/ezmailing/text' )}</h2>
		<div id="last_sending_states_graph">
			{*<img src={"dashboard_graph.jpg"|ezimage()} alt="{"Graph"|i18n('extension/ezmailing/text')}" />*}
			<object width="800" height="400"  type="image/svg+xml" data={"mailing/graph/week/2/png"|ezurl}>
                <p>
                	<img src={"mailing/graph/week/2/png"|ezurl} />
            	</p>
            </object>
		</div>
		<div id="last_sending_states_stats">
			<div id="last_sending_states_stats_values">
				<div id="last_sending_states_stats_total">
					<span class="value">{$countMailSent}</span>
					<span class="text">{"Total"|i18n('extension/ezmailing/text')}</span>
					<div class="clear"></div>
				</div>
				<div id="last_sending_states_stats_received">
					<span class="value">{cond(	is_float( $percentReceived ), $percentReceived|l10n( 'number' ),
												true(), $percentReceived
										)}%</span>
					<span class="text">{"Received"|i18n('extension/ezmailing/text')}</span>
					<div class="clear"></div>
				</div>
				<div id="last_sending_states_stats_open">
					<span class="value">{cond(  is_float( $percentOpened ), $percentOpened|l10n( 'number' ),
												true(), $percentOpened 
										)}%</span>
					<span class="text">{"Opened"|i18n('extension/ezmailing/text')}</span>
					<div class="clear"></div>
				</div>
				<div id="last_sending_states_stats_clicked">
					<span class="value">{cond(	is_float( $percentClicked ), $percentClicked|l10n( 'number' ),
												true(), $percentClicked
										)}%</span>
					<span class="text">{"Clicked"|i18n('extension/ezmailing/text')}</span>
					<div class="clear"></div>
				</div>
				<div id="last_sending_states_stats_bounce">
					<span class="value">{cond(	is_float( $percentBounced ), $percentBounced|l10n( 'number' ),
												true(), $percentBounced
										)}%</span>
					<span class="text">{"Bounce"|i18n('extension/ezmailing/text')}</span>
					<div class="clear"></div>
				</div>
			</div>
			<div id="last_sending_states_stats_description">
                <p>{"During last 2 weeks, you sent"|i18n( 'extension/ezmailing/text')} <strong>{$countMailSent}</strong> {"emails"|i18n( 'extension/ezmailing/text')}.</p>
				<p><span>{cond( is_float( $percentOpenedDelay ), $percentOpenedDelay|l10n( 'number' ),
								true(), $percentOpenedDelay
						 )}%</span> {"have been opened in"|i18n( 'extension/ezmailing/text')} <span>30 {"minutes"|i18n( 'extension/ezmailing/text')}</span> {"after sent."|i18n( 'extension/ezmailing/text')}</p>
			</div>
			<div class="clear"></div>
		</div>		
	</div>
	{/if}
	<div id="dashboard_campaigns" class="emailing_dashboard_item">
		<h2>{'Lastest waiting for sending campaigns'|i18n( 'extension/ezmailing/text' )}</h2>
		<div class="dashboard_record_list">
			{include uri="design:mailing/block/last_campaigns.tpl" state=40}
		</div>		
	</div>
	<div id="dashboard_mailing_lists" class="emailing_dashboard_item">
		<h2>{'Mailing Lists'|i18n( 'extension/ezmailing/text' )}</h2>
		<div id="dashboard_mailing_lists_registrations_header">
			<div>
				<h3>
					<img	src={"pictos/registred_state_on.png"|ezimage()}
							width="55"
							height="55"
							alt="{'Last registrations'|i18n( 'extension/ezmailing/text' )}" />
					{'Last registrations'|i18n( 'extension/ezmailing/text' )}
				</h3>
			</div>
			<div>
				<h3>
					<img	src={"pictos/unregistred_state_on.png"|ezimage()}
							width="55"
							height="55"
							alt="{'Last unregistrations'|i18n( 'extension/ezmailing/text' )}" />
					{'Last unregistrations'|i18n( 'extension/ezmailing/text' )}
				</h3>
			</div>
			<div class="clear"></div>
		</div>		
		<div id="dashboard_mailing_lists_registrations">
			<div class="dashboard_record_list">
				{include uri="design:mailing/block/last_registrations.tpl" state=10}
			</div>
			<div class="dashboard_record_list">
				{include uri="design:mailing/block/last_registrations.tpl" state=20}
			</div>
			<div class="clear"></div>	
		</div>			
	</div>
{include uri="design:mailing/common/footer.tpl"}