/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50155
Source Host           : 127.0.0.1:3306
Source Database       : ycd_old

Target Server Type    : MYSQL
Target Server Version : 50155
File Encoding         : 65001

Date: 2014-09-24 17:25:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lzh_coupon
-- ----------------------------
DROP TABLE IF EXISTS `lzh_coupon`;
CREATE TABLE `lzh_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_code` varchar(20) NOT NULL COMMENT '优惠券编号',
  `coupon_pass` varchar(20) NOT NULL COMMENT '优惠券密码',
  `coupon_val` int(10) NOT NULL DEFAULT '0' COMMENT '优惠券面值',
  `coupon_info` varchar(255) NOT NULL COMMENT '优惠券备注',
  `coupon_uid` int(10) NOT NULL DEFAULT '0' COMMENT '优惠券所属用户',
  `is_used` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已用',
  `used_time` int(10) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否锁定',
  `over_time` int(10) NOT NULL DEFAULT '0' COMMENT '过期时间',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
