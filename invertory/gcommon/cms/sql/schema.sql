--
-- Table structure for table `gxc_resource`
--

CREATE TABLE `resource` (
  `resource_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(255) DEFAULT '',
  `resource_body` text,
  `resource_path` varchar(255) DEFAULT '',
  `resource_type` varchar(50) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `creator` bigint(20) NOT NULL,
  `where` varchar(50) NOT NULL,
  PRIMARY KEY (`resource_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;
--
-- Table structure for table `object`
--

CREATE TABLE `object` (
  `object_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_author` bigint(20) unsigned DEFAULT '0',
  `object_modified_uid` bigint(20) unsigned DEFAULT '0',
  `object_date` datetime NOT NULL,
  `object_date_gmt` datetime NOT NULL,
  `object_content` longtext,
  `ishot` tinyint(4) NOT NULL DEFAULT '0',
  `object_title` text,
  `object_excerpt` text,
  `object_status` tinyint(4) NOT NULL DEFAULT '1',
  `comment_status` tinyint(4) NOT NULL DEFAULT '1',
  `object_password` varchar(20) DEFAULT NULL,
  `object_name` varchar(255) NOT NULL,
  `object_modified` datetime NOT NULL,
  `object_modified_gmt` datetime NOT NULL,
  `object_content_filtered` text,
  `object_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) NOT NULL DEFAULT '',
  `object_type` varchar(20) NOT NULL DEFAULT 'object',
  `comment_count` bigint(20) NOT NULL DEFAULT '0',
  `object_slug` varchar(255) DEFAULT NULL,
  `object_description` text,
  `object_keywords` text,
  `lang` tinyint(4) DEFAULT '1',
  `object_author_name` varchar(255) DEFAULT NULL,
  `total_number_meta` tinyint(3) NOT NULL,
  `total_number_resource` tinyint(3) NOT NULL,
  `tags` text,
  `object_view` int(11) NOT NULL DEFAULT '0',
  `like` int(11) NOT NULL DEFAULT '0',
  `dislike` int(11) NOT NULL DEFAULT '0',
  `rating_scores` int(11) NOT NULL DEFAULT '0',
  `rating_average` float NOT NULL DEFAULT '0',
  `layout` varchar(125) DEFAULT NULL,
  PRIMARY KEY (`object_id`),
  KEY `type_status_date` (`object_type`,`object_status`,`object_date`,`object_id`),
  KEY `object_parent` (`object_parent`),
  KEY `object_author` (`object_author`),
  KEY `object_name` (`object_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
alter table object add `ishot` tinyint(4) NOT NULL DEFAULT '0';

--
-- Table structure for table `gxc_object_meta`
--

CREATE TABLE `object_meta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meta_object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `object_id` (`meta_object_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `object_bak` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` bigint(20) NOT NULL,
  `object_modified_uid` bigint(20) unsigned DEFAULT '0',
  `object_content` longtext,
  `object_title` text,
  `object_excerpt` text,
  `object_author_name` varchar(255) DEFAULT NULL,
  `tags` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Table structure for table `tag`
--
--
-- Table structure for table `object_resource`
--

CREATE TABLE `object_resource` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `resource_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `resource_order` int(11) NOT NULL DEFAULT '0',
  `description` longtext,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`object_id`,`resource_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `tag` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `frequency` int(11) DEFAULT '1',
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `relationships`
--

CREATE TABLE `tag_relationships` (
  `tag_id` bigint(20) NOT NULL,
  `object_id` bigint(20) NOT NULL,
  PRIMARY KEY (`tag_id`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

--
-- Table structure for table `object_term`
--

CREATE TABLE `object_term` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `data` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
CREATE TABLE `taxonomy` (
  `taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'article',
  `lang` tinyint(4) DEFAULT '1',
  `guid` varchar(255) NOT NULL,
  PRIMARY KEY (`taxonomy_id`),
  KEY `taxonomy` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `term`
--
CREATE TABLE `term` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `taxonomy_id` int(20) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `parent` bigint(20) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`term_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
alter table term add `templete_id` tinyint(4) NOT NULL DEFAULT '0';
alter table term add `url` varchar(255) DEFAULT '';


--
-- Table structure for table `page`
--
CREATE TABLE `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `rar_file` varchar(128) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `content` blob NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Table structure for table `templete`
--
CREATE TABLE `templete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  `rar_file` varchar(128) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `modified_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `content` blob NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `object_templete`
--

CREATE TABLE `object_templete` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `templete_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `data` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`templete_id`),
  KEY `templete_id` (`templete_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `templete`
--
CREATE TABLE `category_templete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  `rar_file` varchar(128) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `modified_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `content` blob NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `templete`
--
CREATE TABLE `position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `templete`
--
CREATE TABLE `position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `block`
--
CREATE TABLE `block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `english_name` varchar(64) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `params` text NOT NULL DEFAULT '',
  `db_type` varchar(16) NOT NULL DEFAULT '',
  `content` blob NOT NULL,
  `html` blob NOT NULL,
  `updated` datetime DEFAULT NULL,
  `created_id` int(11) NOT NULL,
  `modified_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `block_bak`
--
CREATE TABLE `block_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_id` int(11) NOT NULL,
  `content` blob NOT NULL,
  `created_id` int(11) NOT NULL,
  `modified_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oterm` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `root` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `short_name` varchar(255) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `url` varchar(255) NOT NULL,
  `admin_id` INT(10) UNSIGNED NOT NULL,
  `left_id` INT(10) UNSIGNED NOT NULL,
  `right_id` INT(10) UNSIGNED NOT NULL,
  `level` SMALLINT(5) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `left_id` (`left_id`),
  KEY `right_id` (`right_id`),
  KEY `level` (`level`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `obj_dependence` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `obj_type` VARCHAR(16) NOT NULL,
  `dep_type` VARCHAR(16) NOT NULL,
  `obj_id` INT(11) NOT NULL,
  `dep_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`obj_type`,`obj_id`,`dep_type`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table templete add `modified_id` int(11) NOT NULL;
alter table templete add `content` blob NOT NULL;
alter table page add `modified_id` int(11) NOT NULL;
alter table object add `istop` tinyint(4) NOT NULL DEFAULT '0';
alter table object add `isred` tinyint(4) NOT NULL DEFAULT '0';
alter table object add `url` varchar(255) NOT NULL;
alter table page change content content longblob; 
alter table page change content content longblob; 
alter table page add `title` varchar(255) NOT NULL;
alter table page add `keywords` varchar(255) NOT NULL;
alter table page add `description` varchar(255) NOT NULL;
alter table block add `params` text NOT NULL DEFAULT '';
alter table block add `updated` datetime DEFAULT NULL;

CREATE TABLE `page_term` (
  `page_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `type` varchar(64) NOT NULL DEFAULT 'active',
  `data` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`page_id`,`term_id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table block add `html` blob NOT NULL;
alter table oterm add `template_id` tinyint(4) NOT NULL DEFAULT '0';
alter table templete add `type` tinyint(4) NOT NULL DEFAULT '0';
alter table oterm add `status` tinyint(4) NOT NULL DEFAULT '0';
alter table object add `object_list_name` varchar(255) NOT NULL DEFAULT "" after `object_name`;
alter table object add `term_cache` varchar(255) NOT NULL;

alter table object add `created` datetime DEFAULT NULL;
alter table object add `modified` datetime DEFAULT NULL;

alter table block add `dbtype` varchar(64) NOT NULL;
alter table page add `is_template` tinyint(4) NOT NULL DEFAULT '0';

--
-- Table structure for table `templete`
--
DROP TABLE IF EXISTS `dynamic_template`;
CREATE TABLE `dynamic_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `content` LONGTEXT NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

