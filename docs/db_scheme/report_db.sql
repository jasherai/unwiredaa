-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- ????: localhost
-- ????? ?? ??????????: 
-- ?????? ?? ???????: 5.5.8
-- ?????? ?? PHP: 5.3.5

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- ??: `uwaa`
--

-- --------------------------------------------------------

--
-- ????????? ?? ??????? `report_codetemplate`
--

DROP TABLE IF EXISTS `report_codetemplate`;
CREATE TABLE IF NOT EXISTS `report_codetemplate` (
  `codetemplate_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`codetemplate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- ???? (?????) ?? ??????? ? ????????? `report_codetemplate`
--

INSERT INTO `report_codetemplate` (`codetemplate_id`, `class_name`, `title`) VALUES
(1, 'Report_Service_CodeTemplate_UpDown', 'Up/Down Traffic'),
(2, 'Report_Service_CodeTemplate_AccessPointsCount', 'Access Point Count'),
(3, 'Report_Service_CodeTemplate_ConnectedCDevices', 'Connected Client Devices'),
(4, 'Report_Service_CodeTemplate_PartConnectedCDevices', 'Connected Client Devices(No Internet)'),
(5, 'Report_Service_CodeTemplate_InternetConnectedCDevices', 'Internet Connected Client Devices');

-- --------------------------------------------------------

--
-- ????????? ?? ??????? `report_groups`
--

DROP TABLE IF EXISTS `report_groups`;
CREATE TABLE IF NOT EXISTS `report_groups` (
  `report_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `codetemplate_id` int(11) DEFAULT NULL,
  `node_id` int(11) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `report_type` int(11) DEFAULT '1',
  `report_interval` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`report_group_id`),
  KEY `template_id` (`codetemplate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- ???? (?????) ?? ??????? ? ????????? `report_groups`
--


-- --------------------------------------------------------

--
-- ????????? ?? ??????? `report_groups_node`
--

DROP TABLE IF EXISTS `report_groups_node`;
CREATE TABLE IF NOT EXISTS `report_groups_node` (
  `report_group_id` int(10) unsigned NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`report_group_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- ???? (?????) ?? ??????? ? ????????? `report_groups_node`
--


-- --------------------------------------------------------

--
-- ????????? ?? ??????? `report_groups_recepients`
--

DROP TABLE IF EXISTS `report_groups_recepients`;
CREATE TABLE IF NOT EXISTS `report_groups_recepients` (
  `recepient_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_group_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`recepient_id`),
  KEY `group_id` (`report_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- ???? (?????) ?? ??????? ? ????????? `report_groups_recepients`
--


-- --------------------------------------------------------

--
-- ????????? ?? ??????? `report_items`
--

DROP TABLE IF EXISTS `report_items`;
CREATE TABLE IF NOT EXISTS `report_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `data` longtext,
  `htmldata` longtext NOT NULL,
  `report_group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `group_id` (`report_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- ???? (?????) ?? ??????? ? ????????? `report_items`
--

SET FOREIGN_KEY_CHECKS=1;
