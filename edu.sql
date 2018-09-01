/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : edu

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-08-29 18:35:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` tinyint(2) NOT NULL DEFAULT '1' COMMENT '角色 权限',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '账户状态',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `login_time` int(11) unsigned NOT NULL COMMENT '登录时间',
  `login_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `is_delete` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'admin@qq.com', '1', '1', null, null, null, '0', '0', '0');
INSERT INTO `user` VALUES ('2', 'test', 'e10adc3949ba59abbe56e057f20f883e', 'test@qq.com', '1', '1', null, null, null, '0', '0', '0');
