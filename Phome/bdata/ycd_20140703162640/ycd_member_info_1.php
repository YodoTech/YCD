<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_member_info`;");
E_C("CREATE TABLE `ycd_member_info` (
  `uid` int(10) unsigned NOT NULL,
  `sex` varchar(20) NOT NULL,
  `zy` varchar(40) NOT NULL,
  `cell_phone` varchar(11) NOT NULL,
  `info` varchar(500) NOT NULL,
  `marry` varchar(20) NOT NULL,
  `education` varchar(50) NOT NULL,
  `income` varchar(20) NOT NULL,
  `age` int(11) NOT NULL,
  `idcard` varchar(20) NOT NULL,
  `card_img` varchar(200) NOT NULL,
  `real_name` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `province` int(11) NOT NULL,
  `province_now` int(11) NOT NULL,
  `city` int(11) NOT NULL,
  `city_now` int(11) NOT NULL,
  `area` int(11) NOT NULL,
  `area_now` int(11) NOT NULL,
  `up_time` int(10) unsigned NOT NULL,
  `card_back_img` varchar(200) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>