-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 19 Feb 2011 om 18:36
-- Serverversie: 5.1.36
-- PHP-Versie: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `blog`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Gegevens worden uitgevoerd voor tabel `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Misc'),
(2, 'Back-end dev'),
(3, 'Front-end dev'),
(4, 'Personal stuff');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `created_on` int(10) unsigned NOT NULL,
  `created_by_ip` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Gegevens worden uitgevoerd voor tabel `comments`
--

INSERT INTO `comments` (`comment_id`, `author_id`, `post_id`, `comment`, `created_on`, `created_by_ip`) VALUES
(1, 3, 3, 'ik kan wel genieten van een\r\n\r\nopen regeltje\r\n\r\nhier en daar =)', 1295801104, ''),
(10, 1, 5, 'editje editje', 1296006240, '127.0.0.1'),
(9, 5, 5, 'boele', 1295892745, '127.0.0.1'),
(4, 2, 3, '2\r\n\r\noelze\r\n\r\n2\r\n\r\nboelze\r\n\r\n2', 1295802841, ''),
(5, 7, 3, 'poesje', 1295802992, ''),
(6, 6, 3, 'halloooo iedereeeeen', 1295803007, ''),
(7, 6, 5, 'Schizofreen anyone?', 1295803518, ''),
(8, 5, 5, 'editable si?', 1295803928, '127.0.0.1'),
(11, 5, 5, 'Nechte commentaaar!', 1296006448, '127.0.0.1'),
(14, 1, 5, 'een nieuwetje om te testen', 1296174825, '127.0.0.1'),
(18, 1, 4, 'Het is nog veel makkelijker dan dat tegenwoordig.\r\n\r\nHet enige dat je hoeft te doen is een route maken naar de goede controller:\r\n\r\n    $router->add(''/crud'', array(''controller'' => ''row\\\\applets\\\\sandboxController'')); // of waar je controller ook staat\r\n\r\nThat''s it! Alle URLs die beginnen met /crud, worden nu naar die (applet!) crontroller gestuurd.\r\n\r\nKewl =)', 1296246050, '127.0.0.1'),
(19, 2, 5, 'dit is een comment', 1296258384, '127.0.0.1'),
(20, 2, 4, 'ff ene commentje', 1298136214, '127.0.0.1'),
(21, 2, 4, 'setUser dus...\r\n\r\nen ginne username oder passowrd nodigt!', 1296258675, '127.0.0.1'),
(22, 4, 16, 'not logged in right now...', 1298136291, '127.0.0.1'),
(23, 4, 16, 'STILL not logged in!?!?', 1298136650, '127.0.0.1'),
(24, 4, 16, 'Okay, fiew, that worked...', 1298136672, '127.0.0.1');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `following_posts`
--

CREATE TABLE IF NOT EXISTS `following_posts` (
  `user_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `started_on` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `following_posts`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `following_users`
--

CREATE TABLE IF NOT EXISTS `following_users` (
  `user_id` int(10) unsigned NOT NULL,
  `follows_user_id` int(10) unsigned NOT NULL,
  `started_on` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`follows_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `following_users`
--

INSERT INTO `following_users` (`user_id`, `follows_user_id`, `started_on`) VALUES
(1, 2, 123455655),
(7, 1, 0),
(8, 1, 0),
(6, 1, 0),
(1, 7, 0),
(99, 99, 99);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `author_id` int(10) unsigned NOT NULL,
  `title` varchar(222) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `created_on` int(10) unsigned NOT NULL DEFAULT '0',
  `is_published` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Gegevens worden uitgevoerd voor tabel `posts`
--

INSERT INTO `posts` (`post_id`, `category_id`, `author_id`, `title`, `body`, `created_on`, `is_published`) VALUES
(1, 1, 1, 'testbericht', 'dit is een testbericht wihii', 1295467772, 1),
(2, 1, 1, 'nog ene', 'nog een testbericht', 1295454158, 0),
(3, 1, 1, 'Numero drei', 'een testbericht met  \r\nnieuwe regels  \r\ner\r\n\r\nin', 1295404557, 1),
(4, 1, 1, 'Via sandbox', 'Al is sandbox misschien niet de goede naam... Scaffolding heet het volgens mij =)\r\n\r\nZou het ook CRUD kunnen noemen. Dat is het namelijk.\r\n\r\nMaakt niet uit. Is twee mapjes en een reference veranderen.', 1296961522, 1),
(5, 1, 3, 'Nog een via de nieuwe scaffolding toolz', 'De scaffolding toolz is errug handig, want zo kan je makkelijk een paar posts invoeren en veranderen en het is 1000 miljard keer zo snel als - gaat ie het zeggen? jaaa hij gaat het zeggen - phpMyAdmin. - **aaaaaah**', 1296961521, 1),
(6, 2, 5, 'nieuwe titel ouwe', 'Dus is de de niewste?', 1296231871, 1),
(16, 3, 1, 'front-end...', '...is almost as cool as\r\n\r\n_back-end_\r\n\r\nwink wink', 1296964295, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL DEFAULT '',
  `password` varchar(90) NOT NULL DEFAULT 'aaa',
  `full_name` varchar(200) NOT NULL DEFAULT '',
  `bio` text NOT NULL,
  `access` text NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Gegevens worden uitgevoerd voor tabel `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `bio`, `access`) VALUES
(1, 'root', 'aaa', 'root', '', 'everything'),
(2, 'jaap', 'aaa', 'Jaap de Koning', '', 'blog publish, blog read unpublished'),
(3, 'bert', 'aaa', 'Bert v.d. Zaagweg', 'Ik ben Bert! =)', 'blog delete hidden comments, blog unpublish'),
(4, 'janneke', 'aaa', 'Janneke Meerzeit', '', 'blog flag as spam, blog flag as inappropriate, blog hide comment'),
(5, 'o.boele', 'aaa', 'Oele Boele', 'Oele Boele is gek. Ik bedoel ik ben gek =)', 'everything'),
(6, 'sanne', 'aaa', 'Sanne Fleskens', 'Sanne zat op een school en toen een andere en daarna waarschijnlijk op nog een andere en misschien wel meer dan 1.', 'browse everything'),
(7, 'loesje', 'aaa', 'Loesje', 'Loesje, die van die drummer van de band... Met dat cheile bloesje', 'browse everything, edit but not update everything, add but not insert everything');
