DROP TABLE IF EXISTS `stat_log`;
CREATE TABLE `stat_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `sid` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `pid` int(11) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `ua` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `info` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `monitoring_point`;
CREATE TABLE `monitoring_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desc` varchar(255) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table stat_log add `source` tinyint(4) NOT NULL;
alter table stat_log add `imei` varchar(64) NOT NULL;
