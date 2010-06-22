--
-- Table structure for table `lime_dynamic_types`
--

DROP TABLE IF EXISTS `lime_dynamic_types`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lime_dynamic_types` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `type` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lime_entity`
--

DROP TABLE IF EXISTS `lime_entity`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lime_entity` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` smallint(5) unsigned NOT NULL,
  `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `label` varchar(255) default NULL,
  `guid` binary(16) default NULL,
  PRIMARY KEY  (`id`),
  KEY `label` (`label`)
) ENGINE=MyISAM AUTO_INCREMENT=14923 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lime_label`
--

DROP TABLE IF EXISTS `lime_label`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lime_label` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` int(10) unsigned NOT NULL default '0',
  `label` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type` (`type`,`label`),
  FULLTEXT KEY `label` (`label`)
) ENGINE=MyISAM AUTO_INCREMENT=10010 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lime_label_base`
--

DROP TABLE IF EXISTS `lime_label_base`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lime_label_base` (
  `id` int(10) unsigned NOT NULL,
  `labels` text,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `labels` (`labels`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table `lime_page`
--

DROP TABLE IF EXISTS `lime_page`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lime_page` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `text` mediumblob,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29593 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lime_resolution`
--

DROP TABLE IF EXISTS `lime_resolution`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lime_resolution` (
  `parent` int(10) unsigned NOT NULL default '0',
  `label` int(10) unsigned NOT NULL,
  `entity` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`label`,`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lime_types`
--

DROP TABLE IF EXISTS `lime_types`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lime_types` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `type` varchar(16) default NULL,
  `label` varchar(32) default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lime_version`
--

DROP TABLE IF EXISTS `lime_version`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lime_version` (
  `type` tinyint(3) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL,
  `owner` int(10) unsigned NOT NULL,
  `page` bigint(20) unsigned NOT NULL,
  `created` datetime default NULL,
  `revised` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `status` tinyint(3) unsigned NOT NULL default '1',
  `length` int(10) unsigned default NULL,
  PRIMARY KEY  (`type`,`id`,`owner`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lime_version_log`
--

DROP TABLE IF EXISTS `lime_version_log`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lime_version_log` (
  `event` int(10) unsigned NOT NULL auto_increment,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `lime_type` tinyint(3) unsigned NOT NULL,
  `lime_id` int(10) unsigned NOT NULL,
  `owner` int(10) unsigned NOT NULL,
  `page` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`event`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lime_version_status`
--

DROP TABLE IF EXISTS `lime_version_status`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lime_version_status` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `label` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
