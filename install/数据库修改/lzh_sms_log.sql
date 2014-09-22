/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50155
Source Host           : 127.0.0.1:3306
Source Database       : ycd_old

Target Server Type    : MYSQL
Target Server Version : 50155
File Encoding         : 65001

Date: 2014-09-22 17:53:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lzh_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `lzh_sms_log`;
CREATE TABLE `lzh_sms_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL COMMENT '用户ID',
  `is_admin` tinyint(1) unsigned DEFAULT '0' COMMENT '是否管理员',
  `to` varchar(200) DEFAULT NULL COMMENT '发送号码',
  `content` varchar(255) DEFAULT NULL COMMENT '发送内容',
  `result` varchar(200) DEFAULT NULL COMMENT '返回结果',
  `add_ip` varchar(16) DEFAULT NULL COMMENT '添加IP',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
