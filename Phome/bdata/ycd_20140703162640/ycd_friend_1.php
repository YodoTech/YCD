<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_friend`;");
E_C("CREATE TABLE `ycd_friend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_txt` varchar(50) NOT NULL,
  `link_href` varchar(50) NOT NULL,
  `link_img` varchar(100) NOT NULL DEFAULT ' ',
  `link_order` int(1) NOT NULL DEFAULT '0',
  `link_type` int(1) NOT NULL DEFAULT '0',
  `is_show` int(1) NOT NULL DEFAULT '1',
  `game_id` int(11) NOT NULL DEFAULT '0',
  `game_name` char(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>