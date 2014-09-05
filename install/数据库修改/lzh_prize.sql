/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50155
Source Host           : 127.0.0.1:3306
Source Database       : ycd_old

Target Server Type    : MYSQL
Target Server Version : 50155
File Encoding         : 65001

Date: 2014-09-05 14:08:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lzh_prize
-- ----------------------------
DROP TABLE IF EXISTS `lzh_prize`;
CREATE TABLE `lzh_prize` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prize_name` varchar(100) NOT NULL COMMENT '奖品名称',
  `prize_img` varchar(200) NOT NULL COMMENT '奖品图片',
  `need_credits` mediumint(9) NOT NULL DEFAULT '0' COMMENT '所需积分',
  `cumulate_num` int(11) NOT NULL DEFAULT '0' COMMENT '累计兑换数量',
  `is_used` tinyint(255) NOT NULL DEFAULT '0' COMMENT '是否启用',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
