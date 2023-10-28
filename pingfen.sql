-- MySQL dump 10.17  Distrib 10.3.23-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: pingfen
-- ------------------------------------------------------
-- Server version	10.3.23-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pf_admins`
--

DROP TABLE IF EXISTS `pf_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pf_admins` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员序号',
  `aname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '管理员名称',
  `loginname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录名称',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录密码',
  `enable` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `permission` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '比赛权限',
  PRIMARY KEY (`aid`),
  UNIQUE KEY `loginname` (`loginname`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pf_admins`
--

LOCK TABLES `pf_admins` WRITE;
/*!40000 ALTER TABLE `pf_admins` DISABLE KEYS */;
INSERT INTO `pf_admins` VALUES (1,'超级管理员','superadmin','25d55ad283aa400af464c76d713c07ad',1,'-1');
/*!40000 ALTER TABLE `pf_admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pf_contest_judgers`
--

DROP TABLE IF EXISTS `pf_contest_judgers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pf_contest_judgers` (
  `cid` int(10) unsigned NOT NULL COMMENT '比赛序号',
  `jid` int(10) unsigned NOT NULL COMMENT '评委序号',
  `jname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '评委名称',
  `logincode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录码',
  `enable` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `login_page` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '评委页面',
  `login_page_content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '评委页面内容',
  PRIMARY KEY (`cid`,`jid`),
  CONSTRAINT `judger_contest_id` FOREIGN KEY (`cid`) REFERENCES `pf_contests` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='评委列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pf_contest_judgers`
--

LOCK TABLES `pf_contest_judgers` WRITE;
/*!40000 ALTER TABLE `pf_contest_judgers` DISABLE KEYS */;
/*!40000 ALTER TABLE `pf_contest_judgers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pf_contest_players`
--

DROP TABLE IF EXISTS `pf_contest_players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pf_contest_players` (
  `cid` int(10) unsigned NOT NULL COMMENT '比赛序号',
  `pid` int(10) unsigned NOT NULL COMMENT '选手序号',
  `pname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '选手名称',
  `sequence` int(10) unsigned DEFAULT NULL COMMENT '选手顺序',
  `point` double DEFAULT NULL COMMENT '选手最终得分',
  `rank` int(10) unsigned DEFAULT NULL COMMENT '选手排名',
  PRIMARY KEY (`cid`,`pid`),
  CONSTRAINT `player_contest_id` FOREIGN KEY (`cid`) REFERENCES `pf_contests` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='选手列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pf_contest_players`
--

LOCK TABLES `pf_contest_players` WRITE;
/*!40000 ALTER TABLE `pf_contest_players` DISABLE KEYS */;
/*!40000 ALTER TABLE `pf_contest_players` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pf_contest_points`
--

DROP TABLE IF EXISTS `pf_contest_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pf_contest_points` (
  `cid` int(10) unsigned NOT NULL COMMENT '比赛序号',
  `pid` int(10) unsigned NOT NULL COMMENT '选手序号',
  `jid` int(10) unsigned NOT NULL COMMENT '评委序号',
  `point` int(10) unsigned NOT NULL COMMENT '打分',
  `enable` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '是否最高/最低分',
  PRIMARY KEY (`cid`,`pid`,`jid`),
  KEY `point_contest_judger_id` (`cid`,`jid`),
  CONSTRAINT `point_contest_id` FOREIGN KEY (`cid`) REFERENCES `pf_contests` (`cid`),
  CONSTRAINT `point_contest_judger_id` FOREIGN KEY (`cid`, `jid`) REFERENCES `pf_contest_judgers` (`cid`, `jid`),
  CONSTRAINT `point_contest_player_id` FOREIGN KEY (`cid`, `pid`) REFERENCES `pf_contest_players` (`cid`, `pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='评分详情';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pf_contest_points`
--

LOCK TABLES `pf_contest_points` WRITE;
/*!40000 ALTER TABLE `pf_contest_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `pf_contest_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pf_contests`
--

DROP TABLE IF EXISTS `pf_contests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pf_contests` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '比赛序号',
  `cname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '比赛名称',
  `cfavicon` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '比赛网标',
  `clogo` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '比赛图标',
  `ccolor` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '比赛颜色',
  `caccent_color` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '比赛强调色',
  `enable` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `min_max_mode` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否去掉最低最高分',
  `judger_page` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '评委所在页面',
  `judger_page_content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '评委所在页面内容',
  `judger_next_page` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '评委下一页面',
  `judger_next_page_content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '评委下一页面内容',
  `screen_page` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '屏幕所在页面',
  `screen_page_content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '屏幕所在页面内容',
  `screen_page_pic` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '屏幕背景图片',
  `screen_background_page_pic` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '屏幕背景页面背景图片',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=20220818 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='比赛列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pf_contests`
--

LOCK TABLES `pf_contests` WRITE;
/*!40000 ALTER TABLE `pf_contests` DISABLE KEYS */;
/*!40000 ALTER TABLE `pf_contests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pf_global_settings`
--

DROP TABLE IF EXISTS `pf_global_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pf_global_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '设置项序号',
  `setting_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '设置项名称',
  `setting_value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '设置项值',
  `setting_remark` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '设置项备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='网站全局设置列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pf_global_settings`
--

LOCK TABLES `pf_global_settings` WRITE;
/*!40000 ALTER TABLE `pf_global_settings` DISABLE KEYS */;
INSERT INTO `pf_global_settings` VALUES (1,'site_url','','站点域名'),(2,'site_name','','站点名称'),(3,'site_favicon','','站点网标'),(4,'site_logo','','站点图标'),(5,'site_color','','站点颜色'),(6,'site_accent_color','','站点强调色'),(7,'site_default_screen_page_pic','','默认屏幕背景图片'),(8,'site_default_screen_background_page_pic','','默认屏幕背景页面背景图片');
/*!40000 ALTER TABLE `pf_global_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'pingfen'
--

--
-- Dumping routines for database 'pingfen'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-08-18  0:43:56
