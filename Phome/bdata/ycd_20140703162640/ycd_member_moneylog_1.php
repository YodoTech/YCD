<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_member_moneylog`;");
E_C("CREATE TABLE `ycd_member_moneylog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `affect_money` decimal(15,2) NOT NULL COMMENT '影响金额',
  `account_money` decimal(15,2) NOT NULL COMMENT '充值资金存放池_可用余额',
  `back_money` decimal(15,2) NOT NULL COMMENT '回款资金存放池_可用余额',
  `collect_money` decimal(15,2) NOT NULL COMMENT '待收金额',
  `freeze_money` decimal(15,2) NOT NULL COMMENT '冻结金额',
  `info` varchar(50) NOT NULL,
  `add_time` int(10) unsigned NOT NULL,
  `add_ip` varchar(16) NOT NULL,
  `target_uid` int(11) NOT NULL DEFAULT '0',
  `target_uname` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`type`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=229 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>