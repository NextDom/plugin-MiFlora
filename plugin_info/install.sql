CREATE TABLE IF NOT EXISTS `MiFlora_remote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remoteName` varchar(128) DEFAULT NULL,
  `configuration` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;