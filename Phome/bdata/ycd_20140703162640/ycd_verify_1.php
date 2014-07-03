<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_verify`;");
E_C("CREATE TABLE `ycd_verify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL,
  `send_time` int(10) NOT NULL,
  `ukey` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL COMMENT '1:邮件激活验证',
  PRIMARY KEY (`id`),
  KEY `code` (`ukey`,`type`,`send_time`,`code`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>