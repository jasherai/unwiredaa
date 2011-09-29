-- phpMyAdmin SQL Dump
-- version 3.3.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2011 at 05:42 PM
-- Server version: 5.1.46
-- PHP Version: 5.2.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `uwaa`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE IF NOT EXISTS `admin_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(40) NOT NULL,
  `phone` varchar(45) NOT NULL,
  `address` varchar(64) NOT NULL,
  `city` varchar(64) NOT NULL,
  `zip` varchar(64) NOT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'AT',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Administration interface users' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin_user`
--

INSERT INTO `admin_user` (`user_id`, `firstname`, `lastname`, `email`, `password`, `phone`, `address`, `city`, `zip`, `country`) VALUES
(1, 'System', 'Administrator', 'system@unwired.at', '317f1e761f2faa8da781a4762b9dcc2c5cad209a', '', '', '', '', 'AT');

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_group`
--

CREATE TABLE IF NOT EXISTS `admin_user_group` (
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(11) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `admin_to_group` (`user_id`),
  KEY `group_to_admin` (`group_id`),
  KEY `fk_admin_user_group_role1` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='UI users - groups relation';

--
-- Dumping data for table `admin_user_group`
--


-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`group_id`),
  KEY `fk_groups_roles1` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Network/Operator/Location group tree' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `group`
--

INSERT INTO `group` (`group_id`, `parent_id`, `name`) VALUES
(1, NULL, 'Network');

-- --------------------------------------------------------

--
-- Table structure for table `nas`
--

CREATE TABLE IF NOT EXISTS `nas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nasname` varchar(128) NOT NULL,
  `shortname` varchar(32) DEFAULT NULL,
  `type` varchar(30) DEFAULT 'other',
  `ports` int(5) DEFAULT NULL,
  `secret` varchar(60) NOT NULL DEFAULT 'secret',
  `server` varchar(64) DEFAULT NULL,
  `community` varchar(50) DEFAULT NULL,
  `description` varchar(200) DEFAULT 'RADIUS Client',
  PRIMARY KEY (`id`),
  KEY `nasname` (`nasname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `nas`
--


-- --------------------------------------------------------

--
-- Table structure for table `network_user`
--

CREATE TABLE IF NOT EXISTS `network_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `username` varchar(64) NOT NULL,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `phone` varchar(45) NOT NULL,
  `address` varchar(64) NOT NULL,
  `city` varchar(64) NOT NULL,
  `zip` varchar(64) NOT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'AT',
  `mac` varchar(17) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  KEY `fk_network_users_groups1` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Network users table. It is used for the UI not to mess with ' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `network_user`
--


--
-- Triggers `network_user`
--
DROP TRIGGER IF EXISTS `delete_user`;
DELIMITER //
CREATE TRIGGER `delete_user` AFTER DELETE ON `network_user`
 FOR EACH ROW BEGIN

    

    DELETE FROM `radusergroup` WHERE `username`=OLD.username;

    DELETE FROM `radcheck` WHERE `username`=OLD.username;

    DELETE FROM `radreply` WHERE `username`=OLD.username;

    

  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `network_user_policy`
--

CREATE TABLE IF NOT EXISTS `network_user_policy` (
  `policy_id` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`policy_id`,`user_id`),
  KEY `fk_policy_groups_has_network_users_network_users1` (`user_id`),
  KEY `fk_policy_groups_has_network_users_policy_groups1` (`policy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Network users to policy group relation';

--
-- Dumping data for table `network_user_policy`
--


--
-- Triggers `network_user_policy`
--
DROP TRIGGER IF EXISTS `insert_user_group`;
DELIMITER //
CREATE TRIGGER `insert_user_group` AFTER INSERT ON `network_user_policy`
 FOR EACH ROW BEGIN

    DECLARE addgroup, adduser VARCHAR(64);

    DECLARE gpriority INT(10);

    

    SELECT `name`,`priority` INTO addgroup, gpriority FROM `policy_group` WHERE `policy_id`=NEW.policy_id LIMIT 1;

    SELECT `username` INTO adduser FROM `network_user` WHERE `user_id`=NEW.user_id LIMIT 1;

    

    INSERT INTO `radusergroup` VALUES(adduser, addgroup, gpriority);

  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `delete_user_group`;
DELIMITER //
CREATE TRIGGER `delete_user_group` AFTER DELETE ON `network_user_policy`
 FOR EACH ROW BEGIN

    DECLARE delgroup, deluser VARCHAR(64);



    SELECT `name` INTO delgroup FROM `policy_group` WHERE `policy_id`=OLD.policy_id LIMIT 1;

    SELECT `username` INTO deluser FROM `network_user` WHERE `user_id`=OLD.user_id LIMIT 1;

    

    DELETE FROM `radusergroup` WHERE `groupname`=delgroup AND `username`=deluser;

  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE IF NOT EXISTS `node` (
  `node_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `mac` varchar(17) NOT NULL,
  `status` enum('enabled','disabled','planning') NOT NULL DEFAULT 'planning',
  `to_update` tinyint(1) NOT NULL DEFAULT '0',
  `online_status_changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `online_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`node_id`),
  UNIQUE KEY `macidx` (`mac`),
  KEY `fk_nodes_groups1` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Network nodes that users can connect to' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `node`
--


-- --------------------------------------------------------

--
-- Table structure for table `node_location`
--

CREATE TABLE IF NOT EXISTS `node_location` (
  `node_id` int(10) unsigned NOT NULL,
  `address` varchar(64) NOT NULL,
  `city` varchar(64) NOT NULL,
  `zip` varchar(64) NOT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'AT',
  `latitude` float(10,6) DEFAULT NULL,
  `longitude` float(10,6) DEFAULT NULL,
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Network node location information';

--
-- Dumping data for table `node_location`
--


-- --------------------------------------------------------

--
-- Table structure for table `node_settings`
--

CREATE TABLE IF NOT EXISTS `node_settings` (
  `node_id` int(10) unsigned NOT NULL,
  `activefrom` tinyint(4) DEFAULT NULL,
  `activeto` tinyint(4) DEFAULT NULL,
  `ipaddress` varchar(15) DEFAULT NULL,
  `netmask` varchar(15) DEFAULT NULL,
  `gateway` varchar(15) DEFAULT NULL,
  `dnsservers` varchar(255) DEFAULT NULL,
  `bandwidthup` int(11) NOT NULL,
  `bandwidthdown` int(11) NOT NULL,
  `trafficlimit` int(11) NOT NULL,
  `ssid` varchar(45) NOT NULL,
  `channel` smallint(6) NOT NULL DEFAULT '11',
  `roaming` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Network node settings (used to create a configuration file)';

--
-- Dumping data for table `node_settings`
--


-- --------------------------------------------------------

--
-- Table structure for table `policy_group`
--

CREATE TABLE IF NOT EXISTS `policy_group` (
  `policy_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT '1000',
  PRIMARY KEY (`policy_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Network policy groups, actual values are held in the freerad' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `policy_group`
--

INSERT INTO `policy_group` (`policy_id`, `name`, `priority`) VALUES
(1, 'Guest', 1000),
(2, 'Authenticated', 900),
(3, 'Disabled', 700);

--
-- Triggers `policy_group`
--
DROP TRIGGER IF EXISTS `update_group_name`;
DELIMITER //
CREATE TRIGGER `update_group_name` AFTER UPDATE ON `policy_group`
 FOR EACH ROW BEGIN
    UPDATE `radusergroup` SET `groupname`=NEW.name, `priority`=NEW.priority WHERE `groupname`=OLD.name;
    IF OLD.name != NEW.name THEN
        UPDATE `radgroupreply` SET `groupname`=NEW.name WHERE `groupname`=OLD.name;
        UPDATE `radgroupcheck` SET `groupname`=NEW.name WHERE `groupname`=OLD.name;
    END IF;
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `delete_group`;
DELIMITER //
CREATE TRIGGER `delete_group` AFTER DELETE ON `policy_group`
 FOR EACH ROW BEGIN

    DELETE FROM `radusergroup` WHERE `groupname`=OLD.name;

    DELETE FROM `radgroupreply` WHERE `groupname`=OLD.name;

    DELETE FROM `radgroupcheck` WHERE `groupname`=OLD.name;

  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `radacct`
--

CREATE TABLE IF NOT EXISTS `radacct` (
  `radacctid` bigint(21) NOT NULL AUTO_INCREMENT,
  `acctsessionid` varchar(64) NOT NULL DEFAULT '',
  `acctuniqueid` varchar(32) NOT NULL DEFAULT '',
  `username` varchar(64) NOT NULL DEFAULT '',
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `realm` varchar(64) DEFAULT '',
  `nasipaddress` varchar(15) NOT NULL DEFAULT '',
  `nasportid` varchar(15) DEFAULT NULL,
  `nasporttype` varchar(32) DEFAULT NULL,
  `acctstarttime` datetime DEFAULT NULL,
  `acctstoptime` datetime DEFAULT NULL,
  `acctsessiontime` int(12) DEFAULT NULL,
  `acctauthentic` varchar(32) DEFAULT NULL,
  `connectinfo_start` varchar(50) DEFAULT NULL,
  `connectinfo_stop` varchar(50) DEFAULT NULL,
  `acctinputoctets` bigint(20) DEFAULT NULL,
  `acctoutputoctets` bigint(20) DEFAULT NULL,
  `calledstationid` varchar(50) NOT NULL DEFAULT '',
  `callingstationid` varchar(50) NOT NULL DEFAULT '',
  `acctterminatecause` varchar(32) NOT NULL DEFAULT '',
  `servicetype` varchar(32) DEFAULT NULL,
  `framedprotocol` varchar(32) DEFAULT NULL,
  `framedipaddress` varchar(15) NOT NULL DEFAULT '',
  `acctstartdelay` int(12) DEFAULT NULL,
  `acctstopdelay` int(12) DEFAULT NULL,
  `xascendsessionsvrkey` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`radacctid`),
  KEY `username` (`username`),
  KEY `framedipaddress` (`framedipaddress`),
  KEY `acctsessionid` (`acctsessionid`),
  KEY `acctsessiontime` (`acctsessiontime`),
  KEY `acctuniqueid` (`acctuniqueid`),
  KEY `acctstarttime` (`acctstarttime`),
  KEY `acctstoptime` (`acctstoptime`),
  KEY `nasipaddress` (`nasipaddress`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `radacct`
--


--
-- Triggers `radacct`
--
DROP TRIGGER IF EXISTS `radius_user_sync`;
DELIMITER //
CREATE TRIGGER `radius_user_sync` AFTER INSERT ON `radacct`
 FOR EACH ROW BEGIN

    DECLARE usercount, guestgroup, guestuser INT;



    SELECT count(*) INTO usercount FROM `network_user` WHERE `username`=NEW.username LIMIT 1;

    SELECT `group_id` INTO guestgroup FROM `policy_group` WHERE `priority`>=1000 ORDER BY `priority` DESC LIMIT 1;

    

    IF usercount = 0 THEN

        INSERT INTO `network_user`(`group_id`,`username`,`firstname`,`lastname`,`email`,`phone`,`address`,`city`,`zip`,`country`,`mac`) VALUES(1, NEW.username, '','','','','','','','AT',NEW.username);

        SELECT LAST_INSERT_ID() INTO guestuser;

        

        IF guestuser > 0 THEN

            INSERT INTO `network_user_policy` VALUES(guestgroup, guestuser);

        END IF;

    END IF;

    

  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `radcheck`
--

CREATE TABLE IF NOT EXISTS `radcheck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `radcheck`
--

INSERT INTO `radcheck` (`id`, `username`, `attribute`, `op`, `value`) VALUES
(2, 'magicshark', 'Cleartext-Password', ':=', 'finger00'),
(1, 'amade', 'Cleartext-Password', ':=', 'edama'),
(3, 'magicshark', 'Max-Daily-Traffic', ':=', '1048576000'),
(4, 'magicshark', 'Max-Daily-Time', ':=', '36000');

-- --------------------------------------------------------

--
-- Table structure for table `radgroupcheck`
--

CREATE TABLE IF NOT EXISTS `radgroupcheck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `radgroupcheck`
--

INSERT INTO `radgroupcheck` (`id`, `groupname`, `attribute`, `op`, `value`) VALUES
(32, 'Authenticated', 'Max-Daily-Traffic', '==', '10485760000'),
(33, 'Disabled', 'Auth-Type', ':=', 'Reject'),
(30, 'Authenticated', 'Max-Daily-Time', '==', '43200'),
(31, 'Authenticated', 'Auth-Type', '=', 'Accept'),
(29, 'Guest', 'Auth-Type', ':=', 'Accept');

-- --------------------------------------------------------

--
-- Table structure for table `radgroupreply`
--

CREATE TABLE IF NOT EXISTS `radgroupreply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '=',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

--
-- Dumping data for table `radgroupreply`
--

INSERT INTO `radgroupreply` (`id`, `groupname`, `attribute`, `op`, `value`) VALUES
(42, 'Authenticated', 'Acct-Interim-Interval', ':=', '60'),
(36, 'Guest', 'ChilliSpot-Bandwidth-Max-Down', ':=', '1048'),
(41, 'Authenticated', 'ChilliSpot-Bandwidth-Max-Up', ':=', '1024'),
(39, 'Authenticated', 'Session-Timeout', ':=', '86400'),
(40, 'Authenticated', 'ChilliSpot-Max-Total-Octets', ':=', '10485760000'),
(38, 'Authenticated', 'Idle-Timeout ', ':=', '3600'),
(37, 'Authenticated', 'ChilliSpot-Bandwidth-Max-Down', ':=', '4096');

-- --------------------------------------------------------

--
-- Table structure for table `radpostauth`
--

CREATE TABLE IF NOT EXISTS `radpostauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `pass` varchar(64) NOT NULL DEFAULT '',
  `reply` varchar(32) NOT NULL DEFAULT '',
  `authdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `radpostauth`
--


-- --------------------------------------------------------

--
-- Table structure for table `radreply`
--

CREATE TABLE IF NOT EXISTS `radreply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '=',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `radreply`
--


-- --------------------------------------------------------

--
-- Table structure for table `radusergroup`
--

CREATE TABLE IF NOT EXISTS `radusergroup` (
  `username` varchar(64) NOT NULL DEFAULT '',
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `priority` int(11) NOT NULL DEFAULT '1',
  KEY `username` (`username`(32))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `radusergroup`
--


-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `permissions` text NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  KEY `fk_role_role1` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='UI roles/permissions' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `name`, `permissions`, `parent_id`) VALUES
(1, 'System', 'a:1:{i:0;a:2:{s:8:"resource";N;s:11:"permissions";N;}}', NULL),
(2, 'Admin', 'a:7:{s:15:"default_setting";N;s:12:"groups_group";a:4:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";}s:13:"groups_policy";a:1:{i:0;s:4:"view";}s:11:"groups_role";a:1:{i:0;s:4:"view";}s:10:"nodes_node";a:4:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";}s:11:"users_admin";a:4:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";}s:13:"users_netuser";a:4:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";}}', 6),
(3, 'Operator', 'a:7:{s:15:"default_setting";N;s:12:"groups_group";N;s:13:"groups_policy";a:1:{i:0;s:4:"view";}s:11:"groups_role";N;s:10:"nodes_node";a:4:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";}s:11:"users_admin";a:4:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";}s:13:"users_netuser";a:4:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";}}', 2),
(4, 'Location', 'a:6:{s:11:"users_admin";N;s:13:"users_netuser";a:3:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";}s:12:"groups_group";N;s:13:"groups_policy";N;s:11:"groups_role";N;s:10:"nodes_node";a:3:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";}}', 3),
(5, 'Statistics', 'a:6:{s:11:"users_admin";N;s:13:"users_netuser";a:1:{i:0;s:4:"view";}s:12:"groups_group";N;s:13:"groups_policy";a:1:{i:0;s:4:"view";}s:11:"groups_role";N;s:10:"nodes_node";a:1:{i:0;s:4:"view";}}', 4),
(6, 'Customer sysadmin', 'a:7:{s:15:"default_setting";N;s:12:"groups_group";a:4:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";}s:13:"groups_policy";a:1:{i:0;s:4:"view";}s:11:"groups_role";N;s:10:"nodes_node";a:4:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";}s:11:"users_admin";a:5:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";i:4;s:7:"special";}s:13:"users_netuser";a:4:{i:0;s:4:"view";i:1;s:3:"add";i:2;s:4:"edit";i:3;s:6:"delete";}}', 1);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(45) NOT NULL,
  `value` text,
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `key_UNIQUE` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `key`, `value`) VALUES
(1, 'site_title', 'Unwired AA'),
(2, 'node_map_center_lat', '47.353711'),
(3, 'node_map_center_lng', '13.358917'),
(4, 'node_map_zoom', '10');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_user_group`
--
ALTER TABLE `admin_user_group`
  ADD CONSTRAINT `group_to_admin` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `admin_to_group` FOREIGN KEY (`user_id`) REFERENCES `admin_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_user_group_role1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `group`
--
ALTER TABLE `group`
  ADD CONSTRAINT `fk_parent_group` FOREIGN KEY (`parent_id`) REFERENCES `group` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `network_user`
--
ALTER TABLE `network_user`
  ADD CONSTRAINT `fk_network_users_groups1` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `network_user_policy`
--
ALTER TABLE `network_user_policy`
  ADD CONSTRAINT `fk_policy_groups_has_network_users_policy_groups1` FOREIGN KEY (`policy_id`) REFERENCES `policy_group` (`policy_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_policy_groups_has_network_users_network_users1` FOREIGN KEY (`user_id`) REFERENCES `network_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `fk_nodes_groups1` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `node_location`
--
ALTER TABLE `node_location`
  ADD CONSTRAINT `fk_nodes_location_nodes` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node_settings`
--
ALTER TABLE `node_settings`
  ADD CONSTRAINT `fk_nodes_settings_nodes1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `fk_role_role1` FOREIGN KEY (`parent_id`) REFERENCES `role` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
