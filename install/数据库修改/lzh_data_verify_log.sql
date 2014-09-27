/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50155
Source Host           : 127.0.0.1:3306
Source Database       : ycd_old

Target Server Type    : MYSQL
Target Server Version : 50155
File Encoding         : 65001

Date: 2014-09-27 12:01:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lzh_data_verify_log
-- ----------------------------
DROP TABLE IF EXISTS `lzh_data_verify_log`;
CREATE TABLE `lzh_data_verify_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '类型',
  `did` int(10) NOT NULL DEFAULT '0' COMMENT '数据ID',
  `op_image` varchar(100) NOT NULL COMMENT '处理图片',
  `op_status` tinyint(3) NOT NULL,
  `op_info` varchar(255) NOT NULL DEFAULT '' COMMENT '操作备注',
  `op_credits` smallint(5) NOT NULL DEFAULT '0' COMMENT '操作积分',
  `op_uid` int(11) NOT NULL DEFAULT '0' COMMENT '操作用户',
  `op_time` int(11) NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
