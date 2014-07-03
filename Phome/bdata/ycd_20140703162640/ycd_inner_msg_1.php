<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_inner_msg`;");
E_C("CREATE TABLE `ycd_inner_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `msg` text NOT NULL,
  `send_time` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=156 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>