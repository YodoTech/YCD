/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50155
Source Host           : 127.0.0.1:3306
Source Database       : ycd_old

Target Server Type    : MYSQL
Target Server Version : 50155
File Encoding         : 65001

Date: 2014-10-14 11:45:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lzh_borrow_apply
-- ----------------------------
DROP TABLE IF EXISTS `lzh_borrow_apply`;
CREATE TABLE `lzh_borrow_apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(10) NOT NULL COMMENT '借款ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `borrow_name` varchar(50) NOT NULL COMMENT '借款标题',
  `borrow_info` varchar(500) NOT NULL COMMENT '借款说明',
  `status` tinyint(3) NOT NULL COMMENT '审核状态',
  `add_group` varchar(20) NOT NULL COMMENT '操作类型（用于区分前台或后台）',
  `add_user` int(11) NOT NULL COMMENT '添加人',
  `add_ip` varchar(16) NOT NULL COMMENT 'IP地址',
  `add_time` int(10) NOT NULL COMMENT '添加时间',
  `deal_info` varchar(40) NOT NULL COMMENT '审核说明',
  `deal_user` int(11) NOT NULL COMMENT '审核人',
  `deal_time` int(10) unsigned NOT NULL COMMENT '审核时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
