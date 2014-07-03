<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_investor_detail`;");
E_C("CREATE TABLE `ycd_investor_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `repayment_time` int(10) unsigned NOT NULL DEFAULT '0',
  `borrow_id` int(10) unsigned NOT NULL,
  `invest_id` int(10) unsigned NOT NULL,
  `investor_uid` int(10) unsigned NOT NULL,
  `borrow_uid` int(10) unsigned NOT NULL,
  `capital` decimal(15,2) NOT NULL,
  `interest` decimal(15,2) NOT NULL,
  `interest_fee` decimal(15,2) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `receive_interest` decimal(15,2) NOT NULL,
  `receive_capital` decimal(15,2) NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL,
  `total` tinyint(3) unsigned NOT NULL,
  `deadline` int(10) unsigned NOT NULL,
  `expired_money` decimal(15,2) NOT NULL,
  `expired_days` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `call_fee` decimal(5,2) NOT NULL,
  `substitute_money` decimal(15,2) NOT NULL,
  `substitute_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `invest_id` (`invest_id`,`status`,`deadline`),
  KEY `borrow_id` (`borrow_id`,`sort_order`,`investor_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=539 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>