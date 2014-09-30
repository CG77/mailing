{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{include uri="design:mailing/common/header.tpl" page="users"}
    {def 
        $page_limit = ezini('PaginationSettings','MailingLimit','ezmailing.ini')
        $page_uri = '/mailing/import/users'
        $lists = fetch('mailing','fetch',hash(
                                                       'type','mailinglist',
                                                       'sort_by', hash( 'created', 'desc')
                                                   )
                              )
    }
         <div id="ezmailing_container">
    		<div id="ezmailing_content">
    			<h2>{'CSV File'|i18n( 'extension/ezmailing/text' )}</h2>
				<form id="form_import_users" class="jqTransform" name="form_import_users" enctype="multipart/form-data" method="post">
					<div id="import_users">
						<div id="file_transform">
							<input id="input_file_import" name="input_file_import" type="file" />
							<a id="filewrapper" href="#">
								<span>{'Choose a file'|i18n( 'extension/ezmailing/text' )}</span>
							</a>
							<span id="file_name">{'No file selected'|i18n( 'extension/ezmailing/text' )}</span>
						</div>
					</div>
                    <div id="import_users_help">
                        <h3 class="context-title">{'Help'|i18n( 'extension/ezmailing/text' )}</h3>
                        <p>{'Your file must have a first row with the identifier of each column.'|i18n( 'extension/ezmailing/text' )}</p>
                        <p>{"According to your configuration you can have:"|i18n( 'extension/ezmailing/text' )}</p>
                        <table class="list">
                            {def $availableFields = ezini('MailingUserAccountSettings', 'Attributes', 'ezmailing.ini')}
                                <tr class="bglight"><th>{'First row identifier'|i18n( 'extension/ezmailing/text' )}</th>
                                {foreach $availableFields as $identifier=>$humanText}
                                    <td>{$identifier}</td>
                                {/foreach}
                                </tr>
                                <tr class="bgdark"><th>{'Corresponding item'|i18n( 'extension/ezmailing/text' )}</th>
                                {foreach $availableFields as $identifier=>$humanText}
                                    <td>{$humanText|i18n( 'extension/ezmailing/text' )}</td>
                                {/foreach}
                                </tr>
                            {undef $availableFields}
                        </table>
                    </div>
					{include uri="design:mailing/import/mailinglist.tpl" lists=$lists}
					<button     type="submit" 
                                name="MailingImportButton" 
                                title="{'Users import'|i18n( 'extension/ezmailing/text' )}">
                                {'Users import'|i18n( 'extension/ezmailing/text' )}
                            </button>
				</form>
    		</div>
    	</div>
    {undef $lists $lists_count $page_limit $page_uri} 
{include uri="design:mailing/common/footer.tpl"}