<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_member_data_info`;");
E_C("CREATE TABLE `ycd_member_data_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `data_url` varchar(100) NOT NULL,
  `type` smallint(5) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `add_time` int(10) unsigned NOT NULL,
  `data_name` varchar(50) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `ext` varchar(10) NOT NULL,
  `deal_info` varchar(40) NOT NULL,
  `deal_credits` smallint(5) unsigned NOT NULL,
  `deal_user` int(11) NOT NULL,
  `deal_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`type`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>