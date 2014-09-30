{* DO NOT EDIT THIS FILE! Use an override template instead. *}
<div id="leftmenu">
	<div id="leftmenu-design">
		<div class="box-header">
			<div class="box-ml">
				<p id="ezmailing_logo">
					<a href={"mailing/dashboard"|ezurl}>
    					<img src={"ezmailing.gif"|ezimage()} width="128" height="98" />
    				</a>
				</p>
			</div>
		</div>
		<div id="ezmailing_left_menu">
		    <ul class="leftmenu-items">
		        <li{if module_params().function_name|eq('mailinglists')} class="current"{/if}>
		            <a href={"mailing/mailinglists"|ezurl}>
		                <img src={"pictos/mailing_list.png"|ezimage()} width="32" height="32" />
		                 <span>{"Mailing Lists"|i18n('extension/ezmailing/text')}</span>
		            </a>
		        </li>
		        <li{if module_params().function_name|eq('campaigns')} class="current"{/if}>
		            <a href={"mailing/campaigns"|ezurl}>
		                <img src={"pictos/campaigns.png"|ezimage()} width="32" height="32" />
		                <span>{"Campaigns"|i18n('extension/ezmailing/text')}</span>
		            </a>
		        </li>
		        <li{if module_params().function_name|eq('registrations')} class="current"{/if}>
		            <a href={"mailing/registrations"|ezurl}>
		                <img src={"pictos/registrations.png"|ezimage()} width="32" height="32" />
		                <span>{"Registrations"|i18n('extension/ezmailing/text')}</span>
		            </a>
		        </li>
		        <li{if module_params().function_name|eq('users')} class="current"{/if}>
		            <a href={"mailing/users"|ezurl}>
		                <img src={"pictos/users.png"|ezimage()} width="32" height="32" />
		                <span>{"Users"|i18n('extension/ezmailing/text')}</span>
		            </a>
		        </li>
                <li{if module_params().function_name|eq('import')} class="current"{/if}>
                    <a href="#">
                        <img src={"pictos/tools.png"|ezimage()} width="31" height="31" />
                        <span>{"Tools"|i18n('extension/ezmailing/text')}</span>
                    </a>
                    <ul{if and(is_set($path.0.url),$path.0.url|eq('mailing/import/users'))} class="active"{/if}>
                        <li><a href={"mailing/import/users"|ezurl}>{"Users import"|i18n('extension/ezmailing/text')}</a></li>
                    </ul>
                </li>
		    </ul>		    
		</div>
		<center>
		    <br /><br />
		    <a href="http://www.novactive.com" target="_blank"><img width="100" src={"logo-novactive.png"|ezimage} /></a>
		</center>
<div class="" id="widthcontrol-handler">
<div class="widthcontrol-grippy"></div>
</div>
</div>
</div>
<hr class="hide" />