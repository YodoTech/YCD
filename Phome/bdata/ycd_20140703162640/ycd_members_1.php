<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_members`;");
E_C("CREATE TABLE `ycd_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `user_pass` char(32) NOT NULL,
  `user_type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `pin_pass` char(32) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_phone` varchar(11) NOT NULL,
  `reg_time` int(10) unsigned NOT NULL,
  `reg_ip` varchar(15) NOT NULL,
  `user_leve` tinyint(4) NOT NULL DEFAULT '0',
  `time_limit` int(10) unsigned NOT NULL,
  `credits` int(10) NOT NULL,
  `recommend_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `customer_name` varchar(20) NOT NULL,
  `province` int(10) unsigned NOT NULL,
  `city` int(10) unsigned NOT NULL,
  `area` int(10) unsigned NOT NULL,
  `is_ban` int(11) NOT NULL DEFAULT '0' COMMENT '是否冻结0：否； 1：是',
  `reward_money` decimal(15,2) NOT NULL COMMENT '奖金金额',
  `invest_credits` decimal(15,2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_email` (`user_email`),
  KEY `user_name` (`user_name`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>