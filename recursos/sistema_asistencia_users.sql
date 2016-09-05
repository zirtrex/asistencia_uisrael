CREATE TABLE IF NOT EXISTS `session` (
  `id` CHAR(32) NOT NULL DEFAULT '',
  `name` CHAR(32) NOT NULL DEFAULT '',
  `modified` INT(11) DEFAULT NULL,
  `lifetime` INT(11) DEFAULT NULL,
  `data` TEXT,
  PRIMARY KEY (`id`,`name`)
) ENGINE=INNODB;