CREATE TABLE IF NOT EXISTS `tbl_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `text` text NOT NULL,
  `params` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_trigger`
--

CREATE TABLE IF NOT EXISTS `tbl_trigger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_identifier` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `show_on_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_out` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`,`created`,`session_identifier`,`user_id`,`show_on_time`,`time_out`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;