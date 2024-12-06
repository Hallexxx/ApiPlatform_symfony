-- --------------------------------------------------------
-- Hôte:                         localhost
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour music_library
CREATE DATABASE IF NOT EXISTS `music_library` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `music_library`;

-- Listage de la structure de table music_library. album
CREATE TABLE IF NOT EXISTS `album` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `artist_id` int NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_39986E43B7970CF8` (`artist_id`),
  CONSTRAINT `FK_album_artist` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table music_library.album : ~8 rows (environ)
DELETE FROM `album`;
INSERT INTO `album` (`id`, `title`, `date`, `artist_id`, `image`) VALUES
	(2, 'The Eminem Show', '2002-05-26', 2, 'eminem_show.jpg'),
	(3, 'Pyramide', '2024-02-16', 4, 'pyramide_werenoi.jpg'),
	(4, 'Utopia', '2023-07-28', 5, 'utopia_ts.jpg'),
	(5, 'Starboy', '2016-11-25', 3, 'starboy_tk.jpg'),
	(6, 'Loud', '2010-11-12', 6, 'loud_rihanna.jpg'),
	(7, 'Heroes & Villains', '2022-12-02', 7, 'hero.jpg'),
	(8, '8 Mile Soundtrack', '2002-10-29', 2, '8-mile.jpg'),
	(9, 'Thriller', '1982-11-29', 8, 'thriller.jpg'),
	(10, 'Unapologetic', '2012-11-19', 6, 'unapolo.jpg');

-- Listage de la structure de table music_library. artist
CREATE TABLE IF NOT EXISTS `artist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `style` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `nationality` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table music_library.artist : ~7 rows (environ)
DELETE FROM `artist`;
INSERT INTO `artist` (`id`, `name`, `style`, `first_name`, `last_name`, `birth_date`, `nationality`, `image`) VALUES
	(2, 'Eminem', 'hip-hop', 'Marshall', 'Mathers', '1972-10-17', 'Américain', 'eminem.jpg'),
	(3, 'The Weeknd', 'R&B', 'Abel', 'Tesfaye', '1990-02-16', 'Canadien', 'theweeknd.jpg'),
	(4, 'Werenoi', 'Rap', 'Jeremy', 'Bana-Owona', '1994-01-30', 'Francais', 'werenoi.jpg'),
	(5, 'Travis Scott', 'hip-hop', 'Jacques', 'Webster', '1991-04-30', 'Américain', 'travis.jpg'),
	(6, 'Rihanna', 'R&B', 'Robyn', 'Fenty', '1988-02-20', 'barbadienne', 'rihanna.jpg'),
	(7, 'Metro Boomin', 'hip-hop', 'Leland', 'Wayne', '1993-09-16', 'Américain', 'metro.jpg'),
	(8, 'Michael Jackson', 'soul', 'Michael', 'Jackson', '1958-08-29', 'Américain', 'Michael_Jackson.jpg');

-- Listage de la structure de table music_library. doctrine_migration_versions
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table music_library.doctrine_migration_versions : ~3 rows (environ)
DELETE FROM `doctrine_migration_versions`;
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20241118103943', '2024-11-19 09:33:00', 118),
	('DoctrineMigrations\\Version20241124112031', '2024-11-24 11:21:47', 283),
	('DoctrineMigrations\\Version20241124112950', '2024-11-24 11:29:57', 10);

-- Listage de la structure de table music_library. favoris
CREATE TABLE IF NOT EXISTS `favoris` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `album_id` int DEFAULT NULL,
  `artist_id` int DEFAULT NULL,
  `song_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8933C432A76ED395` (`user_id`),
  KEY `IDX_8933C4321137ABCF` (`album_id`),
  KEY `IDX_8933C432B7970CF8` (`artist_id`),
  KEY `IDX_8933C432A0BDB2F3` (`song_id`),
  CONSTRAINT `FK_8933C4321137ABCF` FOREIGN KEY (`album_id`) REFERENCES `album` (`id`),
  CONSTRAINT `FK_8933C432A0BDB2F3` FOREIGN KEY (`song_id`) REFERENCES `song` (`id`),
  CONSTRAINT `FK_8933C432A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_8933C432B7970CF8` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table music_library.favoris : ~0 rows (environ)
DELETE FROM `favoris`;

-- Listage de la structure de table music_library. song
CREATE TABLE IF NOT EXISTS `song` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `length` int NOT NULL,
  `album_id` int NOT NULL,
  `date` date NOT NULL,
  `file_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lyrics` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_33EDEEA11137ABCF` (`album_id`),
  CONSTRAINT `FK_33EDEEA11137ABCF` FOREIGN KEY (`album_id`) REFERENCES `album` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table music_library.song : ~22 rows (environ)
DELETE FROM `song`;
INSERT INTO `song` (`id`, `title`, `length`, `album_id`, `date`, `file_url`, `image`, `lyrics`) VALUES
	(2, 'Without Me', 290, 2, '2002-05-21', 'without-me.mp3', 'eminem_show.jpg', 'Deux filles du parc à caravanes font le tour de l\'extérieur\r\nLe tour de l\'extérieur, le tour de l\'extérieur\r\nDeux filles du parc à caravanes font le tour de l\'extérieur\r\nLe tour de l\'extérieur, le tour de l\'extérieur\r\nWoo (Ooh, ooh)\r\n\r\n[Refrain : Eminem]\r\nDevinez qui est de retour, de retour encore ?\r\nShady est de retour, dis-le à un ami\r\nDevine qui est de retour, devine qui est de retour\r\nDevine qui est de retour, devine qui est de retour\r\nDevine qui est de retour, devine qui est de retour\r\nDevine qui est de retour\r\n(Na-na-na, na, na, na, na, na, na) \r\n(Na-na-na, na, na, na, na)\r\n\r\n[Couplet 1 : Eminem]\r\nJ\'ai créé un monstre\r\nParce que personne ne veut plus voir Marshall, ils veulent Shady, je suis du foie haché\r\nBon, si tu veux Shady, c\'est ce que je te donne\r\nUn peu d\'herbe mélangée à de l\'alcool fort\r\nDe la vodka qui fera redémarrer mon cœur plus vite\r\nQu\'un choc quand je suis choqué à l\'hôpital\r\nPar le docteur quand je ne coopère pas\r\nQuand je fais bouger la table pendant qu\'il opère (Hé)\r\nTu as attendu si longtemps, maintenant arrête de débattre\r\nParce que je suis de retour, je suis sous le charme et j\'ovule\r\nJe sais que tu J\'ai un boulot, Mlle Cheney\r\nMais le problème cardiaque de votre mari se complique\r\nAlors la FCC ne me laisse pas tranquille\r\nOu me laisse être moi-même, alors laissez-moi voir\r\nIls ont essayé de me faire taire sur MTV\r\nMais c\'est tellement vide sans moi\r\nAlors venez et trempez, le cul sur vos lèvres\r\nPutain, éjacule sur vos lèvres et un peu sur vos seins\r\nEt préparez-vous, parce que cette merde va devenir lourde\r\nJe viens de régler tous mes procès (Va te faire foutre, Debbie)\r\n\r\n[Refrain : Eminem]\r\nMaintenant, ça a l\'air d\'un boulot pour moi\r\nAlors tout le monde, suivez-moi\r\nParce qu\'il nous faut un peu de controverse\r\nParce que c\'est tellement vide sans moi\r\nJ\'ai dit que ça avait l\'air d\'un boulot pour moi\r\nAlors tout le monde, suivez-moi\r\nParce qu\'il nous faut un peu de controverse\r\nParce que c\'est tellement vide sans moi\r\n\r\n[Couplet 2 : Eminem]\r\nPetits traîtres, des enfants qui se sentent rebelles\r\nEmbarrassés, leurs parents écoutent toujours Elvis\r\nIls commencent à se sentir comme des prisonniers, impuissants\r\nJusqu\'à ce que quelqu\'un arrive en mission et crie, "Salope"\r\nUn visionnaire, la vision fait peur\r\nPourrait déclencher une révolution, polluant les ondes\r\nUn rebelle , alors laisse-moi juste me délecter et me prélasser\r\nDu fait que j\'ai fait en sorte que tout le monde m\'embrasse le cul\r\nEt c\'est un désastre, une telle catastrophe\r\nQue tu voies autant de mon cul,tu m\'as demandé ?\r\nEh bien, je suis de retour, da-na-na-na, na-na-na-na-na-na\r\nRépare ton antenne tordue, accorde-la, et ensuite j\'entrerai\r\net monterai sous ta peau comme une écharde\r\nLe centre d\'attention, de retour pour l\'hiver\r\nJe suis intéressant, la meilleure chose depuis la lutte\r\nInfestant les oreilles de ton enfant et faisant son nid\r\nTestant, "Attention, s\'il te plaît"\r\nRessens la tension dès que quelqu\'un me mentionne\r\nVoici mes dix cents, mes deux cents sont gratuits\r\nUne nuisance, qui m\'a envoyé ? Tu m\'as envoyé chercher ?\r\n\r\n[Refrain : Eminem]\r\nMaintenant, ça a l\'air d\'être un boulot pour moi\r\nAlors tout le monde, suivez-moi\r\nParce qu\'on a besoin d\'un peu de controverse\r\nParce que c\'est si vide sans moi\r\nJ\'ai dit que ça a l\'air d\'être un boulot pour moi\r\nAlors tout le monde, suivez-moi\r\nParce qu\'on a besoin d\'un peu de controverse\r\nParce que c\'est si vide sans moi\r\n\r\n[Couplet 3 : Eminem]\r\nUn mouchoir, un mouchoir, je rendrai coup pour coup avec\r\nTous ceux qui parlent de "cette merde, cette merde"\r\nChris Kirkpatrick, tu peux te faire botter le cul\r\nBien pire que ces petits bâtards de Limp Bizkit\r\nEt Moby ? Obie peut te marcher dessus\r\nEspèce de pédé chauve de trente-six ans, suce-moi\r\nTu ne me connais pas, t\'es trop vieux, lâche-moi\r\nC\'est fini, personne n\'écoute de la techno\r\nMaintenant, allons-y, fais-moi juste signe\r\nJ\'arriverai avec une liste entière de nouvelles insultes\r\nJ\'ai été génial, plein de suspense avec un crayon\r\nDepuis que Prince est devenu un symbole\r\nMais, parfois, la merde semble juste\r\nTout le monde ne veut parler que de moi\r\nAlors ça doit vouloir dire que je suis dégoûtant\r\nMais c\'est juste moi, je suis juste obscène (Ouais)\r\nMême si je ne suis pas le premier roi de la controverse\r\nJe suis la pire chose depuis Elvis Presley\r\nDe faire de la musique noire si égoïstement\r\nEt de l\'utiliser pour m\'enrichir (Hey)\r\nY a un concept qui marche\r\nVingt millions d\'autres rappeurs blancs émergent\r\nMais peu importe le nombre de poissons dans la mer\r\nElle serait si vide sans moi\r\n\r\n[Refrain : Eminem]\r\nMaintenant, ça a l\'air d\'être un boulot pour moi\r\nAlors tout le monde, suivez-moi\r\nParce qu\'on a besoin un peu de controverse\r\nParce que c\'est si vide sans moi\r\nJ\'ai dit que ça ressemblait à un travail pour moi\r\nAlors tout le monde, suivez-moi\r\nParce qu\'on a besoin d\'un peu de controverse\r\nParce que c\'est si vide sans moi\r\n\r\n[Outro : Eminem]\r\nHum, dei-dei, la-la\r\nLa-la, la-la-la\r\nLa-la, la-la-la\r\nLa-la, la-la\r\nHum, dei-dei, la-la\r\nLa-la, la-la-la\r\nLa-la, la-la-la\r\nLa-la, la-la\r\nLes enfants'),
	(3, 'THANK GOD', 184, 4, '2023-07-28', 'thank.mp3', 'utopia_ts.jpg', NULL),
	(4, 'MY EYES', 248, 4, '2023-07-28', 'myeyes.mp3', 'utopia_ts.jpg', NULL),
	(5, 'FE!N', 188, 4, '2023-07-28', 'fein.mp3', 'utopia_ts.jpg', NULL),
	(6, 'I KNOW ?', 208, 4, '2023-07-28', 'iknow.mp3', 'utopia_ts.jpg', NULL),
	(7, 'MELTDOWN', 245, 4, '2023-07-28', 'melt.mp3', 'utopia_ts.jpg', NULL),
	(8, 'TELEKINESIS', 334, 4, '2023-07-28', 'tele.mp3', 'utopia_ts.jpg', NULL),
	(9, 'Raindrops (Insane)', 185, 7, '2022-12-03', 'raindrops.mp3', 'hero.jpg', NULL),
	(10, 'Too Many Nights', 194, 7, '2022-12-03', 'toomany.mp3', 'hero.jpg', NULL),
	(11, 'Trance', 189, 7, '2022-12-03', 'trance.mp3', 'hero.jpg', NULL),
	(12, 'Creepin’', 205, 7, '2022-12-03', 'creepin.mp3', 'hero.jpg', NULL),
	(13, 'Die for You', 253, 5, '2016-11-25', 'die_tk.mp4', 'die_tk.png', NULL),
	(14, 'Lose Yourself', 314, 8, '2002-10-28', 'eminem_lose.mp3', 'lose.jpg', NULL),
	(15, 'Sing for the Moment', 324, 2, '2003-02-25', 'eminem_sing.mp3', 'eminem_show.jpg', NULL),
	(16, 'Superman', 268, 2, '2003-01-27', 'eminem-superman.mp3', 'super_eminem.jpg', NULL),
	(17, 'I Feel It Coming', 275, 5, '2016-11-18', 'ifeel_tk.mp3', 'feel_tk.jpg', NULL),
	(18, 'Thriller', 334, 6, '1983-11-02', 'michael_thriller.mp3', 'thriller.jpg', NULL),
	(19, 'Party Monster', 246, 5, '2016-11-18', 'party_tk.mp3', 'party_tk.jpg', NULL),
	(20, 'Reminder', 211, 5, '2016-11-25', 'remind_tk.mp3', 'reminder_tk.jpg', NULL),
	(21, 'Rihanna: What\'s My Name?', 255, 6, '2010-10-26', 'rihanna_whats.mp3', 'rihanna_what.png', NULL),
	(22, 'Starboy', 211, 5, '2016-11-25', 'starboy.mp4', 'starboy_tk.jpg', NULL),
	(23, 'Diamonds', 308, 10, '2012-11-09', 'diamond.mp3', 'diamond.jpg', NULL);

-- Listage de la structure de table music_library. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roles` json NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table music_library.user : ~3 rows (environ)
DELETE FROM `user`;
INSERT INTO `user` (`id`, `username`, `email`, `password`, `profile_picture`, `roles`) VALUES
	(12, 'newusersname', 'users@example.com', '$2y$13$VTfRZWFDZQgK1vRe0WBWye9nt/9WDMlQV8AoUJxYsblO78IpyClcu', NULL, '[]'),
	(16, 'alex', 'alex@gmail.com', '$2y$13$TLeM0TWLKeBgrFaRVm462OvPSPgxJqK4sFmu7ZXEY0fhVbVWQhFxi', NULL, '[]'),
	(19, 'Hallexx', 'alex.perezab470@gmail.com', '$2y$13$/3YmKBBqcey3ijOSRgmeQOz4efTpA4BQVsoXo8eAfLNn.4nGnULbO', NULL, '[]');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
