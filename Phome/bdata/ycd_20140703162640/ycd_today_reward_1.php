<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_today_reward`;");
E_C("CREATE TABLE `ycd_today_reward` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `borrow_id` int(10) unsigned NOT NULL,
  `reward_uid` int(10) unsigned NOT NULL,
  `invest_money` decimal(15,2) unsigned NOT NULL,
  `reward_money` decimal(10,2) unsigned NOT NULL,
  `reward_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `add_time` int(10) NOT NULL,
  `deal_time` int(10) NOT NULL,
  `add_ip` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>