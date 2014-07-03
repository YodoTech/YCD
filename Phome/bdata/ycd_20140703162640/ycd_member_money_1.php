<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_member_money`;");
E_C("CREATE TABLE `ycd_member_money` (
  `uid` int(10) unsigned NOT NULL,
  `money_freeze` decimal(15,2) NOT NULL COMMENT '冻结金额',
  `money_collect` decimal(15,2) NOT NULL COMMENT '待收金额',
  `account_money` decimal(15,2) NOT NULL COMMENT '充值资金存放池_可用余额',
  `back_money` decimal(15,2) NOT NULL COMMENT '回款资金存放池_可用余额',
  `credit_limit` decimal(15,2) NOT NULL,
  `credit_cuse` decimal(15,2) NOT NULL,
  `borrow_vouch_limit` decimal(15,2) NOT NULL,
  `borrow_vouch_cuse` decimal(15,2) NOT NULL,
  `invest_vouch_limit` decimal(15,2) NOT NULL,
  `invest_vouch_cuse` decimal(15,2) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>