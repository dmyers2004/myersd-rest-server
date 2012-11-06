DROP TABLE IF EXISTS `access`;

CREATE TABLE `access` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT NULL,
  `name` varchar(128) DEFAULT '',
  `gui_access` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `access` (`id`, `created`, `modified`, `name`, `gui_access`)
VALUES (1,'{now}','{now}','Admin',1);


# Dump of table access_resource
# ------------------------------------------------------------

DROP TABLE IF EXISTS `access_resource`;

CREATE TABLE `access_resource` (
  `access_id` bigint(20) DEFAULT NULL,
  `resource_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Dump of table gui_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gui_log`;

CREATE TABLE `gui_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT NULL,
  `time` timestamp NULL DEFAULT NULL,
  `user_id` varchar(128) DEFAULT NULL,
  `object` varchar(64) DEFAULT '',
  `method` varchar(64) DEFAULT NULL,
  `args` varchar(255) DEFAULT NULL,
  `request` text,
  `agent` varchar(255) DEFAULT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `memory` bigint(20) DEFAULT NULL,
  `pmemory` bigint(20) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `etime` double DEFAULT NULL,
  `auth_user` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


# Dump of table resource
# ------------------------------------------------------------

DROP TABLE IF EXISTS `resource`;

CREATE TABLE `resource` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT NULL,
  `object` varchar(64) DEFAULT NULL,
  `method` varchar(64) DEFAULT NULL,
  `old` tinyint(1) DEFAULT '0',
  `type` varchar(32) DEFAULT '',
  `version` varchar(8) DEFAULT NULL,
  `dbconnection` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Dump of table session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL DEFAULT '0',
  `start` int(10) unsigned NOT NULL DEFAULT '0',
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Dump of table settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT NULL,
  `slug` varchar(64) DEFAULT NULL,
  `value` varchar(255) DEFAULT '',
  `usage` varchar(255) DEFAULT NULL,
  `usedon` tinyint(1) DEFAULT '0',
  `canedit` tinyint(1) DEFAULT '0',
  `root` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `settings` (`created`, `modified`, `slug`, `value`, `usage`, `usedon`, `canedit`, `root`)
VALUES
	('{now}','{now}','log_entries','128','How many log entries to show in the dashboard',2,1,1),
	('{now}','{now}','no_user','Unknown','When no user name is available what name should be displayed?',3,1,1),
	('{now}','{now}','gui_version','1.0','Display the GUI version as follows',2,0,1),
	('{now}','{now}','server_version','1.1','The version of the server',1,0,1),
	('{now}','{now}','server_auth','1','Server Auth on/off (integer)',1,1,1),
	('{now}','{now}','realm','MyRESTful Server','This is used as the title in the GUI and the Auth Realm on the server',3,1,1),
	('{now}','{now}','cache_expiration','5','The servers cache length in seconds',1,1,1),
	('{now}','{now}','server_folder','../server/','path from gui router to server router',2,1,1),
	('{now}','{now}','model_segment','2','the url segment to use from the model',1,1,1),
	('{now}','{now}','ajax_only','0','Allow Ajax call only on/off (integer)',1,1,1),
	('{now}','{now}','default_model','default','default model to use when no model given on the server',1,1,1),
	('{now}','{now}','server_access_log','1','Server Access Log on/off (integer)',1,1,1),
	('{now}','{now}','default_server_connection','{host}/{dbname}/{dbpassword}/{database}','default server db connection',1,1,1),
	('{now}','{now}','server_auth_log','1','Server Auth Log on/off (integer)',1,1,1),
	('{now}','{now}','server_connection','{host}/{dbname}/{dbpassword}/{database}','Server database connection',1,0,1),
	('{now}','{now}','hash','{hash}','Server Hash',1,0,1);

# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT NULL,
  `name` varchar(64) DEFAULT '',
  `password` varchar(32) DEFAULT NULL,
  `email` varchar(128) DEFAULT '',
  `access_id` int(11) DEFAULT '0',
  `active` tinyint(1) DEFAULT '0',
  `last_server_visit` timestamp NULL DEFAULT NULL,
  `last_gui_visit` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user` (`id`, `created`, `modified`, `name`, `password`, `email`, `access_id`, `active`, `last_server_visit`,`last_gui_visit`)
VALUES (1,'{now}','{now}','{name}','{password}','{email}',1,1,NULL,NULL);
