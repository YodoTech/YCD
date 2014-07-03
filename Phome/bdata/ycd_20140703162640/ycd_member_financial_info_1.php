<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_member_financial_info`;");
E_C("CREATE TABLE `ycd_member_financial_info` (
  `uid` int(10) unsigned NOT NULL,
  `fin_monthin` varchar(20) NOT NULL,
  `fin_incomedes` varchar(2000) NOT NULL,
  `fin_monthout` varchar(20) NOT NULL,
  `fin_outdes` varchar(2000) NOT NULL,
  `fin_house` varchar(50) NOT NULL,
  `fin_housevalue` varchar(20) NOT NULL,
  `fin_car` varchar(20) NOT NULL,
  `fin_carvalue` varchar(20) NOT NULL,
  `fin_stockcompany` varchar(50) NOT NULL,
  `fin_stockcompanyvalue` varchar(50) NOT NULL,
  `fin_otheremark` varchar(2000) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>