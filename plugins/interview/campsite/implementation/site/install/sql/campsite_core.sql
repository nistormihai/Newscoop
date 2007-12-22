## MySQL dump 10.11
##
## Host: localhost    Database: campsite_a1335
## ------------------------------------------------------
## Server version	5.0.41-log


##
## Table structure for table `Aliases`
##

DROP TABLE IF EXISTS `Aliases`;
CREATE TABLE `Aliases` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` char(128) NOT NULL default '',
  `IdPublication` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=1904 DEFAULT CHARSET=latin1;

##
## Table structure for table `ArticleAttachments`
##

DROP TABLE IF EXISTS `ArticleAttachments`;
CREATE TABLE `ArticleAttachments` (
  `fk_article_number` int(10) unsigned NOT NULL default '0',
  `fk_attachment_id` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `article_attachment_index` (`fk_article_number`,`fk_attachment_id`),
  KEY `fk_article_number` (`fk_article_number`),
  KEY `fk_attachment_id` (`fk_attachment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `ArticleAudioclips`
##

DROP TABLE IF EXISTS `ArticleAudioclips`;
CREATE TABLE `ArticleAudioclips` (
  `fk_article_number` int(10) unsigned NOT NULL default '0',
  `fk_audioclip_gunid` varchar(20) NOT NULL default '0',
  `fk_language_id` int(10) unsigned default NULL,
  `order_no` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_article_number`,`fk_audioclip_gunid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `ArticleComments`
##

DROP TABLE IF EXISTS `ArticleComments`;
CREATE TABLE `ArticleComments` (
  `fk_article_number` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `fk_comment_id` int(10) unsigned NOT NULL default '0',
  `is_first` tinyint(1) NOT NULL default '0',
  KEY `fk_comment_id` (`fk_comment_id`),
  KEY `article_index` (`fk_article_number`,`fk_language_id`),
  KEY `first_message_index` (`fk_article_number`,`fk_language_id`,`is_first`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `ArticleImages`
##

DROP TABLE IF EXISTS `ArticleImages`;
CREATE TABLE `ArticleImages` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `IdImage` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NrArticle`,`IdImage`),
  UNIQUE KEY `ArticleImage` (`NrArticle`,`Number`),
  KEY `IdImage` (`IdImage`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `ArticleIndex`
##

DROP TABLE IF EXISTS `ArticleIndex`;
CREATE TABLE `ArticleIndex` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `IdKeyword` int(10) unsigned NOT NULL default '0',
  `NrIssue` int(10) unsigned NOT NULL default '0',
  `NrSection` int(10) unsigned NOT NULL default '0',
  `NrArticle` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPublication`,`IdLanguage`,`IdKeyword`,`NrIssue`,`NrSection`,`NrArticle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `ArticlePublish`
##

DROP TABLE IF EXISTS `ArticlePublish`;
CREATE TABLE `ArticlePublish` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fk_article_number` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `time_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_action` enum('P','U') default NULL,
  `publish_on_front_page` enum('S','R') default NULL,
  `publish_on_section_page` enum('S','R') default NULL,
  `is_completed` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  KEY `article_index` (`fk_article_number`,`fk_language_id`),
  KEY `event_time_index` (`time_action`,`is_completed`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `ArticleTopics`
##

DROP TABLE IF EXISTS `ArticleTopics`;
CREATE TABLE `ArticleTopics` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `TopicId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NrArticle`,`TopicId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `ArticleTypeMetadata`
##

DROP TABLE IF EXISTS `ArticleTypeMetadata`;
CREATE TABLE `ArticleTypeMetadata` (
  `type_name` varchar(166) NOT NULL default '',
  `field_name` varchar(166) NOT NULL default 'NULL',
  `field_weight` int(11) default NULL,
  `is_hidden` tinyint(1) NOT NULL default '0',
  `comments_enabled` tinyint(1) NOT NULL default '0',
  `fk_phrase_id` int(10) unsigned default NULL,
  `field_type` varchar(255) default NULL,
  `field_type_param` varchar(255) default NULL,
  PRIMARY KEY  (`type_name`,`field_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Articles`
##

DROP TABLE IF EXISTS `Articles`;
CREATE TABLE `Articles` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `NrIssue` int(10) unsigned NOT NULL default '0',
  `NrSection` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  `Type` varchar(70) NOT NULL default '',
  `IdUser` int(10) unsigned NOT NULL default '0',
  `OnFrontPage` enum('N','Y') NOT NULL default 'N',
  `OnSection` enum('N','Y') NOT NULL default 'N',
  `Published` enum('N','S','Y') NOT NULL default 'N',
  `PublishDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `UploadDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Keywords` varchar(255) NOT NULL default '',
  `Public` enum('N','Y') NOT NULL default 'N',
  `IsIndexed` enum('N','Y') NOT NULL default 'N',
  `LockUser` int(10) unsigned NOT NULL default '0',
  `LockTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ShortName` varchar(32) NOT NULL default '',
  `ArticleOrder` int(10) unsigned NOT NULL default '0',
  `comments_enabled` tinyint(1) NOT NULL default '0',
  `comments_locked` tinyint(1) NOT NULL default '0',
  `time_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`IdPublication`,`NrIssue`,`NrSection`,`Number`,`IdLanguage`),
  UNIQUE KEY `IdPublication` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`Name`),
  UNIQUE KEY `Number` (`Number`,`IdLanguage`),
  UNIQUE KEY `other_key` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`Number`),
  UNIQUE KEY `ShortName` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`ShortName`),
  KEY `Type` (`Type`),
  KEY `ArticleOrderIdx` (`ArticleOrder`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Attachments`
##

DROP TABLE IF EXISTS `Attachments`;
CREATE TABLE `Attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fk_language_id` int(10) unsigned default NULL,
  `file_name` varchar(255) default NULL,
  `extension` varchar(50) default NULL,
  `mime_type` varchar(255) default NULL,
  `content_disposition` enum('attachment') default NULL,
  `http_charset` varchar(50) default NULL,
  `size_in_bytes` bigint(20) unsigned default NULL,
  `fk_description_id` int(11) default NULL,
  `fk_user_id` int(10) unsigned default NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `time_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;

##
## Table structure for table `AudioclipMetadata`
##

DROP TABLE IF EXISTS `AudioclipMetadata`;
CREATE TABLE `AudioclipMetadata` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `gunid` varchar(20) NOT NULL default '0',
  `predicate_ns` varchar(10) default '',
  `predicate` varchar(30) NOT NULL default '',
  `object` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gunid_tag_id` (`gunid`,`predicate_ns`,`predicate`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `AutoId`
##

DROP TABLE IF EXISTS `AutoId`;
CREATE TABLE `AutoId` (
  `ArticleId` int(10) unsigned NOT NULL default '0',
  `LogTStamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `TopicId` int(10) unsigned NOT NULL default '0',
  `translation_phrase_id` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Classes`
##

DROP TABLE IF EXISTS `Classes`;
CREATE TABLE `Classes` (
  `Id` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`Id`,`IdLanguage`),
  UNIQUE KEY `IdLanguage` (`IdLanguage`,`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Countries`
##

DROP TABLE IF EXISTS `Countries`;
CREATE TABLE `Countries` (
  `Code` char(2) NOT NULL default '',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`Code`,`IdLanguage`),
  UNIQUE KEY `IdLanguage` (`IdLanguage`,`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `Countries`
##

LOCK TABLES `Countries` WRITE;
INSERT INTO `Countries` VALUES ('AR',1,'Argentina'),('AG',1,'Antigua And Barbuda'),('AQ',1,'Antarctica'),('AI',1,'Anguilla'),('AO',1,'Angola'),('AD',1,'Andorra'),('AS',1,'American Samoa'),('DZ',1,'Algeria'),('AL',1,'Albania'),('AF',1,'Afghanistan'),('AM',1,'Armenia'),('AW',1,'Aruba'),('AU',1,'Australia'),('AT',1,'Austria'),('AZ',1,'Azerbaijan'),('BS',1,'Bahamas'),('BH',1,'Bahrain'),('BD',1,'Bangladesh'),('BB',1,'Barbados'),('BY',1,'Belarus'),('BE',1,'Belgium'),('BZ',1,'Belize'),('BJ',1,'Benin'),('BM',1,'Bermuda'),('BT',1,'Bhutan'),('BO',1,'Bolivia'),('BA',1,'Bosnia And Herzegovina'),('BW',1,'Botswana'),('BV',1,'Bouvet Island'),('BR',1,'Brazil'),('IO',1,'British Indian Ocean Territory'),('BN',1,'Brunei Darussalam'),('BG',1,'Bulgaria'),('BF',1,'Burkina Faso'),('BI',1,'Burundi'),('KH',1,'Cambodia'),('CM',1,'Cameroon'),('CA',1,'Canada'),('CV',1,'Cape Verde'),('KY',1,'Cayman Islands'),('CF',1,'Central African Republic'),('TD',1,'Chad'),('CL',1,'Chile'),('CN',1,'China'),('CX',1,'Christmas Island'),('CC',1,'Cocos (Keeling) Islands'),('CO',1,'Colombia'),('KM',1,'Comoros'),('CG',1,'Congo'),('CD',1,'Congo, The Democratic Republic Of The'),('CK',1,'Cook Islands'),('CR',1,'Costa Rica'),('CI',1,'Cote Divoire'),('HR',1,'Croatia'),('CU',1,'Cuba'),('CY',1,'Cyprus'),('CZ',1,'Czech Republic'),('DK',1,'Denmark'),('DJ',1,'Djibouti'),('DM',1,'Dominica'),('DO',1,'Dominican Republic'),('TP',1,'East Timor'),('EC',1,'Ecuador'),('EG',1,'Egypt'),('SV',1,'El Salvador'),('GQ',1,'Equatorial Guinea'),('ER',1,'Eritrea'),('EE',1,'Estonia'),('ET',1,'Ethiopia'),('FK',1,'Falkland Islands (Malvinas)'),('FO',1,'Faroe Islands'),('FJ',1,'Fiji'),('FI',1,'Finland'),('FR',1,'France'),('FX',1,'France, Metropolitan'),('GF',1,'French Guiana'),('PF',1,'French Polynesia'),('TF',1,'French Southern Territories'),('GA',1,'Gabon'),('GM',1,'Gambia'),('GE',1,'Georgia'),('DE',1,'Germany'),('GH',1,'Ghana'),('GI',1,'Gibraltar'),('GR',1,'Greece'),('GL',1,'Greenland'),('GD',1,'Grenada'),('GP',1,'Guadeloupe'),('GU',1,'Guam'),('GT',1,'Guatemala'),('GN',1,'Guinea'),('GW',1,'Guinea-bissau'),('GY',1,'Guyana'),('HT',1,'Haiti'),('HM',1,'Heard Island And Mcdonald Islands'),('VA',1,'Holy See (Vatican City State)'),('HN',1,'Honduras'),('HK',1,'Hong Kong'),('HU',1,'Hungary'),('IS',1,'Iceland'),('IN',1,'India'),('ID',1,'Indonesia'),('IR',1,'Iran, Islamic Republic Of'),('IQ',1,'Iraq'),('IE',1,'Ireland'),('IL',1,'Israel'),('IT',1,'Italy'),('JM',1,'Jamaica'),('JP',1,'Japan'),('JO',1,'Jordan'),('KZ',1,'Kazakstan'),('KE',1,'Kenya'),('KI',1,'Kiribati'),('KP',1,'Korea, Democratic Peoples Republic Of'),('KR',1,'Korea, Republic Of'),('KW',1,'Kuwait'),('KG',1,'Kyrgyzstan'),('LA',1,'Lao Peoples Democratic Republic'),('LV',1,'Latvia'),('LB',1,'Lebanon'),('LS',1,'Lesotho'),('LR',1,'Liberia'),('LY',1,'Libyan Arab Jamahiriya'),('LI',1,'Liechtenstein'),('LT',1,'Lithuania'),('LU',1,'Luxembourg'),('MO',1,'Macau'),('MK',1,'Macedonia, The Former Yugoslav Republic Of'),('MG',1,'Madagascar'),('MW',1,'Malawi'),('MY',1,'Malaysia'),('MV',1,'Maldives'),('ML',1,'Mali'),('MT',1,'Malta'),('MH',1,'Marshall Islands'),('MQ',1,'Martinique'),('MR',1,'Mauritania'),('MU',1,'Mauritius'),('YT',1,'Mayotte'),('MX',1,'Mexico'),('FM',1,'Micronesia, Federated States Of'),('MD',1,'Moldova, Republic Of'),('MC',1,'Monaco'),('MN',1,'Mongolia'),('MS',1,'Montserrat'),('MA',1,'Morocco'),('MZ',1,'Mozambique'),('MM',1,'Myanmar'),('NA',1,'Namibia'),('NR',1,'Nauru'),('NP',1,'Nepal'),('NL',1,'Netherlands'),('AN',1,'Netherlands Antilles'),('NC',1,'New Caledonia'),('NZ',1,'New Zealand'),('NI',1,'Nicaragua'),('NE',1,'Niger'),('NG',1,'Nigeria'),('NU',1,'Niue'),('NF',1,'Norfolk Island'),('MP',1,'Northern Mariana Islands'),('NO',1,'Norway'),('OM',1,'Oman'),('PK',1,'Pakistan'),('PW',1,'Palau'),('PS',1,'Palestinian Territory, Occupied'),('PA',1,'Panama'),('PG',1,'Papua New Guinea'),('PY',1,'Paraguay'),('PE',1,'Peru'),('PH',1,'Philippines'),('PN',1,'Pitcairn'),('PL',1,'Poland'),('PT',1,'Portugal'),('PR',1,'Puerto Rico'),('QA',1,'Qatar'),('RE',1,'Reunion'),('RO',1,'Romania'),('RU',1,'Russian Federation'),('RW',1,'Rwanda'),('SH',1,'Saint Helena'),('KN',1,'Saint Kitts And Nevis'),('LC',1,'Saint Lucia'),('PM',1,'Saint Pierre And Miquelon'),('VC',1,'Saint Vincent And The Grenadines'),('WS',1,'Samoa'),('SM',1,'San Marino'),('ST',1,'Sao Tome And Principe'),('SA',1,'Saudi Arabia'),('SN',1,'Senegal'),('CS',1,'Serbia and Montenegro'),('SC',1,'Seychelles'),('SL',1,'Sierra Leone'),('SG',1,'Singapore'),('SK',1,'Slovakia'),('SI',1,'Slovenia'),('SB',1,'Solomon Islands'),('SO',1,'Somalia'),('ZA',1,'South Africa'),('GS',1,'South Georgia And The South Sandwich Islands'),('ES',1,'Spain'),('LK',1,'Sri Lanka'),('SD',1,'Sudan'),('SR',1,'Suriname'),('SJ',1,'Svalbard And Jan Mayen'),('SZ',1,'Swaziland'),('SE',1,'Sweden'),('CH',1,'Switzerland'),('SY',1,'Syrian Arab Republic'),('TW',1,'Taiwan, Province Of China'),('TJ',1,'Tajikistan'),('TZ',1,'Tanzania, United Republic Of'),('TH',1,'Thailand'),('TG',1,'Togo'),('TK',1,'Tokelau'),('TO',1,'Tonga'),('TT',1,'Trinidad And Tobago'),('TN',1,'Tunisia'),('TR',1,'Turkey'),('TM',1,'Turkmenistan'),('TC',1,'Turks And Caicos Islands'),('TV',1,'Tuvalu'),('UG',1,'Uganda'),('UA',1,'Ukraine'),('AE',1,'United Arab Emirates'),('GB',1,'United Kingdom'),('US',1,'United States'),('UM',1,'United States Minor Outlying Islands'),('UY',1,'Uruguay'),('UZ',1,'Uzbekistan'),('VU',1,'Vanuatu'),('VE',1,'Venezuela'),('VN',1,'Vietnam'),('VG',1,'Virgin Islands, British'),('VI',1,'Virgin Islands, U.S.'),('WF',1,'Wallis And Futuna'),('EH',1,'Western Sahara'),('YE',1,'Yemen'),('ZM',1,'Zambia'),('ZW',1,'Zimbabwe');
UNLOCK TABLES;

##
## Table structure for table `Dictionary`
##

DROP TABLE IF EXISTS `Dictionary`;
CREATE TABLE `Dictionary` (
  `Id` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Keyword` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`IdLanguage`,`Keyword`),
  UNIQUE KEY `Id` (`Id`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Errors`
##

DROP TABLE IF EXISTS `Errors`;
CREATE TABLE `Errors` (
  `Number` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Message` char(255) NOT NULL default '',
  PRIMARY KEY  (`Number`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `Errors`
##

LOCK TABLES `Errors` WRITE;
INSERT INTO `Errors` VALUES (4000,1,'Internal error.'),(4001,1,'Username not specified.'),(4002,1,'Invalid username.'),(4003,1,'Password not specified.'),(4004,1,'Invalid password.'),(2000,1,'Internal error'),(2001,1,'Username is not specified. Please fill out login name field.'),(2002,1,'You are not a reader.'),(2003,1,'Publication not specified.'),(2004,1,'There are other subscriptions not payed.'),(2005,1,'Time unit not specified.'),(3000,1,'Internal error.'),(3001,1,'Username already exists.'),(3002,1,'Name is not specified. Please fill out name field.'),(3003,1,'Username is not specified. Please fill out login name field.'),(3004,1,'Password is not specified. Please fill out password field.'),(3005,1,'EMail is not specified. Please fill out EMail field.'),(3006,1,'EMail address already exists. Please try to login with your old account.'),(3007,1,'Invalid user identifier'),(3008,1,'No country specified. Please select a country.'),(3009,1,'Password (again) is not specified. Please fill out password (again) field.'),(3010,1,'Passwords do not match. Please fill out the same password to both password fields.'),(3011,1,'Password is too simple. Please choose a better password (at least 6 characters).'),(5009,1,'The code you entered is not the same with the one shown in the image.'),(5008,1,'Please enter the code shown in the image.'),(5007,1,'EMail field is empty. You must fill in your EMail address.'),(5006,1,'The comment was rejected by the spam filters.'),(5005,1,'You are banned from submitting comments.'),(5004,1,'Comments are not enabled for this publication/article.'),(5003,1,'The article was not selected. You must view an article in order to post comments.'),(5002,1,'The comment content was empty.'),(5001,1,'You must be a registered user in order to submit a comment. Please subscribe or log in if you already have a subscription.'),(5000,1,'There was an internal error when submitting the comment.');
UNLOCK TABLES;

##
## Table structure for table `Events`
##

DROP TABLE IF EXISTS `Events`;
CREATE TABLE `Events` (
  `Id` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  `Notify` enum('N','Y') NOT NULL default 'N',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`,`IdLanguage`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `Events`
##

LOCK TABLES `Events` WRITE;
INSERT INTO `Events` VALUES (1,'Add Publication','N',1),(2,'Delete Publication','N',1),(11,'Add Issue','N',1),(12,'Delete Issue','N',1),(13,'Change Issue Template','N',1),(14,'Change issue status','N',1),(15,'Add Issue Translation','N',1),(21,'Add Section','N',1),(22,'Delete section','N',1),(31,'Add Article','Y',1),(32,'Delete article','N',1),(33,'Change article field','N',1),(34,'Change article properties','N',1),(35,'Change article status','Y',1),(41,'Add Image','Y',1),(42,'Delete image','N',1),(43,'Change image properties','N',1),(51,'Add User','N',1),(52,'Delete User','N',1),(53,'Changes Own Password','N',1),(54,'Change User Password','N',1),(55,'Change User Permissions','N',1),(56,'Change user information','N',1),(61,'Add article type','N',1),(62,'Delete article type','N',1),(71,'Add article type field','N',1),(72,'Delete article type field','N',1),(81,'Add dictionary class','N',1),(82,'Delete dictionary class','N',1),(91,'Add dictionary keyword','N',1),(92,'Delete dictionary keyword','N',1),(101,'Add language','N',1),(102,'Delete language','N',1),(103,'Modify language','N',1),(112,'Delete templates','N',1),(111,'Add templates','N',1),(121,'Add user type','N',1),(122,'Delete user type','N',1),(123,'Change user type','N',1),(3,'Change publication information','N',1),(36,'Change article template','N',1),(57,'Add IP Group','N',1),(58,'Delete IP Group','N',1),(131,'Add country','N',1),(132,'Add country translation','N',1),(133,'Change country name','N',1),(134,'Delete country','N',1),(4,'Add default subscription time','N',1),(5,'Delete default subscription time','N',1),(6,'Change default subscription time','N',1),(113,'Edit template','N',1),(114,'Create template','N',1),(115,'Duplicate template','N',1),(141,'Add topic','N',1),(142,'Delete topic','N',1),(143,'Update topic','N',1),(144,'Add topic to article','N',1),(145,'Delete topic from article','N',1),(151,'Add alias','N',1),(152,'Delete alias','N',1),(153,'Update alias','N',1),(154,'Duplicate section','N',1),(155,'Duplicate article','N',1),(161,'Sync campsite and phorum users','N',1),(171,'Change system preferences','N',1),(116,'Rename Template','N',1),(117,'Move Template','N',1);
UNLOCK TABLES;

##
## Table structure for table `FailedLoginAttempts`
##

DROP TABLE IF EXISTS `FailedLoginAttempts`;
CREATE TABLE `FailedLoginAttempts` (
  `ip_address` varchar(40) NOT NULL default '',
  `time_of_attempt` bigint(20) NOT NULL default '0',
  KEY `ip_address` (`ip_address`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Images`
##

DROP TABLE IF EXISTS `Images`;
CREATE TABLE `Images` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Description` varchar(255) NOT NULL default '',
  `Photographer` varchar(255) NOT NULL default '',
  `Place` varchar(255) NOT NULL default '',
  `Caption` varchar(255) NOT NULL default '',
  `Date` date NOT NULL default '0000-00-00',
  `ContentType` varchar(64) NOT NULL default '',
  `Location` enum('local','remote') NOT NULL default 'local',
  `URL` varchar(255) NOT NULL default '',
  `ThumbnailFileName` varchar(50) NOT NULL default '',
  `ImageFileName` varchar(50) NOT NULL default '',
  `UploadedByUser` int(11) default NULL,
  `LastModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `TimeCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=latin1;

##
## Table structure for table `IssuePublish`
##

DROP TABLE IF EXISTS `IssuePublish`;
CREATE TABLE `IssuePublish` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  `fk_issue_id` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `time_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_action` enum('P','U') NOT NULL default 'P',
  `do_publish_articles` enum('N','Y') NOT NULL default 'Y',
  `is_completed` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  KEY `issue_index` (`fk_publication_id`,`fk_issue_id`,`fk_language_id`),
  KEY `action_time_index` (`time_action`,`is_completed`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Issues`
##

DROP TABLE IF EXISTS `Issues`;
CREATE TABLE `Issues` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  `PublicationDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Published` enum('N','Y') NOT NULL default 'N',
  `IssueTplId` int(10) unsigned default NULL,
  `SectionTplId` int(10) unsigned default NULL,
  `ArticleTplId` int(10) unsigned default NULL,
  `ShortName` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`IdPublication`,`Number`,`IdLanguage`),
  UNIQUE KEY `ShortName` (`IdPublication`,`IdLanguage`,`ShortName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `KeywordClasses`
##

DROP TABLE IF EXISTS `KeywordClasses`;
CREATE TABLE `KeywordClasses` (
  `IdDictionary` int(10) unsigned NOT NULL default '0',
  `IdClasses` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Definition` mediumblob NOT NULL,
  PRIMARY KEY  (`IdDictionary`,`IdClasses`,`IdLanguage`),
  KEY `IdClasses` (`IdClasses`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `KeywordIndex`
##

DROP TABLE IF EXISTS `KeywordIndex`;
CREATE TABLE `KeywordIndex` (
  `Keyword` varchar(70) NOT NULL default '',
  `Id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Languages`
##

DROP TABLE IF EXISTS `Languages`;
CREATE TABLE `Languages` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(140) NOT NULL default '',
  `CodePage` varchar(140) NOT NULL default '',
  `OrigName` varchar(140) NOT NULL default '',
  `Code` varchar(21) NOT NULL default '',
  `Month1` varchar(140) NOT NULL default '',
  `Month2` varchar(140) NOT NULL default '',
  `Month3` varchar(140) NOT NULL default '',
  `Month4` varchar(140) NOT NULL default '',
  `Month5` varchar(140) NOT NULL default '',
  `Month6` varchar(140) NOT NULL default '',
  `Month7` varchar(140) NOT NULL default '',
  `Month8` varchar(140) NOT NULL default '',
  `Month9` varchar(140) NOT NULL default '',
  `Month10` varchar(140) NOT NULL default '',
  `Month11` varchar(140) NOT NULL default '',
  `Month12` varchar(140) NOT NULL default '',
  `WDay1` varchar(140) NOT NULL default '',
  `WDay2` varchar(140) NOT NULL default '',
  `WDay3` varchar(140) NOT NULL default '',
  `WDay4` varchar(140) NOT NULL default '',
  `WDay5` varchar(140) NOT NULL default '',
  `WDay6` varchar(140) NOT NULL default '',
  `WDay7` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=1171 DEFAULT CHARSET=latin1;

##
## Dumping data for table `Languages`
##

LOCK TABLES `Languages` WRITE;
INSERT INTO `Languages` VALUES (1,'English','ISO_8859-1','English','en','January','February','March','April','May','June','July','August','September','October','November','December','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),(5,'German','ISO_8859-1','Deutsch','de','Januar','Februar','MÃ¤rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'),(9,'Portuguese','ISO_8859-1','PortuguÃªs','pt','Janeiro','Fevereiro','MarÃ§o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','Domingo','Segunda-feira','TerÃ§a-feira','Quarta-feira','Quinta-feira','Sexta-feira','SÃ¡bado'),(12,'French','ISO_8859-1','FranÃ§ais','fr','Janvier','FÃ©vrier','Mars','Avril','Peut','Juin','Juli','AoÃ»t','Septembre','Octobre','Novembre','DÃ©cembre','Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'),(13,'Spanish','ISO_8859-1','EspaÃ±ol','es','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre','Domingo','Lunes','Martes','MiÃ©rcoles','Jueves','Viernes','SÃ¡bado'),(2,'Romanian','ISO_8859-2','RomÃ¢nÄƒ','ro','Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie','DuminicÄƒ','Luni','MarÅ£i','Miercuri','Joi','Vineri','SÃ¢mbÄƒtÄƒ'),(7,'Croatian','ISO_8859-2','Hrvatski','hr','SijeÄanj','VeljaÄa','OÅ¾ujak','Travanj','Svibanj','Lipanj','Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac','Nedjelja','Ponedjeljak','Utorak','Srijeda','ÄŒetvrtak','Petak','Subota'),(8,'Czech','ISO_8859-2','ÄŒeskÃ½','cz','Leden','Ãšnor','BÅ™ezen','Duben','KvÄ›ten','ÄŒerven','ÄŒervenec','Srpen','ZÃ¡Å™Ã­','Å˜Ã­jen','Listopad','Prosinec','NedÄ›le','PondÄ›lÃ­','ÃšterÃ½','StÅ™eda','ÄŒtvrtek','PÃ¡tek','Sobota'),(11,'Serbo-Croatian','ISO_8859-2','Srpskohrvatski','sh','Januar','Februar','Mart','April','Maj','Juni','Juli','Avgust','Septembar','Oktobar','Novembar','Decembar','Nedjelja','Ponedeljak','Utorak','Srijeda','ÄŒetvrtak','Petak','Subota'),(10,'Serbian (Cyrillic)','ISO_8859-5','Ð¡Ñ€Ð¿ÑÐºÐ¸ (Ð‹Ð¸Ñ€Ð¸Ð»Ð¸Ñ†Ð°)','sr','Ñ˜Ð°Ð½ÑƒÐ°Ñ€','Ñ„ÐµÐ±Ñ€ÑƒÐ°Ñ€','Ð¼Ð°Ñ€Ñ‚','Ð°Ð¿Ñ€Ð¸Ð»','Ð¼Ð°Ñ˜','Ñ˜ÑƒÐ½','Ñ˜ÑƒÐ»','Ð°Ð²Ð³ÑƒÑÑ‚','ÑÐµÐ¿Ñ‚ÐµÐ¼Ð±Ð°Ñ€','Ð¾ÐºÑ‚Ð¾Ð±Ð°Ñ€','Ð½Ð¾Ð²ÐµÐ¼Ð±Ð°Ñ€','Ð´ÐµÑ†ÐµÐ¼Ð±Ð°Ñ€','ÐÐµÐ´ÐµÑ™Ð°','ÐŸÐ¾Ð½ÐµÐ´ÐµÑ™Ð°Ðº','Ð£Ñ‚Ð¾Ñ€Ð°Ðº','Ð¡Ñ€ÐµÐ´Ð°','Ð§ÐµÑ‚Ð²Ñ€Ñ‚Ð°Ðº','ÐŸÐµÑ‚Ð°Ðº','Ð¡ÑƒÐ±Ð¾Ñ‚Ð°'),(15,'Russian','ISO_8859-5','Ð ÑƒÑÑÐºÐ¸Ð¹','ru','ÑÐ½Ð²Ð°Ñ€ÑŒ','Ñ„ÐµÐ²Ñ€Ð°Ð»ÑŒ','Ð¼Ð°Ñ€Ñ‚','Ð°Ð¿Ñ€ÐµÐ»ÑŒ','Ð¼Ð°Ð¹','Ð¸ÑŽÐ½ÑŒ','Ð¸ÑŽÐ»ÑŒ','Ð°Ð²Ð³ÑƒÑÑ‚','ÑÐµÐ½Ñ‚ÑÐ±Ñ€ÑŒ','Ð¾ÐºÑ‚ÑÐ±Ñ€ÑŒ','Ð½Ð¾ÑÐ±Ñ€ÑŒ','Ð´ÐµÐºÐ°Ð±Ñ€ÑŒ','Ð²Ð¾ÑÐºÑ€ÐµÑÐµÐ½ÑŒÐµ','Ð¿Ð¾Ð½ÐµÐ´ÐµÐ»ÑŒÐ½Ð¸Ðº','Ð²Ñ‚Ð¾Ñ€Ð½Ð¸Ðº','ÑÑ€ÐµÐ´Ð°','Ñ‡ÐµÑ‚Ð²ÐµÑ€Ð³','Ð¿ÑÑ‚Ð½Ð¸Ñ†Ð°','ÑÑƒÐ±Ð±Ð¾Ñ‚Ð°'),(18,'Swedish','','Svenska','sv','januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december','sÃ¶ndag','mÃ¥ndag','tisdag','onsdag','torsdag','fredag','lÃ¶rdag'),(16,'Chinese','UTF-8','ä¸­æ–‡','zh','ä¸€æœˆ','äºŒæœˆ','ä¸‰æœˆ','å››æœˆ','äº”æœˆ','å…­æœˆ','ä¸ƒæœˆ','å…«æœˆ','ä¹æœˆ','åæœˆ','åä¸€æœˆ','åäºŒæœˆ','æ˜ŸæœŸ','æ˜ŸæœŸ','æ˜ŸæœŸ','æ˜ŸæœŸ','æ˜ŸæœŸ','æ˜ŸæœŸ','æ˜ŸæœŸ'),(17,'Arabic','UTF-8','Ø¹Ø±Ø¨ÙŠ','ar','ÙƒØ§Ù†ÙˆÙ† \nØ§Ù„Ø«Ø§Ù†ÙŠ','Ø´Ø¨Ø§Ø·','Ø¢Ø°Ø§Ø±','Ù†ÙŠØ³Ø§Ù†','Ø¢ÙŠØ§Ø±','Ø­Ø²ÙŠØ±Ø§Ù†','ØªÙ…ÙˆØ²','Ø¢Ø¨','Ø£ÙŠÙ„ÙˆÙ„','ØªØ´Ø±ÙŠÙ† \nØ£ÙˆÙ„','ØªØ´Ø±ÙŠÙ† Ø§Ù„Ø«Ø§Ù†ÙŠ','ÙƒØ§Ù†ÙˆÙ† \nØ£ÙˆÙ„','Ø§Ù„Ø£Ø­Ø¯','Ø§Ù„Ø§Ø«Ù†ÙŠÙ†','Ø§Ù„Ø«Ù„Ø§Ø«Ø§Ø¡','Ø§Ù„Ø£Ø±Ø¨Ø¹Ø§Ø¡','Ø§Ù„Ø®Ù…ÙŠØ³','Ø§Ù„Ø¬Ù…Ø¹Ø©','Ø§Ù„Ø³Ø¨Øª');
UNLOCK TABLES;

##
## Table structure for table `Log`
##

DROP TABLE IF EXISTS `Log`;
CREATE TABLE `Log` (
  `time_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `fk_event_id` int(10) unsigned NOT NULL default '0',
  `fk_user_id` int(10) unsigned default NULL,
  `text` varchar(255) NOT NULL default '',
  KEY `IdEvent` (`fk_event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Publications`
##

DROP TABLE IF EXISTS `Publications`;
CREATE TABLE `Publications` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '',
  `IdDefaultLanguage` int(10) unsigned NOT NULL default '0',
  `TimeUnit` enum('D','W','M','Y') NOT NULL default 'D',
  `UnitCost` float(10,2) unsigned NOT NULL default '0.00',
  `UnitCostAllLang` float(10,2) unsigned NOT NULL default '0.00',
  `Currency` varchar(140) NOT NULL default '',
  `TrialTime` int(10) unsigned NOT NULL default '0',
  `PaidTime` int(10) unsigned NOT NULL default '0',
  `IdDefaultAlias` int(10) unsigned NOT NULL default '0',
  `IdURLType` int(10) unsigned NOT NULL default '1',
  `fk_forum_id` int(11) default NULL,
  `comments_enabled` tinyint(1) NOT NULL default '0',
  `comments_article_default_enabled` tinyint(1) NOT NULL default '0',
  `comments_subscribers_moderated` tinyint(1) NOT NULL default '0',
  `comments_public_moderated` tinyint(1) NOT NULL default '0',
  `comments_captcha_enabled` tinyint(1) NOT NULL default '0',
  `comments_spam_blocking_enabled` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Alias` (`IdDefaultAlias`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

##
## Table structure for table `Sections`
##

DROP TABLE IF EXISTS `Sections`;
CREATE TABLE `Sections` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `NrIssue` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `ShortName` varchar(32) NOT NULL default '',
  `Description` blob,
  `SectionTplId` int(10) unsigned default NULL,
  `ArticleTplId` int(10) unsigned default NULL,
  PRIMARY KEY  (`IdPublication`,`NrIssue`,`IdLanguage`,`Number`),
  UNIQUE KEY `IdPublication` (`IdPublication`,`NrIssue`,`IdLanguage`,`Name`),
  UNIQUE KEY `ShortName` (`IdPublication`,`NrIssue`,`IdLanguage`,`ShortName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `SubsByIP`
##

DROP TABLE IF EXISTS `SubsByIP`;
CREATE TABLE `SubsByIP` (
  `IdUser` int(10) unsigned NOT NULL default '0',
  `StartIP` int(10) unsigned NOT NULL default '0',
  `Addresses` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdUser`,`StartIP`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `SubsDefTime`
##

DROP TABLE IF EXISTS `SubsDefTime`;
CREATE TABLE `SubsDefTime` (
  `CountryCode` char(21) NOT NULL default '',
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `TrialTime` int(10) unsigned NOT NULL default '0',
  `PaidTime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`CountryCode`,`IdPublication`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `SubsSections`
##

DROP TABLE IF EXISTS `SubsSections`;
CREATE TABLE `SubsSections` (
  `IdSubscription` int(10) unsigned NOT NULL default '0',
  `SectionNumber` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) NOT NULL default '0',
  `StartDate` date NOT NULL default '0000-00-00',
  `Days` int(10) unsigned NOT NULL default '0',
  `PaidDays` int(10) unsigned NOT NULL default '0',
  `NoticeSent` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`IdSubscription`,`SectionNumber`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Subscriptions`
##

DROP TABLE IF EXISTS `Subscriptions`;
CREATE TABLE `Subscriptions` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `IdUser` int(10) unsigned NOT NULL default '0',
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `Active` enum('Y','N') NOT NULL default 'Y',
  `ToPay` float(10,2) unsigned NOT NULL default '0.00',
  `Currency` varchar(70) NOT NULL default '',
  `Type` enum('T','P') NOT NULL default 'T',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `IdUser` (`IdUser`,`IdPublication`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `SystemPreferences`
##

DROP TABLE IF EXISTS `SystemPreferences`;
CREATE TABLE `SystemPreferences` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `varname` varchar(100) NOT NULL default '',
  `value` varchar(100) default NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `varname` (`varname`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

##
## Dumping data for table `SystemPreferences`
##

LOCK TABLES `SystemPreferences` WRITE;
INSERT INTO `SystemPreferences` VALUES (1,'ExternalSubscriptionManagement','N','2007-03-07 07:15:36'),(2,'KeywordSeparator',',','2007-03-07 07:15:36'),(3,'LoginFailedAttemptsNum','3','2007-06-16 04:52:31'),(4,'MaxUploadFileSize','16M','2007-10-04 22:16:54'),(5,'UseDBReplication','N','2007-03-07 07:15:36'),(6,'DBReplicationHost','','2007-03-07 07:15:36'),(7,'DBReplicationUser','','2007-03-07 07:15:36'),(8,'DBReplicationPass','','2007-03-07 07:15:36'),(9,'DBReplicationPort','3306','2007-03-07 07:15:36'),(10,'UseCampcasterAudioclips','N','2007-03-07 07:15:36'),(11,'CampcasterHostName','localhost','2007-03-07 07:15:36'),(12,'CampcasterHostPort','80','2007-03-07 07:15:36'),(13,'CampcasterXRPCPath','/campcaster/storageServer/var/xmlrpc/','2007-03-07 07:15:36'),(14,'CampcasterXRPCFile','xrLocStor.php','2007-03-07 07:15:36'),(15,'SiteOnline','Y','2007-10-07 01:49:11'),(16,'SiteCharset','utf-8','2007-07-26 04:49:32'),(17,'SiteLocale','en-US','2007-07-26 04:49:56'),(18,'SiteCacheEnabled','Y','2007-07-26 04:50:19'),(22,'SiteMetaKeywords','Campsite, MDLF, Campware, CMS, OpenSource, Media','2007-10-05 01:31:36'),(19,'SiteSecretKey','4b506c2968184be185f6282f5dcac832','2007-10-04 20:51:41'),(20,'SiteSessionLifeTime','1400','2007-10-04 20:51:51'),(21,'SiteTitle','Campsite 3.0-alpha Demo Site','2007-10-07 01:39:13'),(23,'SiteMetaDescription','Campsite 3.0 alpha demo site, try it out!','2007-10-07 01:36:18'),(24,'SMTPHost','localhost','2007-10-26 01:30:45'),(25,'SMTPPort','25','2007-10-26 01:30:45');
UNLOCK TABLES;

##
## Table structure for table `TemplateTypes`
##

DROP TABLE IF EXISTS `TemplateTypes`;
CREATE TABLE `TemplateTypes` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` char(20) NOT NULL default '',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

##
## Dumping data for table `TemplateTypes`
##

LOCK TABLES `TemplateTypes` WRITE;
INSERT INTO `TemplateTypes` VALUES (1,'default'),(2,'issue'),(3,'section'),(4,'article'),(5,'nontpl');
UNLOCK TABLES;

##
## Table structure for table `Templates`
##

DROP TABLE IF EXISTS `Templates`;
CREATE TABLE `Templates` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` char(255) NOT NULL default '',
  `Type` int(10) unsigned NOT NULL default '1',
  `Level` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=158 DEFAULT CHARSET=latin1;

##
## Table structure for table `TimeUnits`
##

DROP TABLE IF EXISTS `TimeUnits`;
CREATE TABLE `TimeUnits` (
  `Unit` char(1) NOT NULL default '',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(70) NOT NULL default '',
  PRIMARY KEY  (`Unit`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `TimeUnits`
##

LOCK TABLES `TimeUnits` WRITE;
INSERT INTO `TimeUnits` VALUES ('D',1,'days'),('W',1,'weeks'),('M',1,'months'),('Y',1,'years'),('D',13,'dÃ­as'),('W',13,'semanas'),('M',13,'meses'),('Y',13,'aÃ±os');
UNLOCK TABLES;

##
## Table structure for table `TopicFields`
##

DROP TABLE IF EXISTS `TopicFields`;
CREATE TABLE `TopicFields` (
  `ArticleType` varchar(166) NOT NULL default '',
  `FieldName` varchar(166) NOT NULL default '',
  `RootTopicId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ArticleType`,`FieldName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Topics`
##

DROP TABLE IF EXISTS `Topics`;
CREATE TABLE `Topics` (
  `Id` int(10) unsigned NOT NULL default '0',
  `LanguageId` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `ParentId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`,`LanguageId`),
  UNIQUE KEY `Name` (`LanguageId`,`Name`),
  KEY `topic_id` (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `Translations`
##

DROP TABLE IF EXISTS `Translations`;
CREATE TABLE `Translations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `phrase_id` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `translation_text` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `phrase_language_index` (`phrase_id`,`fk_language_id`),
  KEY `phrase_id` (`phrase_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1089 DEFAULT CHARSET=latin1;

##
## Table structure for table `URLTypes`
##

DROP TABLE IF EXISTS `URLTypes`;
CREATE TABLE `URLTypes` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(15) NOT NULL default '',
  `Description` mediumblob NOT NULL,
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

##
## Dumping data for table `URLTypes`
##

LOCK TABLES `URLTypes` WRITE;
INSERT INTO `URLTypes` VALUES (1,'template path',''),(2,'short names','');
UNLOCK TABLES;

##
## Table structure for table `XArticle`
##

DROP TABLE IF EXISTS `XArticle`;
CREATE TABLE `XArticle` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `FDeck` varchar(255) NOT NULL default '',
  `FByline` varchar(255) NOT NULL default '',
  `FTeaser_a` varchar(255) NOT NULL default '',
  `FTeaser_b` varchar(255) NOT NULL default '',
  `FIntro` mediumblob NOT NULL,
  `FFull_text` mediumblob NOT NULL,
  PRIMARY KEY  (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_applications`
##

DROP TABLE IF EXISTS `liveuser_applications`;
CREATE TABLE `liveuser_applications` (
  `application_id` int(11) NOT NULL default '0',
  `application_define_name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`application_id`),
  UNIQUE KEY `applications_define_name_i_idx` (`application_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_applications`
##

LOCK TABLES `liveuser_applications` WRITE;
INSERT INTO `liveuser_applications` VALUES (1,'Campsite'),(2,'Campcaster');
UNLOCK TABLES;

##
## Table structure for table `liveuser_applications_application_id_seq`
##

DROP TABLE IF EXISTS `liveuser_applications_application_id_seq`;
CREATE TABLE `liveuser_applications_application_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_applications_application_id_seq`
##

LOCK TABLES `liveuser_applications_application_id_seq` WRITE;
INSERT INTO `liveuser_applications_application_id_seq` VALUES (2);
UNLOCK TABLES;

##
## Table structure for table `liveuser_applications_seq`
##

DROP TABLE IF EXISTS `liveuser_applications_seq`;
CREATE TABLE `liveuser_applications_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_area_admin_areas`
##

DROP TABLE IF EXISTS `liveuser_area_admin_areas`;
CREATE TABLE `liveuser_area_admin_areas` (
  `area_id` int(11) NOT NULL default '0',
  `perm_user_id` int(11) NOT NULL default '0',
  UNIQUE KEY `area_admin_areas_id_i_idx` (`area_id`,`perm_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_areas`
##

DROP TABLE IF EXISTS `liveuser_areas`;
CREATE TABLE `liveuser_areas` (
  `area_id` int(11) NOT NULL default '0',
  `application_id` int(11) NOT NULL default '0',
  `area_define_name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`area_id`),
  UNIQUE KEY `areas_define_name_i_idx` (`application_id`,`area_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_areas`
##

LOCK TABLES `liveuser_areas` WRITE;
INSERT INTO `liveuser_areas` VALUES (1,1,'Articles');
UNLOCK TABLES;

##
## Table structure for table `liveuser_areas_seq`
##

DROP TABLE IF EXISTS `liveuser_areas_seq`;
CREATE TABLE `liveuser_areas_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_group_subgroups`
##

DROP TABLE IF EXISTS `liveuser_group_subgroups`;
CREATE TABLE `liveuser_group_subgroups` (
  `group_id` int(11) NOT NULL default '0',
  `subgroup_id` int(11) NOT NULL default '0',
  UNIQUE KEY `group_subgroups_id_i_idx` (`group_id`,`subgroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_grouprights`
##

DROP TABLE IF EXISTS `liveuser_grouprights`;
CREATE TABLE `liveuser_grouprights` (
  `group_id` int(11) NOT NULL default '0',
  `right_id` int(11) NOT NULL default '0',
  `right_level` int(11) NOT NULL default '3',
  UNIQUE KEY `grouprights_id_i_idx` (`group_id`,`right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_grouprights`
##

LOCK TABLES `liveuser_grouprights` WRITE;
INSERT INTO `liveuser_grouprights` VALUES (1,1,3),(1,2,3),(1,3,3),(1,4,3),(1,5,3),(1,6,3),(1,7,3),(1,8,3),(1,9,3),(1,10,3),(1,11,3),(1,12,3),(1,13,3),(1,14,3),(1,15,3),(1,16,3),(1,17,3),(1,18,3),(1,19,3),(1,20,3),(1,21,3),(1,22,3),(1,23,3),(1,24,3),(1,25,3),(1,26,3),(1,27,3),(1,28,3),(1,29,3),(1,30,3),(1,31,3),(1,32,3),(1,33,3),(1,34,3),(1,35,3),(1,36,3),(1,37,3),(1,38,3),(1,39,3),(1,40,3),(1,41,3),(1,42,3),(1,43,3),(1,44,3),(1,45,3),(1,46,3),(1,47,3),(1,48,3),(1,49,3),(1,50,3),(1,51,3),(1,52,3),(1,53,3),(1,54,3),(1,55,3),(1,56,3),(1,57,3),(1,58,3),(1,59,3),(1,60,3),(1,61,3),(1,62,3),(1,63,3),(1,64,3),(1,65,3),(1,66,3),(1,67,3),(1,68,3),(1,69,3),(1,70,3),(2,1,3),(2,2,3),(2,3,3),(2,4,3),(2,5,3),(2,6,3),(2,7,3),(2,8,3),(2,9,3),(2,10,3),(2,12,3),(2,13,3),(2,14,3),(2,15,3),(2,17,3),(2,18,3),(2,19,3),(2,22,3),(2,23,3),(2,25,3),(2,26,3),(2,27,3),(2,28,3),(2,29,3),(2,30,3),(2,34,3),(2,35,3),(2,36,3),(2,37,3),(2,38,3),(2,39,3),(2,41,3),(2,42,3),(2,43,3),(2,44,3),(2,45,3),(2,47,3),(2,48,3),(2,49,3),(2,52,3),(2,55,3),(2,57,3),(2,60,3),(2,62,3),(2,63,3),(2,66,3),(2,67,3),(2,68,3),(2,69,3),(3,1,3),(3,2,3),(3,3,3),(3,4,3),(3,5,3),(3,6,3),(3,7,3),(3,8,3),(3,9,3),(3,10,3),(3,14,3),(3,17,3),(3,18,3),(3,25,3),(3,26,3),(3,27,3),(3,28,3),(3,29,3),(3,34,3),(3,35,3),(3,36,3),(3,37,3),(3,38,3),(3,39,3),(3,42,3),(3,43,3),(3,44,3),(3,45,3),(3,46,3),(3,47,3),(3,48,3),(3,49,3),(3,51,3),(3,66,3),(3,68,3),(4,4,3),(4,10,3),(4,18,3),(4,6,3),(4,8,3),(5,59,3),(5,70,3),(5,61,3);
UNLOCK TABLES;

##
## Table structure for table `liveuser_groups`
##

DROP TABLE IF EXISTS `liveuser_groups`;
CREATE TABLE `liveuser_groups` (
  `group_id` int(11) NOT NULL default '0',
  `group_type` int(11) NOT NULL default '0',
  `group_define_name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `groups_define_name_i_idx` (`group_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_groups`
##

LOCK TABLES `liveuser_groups` WRITE;
INSERT INTO `liveuser_groups` VALUES (1,0,'Administrator'),(2,0,'Chief Editor'),(3,0,'Editor'),(4,0,'Journalist'),(5,0,'Subscription manager');
UNLOCK TABLES;

##
## Table structure for table `liveuser_groups_group_id_seq`
##

DROP TABLE IF EXISTS `liveuser_groups_group_id_seq`;
CREATE TABLE `liveuser_groups_group_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_groups_group_id_seq`
##

LOCK TABLES `liveuser_groups_group_id_seq` WRITE;
INSERT INTO `liveuser_groups_group_id_seq` VALUES (6);
UNLOCK TABLES;

##
## Table structure for table `liveuser_groups_seq`
##

DROP TABLE IF EXISTS `liveuser_groups_seq`;
CREATE TABLE `liveuser_groups_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_groupusers`
##

DROP TABLE IF EXISTS `liveuser_groupusers`;
CREATE TABLE `liveuser_groupusers` (
  `perm_user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  UNIQUE KEY `groupusers_id_i_idx` (`perm_user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_groupusers`
##

LOCK TABLES `liveuser_groupusers` WRITE;
INSERT INTO `liveuser_groupusers` VALUES (1,1);
UNLOCK TABLES;

##
## Table structure for table `liveuser_perm_users`
##

DROP TABLE IF EXISTS `liveuser_perm_users`;
CREATE TABLE `liveuser_perm_users` (
  `perm_user_id` int(11) NOT NULL default '0',
  `auth_user_id` varchar(32) NOT NULL default '',
  `auth_container_name` varchar(32) NOT NULL default '',
  `perm_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`perm_user_id`),
  UNIQUE KEY `perm_users_auth_id_i_idx` (`auth_user_id`,`auth_container_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_perm_users`
##

LOCK TABLES `liveuser_perm_users` WRITE;
INSERT INTO `liveuser_perm_users` VALUES (1,'1','DB',1);
UNLOCK TABLES;

##
## Table structure for table `liveuser_perm_users_perm_user_id_seq`
##

DROP TABLE IF EXISTS `liveuser_perm_users_perm_user_id_seq`;
CREATE TABLE `liveuser_perm_users_perm_user_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_perm_users_perm_user_id_seq`
##

LOCK TABLES `liveuser_perm_users_perm_user_id_seq` WRITE;
INSERT INTO `liveuser_perm_users_perm_user_id_seq` VALUES (3);
UNLOCK TABLES;

##
## Table structure for table `liveuser_perm_users_seq`
##

DROP TABLE IF EXISTS `liveuser_perm_users_seq`;
CREATE TABLE `liveuser_perm_users_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_right_implied`
##

DROP TABLE IF EXISTS `liveuser_right_implied`;
CREATE TABLE `liveuser_right_implied` (
  `right_id` int(11) NOT NULL default '0',
  `implied_right_id` int(11) NOT NULL default '0',
  UNIQUE KEY `right_implied_id_i_idx` (`right_id`,`implied_right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_rights`
##

DROP TABLE IF EXISTS `liveuser_rights`;
CREATE TABLE `liveuser_rights` (
  `right_id` int(11) NOT NULL default '0',
  `area_id` int(11) NOT NULL default '0',
  `right_define_name` varchar(32) NOT NULL default '',
  `has_implied` tinyint(1) default '1',
  PRIMARY KEY  (`right_id`),
  UNIQUE KEY `rights_define_name_i_idx` (`area_id`,`right_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_rights`
##

LOCK TABLES `liveuser_rights` WRITE;
INSERT INTO `liveuser_rights` VALUES (1,0,'AddArticle',1),(2,0,'AddAudioclip',1),(3,0,'AddFile',1),(4,0,'AddImage',1),(5,0,'AttachAudioclipToArticle',1),(6,0,'AttachImageToArticle',1),(7,0,'AttachTopicToArticle',1),(8,0,'ChangeArticle',1),(9,0,'ChangeFile',1),(10,0,'ChangeImage',1),(11,0,'ChangeSystemPreferences',1),(12,0,'CommentEnable',1),(13,0,'CommentModerate',1),(14,0,'DeleteArticle',1),(15,0,'DeleteArticleTypes',1),(16,0,'DeleteCountries',1),(17,0,'DeleteFile',1),(18,0,'DeleteImage',1),(19,0,'DeleteIssue',1),(20,0,'DeleteLanguages',1),(21,0,'DeletePub',1),(22,0,'DeleteSection',1),(23,0,'DeleteTempl',1),(24,0,'DeleteUsers',1),(25,0,'EditorBold',1),(26,0,'EditorCharacterMap',1),(27,0,'EditorCopyCutPaste',1),(28,0,'EditorEnlarge',1),(29,0,'EditorFindReplace',1),(30,0,'EditorFontColor',1),(31,0,'EditorFontFace',1),(32,0,'EditorFontSize',1),(33,0,'EditorHorizontalRule',1),(34,0,'EditorImage',1),(35,0,'EditorIndent',1),(36,0,'EditorItalic',1),(37,0,'EditorLink',1),(38,0,'EditorListBullet',1),(39,0,'EditorListNumber',1),(40,0,'EditorSourceView',1),(41,0,'EditorStrikethrough',1),(42,0,'EditorSubhead',1),(43,0,'EditorSubscript',1),(44,0,'EditorSuperscript',1),(45,0,'EditorTable',1),(46,0,'EditorTextAlignment',1),(47,0,'EditorTextDirection',1),(48,0,'EditorUnderline',1),(49,0,'EditorUndoRedo',1),(50,0,'InitializeTemplateEngine',1),(51,0,'MailNotify',1),(52,0,'ManageArticleTypes',1),(53,0,'ManageCountries',1),(54,0,'ManageIndexer',1),(55,0,'ManageIssue',1),(56,0,'ManageLanguages',1),(57,0,'ManageLocalizer',1),(58,0,'ManagePub',1),(59,0,'ManageReaders',1),(60,0,'ManageSection',1),(61,0,'ManageSubscriptions',1),(62,0,'ManageTempl',1),(63,0,'ManageTopics',1),(64,0,'ManageUserTypes',1),(65,0,'ManageUsers',1),(66,0,'MoveArticle',1),(67,0,'Publish',1),(68,0,'TranslateArticle',1),(69,0,'ViewLogs',1),(70,0,'SyncPhorumUsers',1);
UNLOCK TABLES;

##
## Table structure for table `liveuser_rights_right_id_seq`
##

DROP TABLE IF EXISTS `liveuser_rights_right_id_seq`;
CREATE TABLE `liveuser_rights_right_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_rights_right_id_seq`
##

LOCK TABLES `liveuser_rights_right_id_seq` WRITE;
INSERT INTO `liveuser_rights_right_id_seq` VALUES (70);
UNLOCK TABLES;

##
## Table structure for table `liveuser_rights_seq`
##

DROP TABLE IF EXISTS `liveuser_rights_seq`;
CREATE TABLE `liveuser_rights_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_translations`
##

DROP TABLE IF EXISTS `liveuser_translations`;
CREATE TABLE `liveuser_translations` (
  `translation_id` int(11) NOT NULL default '0',
  `section_id` int(11) NOT NULL default '0',
  `section_type` int(11) NOT NULL default '0',
  `language_id` varchar(32) NOT NULL default '',
  `name` varchar(32) NOT NULL default '',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`translation_id`),
  UNIQUE KEY `translations_translation_i_idx` (`section_id`,`section_type`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_translations_seq`
##

DROP TABLE IF EXISTS `liveuser_translations_seq`;
CREATE TABLE `liveuser_translations_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_userrights`
##

DROP TABLE IF EXISTS `liveuser_userrights`;
CREATE TABLE `liveuser_userrights` (
  `perm_user_id` int(11) NOT NULL default '0',
  `right_id` int(11) NOT NULL default '0',
  `right_level` int(11) NOT NULL default '3',
  UNIQUE KEY `userrights_id_i_idx` (`perm_user_id`,`right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `liveuser_users`
##

DROP TABLE IF EXISTS `liveuser_users`;
CREATE TABLE `liveuser_users` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `KeyId` int(10) unsigned default NULL,
  `Name` varchar(255) character set utf8 collate utf8_bin NOT NULL default '',
  `UName` varchar(70) character set utf8 collate utf8_bin NOT NULL default '',
  `Password` varchar(64) character set utf8 collate utf8_bin NOT NULL default '',
  `EMail` varchar(255) character set utf8 collate utf8_bin NOT NULL default '',
  `Reader` enum('Y','N') character set utf8 collate utf8_bin NOT NULL default 'Y',
  `fk_user_type` int(10) unsigned default NULL,
  `City` varchar(100) character set utf8 collate utf8_bin NOT NULL default '',
  `StrAddress` varchar(255) character set utf8 collate utf8_bin NOT NULL default '',
  `State` varchar(32) character set utf8 collate utf8_bin NOT NULL default '',
  `CountryCode` varchar(21) character set utf8 collate utf8_bin default NULL,
  `Phone` varchar(20) character set utf8 collate utf8_bin NOT NULL default '',
  `Fax` varchar(20) character set utf8 collate utf8_bin NOT NULL default '',
  `Contact` varchar(64) character set utf8 collate utf8_bin NOT NULL default '',
  `Phone2` varchar(20) character set utf8 collate utf8_bin NOT NULL default '',
  `Title` enum('Mr.','Mrs.','Ms.','Dr.') character set utf8 collate utf8_bin NOT NULL default 'Mr.',
  `Gender` enum('M','F') character set utf8 collate utf8_bin default NULL,
  `Age` enum('0-17','18-24','25-39','40-49','50-65','65-') character set utf8 collate utf8_bin NOT NULL default '0-17',
  `PostalCode` varchar(70) character set utf8 collate utf8_bin NOT NULL default '',
  `Employer` varchar(140) character set utf8 collate utf8_bin NOT NULL default '',
  `EmployerType` varchar(140) character set utf8 collate utf8_bin NOT NULL default '',
  `Position` varchar(70) character set utf8 collate utf8_bin NOT NULL default '',
  `Interests` mediumblob NOT NULL,
  `How` varchar(255) character set utf8 collate utf8_bin NOT NULL default '',
  `Languages` varchar(100) character set utf8 collate utf8_bin NOT NULL default '',
  `Improvements` mediumblob NOT NULL,
  `Pref1` enum('N','Y') character set utf8 collate utf8_bin NOT NULL default 'N',
  `Pref2` enum('N','Y') character set utf8 collate utf8_bin NOT NULL default 'N',
  `Pref3` enum('N','Y') character set utf8 collate utf8_bin NOT NULL default 'N',
  `Pref4` enum('N','Y') character set utf8 collate utf8_bin NOT NULL default 'N',
  `Field1` varchar(150) character set utf8 collate utf8_bin NOT NULL default '',
  `Field2` varchar(150) character set utf8 collate utf8_bin NOT NULL default '',
  `Field3` varchar(150) character set utf8 collate utf8_bin NOT NULL default '',
  `Field4` varchar(150) character set utf8 collate utf8_bin NOT NULL default '',
  `Field5` varchar(150) character set utf8 collate utf8_bin NOT NULL default '',
  `Text1` mediumblob NOT NULL,
  `Text2` mediumblob NOT NULL,
  `Text3` mediumblob NOT NULL,
  `time_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `time_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `lastLogin` datetime default '1970-01-01 00:00:00',
  `isActive` tinyint(1) default '1',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `UName` (`UName`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_users`
##

LOCK TABLES `liveuser_users` WRITE;
INSERT INTO `liveuser_users` VALUES (1,948604862,'Administrator','admin','','campsite-support@campware.org','N',1,'','','','US','','','','','Mr.','M','25-39','','','Media','','','','','','N','N','N','N','','','','','','','','','2007-10-21 19:00:48','0000-00-00 00:00:00','2007-10-21 14:00:48',1);
UNLOCK TABLES;

##
## Table structure for table `liveuser_users_auth_user_id_seq`
##

DROP TABLE IF EXISTS `liveuser_users_auth_user_id_seq`;
CREATE TABLE `liveuser_users_auth_user_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

##
## Dumping data for table `liveuser_users_auth_user_id_seq`
##

LOCK TABLES `liveuser_users_auth_user_id_seq` WRITE;
INSERT INTO `liveuser_users_auth_user_id_seq` VALUES (3);
UNLOCK TABLES;

##
## Table structure for table `phorum_banlists`
##

DROP TABLE IF EXISTS `phorum_banlists`;
CREATE TABLE `phorum_banlists` (
  `id` int(11) NOT NULL auto_increment,
  `forum_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `pcre` tinyint(4) NOT NULL default '0',
  `string` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_files`
##

DROP TABLE IF EXISTS `phorum_files`;
CREATE TABLE `phorum_files` (
  `file_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `filesize` int(11) NOT NULL default '0',
  `file_data` mediumtext NOT NULL,
  `add_datetime` int(10) unsigned NOT NULL default '0',
  `message_id` int(10) unsigned NOT NULL default '0',
  `link` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`file_id`),
  KEY `add_datetime` (`add_datetime`),
  KEY `message_id_link` (`message_id`,`link`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_forum_group_xref`
##

DROP TABLE IF EXISTS `phorum_forum_group_xref`;
CREATE TABLE `phorum_forum_group_xref` (
  `forum_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `permission` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forum_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_forums`
##

DROP TABLE IF EXISTS `phorum_forums`;
CREATE TABLE `phorum_forums` (
  `forum_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `active` smallint(6) NOT NULL default '0',
  `description` text NOT NULL,
  `template` varchar(50) NOT NULL default '',
  `folder_flag` tinyint(1) NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `list_length_flat` int(10) unsigned NOT NULL default '0',
  `list_length_threaded` int(10) unsigned NOT NULL default '0',
  `moderation` int(10) unsigned NOT NULL default '0',
  `threaded_list` tinyint(4) NOT NULL default '0',
  `threaded_read` tinyint(4) NOT NULL default '0',
  `float_to_top` tinyint(4) NOT NULL default '0',
  `check_duplicate` tinyint(4) NOT NULL default '0',
  `allow_attachment_types` varchar(100) NOT NULL default '',
  `max_attachment_size` int(10) unsigned NOT NULL default '0',
  `max_totalattachment_size` int(10) unsigned NOT NULL default '0',
  `max_attachments` int(10) unsigned NOT NULL default '0',
  `pub_perms` int(10) unsigned NOT NULL default '0',
  `reg_perms` int(10) unsigned NOT NULL default '0',
  `display_ip_address` smallint(5) unsigned NOT NULL default '1',
  `allow_email_notify` smallint(5) unsigned NOT NULL default '1',
  `language` varchar(100) NOT NULL default 'english',
  `email_moderators` tinyint(1) NOT NULL default '0',
  `message_count` int(10) unsigned NOT NULL default '0',
  `sticky_count` int(10) unsigned NOT NULL default '0',
  `thread_count` int(10) unsigned NOT NULL default '0',
  `last_post_time` int(10) unsigned NOT NULL default '0',
  `display_order` int(10) unsigned NOT NULL default '0',
  `read_length` int(10) unsigned NOT NULL default '0',
  `vroot` int(10) unsigned NOT NULL default '0',
  `edit_post` tinyint(1) NOT NULL default '1',
  `template_settings` text NOT NULL,
  `count_views` tinyint(1) unsigned NOT NULL default '0',
  `display_fixed` tinyint(1) unsigned NOT NULL default '0',
  `reverse_threading` tinyint(1) NOT NULL default '0',
  `inherit_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`forum_id`),
  KEY `name` (`name`),
  KEY `active` (`active`,`parent_id`),
  KEY `group_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_groups`
##

DROP TABLE IF EXISTS `phorum_groups`;
CREATE TABLE `phorum_groups` (
  `group_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '0',
  `open` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_messages`
##

DROP TABLE IF EXISTS `phorum_messages`;
CREATE TABLE `phorum_messages` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `forum_id` int(10) unsigned NOT NULL default '0',
  `thread` int(10) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `author` varchar(37) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  `email` varchar(100) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `status` tinyint(4) NOT NULL default '2',
  `msgid` varchar(100) NOT NULL default '',
  `modifystamp` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `thread_count` int(10) unsigned NOT NULL default '0',
  `moderator_post` tinyint(3) unsigned NOT NULL default '0',
  `sort` tinyint(4) NOT NULL default '2',
  `datestamp` int(10) unsigned NOT NULL default '0',
  `meta` mediumtext NOT NULL,
  `viewcount` int(10) unsigned NOT NULL default '0',
  `closed` tinyint(4) NOT NULL default '0',
  `thread_depth` tinyint(3) unsigned NOT NULL default '0',
  `thread_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`message_id`),
  KEY `thread_message` (`thread`,`message_id`),
  KEY `thread_forum` (`thread`,`forum_id`),
  KEY `special_threads` (`sort`,`forum_id`),
  KEY `status_forum` (`status`,`forum_id`),
  KEY `list_page_float` (`forum_id`,`parent_id`,`modifystamp`),
  KEY `list_page_flat` (`forum_id`,`parent_id`,`thread`),
  KEY `post_count` (`forum_id`,`status`,`parent_id`),
  KEY `dup_check` (`forum_id`,`author`,`subject`,`datestamp`),
  KEY `forum_max_message` (`forum_id`,`message_id`,`status`,`parent_id`),
  KEY `last_post_time` (`forum_id`,`status`,`modifystamp`),
  KEY `next_prev_thread` (`forum_id`,`status`,`thread`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_pm_buddies`
##

DROP TABLE IF EXISTS `phorum_pm_buddies`;
CREATE TABLE `phorum_pm_buddies` (
  `pm_buddy_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `buddy_user_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pm_buddy_id`),
  UNIQUE KEY `userids` (`user_id`,`buddy_user_id`),
  KEY `buddy_user_id` (`buddy_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_pm_folders`
##

DROP TABLE IF EXISTS `phorum_pm_folders`;
CREATE TABLE `phorum_pm_folders` (
  `pm_folder_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `foldername` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`pm_folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_pm_messages`
##

DROP TABLE IF EXISTS `phorum_pm_messages`;
CREATE TABLE `phorum_pm_messages` (
  `pm_message_id` int(10) unsigned NOT NULL auto_increment,
  `from_user_id` int(10) unsigned NOT NULL default '0',
  `from_username` varchar(50) NOT NULL default '',
  `subject` varchar(100) NOT NULL default '',
  `message` text NOT NULL,
  `datestamp` int(10) unsigned NOT NULL default '0',
  `meta` mediumtext NOT NULL,
  PRIMARY KEY  (`pm_message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_pm_xref`
##

DROP TABLE IF EXISTS `phorum_pm_xref`;
CREATE TABLE `phorum_pm_xref` (
  `pm_xref_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `pm_folder_id` int(10) unsigned NOT NULL default '0',
  `special_folder` varchar(10) default NULL,
  `pm_message_id` int(10) unsigned NOT NULL default '0',
  `read_flag` tinyint(1) NOT NULL default '0',
  `reply_flag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`pm_xref_id`),
  KEY `xref` (`user_id`,`pm_folder_id`,`pm_message_id`),
  KEY `read_flag` (`read_flag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_search`
##

DROP TABLE IF EXISTS `phorum_search`;
CREATE TABLE `phorum_search` (
  `message_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `search_text` mediumtext NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `forum_id` (`forum_id`),
  FULLTEXT KEY `search_text` (`search_text`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_settings`
##

DROP TABLE IF EXISTS `phorum_settings`;
CREATE TABLE `phorum_settings` (
  `name` varchar(255) NOT NULL default '',
  `type` enum('V','S') NOT NULL default 'V',
  `data` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Dumping data for table `phorum_settings`
##

LOCK TABLES `phorum_settings` WRITE;
INSERT INTO `phorum_settings` VALUES ('title','V','Phorum 5'),('cache','V','/tmp'),('session_timeout','V','30'),('short_session_timeout','V','60'),('tight_security','V','0'),('session_path','V','/'),('session_domain','V',''),('admin_session_salt','V','0.62629000 1146135136'),('cache_users','V','0'),('register_email_confirm','V','0'),('default_template','V','default'),('default_language','V','english'),('use_cookies','V','1'),('use_bcc','V','1'),('use_rss','V','1'),('internal_version','V','2006032300'),('PROFILE_FIELDS','S','a:1:{i:0;a:3:{s:4:\"name\";s:9:\"real_name\";s:6:\"length\";i:255;s:13:\"html_disabled\";i:1;}}'),('enable_pm','V','0'),('user_edit_timelimit','V','0'),('enable_new_pm_count','V','1'),('enable_dropdown_userlist','V','1'),('enable_moderator_notifications','V','1'),('show_new_on_index','V','1'),('dns_lookup','V','0'),('tz_offset','V','0'),('user_time_zone','V','1'),('user_template','V','0'),('registration_control','V','1'),('file_uploads','V','0'),('file_types','V',''),('max_file_size','V','0'),('file_space_quota','V','0'),('file_offsite','V','0'),('system_email_from_name','V',''),('hide_forums','V','1'),('track_user_activity','V','86400'),('html_title','V','Phorum'),('head_tags','V',''),('redirect_after_post','V','list'),('reply_on_read_page','V','1'),('status','V','normal'),('use_new_folder_style','V','1'),('default_forum_options','S','a:24:{s:8:\"forum_id\";i:0;s:10:\"moderation\";i:0;s:16:\"email_moderators\";i:0;s:9:\"pub_perms\";i:1;s:9:\"reg_perms\";i:15;s:13:\"display_fixed\";i:0;s:8:\"template\";s:7:\"default\";s:8:\"language\";s:7:\"english\";s:13:\"threaded_list\";i:0;s:13:\"threaded_read\";i:0;s:17:\"reverse_threading\";i:0;s:12:\"float_to_top\";i:1;s:16:\"list_length_flat\";i:30;s:20:\"list_length_threaded\";i:15;s:11:\"read_length\";i:30;s:18:\"display_ip_address\";i:0;s:18:\"allow_email_notify\";i:0;s:15:\"check_duplicate\";i:1;s:11:\"count_views\";i:2;s:15:\"max_attachments\";i:0;s:22:\"allow_attachment_types\";s:0:\"\";s:19:\"max_attachment_size\";i:0;s:24:\"max_totalattachment_size\";i:0;s:5:\"vroot\";i:0;}'),('hooks','S','a:1:{s:6:\"format\";a:2:{s:4:\"mods\";a:2:{i:0;s:7:\"smileys\";i:1;s:6:\"bbcode\";}s:5:\"funcs\";a:2:{i:0;s:18:\"phorum_mod_smileys\";i:1;s:14:\"phorum_bb_code\";}}}'),('mods','S','a:4:{s:4:\"html\";i:0;s:7:\"replace\";i:0;s:7:\"smileys\";i:1;s:6:\"bbcode\";i:1;}'),('mod_emailcomments','S','a:2:{s:9:\"addresses\";a:1:{i:1;s:0:\"\";}s:14:\"from_addresses\";a:1:{i:1;s:0:\"\";}}'),('http_path','V','http://campsite.localhost.localdomain:8080'),('system_email_from_address','V','holman.romero@campware.org'),('installed','V','1'),('mod_smileys','S','a:4:{s:6:\"prefix\";s:22:\"./mods/smileys/images/\";s:7:\"smileys\";a:21:{i:0;a:6:{s:6:\"search\";s:4:\"(:P)\";s:3:\"alt\";s:39:\"spinning smiley sticking its tongue out\";s:6:\"smiley\";s:12:\"smiley25.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:1;a:6:{s:6:\"search\";s:4:\"(td)\";s:3:\"alt\";s:9:\"thumbs up\";s:6:\"smiley\";s:12:\"smiley23.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:2;a:6:{s:6:\"search\";s:4:\"(tu)\";s:3:\"alt\";s:11:\"thumbs down\";s:6:\"smiley\";s:12:\"smiley24.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:3;a:6:{s:6:\"search\";s:4:\":)-D\";s:3:\"alt\";s:17:\"smileys with beer\";s:6:\"smiley\";s:12:\"smiley15.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:4;a:6:{s:6:\"search\";s:4:\">:D<\";s:3:\"alt\";s:17:\"the finger smiley\";s:6:\"smiley\";s:12:\"smiley14.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:5;a:6:{s:6:\"search\";s:3:\"(:D\";s:3:\"alt\";s:23:\"smiling bouncing smiley\";s:6:\"smiley\";s:12:\"smiley12.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:6;a:6:{s:6:\"search\";s:3:\"8-)\";s:3:\"alt\";s:18:\"eye rolling smiley\";s:6:\"smiley\";s:11:\"smilie8.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:7;a:6:{s:6:\"search\";s:3:\":)o\";s:3:\"alt\";s:15:\"drinking smiley\";s:6:\"smiley\";s:12:\"smiley16.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:8;a:6:{s:6:\"search\";s:3:\"::o\";s:3:\"alt\";s:18:\"eye popping smiley\";s:6:\"smiley\";s:12:\"smilie10.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:9;a:6:{s:6:\"search\";s:3:\"B)-\";s:3:\"alt\";s:14:\"smoking smiley\";s:6:\"smiley\";s:11:\"smilie7.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:10;a:6:{s:6:\"search\";s:2:\":(\";s:3:\"alt\";s:10:\"sad smiley\";s:6:\"smiley\";s:11:\"smilie2.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:11;a:6:{s:6:\"search\";s:2:\":)\";s:3:\"alt\";s:14:\"smiling smiley\";s:6:\"smiley\";s:11:\"smilie1.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:12;a:6:{s:6:\"search\";s:2:\":?\";s:3:\"alt\";s:12:\"moody smiley\";s:6:\"smiley\";s:12:\"smiley17.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:13;a:6:{s:6:\"search\";s:2:\":D\";s:3:\"alt\";s:15:\"grinning smiley\";s:6:\"smiley\";s:11:\"smilie5.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:14;a:6:{s:6:\"search\";s:2:\":P\";s:3:\"alt\";s:26:\"tongue sticking out smiley\";s:6:\"smiley\";s:11:\"smilie6.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:15;a:6:{s:6:\"search\";s:2:\":S\";s:3:\"alt\";s:15:\"confused smiley\";s:6:\"smiley\";s:12:\"smilie11.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:16;a:6:{s:6:\"search\";s:2:\":X\";s:3:\"alt\";s:12:\"angry smiley\";s:6:\"smiley\";s:11:\"smilie9.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:17;a:6:{s:6:\"search\";s:2:\":o\";s:3:\"alt\";s:14:\"yawning smiley\";s:6:\"smiley\";s:11:\"smilie4.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:18;a:6:{s:6:\"search\";s:2:\";)\";s:3:\"alt\";s:14:\"winking smiley\";s:6:\"smiley\";s:11:\"smilie3.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:19;a:6:{s:6:\"search\";s:2:\"B)\";s:3:\"alt\";s:11:\"cool smiley\";s:6:\"smiley\";s:8:\"cool.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}i:20;a:6:{s:6:\"search\";s:2:\"X(\";s:3:\"alt\";s:10:\"hot smiley\";s:6:\"smiley\";s:7:\"hot.gif\";s:4:\"uses\";i:2;s:6:\"active\";b:1;s:8:\"is_alias\";b:0;}}s:12:\"replacements\";a:2:{s:7:\"subject\";a:2:{i:0;a:21:{i:0;s:4:\"(:P)\";i:1;s:4:\"(td)\";i:2;s:4:\"(tu)\";i:3;s:4:\":)-D\";i:4;s:10:\"&gt;:D&lt;\";i:5;s:3:\"(:D\";i:6;s:3:\"8-)\";i:7;s:3:\":)o\";i:8;s:3:\"::o\";i:9;s:3:\"B)-\";i:10;s:2:\":(\";i:11;s:2:\":)\";i:12;s:2:\":?\";i:13;s:2:\":D\";i:14;s:2:\":P\";i:15;s:2:\":S\";i:16;s:2:\":X\";i:17;s:2:\":o\";i:18;s:2:\";)\";i:19;s:2:\"B)\";i:20;s:2:\"X(\";}i:1;a:21:{i:0;s:165:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley25.gif\" alt=\"spinning smiley sticking its tongue out\" title=\"spinning smiley sticking its tongue out\"/>\";i:1;s:105:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley23.gif\" alt=\"thumbs up\" title=\"thumbs up\"/>\";i:2;s:109:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley24.gif\" alt=\"thumbs down\" title=\"thumbs down\"/>\";i:3;s:121:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley15.gif\" alt=\"smileys with beer\" title=\"smileys with beer\"/>\";i:4;s:121:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley14.gif\" alt=\"the finger smiley\" title=\"the finger smiley\"/>\";i:5;s:133:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley12.gif\" alt=\"smiling bouncing smiley\" title=\"smiling bouncing smiley\"/>\";i:6;s:122:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie8.gif\" alt=\"eye rolling smiley\" title=\"eye rolling smiley\"/>\";i:7;s:117:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley16.gif\" alt=\"drinking smiley\" title=\"drinking smiley\"/>\";i:8;s:123:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie10.gif\" alt=\"eye popping smiley\" title=\"eye popping smiley\"/>\";i:9;s:114:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie7.gif\" alt=\"smoking smiley\" title=\"smoking smiley\"/>\";i:10;s:106:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie2.gif\" alt=\"sad smiley\" title=\"sad smiley\"/>\";i:11;s:114:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie1.gif\" alt=\"smiling smiley\" title=\"smiling smiley\"/>\";i:12;s:111:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley17.gif\" alt=\"moody smiley\" title=\"moody smiley\"/>\";i:13;s:116:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie5.gif\" alt=\"grinning smiley\" title=\"grinning smiley\"/>\";i:14;s:138:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie6.gif\" alt=\"tongue sticking out smiley\" title=\"tongue sticking out smiley\"/>\";i:15;s:117:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie11.gif\" alt=\"confused smiley\" title=\"confused smiley\"/>\";i:16;s:110:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie9.gif\" alt=\"angry smiley\" title=\"angry smiley\"/>\";i:17;s:114:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie4.gif\" alt=\"yawning smiley\" title=\"yawning smiley\"/>\";i:18;s:114:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie3.gif\" alt=\"winking smiley\" title=\"winking smiley\"/>\";i:19;s:105:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/cool.gif\" alt=\"cool smiley\" title=\"cool smiley\"/>\";i:20;s:102:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/hot.gif\" alt=\"hot smiley\" title=\"hot smiley\"/>\";}}s:4:\"body\";a:2:{i:0;a:21:{i:0;s:4:\"(:P)\";i:1;s:4:\"(td)\";i:2;s:4:\"(tu)\";i:3;s:4:\":)-D\";i:4;s:10:\"&gt;:D&lt;\";i:5;s:3:\"(:D\";i:6;s:3:\"8-)\";i:7;s:3:\":)o\";i:8;s:3:\"::o\";i:9;s:3:\"B)-\";i:10;s:2:\":(\";i:11;s:2:\":)\";i:12;s:2:\":?\";i:13;s:2:\":D\";i:14;s:2:\":P\";i:15;s:2:\":S\";i:16;s:2:\":X\";i:17;s:2:\":o\";i:18;s:2:\";)\";i:19;s:2:\"B)\";i:20;s:2:\"X(\";}i:1;a:21:{i:0;s:165:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley25.gif\" alt=\"spinning smiley sticking its tongue out\" title=\"spinning smiley sticking its tongue out\"/>\";i:1;s:105:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley23.gif\" alt=\"thumbs up\" title=\"thumbs up\"/>\";i:2;s:109:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley24.gif\" alt=\"thumbs down\" title=\"thumbs down\"/>\";i:3;s:121:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley15.gif\" alt=\"smileys with beer\" title=\"smileys with beer\"/>\";i:4;s:121:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley14.gif\" alt=\"the finger smiley\" title=\"the finger smiley\"/>\";i:5;s:133:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley12.gif\" alt=\"smiling bouncing smiley\" title=\"smiling bouncing smiley\"/>\";i:6;s:122:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie8.gif\" alt=\"eye rolling smiley\" title=\"eye rolling smiley\"/>\";i:7;s:117:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley16.gif\" alt=\"drinking smiley\" title=\"drinking smiley\"/>\";i:8;s:123:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie10.gif\" alt=\"eye popping smiley\" title=\"eye popping smiley\"/>\";i:9;s:114:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie7.gif\" alt=\"smoking smiley\" title=\"smoking smiley\"/>\";i:10;s:106:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie2.gif\" alt=\"sad smiley\" title=\"sad smiley\"/>\";i:11;s:114:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie1.gif\" alt=\"smiling smiley\" title=\"smiling smiley\"/>\";i:12;s:111:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smiley17.gif\" alt=\"moody smiley\" title=\"moody smiley\"/>\";i:13;s:116:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie5.gif\" alt=\"grinning smiley\" title=\"grinning smiley\"/>\";i:14;s:138:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie6.gif\" alt=\"tongue sticking out smiley\" title=\"tongue sticking out smiley\"/>\";i:15;s:117:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie11.gif\" alt=\"confused smiley\" title=\"confused smiley\"/>\";i:16;s:110:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie9.gif\" alt=\"angry smiley\" title=\"angry smiley\"/>\";i:17;s:114:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie4.gif\" alt=\"yawning smiley\" title=\"yawning smiley\"/>\";i:18;s:114:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/smilie3.gif\" alt=\"winking smiley\" title=\"winking smiley\"/>\";i:19;s:105:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/cool.gif\" alt=\"cool smiley\" title=\"cool smiley\"/>\";i:20;s:102:\"<img class=\"mod_smileys_img\" src=\"./mods/smileys/images/hot.gif\" alt=\"hot smiley\" title=\"hot smiley\"/>\";}}}s:10:\"do_smileys\";b:1;}'),('error_logging','V','screen'),('disabled_url','V',''),('max_pm_messagecount','V',''),('email_ignore_admin','V','0');
UNLOCK TABLES;

##
## Table structure for table `phorum_subscribers`
##

DROP TABLE IF EXISTS `phorum_subscribers`;
CREATE TABLE `phorum_subscribers` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `sub_type` int(10) unsigned NOT NULL default '0',
  `thread` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`forum_id`,`thread`),
  KEY `forum_id` (`forum_id`,`thread`,`sub_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_user_custom_fields`
##

DROP TABLE IF EXISTS `phorum_user_custom_fields`;
CREATE TABLE `phorum_user_custom_fields` (
  `user_id` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  `data` text NOT NULL,
  PRIMARY KEY  (`user_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_user_group_xref`
##

DROP TABLE IF EXISTS `phorum_user_group_xref`;
CREATE TABLE `phorum_user_group_xref` (
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `status` tinyint(3) NOT NULL default '1',
  PRIMARY KEY  (`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_user_newflags`
##

DROP TABLE IF EXISTS `phorum_user_newflags`;
CREATE TABLE `phorum_user_newflags` (
  `user_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  `message_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`forum_id`,`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_user_permissions`
##

DROP TABLE IF EXISTS `phorum_user_permissions`;
CREATE TABLE `phorum_user_permissions` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `permission` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`forum_id`),
  KEY `forum_id` (`forum_id`,`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

##
## Table structure for table `phorum_users`
##

DROP TABLE IF EXISTS `phorum_users`;
CREATE TABLE `phorum_users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `fk_campsite_user_id` int(10) unsigned default NULL,
  `username` varchar(50) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  `cookie_sessid_lt` varchar(50) NOT NULL default '',
  `sessid_st` varchar(50) NOT NULL default '',
  `sessid_st_timeout` int(10) unsigned NOT NULL default '0',
  `password_temp` varchar(50) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `email_temp` varchar(110) NOT NULL default '',
  `hide_email` tinyint(1) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '0',
  `user_data` text NOT NULL,
  `signature` text NOT NULL,
  `threaded_list` tinyint(4) NOT NULL default '0',
  `posts` int(10) NOT NULL default '0',
  `admin` tinyint(1) NOT NULL default '0',
  `threaded_read` tinyint(4) NOT NULL default '0',
  `date_added` int(10) unsigned NOT NULL default '0',
  `date_last_active` int(10) unsigned NOT NULL default '0',
  `last_active_forum` int(10) unsigned NOT NULL default '0',
  `hide_activity` tinyint(1) NOT NULL default '0',
  `show_signature` tinyint(1) NOT NULL default '0',
  `email_notify` tinyint(1) NOT NULL default '0',
  `pm_email_notify` tinyint(1) NOT NULL default '1',
  `tz_offset` tinyint(2) NOT NULL default '-99',
  `is_dst` tinyint(1) NOT NULL default '0',
  `user_language` varchar(100) NOT NULL default '',
  `user_template` varchar(100) NOT NULL default '',
  `moderator_data` text NOT NULL,
  `moderation_email` tinyint(2) unsigned NOT NULL default '1',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `fk_campsite_user_id` (`fk_campsite_user_id`),
  KEY `active` (`active`),
  KEY `userpass` (`username`,`password`),
  KEY `sessid_st` (`sessid_st`),
  KEY `cookie_sessid_lt` (`cookie_sessid_lt`),
  KEY `activity` (`date_last_active`,`hide_activity`,`last_active_forum`),
  KEY `date_added` (`date_added`),
  KEY `email_temp` (`email_temp`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

##
## Dumping data for table `phorum_users`
##

LOCK TABLES `phorum_users` WRITE;
INSERT INTO `phorum_users` VALUES (1,1,'admin','','','',0,'','campsite-support@campware.org','',0,0,'','',0,0,1,0,0,0,0,0,0,0,1,-99,0,'','','',1);
UNLOCK TABLES;

## Dump completed on 2007-10-22  4:36:55
