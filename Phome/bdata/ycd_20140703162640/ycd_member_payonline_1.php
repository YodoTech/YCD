<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_member_payonline`;");
E_C("CREATE TABLE `ycd_member_payonline` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `nid` char(32) NOT NULL,
  `money` decimal(15,2) NOT NULL,
  `fee` decimal(8,2) NOT NULL,
  `way` varchar(20) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `add_time` int(10) unsigned NOT NULL,
  `add_ip` varchar(16) NOT NULL,
  `tran_id` varchar(50) NOT NULL,
  `off_bank` varchar(50) NOT NULL,
  `off_way` varchar(100) NOT NULL,
  `deal_user` varchar(40) NOT NULL,
  `deal_uid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`status`,`nid`,`id`),
  KEY `uid_2` (`uid`,`money`,`add_time`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>