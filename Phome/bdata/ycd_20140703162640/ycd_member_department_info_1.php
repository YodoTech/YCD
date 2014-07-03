<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_member_department_info`;");
E_C("CREATE TABLE `ycd_member_department_info` (
  `uid` int(11) NOT NULL,
  `department_name` varchar(50) NOT NULL,
  `department_tel` varchar(20) NOT NULL,
  `department_address` varchar(200) NOT NULL,
  `department_year` varchar(20) NOT NULL,
  `voucher_name` varchar(20) NOT NULL,
  `voucher_tel` varchar(20) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>