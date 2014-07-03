<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_ad`;");
E_C("CREATE TABLE `ycd_ad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(5000) NOT NULL,
  `start_time` int(10) NOT NULL,
  `end_time` int(10) NOT NULL,
  `add_time` int(10) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `ad_type` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8");
E_D("replace into `ycd_ad` values('1','<img style=\"margin: 0px; float: none\" alt=\"\" src=\"/UF/Uploads/Article/20131227174856.png\" />','1357660800','1391097600','1357715233','首页LOGO图片（推荐LOGO图片大小：220*65像素）','0');");
E_D("replace into `ycd_ad` values('4','N;','1357660800','1485360000','1357715551','首页幻灯片展示','1');");
E_D("replace into `ycd_ad` values('6','N;','1365436800','1366214400','1367025431','aaaaaaa','1');");
E_D("replace into `ycd_ad` values('5','<img style=\"margin: 0px; width: 980px; float: none\" alt=\"\" src=\"/UF/Uploads/Article/20130109152815.jpg\" />','1357660800','1393516800','1357716501','内页中上部大广告位','0');");

require("../../inc/footer.php");
?>