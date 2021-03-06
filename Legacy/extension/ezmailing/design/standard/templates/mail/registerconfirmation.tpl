{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{set-block scope=root variable=subject}{'Confirm your newsletter subscription at %siteurl'|i18n('extension/ezmailing/design/standard/mail',,hash('%siteurl',ezini('SiteSettings','SiteURL')))}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}

<p>{'Hello, '|i18n('extension/ezmailing/design/standard/mail')}</p>

<p>{'To confirm your subscription to these mailing lists: '|i18n('extension/ezmailing/design/standard/mail')}</p>

<ul>
{foreach $newsletters as $newsletter}
    <li>{$newsletter}</li>
{/foreach}
</ul>

<p>{'Please click on this link: '|i18n('extension/ezmailing/design/standard/mail')}</p>

<a href="http://{$hostname}{concat('/mailing/register/', $key)|ezurl('no')}">http://{$hostname}{concat('/mailing/register/', $key)|ezurl('no')}</a>

<p>{'Thank you for subscribing'|i18n('extension/ezmailing/design/standard/mail')}</p>