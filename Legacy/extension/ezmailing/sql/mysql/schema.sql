SET NAMES utf8;

CREATE TABLE `JNT_resendcampaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) DEFAULT NULL,
  `remote_id` int(11) NOT NULL DEFAULT '0',
  `last_update` int(11) DEFAULT NULL,
  `message` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id`,`remote_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ezmailingmailinglist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `lang` varchar(6) NOT NULL,
  `last_synchro` int(20) NULL,
  `remote_id` varchar(100) NULL DEFAULT '',
  `count_remote_registration` int(11) NULL DEFAULT '0',
  `state` int(11) DEFAULT '0',
  `created` bigint(20) unsigned NOT NULL,
  `updated` bigint(20) unsigned NOT NULL,
  `draft` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ezmailingcampaign` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) DEFAULT NULL,
  `description` text,
  `content` text,
  `sender_name` varchar(255) DEFAULT NULL,
  `sender_email` varchar(255) DEFAULT NULL,
  `report_email` varchar(255) DEFAULT NULL,
  `siteaccess` varchar(255) DEFAULT NULL,
  `content_type` tinyint(1) DEFAULT '0',
  `node_id` int(11) DEFAULT NULL,
  `destination_mailing_list` varchar(255) DEFAULT NULL,
  `sending_date` bigint(20) unsigned DEFAULT NULL,
  `recurrency_period` bigint(20) unsigned DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `report_sent` tinyint(1) DEFAULT '0',
  `last_synchro` bigint(20) unsigned NULL,
  `remote_id` varchar(100) NULL DEFAULT '',
  `state_updated` bigint(20) unsigned DEFAULT NULL,
  `created` int(11) unsigned DEFAULT NULL,
  `updated` int(11) unsigned DEFAULT NULL,
  `draft` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ezmailingregistration` (
  `mailing_user_id` int(11) unsigned NOT NULL,
  `mailinglist_id` int(11) unsigned NOT NULL,
  `registred` bigint(20) NOT NULL,
  `state` int(11) NOT NULL,
  `state_updated` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`mailing_user_id`,`mailinglist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `ezmailinguser` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `birthday` varchar(255) DEFAULT NULL,
  `address` text,
  `zipcode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `profession` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `company_member` tinyint(1) DEFAULT '0',
  `profession_status` varchar(255) DEFAULT NULL,
  `number_icom` varchar(255) DEFAULT NULL,
  `family_status` varchar(255) DEFAULT NULL,
  `children_count` int(11) DEFAULT NULL,
  `house_member_count` int(11) DEFAULT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `registred` bigint(20) unsigned NOT NULL,
  `updated` bigint(20) DEFAULT NULL,
  `draft` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IndexEmail` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ezmailingstat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` text,
  `clicked` bigint(20) DEFAULT NULL,
  `user_key` varchar(255) DEFAULT NULL,
  `os_name` varchar(255) DEFAULT NULL,
  `browser_name` varchar(255) DEFAULT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IdxCampaignID` (`campaign_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ezmailingkey` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hash_key` varchar(255) DEFAULT NULL,
  `time` bigint(20) unsigned DEFAULT NULL,
  `params` TEXT,
  PRIMARY KEY (`id`),
  KEY `IdxHash` (`hash_key`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
