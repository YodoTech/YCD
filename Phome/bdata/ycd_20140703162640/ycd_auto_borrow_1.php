<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_auto_borrow`;");
E_C("CREATE TABLE `ycd_auto_borrow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `tender_type` tinyint(4) NOT NULL DEFAULT '0',
  `tender_account` float(15,2) unsigned NOT NULL,
  `tender_rate` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `my_friend` tinyint(4) NOT NULL DEFAULT '0',
  `not_black` tinyint(4) NOT NULL DEFAULT '0',
  `target_user` tinyint(4) NOT NULL DEFAULT '0',
  `credits` tinyint(4) NOT NULL DEFAULT '0',
  `borrow_credit_status` tinyint(4) NOT NULL DEFAULT '0',
  `borrow_credit_first` int(11) NOT NULL,
  `borrow_credit_last` int(11) NOT NULL,
  `repayment_type` tinyint(4) NOT NULL DEFAULT '0',
  `timelimit_status` tinyint(4) NOT NULL DEFAULT '0',
  `timelimit_month_first` int(11) NOT NULL,
  `timelimit_month_last` int(11) NOT NULL,
  `apr_status` tinyint(4) NOT NULL DEFAULT '0',
  `apr_first` float(5,2) NOT NULL,
  `apr_last` float(5,2) NOT NULL,
  `award_status` tinyint(4) NOT NULL DEFAULT '0',
  `award_first` float(5,2) NOT NULL,
  `award_last` float(5,2) NOT NULL,
  `borrow_type` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `is_use` (`borrow_type`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>