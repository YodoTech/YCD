/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50155
Source Host           : 127.0.0.1:3306
Source Database       : ycd_old

Target Server Type    : MYSQL
Target Server Version : 50155
File Encoding         : 65001

Date: 2014-10-14 11:45:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lzh_borrow_apply_file
-- ----------------------------
DROP TABLE IF EXISTS `lzh_borrow_apply_file`;
CREATE TABLE `lzh_borrow_apply_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(10) NOT NULL COMMENT '借款ID',
  `iid` int(10) NOT NULL COMMENT '信息ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `status` tinyint(3) unsigned NOT NULL COMMENT '审核状态',
  `data_name` varchar(100) NOT NULL COMMENT '文件名称',
  `data_url` varchar(100) NOT NULL COMMENT '文件路径',
  `ext` varchar(10) NOT NULL COMMENT '文件类型',
  `size` int(10) unsigned NOT NULL COMMENT '文件大小',
  `deal_image` varchar(100) NOT NULL COMMENT '后台处理图片',
  `deal_info` varchar(200) NOT NULL COMMENT '处理备注',
  `deal_user` int(11) NOT NULL COMMENT '管理员',
  `deal_time` int(10) unsigned NOT NULL COMMENT '处理时间',
  `add_ip` varchar(20) NOT NULL COMMENT 'IP地址',
  `add_time` int(10) unsigned NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
