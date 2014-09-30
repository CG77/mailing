{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{set-block scope=root variable=subject}{'Confirm your unsubscription at %siteurl'|i18n('extension/ezmailing/design/standard/mail',,hash('%siteurl',ezini('SiteSettings','SiteURL')))}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}

<p>{'Hello, '|i18n('extension/ezmailing/design/standard/mail')}</p>

<p>{'To confirm your unsubscription to these mailing lists: '|i18n('extension/ezmailing/design/standard/mail')}</p>

<ul>
{foreach $newsletters as $newsletter}
    <li>{$newsletter}</li>
{/foreach}
</ul>


<p>{'Please click on this link: '|i18n('extension/ezmailing/design/standard/mail')}</p>

<a href="http://{$hostname}{concat('/mailing/unregister/', $key)|ezurl('no')}">http://{$hostname}{concat('/mailing/unregister/', $key)|ezurl('no')}</a>

<p>{'Best regards'|i18n('extension/ezmailing/design/standard/mail')}</p>