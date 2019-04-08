-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 06 月 03 日 10:58
-- 服务器版本: 5.1.69
-- PHP 版本: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `mysqlznckapi`
--
CREATE DATABASE `mysqlznckapi` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `mysqlznckapi`;

-- --------------------------------------------------------

--
-- 表的结构 `api_datalist`
--

CREATE TABLE IF NOT EXISTS `api_datalist` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL COMMENT '1网设2上传3定时',
  `uid` int(8) NOT NULL,
  `sid` varchar(3) NOT NULL,
  `nid` varchar(3) NOT NULL,
  `data` varchar(32) NOT NULL,
  `note` varchar(64) NOT NULL,
  `status` int(2) NOT NULL COMMENT '1成功2失败3超次数4超15分',
  `time` datetime NOT NULL,
  `ip` varchar(16) NOT NULL,
  `num` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=257998 ;

-- --------------------------------------------------------

--
-- 表的结构 `api_device`
--

CREATE TABLE IF NOT EXISTS `api_device` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL COMMENT '1网关3插座4电灯5红外6门窗7安防8摄像头9语音',
  `uid` int(8) NOT NULL,
  `sid` varchar(3) NOT NULL,
  `nid` varchar(3) NOT NULL COMMENT '当sid=1时，2温湿度，3pm2.5，4人体，5烟雾，6水滴',
  `data` varchar(32) NOT NULL,
  `note` varchar(64) NOT NULL,
  `status` int(2) NOT NULL COMMENT '1正常2超时',
  `regdate` datetime NOT NULL,
  `lasttime` datetime NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `time` (`regdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3552 ;

-- --------------------------------------------------------

--
-- 表的结构 `api_member`
--

CREATE TABLE IF NOT EXISTS `api_member` (
  `uid` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `subpass` varchar(32) NOT NULL,
  `level` int(2) NOT NULL,
  `status` int(2) NOT NULL,
  `subname` varchar(32) NOT NULL,
  `mobile` varchar(16) NOT NULL,
  `qq` varchar(16) NOT NULL,
  `question` varchar(64) NOT NULL,
  `answer` varchar(32) NOT NULL,
  `address` varchar(128) NOT NULL,
  `regdate` datetime NOT NULL,
  `lasttime` datetime NOT NULL,
  `apikey` varchar(16) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1430 ;

-- --------------------------------------------------------

--
-- 表的结构 `api_number`
--

CREATE TABLE IF NOT EXISTS `api_number` (
  `nid` varchar(3) NOT NULL,
  `name` varchar(32) NOT NULL,
  `status` int(2) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `api_species`
--

CREATE TABLE IF NOT EXISTS `api_species` (
  `type` varchar(3) NOT NULL COMMENT '1网关3插座4电灯5红外6门窗7安防8摄像头9语音',
  `name` varchar(32) NOT NULL,
  `status` int(2) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='种类表';

-- --------------------------------------------------------

--
-- 表的结构 `api_temperature`
--

CREATE TABLE IF NOT EXISTS `api_temperature` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL COMMENT '类型',
  `uid` int(8) NOT NULL,
  `sid` varchar(3) NOT NULL,
  `nid` varchar(3) NOT NULL,
  `data` varchar(32) NOT NULL,
  `temperature` float NOT NULL,
  `humidity` float NOT NULL,
  `status` int(2) NOT NULL,
  `time` datetime NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=846993 ;

-- --------------------------------------------------------

--
-- 表的结构 `api_timing`
--

CREATE TABLE IF NOT EXISTS `api_timing` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL COMMENT '1时间2每天3工作4周未',
  `uid` int(8) NOT NULL,
  `sid` varchar(3) NOT NULL,
  `nid` varchar(3) NOT NULL,
  `data` varchar(32) NOT NULL,
  `note` varchar(64) NOT NULL,
  `status` int(2) NOT NULL COMMENT '1成功2失败',
  `time` datetime NOT NULL,
  `ip` varchar(16) NOT NULL,
  `num` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=199 ;

-- --------------------------------------------------------

--
-- 表的结构 `api_worklist`
--

CREATE TABLE IF NOT EXISTS `api_worklist` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL COMMENT '1网设2上传3定时',
  `uid` int(8) NOT NULL,
  `sid` varchar(3) NOT NULL,
  `nid` varchar(3) NOT NULL,
  `data` varchar(32) NOT NULL,
  `note` varchar(64) NOT NULL,
  `status` int(2) NOT NULL COMMENT '1成功2失败3超次数4超15分',
  `time` datetime NOT NULL,
  `ip` varchar(16) NOT NULL,
  `num` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65351 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
