DROP TABLE IF EXISTS `manager`;
CREATE TABLE `manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(128) NOT NULL,
  `password_reset_token` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `role` smallint(6) NOT NULL DEFAULT '10',
  `status` smallint(6) NOT NULL DEFAULT '10',
  `create_time` int(10) NOT NULL,
  `update_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `manager` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `role`, `status`, `create_time`, `update_time`) VALUES
(1, 'admin', 'D179fKcB3pJbImNK4Oy279FszUfU7jbS', '$2y$13$J870q/urL8UWO/LfiW2ym.hwwuUUpOGDAsmWbb/2ChS8lKaLwluQG', '$2y$13$J870q/urL8UWO/LfiW2ym.hwwuUUpOGDAsmWbb/2ChS8lKaLwluQG', 'admin@goumin.com', 10, 10, 1388983719, 1390280071);



DROP TABLE IF EXISTS `AuthAssignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthAssignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthAssignment`
--

LOCK TABLES `AuthAssignment` WRITE;
/*!40000 ALTER TABLE `AuthAssignment` DISABLE KEYS */;
INSERT INTO `AuthAssignment` VALUES ('Administrator','1','','s:0:\"\";');
/*!40000 ALTER TABLE `AuthAssignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthItem`
--

DROP TABLE IF EXISTS `AuthItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthItem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthItem`
--

LOCK TABLES `AuthItem` WRITE;
/*!40000 ALTER TABLE `AuthItem` DISABLE KEYS */;
INSERT INTO `AuthItem` VALUES ('Authorizer',2,NULL,NULL,NULL),('Administrator',2,NULL,NULL,NULL);
/*!40000 ALTER TABLE `AuthItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthItemChild`
--

DROP TABLE IF EXISTS `AuthItemChild`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthItemChild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- 表的结构 `new_species`
--
DROP TABLE IF EXISTS `new_species`;
CREATE TABLE IF NOT EXISTS `new_species` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `spe_id` int(11) NOT NULL COMMENT '犬种id',
  `spe_title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `seo_admin`;
CREATE TABLE IF NOT EXISTS `seo_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tdk_name` varchar(255) NOT NULL COMMENT 'tdk name',
  `title` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `description` text,
  `scenarios` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `link_admin`;
CREATE TABLE IF NOT EXISTS `link_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'name',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `link_list`;
CREATE TABLE IF NOT EXISTS `link_list` (
  `link_id` int(11) NOT NULL AUTO_INCREMENT,
  `link_text` varchar(255) NOT NULL COMMENT 'link text',
  `link_url` varchar(255) NOT NULL,
  `link_type` int(11) NOT NULL,
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `bbs_ad`;
CREATE TABLE IF NOT EXISTS `bbs_ad` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_image` varchar(255) NOT NULL COMMENT 'ad image',
  `ad_url` varchar(255) NOT NULL,
  `ad_order` int(11) NOT NULL,
  PRIMARY KEY (`ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
alter table bbs_ad add `type` smallint(6) NOT NULL DEFAULT '0';

DROP TABLE IF EXISTS `white_list`;
CREATE TABLE IF NOT EXISTS `white_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL COMMENT 'ad keyword',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
