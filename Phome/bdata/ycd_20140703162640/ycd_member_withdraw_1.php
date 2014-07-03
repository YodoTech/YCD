<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_member_withdraw`;");
E_C("CREATE TABLE `ycd_member_withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `withdraw_money` decimal(15,2) NOT NULL,
  `withdraw_status` tinyint(4) NOT NULL,
  `withdraw_fee` decimal(15,2) NOT NULL,
  `add_time` int(10) unsigned NOT NULL,
  `add_ip` varchar(16) NOT NULL,
  `deal_time` int(10) unsigned NOT NULL,
  `deal_user` varchar(50) NOT NULL,
  `deal_info` varchar(200) NOT NULL,
  `second_fee` decimal(15,2) NOT NULL COMMENT '修改后的提现手续费',
  `success_money` decimal(15,2) NOT NULL COMMENT '实际到账金额',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`withdraw_status`,`add_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>