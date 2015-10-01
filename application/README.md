```
CREATE DATABASE `mail` CHARACTER SET utf8 COLLATE utf8_general_ci; 
USE `mail`;
CREATE TABLE `mail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `body` text,
  `to` varchar(2000) NOT NULL,
  `cc` varchar(2000) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1: waiting, 2: pending, 3:complete',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'create time',
  `ftime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Finish time',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```