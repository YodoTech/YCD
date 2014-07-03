<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_ausers`;");
E_C("CREATE TABLE `ycd_ausers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `user_pass` varchar(50) NOT NULL,
  `u_group_id` smallint(6) NOT NULL,
  `real_name` varchar(20) NOT NULL DEFAULT '匿名',
  `last_log_time` int(10) NOT NULL DEFAULT '0',
  `last_log_ip` varchar(30) NOT NULL DEFAULT '0',
  `is_ban` int(1) NOT NULL DEFAULT '0',
  `area_id` int(11) NOT NULL,
  `area_name` varchar(10) NOT NULL,
  `is_kf` int(10) unsigned NOT NULL DEFAULT '0',
  `qq` varchar(20) NOT NULL COMMENT '管理员qq',
  `phone` varchar(20) NOT NULL COMMENT '客服电话',
  `user_word` varchar(100) NOT NULL COMMENT '密码口令',
  PRIMARY KEY (`id`),
  KEY `is_kf` (`is_kf`,`area_id`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=utf8");
E_D("replace into `ycd_ausers` values('114','2981855399 ','5b49898059593571520a78c3c90f4388','22','铭铭','0','0','0','1','中国','1','','','aaaaa');");
E_D("replace into `ycd_ausers` values('113','2066840624','5b49898059593571520a78c3c90f4388','22','宁宁','0','0','0','1','中国','1','','','aaaaa');");
E_D("replace into `ycd_ausers` values('101','admin','2f9b1e952939cf699650aa8663d30161','5','john','0','0','0','1','中国','0','','','zhiwen');");
E_D("replace into `ycd_ausers` values('112','2978824690','5b49898059593571520a78c3c90f4388','22','妍妍','0','0','0','1','中国','1','','','aaaaa');");

require("../../inc/footer.php");
?>