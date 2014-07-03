<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_name_apply`;");
E_C("CREATE TABLE `ycd_name_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `up_time` int(10) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `idcard` varchar(20) NOT NULL,
  `deal_info` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>