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
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `manager` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `role`, `status`, `created`, `modified`) VALUES
(1, 'admin', 'D179fKcB3pJbImNK4Oy279FszUfU7jbS', '$2y$13$J870q/urL8UWO/LfiW2ym.hwwuUUpOGDAsmWbb/2ChS8lKaLwluQG', '$2y$13$J870q/urL8UWO/LfiW2ym.hwwuUUpOGDAsmWbb/2ChS8lKaLwluQG', 'admin@goumin.com', 10, 10, "2014-12-06 12:10:15", "2014-12-06 12:10:15");

DROP TABLE IF EXISTS `storeroom`;
CREATE TABLE `storeroom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `contact` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(64) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `owner`;
CREATE TABLE `owner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `english_name` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) NOT NULL DEFAULT '',
  `tell` varchar(255) NOT NULL DEFAULT '',
  `auth_key` varchar(64) NOT NULL DEFAULT '',
  `password_hash` varchar(128) NOT NULL DEFAULT '',
  `password_reset_token` varchar(128) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `role` smallint(6) NOT NULL DEFAULT '10',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `material`;
CREATE TABLE `material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `english_name` varchar(255) NOT NULL DEFAULT '',
  `owner_id` int(11) NOT NULL DEFAULT '0', 
  `project_id` int(11) NOT NULL DEFAULT '0', 
  `desc` text NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `stock`;
CREATE TABLE `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL DEFAULT '0',
  `storeroom_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `owner_id` int(11) NOT NULL DEFAULT '0',
  `forecast_quantity` int(11) NOT NULL DEFAULT '1',
  `actual_quantity` int(11) NOT NULL DEFAULT '1',
  `stock_time` datetime NOT NULL,
  `delivery` varchar(64) NOT NULL DEFAULT '',
  `increase` tinyint(4) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `stock_total`;
CREATE TABLE `stock_total` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL DEFAULT '0',
  `total` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `material_id`(`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `viewid` varchar(128) NOT NULL DEFAULT '',
  `goods_active` varchar(255) NOT NULL DEFAULT '',
  `storeroom_id` int(11) NOT NULL DEFAULT '0',
  `owner_id` int(11) NOT NULL DEFAULT '0',
  `to_city` varchar(64) NOT NULL DEFAULT '',
  `recipients` varchar(64) NOT NULL DEFAULT '',
  `recipients_address` varchar(255) NOT NULL DEFAULT '',
  `recipients_contact` varchar(255) NOT NULL DEFAULT '',
  `info` text NOT NULL,
  `limitday` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `is_del` tinyint(4) NOT NULL DEFAULT '0',
  `source` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=100;

DROP TABLE IF EXISTS `order_detail`;
CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `goods_code` varchar(128) NOT NULL DEFAULT '',
  `goods_quantity` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `package`;
CREATE TABLE `package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num` int(11) NOT NULL,
  `actual_weight` int(11) NOT NULL,
  `throw_weight` int(11) NOT NULL,
  `volume` int(11) NOT NULL,
  `box` varchar(255) NOT NULL DEFAULT '',
  `method` tinyint(4) NOT NULL DEFAULT '1',
  `trunk` varchar(64) NOT NULL DEFAULT '',
  `delivery` varchar(64) NOT NULL DEFAULT '',
  `price` decimal(32,2) NOT NULL DEFAULT '0.00', 
  `info` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table `package` add column height int(11) NOT NULL DEFAULT '0';
alter table `package` add column width int(11) NOT NULL DEFAULT '0';
alter table `package` add column length int(11) NOT NULL DEFAULT '0';

DROP TABLE IF EXISTS `order_package`;
CREATE TABLE `order_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `channel`;
CREATE TABLE `channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connect_number` varchar(64) NOT NULL DEFAULT '',
  `channel_number` varchar(64) NOT NULL DEFAULT '',
  `goods_name` varchar(255) NOT NULL DEFAULT '',
  `goods_quantity` int(11) NOT NULL DEFAULT '0',
  `goods_weight` int(11) NOT NULL DEFAULT '0',
  `goods_volume` int(11) NOT NULL DEFAULT '0',
  `expected_time` datetime NOT NULL,
  `actual_time` datetime NOT NULL,
  `receiver` varchar(64) NOT NULL DEFAULT '',
  `order_receiver` varchar(64) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `packing_details` text,
  `info` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `order_channel`;
CREATE TABLE `order_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connect_number` varchar(64) NOT NULL DEFAULT '',
  `order_id` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `order_channel`;
CREATE TABLE `order_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connect_number` varchar(64) NOT NULL DEFAULT '',
  `order_id` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `trunk`;
CREATE TABLE `trunk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `contact` varchar(64) NOT NULL DEFAULT '',
  `phone` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `delivery`;
CREATE TABLE `delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `contact` varchar(64) NOT NULL DEFAULT '',
  `phone` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `order_sign`;
CREATE TABLE `order_sign` (
  `order_id` int(11) NOT NULL,
  `sign_date` datetime NOT NULL,
  `signer` varchar(64) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `info` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_uid` int(11) NOT NULL DEFAULT '1',
  `modified_uid` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table `stock` add column `destory` int(11) NOT NULL DEFAULT '0';
alter table `stock` add column `destory_reason` blob NOT NULL;
alter table `stock_total` add column `storeroom_id` int(11) NOT NULL DEFAULT '1';
alter table `stock` add column activite varchar(64) NOT NULL DEFAULT '';
alter table `order_sign` add column `type` tinyint(4) NOT NULL DEFAULT '0';
alter table `owner` add column `department` varchar(64) NOT NULL DEFAULT '';
alter table stock add column active varchar(64) NOT NULL DEFAULT '';