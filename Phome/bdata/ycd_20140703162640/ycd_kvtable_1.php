<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_kvtable`;");
E_C("CREATE TABLE `ycd_kvtable` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `value` varchar(50) NOT NULL,
  `nid` varchar(10) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `son_count` int(10) unsigned NOT NULL,
  `field_1` int(10) unsigned NOT NULL,
  `field_2` int(10) unsigned NOT NULL,
  `field_3` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nid` (`nid`,`value`,`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>