CREATE TABLE IF NOT EXISTS `crop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ejer` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `imagesname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `profilbillede` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
