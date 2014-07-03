<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_member_limitlog`;");
E_C("CREATE TABLE `ycd_member_limitlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `credit_limit` float(15,2) NOT NULL,
  `borrow_vouch_limit` float(15,2) NOT NULL,
  `invest_vouch_limit` float(15,2) NOT NULL,
  `info` varchar(50) NOT NULL,
  `add_time` int(10) unsigned NOT NULL,
  `add_ip` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`type`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>