<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=ezmailing

[RegionalSettings]
TranslationExtensions[]=ezmailing

[MailSettings]
# Normaly overrided in the settings/override/site.ini.append.php
# Transport=file
# CampaignTransport=file

# Don't touch this settings
TransportCampaignAlias[file]=Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Standard\File
TransportCampaignAlias[sendmail]=Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Standard\Sendmail
TransportCampaignAlias[smtp]=Novactive\eZPublish\Extension\eZMailing\Core\Transports\Campaign\Standard\SMTP

[RoleSettings]
PolicyOmitList[]=mailing/continue
PolicyOmitList[]=mailing/read
PolicyOmitList[]=mailing/register
PolicyOmitList[]=mailing/unregister
PolicyOmitList[]=mailing/show


*/ ?>