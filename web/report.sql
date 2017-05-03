-- phpMyAdmin SQL Dump
-- version 4.4.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 07. Dez 2016 um 22:30
-- Server-Version: 5.5.53-0+deb8u1
-- PHP-Version: 5.6.27-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `report`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `banned`
--

CREATE TABLE IF NOT EXISTS `banned` (
  `id` int(11) NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datum-report` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `steamid` varchar(255) NOT NULL,
  `ow` varchar(255) NOT NULL,
  `vac` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `info`
--

CREATE TABLE IF NOT EXISTS `info` (
  `lastcheck` varchar(255) NOT NULL,
  `ow` int(255) NOT NULL,
  `vac` int(255) NOT NULL,
  `checked` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
--
-- Daten für Tabelle `info`
--

INSERT INTO `info` (lastcheck, ow, vac, checked)
VALUES ('29/11/2016 15:11', 0, 0, 0);
--
-- Tabellenstruktur für Tabelle `list`
--

CREATE TABLE IF NOT EXISTS `list` (
  `id` int(11) NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `steamid` varchar(255) NOT NULL,
  `ow` varchar(255) NOT NULL DEFAULT 'false',
  `vac` varchar(255) NOT NULL DEFAULT 'false',
  `ip` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `whitelist`
--

CREATE TABLE IF NOT EXISTS `whitelist` (
  `steamid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `accounts` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `banned`
--
ALTER TABLE `banned`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`lastcheck`);

--
-- Indizes für die Tabelle `list`
--
ALTER TABLE `list`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `whitelist`
--
ALTER TABLE `whitelist`
  ADD UNIQUE KEY `steamid` (`steamid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `banned`
--
ALTER TABLE `banned`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `list`
--
ALTER TABLE `list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
