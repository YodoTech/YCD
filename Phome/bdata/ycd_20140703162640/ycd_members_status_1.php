<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_members_status`;");
E_C("CREATE TABLE `ycd_members_status` (
  `uid` int(10) unsigned NOT NULL,
  `phone_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `id_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0:未上传1:验证通过2:等待验证',
  `email_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `account_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `credit_status` tinyint(4) NOT NULL DEFAULT '0',
  `safequestion_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `video_status` tinyint(4) NOT NULL DEFAULT '0',
  `face_status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>