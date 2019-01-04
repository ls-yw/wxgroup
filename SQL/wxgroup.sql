/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50633
Source Host           : 127.0.0.1:3306
Source Database       : wxgroup

Target Server Type    : MYSQL
Target Server Version : 50633
File Encoding         : 65001

Date: 2019-01-04 18:02:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_config
-- ----------------------------
DROP TABLE IF EXISTS `wx_config`;
CREATE TABLE `wx_config` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `config_type` enum('email','system') NOT NULL DEFAULT 'system' COMMENT '类型',
  `config_key` varchar(20) NOT NULL COMMENT 'key',
  `config_value` varchar(200) NOT NULL COMMENT 'value',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_config_type_config_key` (`config_type`,`config_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='配置表';

-- ----------------------------
-- Records of wx_config
-- ----------------------------
INSERT INTO `wx_config` VALUES ('1', 'system', 'host', 'http://wxq.woodlsy.com', '2019-01-02 17:32:51', '2019-01-02 17:32:51');

-- ----------------------------
-- Table structure for wx_group
-- ----------------------------
DROP TABLE IF EXISTS `wx_group`;
CREATE TABLE `wx_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'uid',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '微信群名称',
  `qz_number` varchar(20) NOT NULL DEFAULT '' COMMENT '群主微信号',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '简介',
  `code` varchar(255) NOT NULL DEFAULT '' COMMENT '微信群二维码',
  `qz_code` varchar(255) NOT NULL DEFAULT '' COMMENT '群主二维码',
  `realname` varchar(20) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '联系人手机',
  `qq` varchar(13) NOT NULL DEFAULT '' COMMENT 'qq',
  `deleted` enum('2','1','3','0') NOT NULL DEFAULT '1' COMMENT '状态 0正常 1审核中 2删除 3审核失败',
  `examine_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核时间',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wx_group
-- ----------------------------
INSERT INTO `wx_group` VALUES ('1', '1', '1', '求赞群bbb', 'yixiluozhu_aa', '双十一求赞啊啊啊啊  ', '/upload/20190104/5c2ecc634d5b7.png', '/upload/20190104/5c2ecc665666f.jpg', '叶希', '13757979103', '405548753', '0', '0000-00-00 00:00:00', '2019-01-04 11:01:02', '2019-01-04 15:30:37');
INSERT INTO `wx_group` VALUES ('2', '1', '0', '求赞群a', 'yixiluozhu_aa', '双十一求赞啊啊啊啊  ', '/upload/20190104/5c2ecc634d5b7.png', '/upload/20190104/5c2ecc665666f.jpg', '叶希', '13757979103', '405548753', '0', '0000-00-00 00:00:00', '2019-01-04 14:42:06', '2019-01-04 14:42:06');

-- ----------------------------
-- Table structure for wx_group_category
-- ----------------------------
DROP TABLE IF EXISTS `wx_group_category`;
CREATE TABLE `wx_group_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '名称',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父类ID',
  `deleted` enum('2','1','0') NOT NULL DEFAULT '0' COMMENT '状态 0正常 1禁用 2删除',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wx_group_category
-- ----------------------------
INSERT INTO `wx_group_category` VALUES ('1', '微商', '0', '0', '2019-01-04 15:22:51', '2019-01-04 15:22:51');
INSERT INTO `wx_group_category` VALUES ('2', '互粉群', '0', '0', '2019-01-04 15:22:58', '2019-01-04 15:22:58');

-- ----------------------------
-- Table structure for wx_member
-- ----------------------------
DROP TABLE IF EXISTS `wx_member`;
CREATE TABLE `wx_member` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_name` varchar(20) NOT NULL COMMENT '用户名',
  `email` varchar(50) NOT NULL COMMENT 'email',
  `email_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '邮箱状态 0 未激活 1激活',
  `email_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '邮箱验证时间',
  `password` char(32) NOT NULL COMMENT '密码',
  `uniqid` char(27) NOT NULL COMMENT '唯一码',
  `role` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '角色 1管理员',
  `last_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最后登录时间',
  `last_ip` char(15) NOT NULL DEFAULT '0.0.0.0' COMMENT '最后登录IP',
  `count` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `deleted` enum('2','1','0') NOT NULL DEFAULT '0' COMMENT '状态 0正常 1禁用 2删除',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_name` (`user_name`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of wx_member
-- ----------------------------
INSERT INTO `wx_member` VALUES ('1', 'woodlsy', '405548753@qq.com', '1', '2019-01-03 15:13:16', '19310593a11ba238afafaf9b105ca2fc', '28705c2c6ec5647ad4.38727332', '0', '2019-01-04 10:26:58', '10.0.2.2', '8', '0', '2019-01-02 15:56:53', '2019-01-04 10:26:58');
INSERT INTO `wx_member` VALUES ('6', '123456', '123@qq.com', '0', '0000-00-00 00:00:00', '16ffcba55b79406a0ab1dae82c0b602d', '20925c2c74a3dcf661.80339037', '0', '2019-01-02 16:21:55', '0.0.0.0', '0', '0', '2019-01-02 16:21:55', '2019-01-02 16:21:55');
INSERT INTO `wx_member` VALUES ('7', '1234567', '1234@qq.com', '0', '0000-00-00 00:00:00', '808c80c8e40ff1c42d482805d5f6c0a0', '12075c2c74e3e79021.87557326', '0', '2019-01-02 16:22:59', '0.0.0.0', '0', '0', '2019-01-02 16:22:59', '2019-01-02 16:22:59');
INSERT INTO `wx_member` VALUES ('8', '1231qq', '123456@qq.com', '0', '0000-00-00 00:00:00', 'e77f824934750a101e733709bd8c8e6b', '90175c2c74fd2cc675.53738634', '0', '2019-01-02 16:23:25', '0.0.0.0', '0', '0', '2019-01-02 16:23:25', '2019-01-02 16:23:25');
