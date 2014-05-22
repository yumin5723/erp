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

DROP TABLE IF EXISTS `operations`;
CREATE TABLE `operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `register_nums` int(11) NOT NULL COMMENT '注册用户数',
  `lottery_nums` int(11) NOT NULL COMMENT '抽奖人数',
  `auction_nums` int(11) NOT NULL COMMENT '竞拍人数',
  `posts` int(11) NOT NULL COMMENT '发帖数',
  `ask_nums` int(11) NOT NULL COMMENT '知道参与人数',
  `health_nums` int(11) NOT NULL COMMENT '医疗参与人数',
  `statistics_date` date NOT NULL COMMENT '数据统计时间',
  `create_date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `statistics_date` (`statistics_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `operations` (`id`, `register_nums`, `lottery_nums`, `auction_nums`, `posts`, `ask_nums`, `health_nums`, `statistics_date`, `create_date`) VALUES
(1, 11, 212, 12, 123, 123, 123, '2014-01-08', '2014-01-17'),
(2, 123, 123, 123, 123, 123, 123, '2014-01-03', '2014-01-10');

DROP TABLE IF EXISTS `electricity`;
CREATE TABLE `electricity` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `mobile_sales` int(11) NOT NULL COMMENT '移动端销售额',
  `buy_nums` int(11) NOT NULL COMMENT '移动端购买人数',
  `visitors` int(11) NOT NULL COMMENT '移动端访问人数',
  `dau` int(11) NOT NULL COMMENT '活跃度',
  `second_retain` int(11) NOT NULL COMMENT '次日留存',
  `increasing` int(11) NOT NULL COMMENT '日新增用户',
  `week_retain` int(11) NOT NULL COMMENT '七日留存',
  `statistics_date` date NOT NULL COMMENT '统计时间',
  `create_date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `statistics_date` (`statistics_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `electricity` (`id`, `mobile_sales`, `buy_nums`, `visitors`, `dau`, `second_retain`, `increasing`, `week_retain`, `statistics_date`, `create_date`) VALUES
(1, 2342, 4234, 432, 4243, 234, 234, 234, '2014-01-11', '2014-01-18'),
(2, 234, 234, 243, 234, 234, 234, 234, '2014-01-03', '2014-01-04');

