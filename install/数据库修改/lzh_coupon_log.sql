/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50155
Source Host           : 127.0.0.1:3306
Source Database       : ycd_old

Target Server Type    : MYSQL
Target Server Version : 50155
File Encoding         : 65001

Date: 2014-09-24 17:26:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lzh_coupon_log
-- ----------------------------
DROP TABLE IF EXISTS `lzh_coupon_log`;
CREATE TABLE `lzh_coupon_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT '0' COMMENT '用户ID',
  `coupon_id` int(10) unsigned DEFAULT '0' COMMENT '优惠券ID',
  `info` varchar(200) DEFAULT NULL COMMENT '备注',
  `add_ip` varchar(16) DEFAULT NULL COMMENT '添加IP',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
