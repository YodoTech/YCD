CREATE TABLE `lzh_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `map_lng` varchar(255) NOT NULL DEFAULT '0',
  `map_lat` varchar(255) NOT NULL DEFAULT '0',
  `map_content` text NOT NULL,
  `map_writer` varchar(20) NOT NULL,
  `map_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
