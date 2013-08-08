CREATE TABLE `xcms_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '可重复，代表数组',
  `value` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `config_name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='网站设置';