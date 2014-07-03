<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_member_msg`;");
E_C("CREATE TABLE `ycd_member_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_uid` int(11) NOT NULL,
  `from_uname` varchar(20) NOT NULL,
  `to_uid` int(11) NOT NULL,
  `to_uname` varchar(20) NOT NULL,
  `title` varchar(50) NOT NULL,
  `msg` varchar(2000) NOT NULL,
  `add_time` int(10) unsigned NOT NULL,
  `is_read` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL,
  `to_del` tinyint(4) NOT NULL DEFAULT '0',
  `from_del` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>