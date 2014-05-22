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