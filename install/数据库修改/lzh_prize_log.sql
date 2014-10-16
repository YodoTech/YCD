/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50155
Source Host           : 127.0.0.1:3306
Source Database       : ycd_old

Target Server Type    : MYSQL
Target Server Version : 50155
File Encoding         : 65001

Date: 2014-09-05 14:08:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lzh_prize_log
-- ----------------------------
DROP TABLE IF EXISTS `lzh_prize_log`;
CREATE TABLE `lzh_prize_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL COMMENT '用户ID',
  `prize_id` int(10) DEFAULT NULL COMMENT '奖品ID',
  `current_num` int(10) unsigned DEFAULT '0' COMMENT '本次兑换数量',
  `current_cost` mediumint(9) DEFAULT NULL COMMENT '本次花费积分',
  `cumulate_cost` mediumint(9) DEFAULT NULL COMMENT '累计花费积分',
  `info` varchar(255) DEFAULT NULL COMMENT '备注',
  `add_ip` varchar(16) DEFAULT NULL COMMENT '添加IP',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
