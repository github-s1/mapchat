-- MySQL dump 10.13  Distrib 5.5.46, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: map_chat
-- ------------------------------------------------------
-- Server version	5.5.46-0ubuntu0.14.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audio`
--

DROP TABLE IF EXISTS `audio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mark` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mark` (`id_mark`),
  CONSTRAINT `audio_mark_key` FOREIGN KEY (`id_mark`) REFERENCES `mark` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audio`
--

LOCK TABLES `audio` WRITE;
/*!40000 ALTER TABLE `audio` DISABLE KEYS */;
/*!40000 ALTER TABLE `audio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `avatar`
--

DROP TABLE IF EXISTS `avatar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `avatar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `big_photo` varchar(45) NOT NULL,
  `small_photo` varchar(45) NOT NULL,
  `src` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avatar`
--

LOCK TABLES `avatar` WRITE;
/*!40000 ALTER TABLE `avatar` DISABLE KEYS */;
INSERT INTO `avatar` VALUES (1,'avatar_1459175258.gif','avatar_1459175258.gif','/var/www/map_chat/data/www/map.taxiavenue.com/img/users_avatar/avatar_1459175258.gif'),(2,'avatar_1459409867.png','avatar_1459409867.png','/var/www/map_chat/data/www/map.taxiavenue.com/img/users_avatar/avatar_1459409867.png');
/*!40000 ALTER TABLE `avatar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'в таблице храняться названия не только городов, но и названия населенных пунктов',
  `id_region` int(11) NOT NULL,
  `name_ru` varchar(145) NOT NULL,
  `name_en` varchar(145) DEFAULT NULL,
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  `northeast_lat` float(10,6) NOT NULL,
  `northeast_lng` float(10,6) NOT NULL,
  `southwest_lat` float(10,6) NOT NULL,
  `southwest_lng` float(10,6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_city_region_idx` (`id_region`),
  CONSTRAINT `id_city_region` FOREIGN KEY (`id_region`) REFERENCES `region` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `city`
--

LOCK TABLES `city` WRITE;
/*!40000 ALTER TABLE `city` DISABLE KEYS */;
INSERT INTO `city` VALUES (1,3,'Днепропетровск','Dnepropetrovsk',48.464718,35.046185,48.568867,35.242737,48.355728,34.757977),(2,103,'Амстердам','Amsterdam',52.370216,4.895168,52.430950,5.068373,52.318275,4.728856),(3,2,'Уфа','Ufa',54.738762,55.972054,54.955173,56.262959,54.662384,55.778790),(4,65,'Ибадан','Ibadan',7.377535,3.947040,7.557696,4.032841,7.283934,3.775520),(5,4,'Киев','Kiev',50.450100,30.523399,50.590797,30.825941,50.213272,30.239441);
/*!40000 ALTER TABLE `city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mark` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `text` text NOT NULL,
  `active` varchar(1) NOT NULL,
  `createDatatime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_mark` (`id_mark`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `comment_mark_key` FOREIGN KEY (`id_mark`) REFERENCES `mark` (`id`),
  CONSTRAINT `comment_user_key` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,19,356,'123123','Y',1459408862),(2,27,292,'sdfsdf','Y',1459432470),(3,27,292,'fujfgh','Y',1459432472),(4,27,292,'trytrt','Y',1459432474),(5,27,292,'rtyrty','Y',1459432475),(6,27,292,'ertyrt','Y',1459432476);
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_ru` varchar(145) NOT NULL,
  `name_en` varchar(145) DEFAULT NULL,
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_ru` (`name_ru`),
  UNIQUE KEY `name_en` (`name_en`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country` VALUES (1,'Россия','Russia',61.524010,105.318756),(2,'Украина','Ukraine',48.379433,31.165581),(3,'Румыния','Rumyniya',45.943161,24.966761),(4,'Болгария','Bulgaria',42.733883,25.485830),(5,'Куба','Kuba',21.521757,-77.781166),(6,'Индия','India',20.593683,78.962883),(7,'Германия','Germany',51.165691,10.451526),(8,'Литва','Lithuania',55.169437,23.881275),(9,'Соединенные Штаты','United States',37.090240,-95.712891),(10,'Франция','France',46.227638,2.213749),(11,'Мозамбик','Mozambique',-18.665695,35.529564),(12,'Багамские Острова','The Bahamas',25.034281,-77.396278),(13,'Соединенные Штаты Америки','Soedinennye_shtaty_ameriki',37.090240,-95.712891),(14,'Ирландия','Irlandiya',53.412910,-8.243890),(16,'Великобритания','Velikobritaniya',55.378052,-3.435973),(17,'Исландия','Islandiya',65.970703,-18.532694),(18,'Нигерия','Nigeria',9.081999,8.675277),(19,'Молдова','Moldova',47.411633,28.369884),(20,'Соединенное Королевство','United Kingdom',55.378052,-3.435973),(21,'Беларусь','Belarus',53.709808,27.953388),(22,'Южная Африка','South Africa',-30.559483,22.937506),(23,'Австралия','Australia',-25.274399,133.775131),(29,'Нидерланды','Netherlands',52.132633,5.291266),(30,'Польша','Poland',51.919437,19.145136),(31,'Япония','Japan',36.204823,138.252930),(32,'Китай','China',35.861660,104.195396),(33,'Объединенные Арабские Эмираты','United Arab Emirates',23.424076,53.847816),(34,'Турция','Turkey',38.963745,35.243320),(35,'Пакистан','Pakistan',30.375320,69.345116),(50,'Iceland','Iceland',64.963051,-19.020836);
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fields_kind`
--

DROP TABLE IF EXISTS `fields_kind`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fields_kind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_kind` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `value` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_kind_fields` (`id_kind`),
  CONSTRAINT `fields_kind_ibfk_1` FOREIGN KEY (`id_kind`) REFERENCES `kind` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fields_kind`
--

LOCK TABLES `fields_kind` WRITE;
/*!40000 ALTER TABLE `fields_kind` DISABLE KEYS */;
INSERT INTO `fields_kind` VALUES (1,2,'Веб-сайт','http://vkontakte.ru/akom10'),(2,2,'тестовое поле','тестовое значение');
/*!40000 ALTER TABLE `fields_kind` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `icon`
--

DROP TABLE IF EXISTS `icon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `icon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `width` smallint(6) NOT NULL,
  `height` smallint(6) NOT NULL,
  `version` int(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `icon`
--

LOCK TABLES `icon` WRITE;
/*!40000 ALTER TABLE `icon` DISABLE KEYS */;
INSERT INTO `icon` VALUES (-1,'general_kind.png',56,56,1),(1,'dps_icon.png',50,50,1),(2,'Repair cars,platforms.png',50,50,1),(3,'OMS.png',50,50,1),(4,'ponds_icon.jpg',28,30,1),(5,'shops_ponds_icon.jpg',30,20,1);
/*!40000 ALTER TABLE `icon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interests`
--

DROP TABLE IF EXISTS `interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `id_interest_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interests`
--

LOCK TABLES `interests` WRITE;
/*!40000 ALTER TABLE `interests` DISABLE KEYS */;
INSERT INTO `interests` VALUES (1,3,'авто'),(2,3,'спорт'),(5,3,'общение'),(6,3,'программирование');
/*!40000 ALTER TABLE `interests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kind`
--

DROP TABLE IF EXISTS `kind`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_theme` int(11) NOT NULL,
  `id_icon` int(11) NOT NULL,
  `id_type` int(11) DEFAULT NULL,
  `name_ru` varchar(45) NOT NULL,
  `code` varchar(45) NOT NULL COMMENT 'исп в урл',
  `description` varchar(5000) NOT NULL DEFAULT 'Описание отсутствует',
  `lider` varchar(150) DEFAULT NULL,
  `site` varchar(60) DEFAULT NULL,
  `color` varchar(8) NOT NULL DEFAULT '#ff0000',
  PRIMARY KEY (`id`),
  KEY `id_theme_idx` (`id_theme`),
  KEY `id_icon_idx` (`id_icon`),
  KEY `id_type_kind_idx` (`id_type`),
  KEY `id_user_idx` (`id_user`),
  CONSTRAINT `id_icon_kind` FOREIGN KEY (`id_icon`) REFERENCES `icon` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `id_theme_kind` FOREIGN KEY (`id_theme`) REFERENCES `theme` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `id_type_kind` FOREIGN KEY (`id_type`) REFERENCES `type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `id_users_kind` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kind`
--

LOCK TABLES `kind` WRITE;
/*!40000 ALTER TABLE `kind` DISABLE KEYS */;
INSERT INTO `kind` VALUES (-1,3,-1,-1,1,'Общий вид','general','Описание отсутствует',NULL,NULL,'#ff0000'),(1,3,1,1,1,'Пост ДПС','post-dps','Пост дорожно-постовой службы!','Иван Иванов','vk.com','#ff0000'),(2,3,2,2,1,'СТО','tss','Станция технического обслуживания - 5','Вася','tss.dp.ua','#ff0000'),(3,3,3,3,1,'Больница','hospital','Описание отсутствует','test2','test2.com','#ff0000'),(4,176,4,4,2,'Рыбалка','fishing','Водоемы для рыбалки. Реки, заливы, озера. Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы,озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.Водоемы для рыбалки. Реки, заливы, озера.','lider3','test34.com','#ff0000'),(5,3,4,5,1,'Магазины для рыбаков','shops-for-fishing','Описание отсутствует','test4','test4.com','#ff0000');
/*!40000 ALTER TABLE `kind` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mark`
--

DROP TABLE IF EXISTS `mark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_kind` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `description` varchar(5000) DEFAULT 'Описание отсутствует',
  `address` varchar(255) DEFAULT NULL,
  `createDatatime` int(11) NOT NULL DEFAULT '0',
  `active` varchar(1) DEFAULT NULL,
  `anonymous` varchar(1) NOT NULL DEFAULT 'n',
  `click_spam` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `active_balloon` varchar(1) NOT NULL DEFAULT 'n',
  `period` int(5) NOT NULL DEFAULT '0',
  `color` varchar(8) NOT NULL DEFAULT '#ff0000',
  PRIMARY KEY (`id`),
  KEY `id_kind_idx` (`id_kind`),
  KEY `id_user_idx` (`id_user`),
  CONSTRAINT `id_kind_mark` FOREIGN KEY (`id_kind`) REFERENCES `kind` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `id_user_mark` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mark`
--

LOCK TABLES `mark` WRITE;
/*!40000 ALTER TABLE `mark` DISABLE KEYS */;
INSERT INTO `mark` VALUES (1,-1,275,'Описание отсутствует','вулиця Петра Калнишевського 12',1459175133,'Y','n',0,2,'n',0,'#ff0000'),(2,3,182,'Описание отсутствует','улица Николая Ковалева',1459405217,'Y','n',0,2,'n',0,'#ff0000'),(3,2,182,'Описание отсутствует','Хадии Давлетшиной бульвар 32',1459405321,'Y','n',0,3,'n',0,'#ff0000'),(4,3,182,'Описание отсутствует','Уфимское шоссе 17',1459405408,'Y','n',0,2,'n',0,'#ff0000'),(5,3,182,'Описание отсутствует','улица Султанова 47',1459405440,'Y','n',0,1,'n',0,'#ff0000'),(6,3,182,'Описание отсутствует','улица Ангарская 13А',1459405466,'Y','n',0,1,'n',0,'#ff0000'),(7,3,182,'Описание отсутствует',NULL,1459405494,'Y','n',0,0,'n',0,'#ff0000'),(8,3,182,'Описание отсутствует','улица Богдана Хмельницкого 143/1',1459405513,'Y','n',0,1,'n',0,'#ff0000'),(9,3,182,'Описание отсутствует',NULL,1459405529,'Y','n',0,0,'n',0,'#ff0000'),(10,3,182,'Описание отсутствует','улица Рихарда Зорге 73',1459405575,'Y','n',0,2,'n',0,'#ff0000'),(11,3,182,'Описание отсутствует','Соединительное шоссе 2',1459405588,'Y','n',0,1,'n',0,'#ff0000'),(12,3,182,'Описание отсутствует','улица Парковая 18А',1459405603,'Y','n',0,1,'n',0,'#ff0000'),(13,3,182,'Описание отсутствует','Болотная улица 38',1459405616,'Y','n',0,1,'n',0,'#ff0000'),(14,3,182,'Описание отсутствует','улица Фурманова 59А',1459405629,'Y','n',0,1,'n',0,'#ff0000'),(15,3,182,'Описание отсутствует','улица Трамвайная 272',1459405642,'Y','n',0,1,'n',0,'#ff0000'),(16,3,182,'Описание отсутствует','автодорога Волга',1459405655,'Y','n',0,1,'n',0,'#ff0000'),(17,3,182,'Описание отсутствует','улица Цветочная 2/1',1459405667,'Y','n',0,5,'n',0,'#ff0000'),(18,3,182,'Описание отсутствует','Лесозаводская улица 12',1459405682,'Y','n',0,1,'n',0,'#ff0000'),(19,3,182,'Описание отсутствует','улица Кольцевая 197',1459405696,'Y','n',0,3,'n',0,'#ff0000'),(20,3,182,'Описание отсутствует','вулиця Софії Ковалевської 59А',1459405959,'Y','n',0,4,'n',0,'#ff0000'),(21,4,357,'Описание отсутствует','улица Мингажева 102',1459409271,'Y','n',0,0,'n',0,'#ff0000'),(22,4,357,'Описание отсутствует','улица Мингажева 102',1459409424,'Y','n',0,0,'n',0,'#ff0000'),(23,1,357,'Описание отсутствует','улица Мингажева 102',1459409448,'Y','n',0,0,'n',0,'#ff0000'),(24,2,356,'123455','улица Маршала Жукова 45',1459410395,'Y','n',0,3,'n',0,'#ff0000'),(25,5,292,'Описание отсутствует','вулиця Березинська 80',1459431999,'Y','n',0,1,'n',0,'#ff0000'),(26,2,292,'Описание отсутствует',NULL,1459432028,'Y','n',0,0,'n',0,'#ff0000'),(27,1,292,'Описание отсутствует','вулиця Павла Нірінберга 4-6',1459432047,'Y','n',0,4,'n',0,'#ff0000'),(28,2,356,'Описание отсутствует','улица Парковая 22',1459495257,'Y','n',0,1,'n',0,'#ff0000'),(29,2,356,'Описание отсутствует','проспект Салавата Юлаева 32',1459495274,'Y','n',0,1,'n',0,'#ff0000'),(30,2,356,'Описание отсутствует','Перевалочная улица 63а',1459495451,'Y','n',0,1,'n',0,'#ff0000'),(31,2,356,'Описание отсутствует','улица Образцовая 2',1459495470,'Y','n',0,1,'n',0,'#ff0000'),(32,2,356,'Описание отсутствует','Набережная',1459495484,'Y','n',0,1,'n',0,'#ff0000'),(33,2,356,'Описание отсутствует','улица Юбилейная 1/2',1459495497,'Y','n',0,1,'n',0,'#ff0000'),(34,2,356,'Описание отсутствует','проспект Салавата Юлаева 55',1459495510,'Y','n',0,1,'n',0,'#ff0000'),(35,2,356,'Описание отсутствует','улица Блюхера 15/4',1459495523,'Y','n',0,1,'n',0,'#ff0000'),(36,2,356,'Описание отсутствует','улица Степана Кувыкина 100/1',1459495538,'Y','n',0,1,'n',0,'#ff0000'),(37,2,356,'Описание отсутствует','Боровая улица 14 корпус 1',1459495551,'Y','n',0,1,'n',0,'#ff0000'),(38,2,356,'Описание отсутствует','Дёмское шоссе',1459495566,'Y','n',0,1,'n',0,'#ff0000'),(39,2,356,'Описание отсутствует','Бакалинская улица 10',1459495584,'Y','n',0,1,'n',0,'#ff0000'),(40,2,356,'Описание отсутствует','автодорога Волга',1459495597,'Y','n',0,1,'n',0,'#ff0000');
/*!40000 ALTER TABLE `mark` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mark_city`
--

DROP TABLE IF EXISTS `mark_city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mark_city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mark` int(11) NOT NULL,
  `id_city` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mark_idx` (`id_mark`),
  KEY `id_city_idx` (`id_city`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mark_city`
--

LOCK TABLES `mark_city` WRITE;
/*!40000 ALTER TABLE `mark_city` DISABLE KEYS */;
INSERT INTO `mark_city` VALUES (1,1,1),(2,2,3),(3,3,3),(4,4,3),(5,5,3),(6,6,3),(7,8,3),(8,10,3),(9,11,3),(10,12,3),(11,13,3),(12,14,3),(13,15,3),(14,16,3),(15,17,3),(16,18,3),(17,19,3),(18,20,1),(19,21,3),(20,22,3),(21,23,3),(23,24,3),(24,25,1),(25,27,1),(26,28,3),(27,29,3),(28,30,3),(29,31,3),(30,32,3),(31,33,3),(32,34,3),(33,35,3),(34,36,3),(35,37,3),(36,38,3),(37,39,3),(38,40,3);
/*!40000 ALTER TABLE `mark_city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mark_country`
--

DROP TABLE IF EXISTS `mark_country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mark_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mark` int(11) NOT NULL,
  `id_country` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mark_idx` (`id_mark`),
  KEY `id_country_idx` (`id_country`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mark_country`
--

LOCK TABLES `mark_country` WRITE;
/*!40000 ALTER TABLE `mark_country` DISABLE KEYS */;
INSERT INTO `mark_country` VALUES (1,1,2),(2,2,1),(3,3,1),(4,4,1),(5,5,1),(6,6,1),(7,8,1),(8,10,1),(9,11,1),(10,12,1),(11,13,1),(12,14,1),(13,15,1),(14,16,1),(15,17,1),(16,18,1),(17,19,1),(18,20,2),(19,21,1),(20,22,1),(21,23,1),(23,24,1),(24,25,2),(25,27,2),(26,28,1),(27,29,1),(28,30,1),(29,31,1),(30,32,1),(31,33,1),(32,34,1),(33,35,1),(34,36,1),(35,37,1),(36,38,1),(37,39,1),(38,40,1);
/*!40000 ALTER TABLE `mark_country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mark_region`
--

DROP TABLE IF EXISTS `mark_region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mark_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mark` int(11) NOT NULL,
  `id_region` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mark_idx` (`id_mark`),
  KEY `id_region_idx` (`id_region`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mark_region`
--

LOCK TABLES `mark_region` WRITE;
/*!40000 ALTER TABLE `mark_region` DISABLE KEYS */;
INSERT INTO `mark_region` VALUES (1,1,3),(2,2,2),(3,3,2),(4,4,2),(5,5,2),(6,6,2),(7,8,2),(8,10,2),(9,11,2),(10,12,2),(11,13,2),(12,14,2),(13,15,2),(14,16,2),(15,17,2),(16,18,2),(17,19,2),(18,20,3),(19,21,2),(20,22,2),(21,23,2),(23,24,2),(24,25,3),(25,27,3),(26,28,2),(27,29,2),(28,30,2),(29,31,2),(30,32,2),(31,33,2),(32,34,2),(33,35,2),(34,36,2),(35,37,2),(36,38,2),(37,39,2),(38,40,2);
/*!40000 ALTER TABLE `mark_region` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `id_city` int(11) NOT NULL,
  `content` text NOT NULL,
  `date_create` int(15) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,356,2,'111',1459408093),(2,356,2,'123123',1459408105),(3,356,2,'11',1459408460),(4,357,2,'rrr',4294967295);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photo`
--

DROP TABLE IF EXISTS `photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mark` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_mark_photo_idx` (`id_mark`),
  CONSTRAINT `id_mark_photo` FOREIGN KEY (`id_mark`) REFERENCES `mark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photo`
--

LOCK TABLES `photo` WRITE;
/*!40000 ALTER TABLE `photo` DISABLE KEYS */;
INSERT INTO `photo` VALUES (1,21,'photo_1459409279.jpg',0),(2,21,'photo_1459409285.jpg',1),(3,22,'photo_1459409429.jpg',0),(4,23,'photo_1459409453.jpg',0);
/*!40000 ALTER TABLE `photo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `point`
--

DROP TABLE IF EXISTS `point`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mark` int(11) NOT NULL,
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  `order` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mark_idx` (`id_mark`),
  CONSTRAINT `id_mark_point` FOREIGN KEY (`id_mark`) REFERENCES `mark` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `point`
--

LOCK TABLES `point` WRITE;
/*!40000 ALTER TABLE `point` DISABLE KEYS */;
INSERT INTO `point` VALUES (1,1,48.512390,35.085159,0),(2,2,54.752590,56.011261,0),(3,3,54.733124,55.996326,0),(4,4,54.792583,56.063446,0),(5,5,54.732494,55.927067,0),(6,6,54.701920,55.991966,0),(7,7,54.773930,56.095718,0),(8,8,54.820347,56.110825,0),(9,9,54.750107,55.875305,0),(10,10,54.771946,56.016068,0),(11,11,54.857204,56.095718,0),(12,12,54.763607,55.993408,0),(13,13,54.735806,55.913071,0),(14,14,54.804089,56.156830,0),(15,15,54.798138,56.078552,0),(16,16,54.754078,55.970749,0),(17,17,54.806866,56.056580,0),(18,18,54.742161,55.905518,0),(19,19,54.814003,56.128677,0),(20,20,48.515926,35.058723,0),(21,21,54.727970,55.967712,0),(22,22,54.727848,55.967857,0),(23,23,54.727867,55.967796,0),(25,24,54.782265,56.073402,0),(26,25,48.535652,35.018211,0),(27,26,48.564373,35.048424,0),(28,27,48.467926,35.055664,0),(29,28,54.769363,55.993408,0),(30,29,54.725872,55.991348,0),(31,30,54.732628,55.902771,0),(32,31,54.773731,55.952209,0),(33,32,54.760036,56.081985,0),(34,33,54.842545,56.077866,0),(35,34,54.724678,55.987228,0),(36,35,54.788219,56.024994,0),(37,36,54.713150,56.003021,0),(38,37,54.804485,56.174683,0),(39,38,54.696449,55.893158,0),(40,39,54.717724,55.987572,0),(41,40,54.775120,55.947403,0);
/*!40000 ALTER TABLE `point` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `private_message`
--

DROP TABLE IF EXISTS `private_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `private_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `from_id` int(10) NOT NULL,
  `to_id` int(10) NOT NULL,
  `text` text NOT NULL,
  `date_create` int(11) NOT NULL,
  `status` enum('new','read') NOT NULL DEFAULT 'new',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `private_message`
--

LOCK TABLES `private_message` WRITE;
/*!40000 ALTER TABLE `private_message` DISABLE KEYS */;
INSERT INTO `private_message` VALUES (1,356,357,'Привет',1459408587,'read'),(2,357,356,'hello',2147483647,'read'),(3,356,357,'kak dela',1459409785,'read'),(4,357,356,'<img src=\"http://map.taxiavenue.com/img/emoji/D83CDF6A.png\" emoji=\"D83CDF6A\">',2147483647,'read'),(5,356,357,'jjj',1459409952,'read'),(6,356,357,'111',1459410014,'read'),(7,357,356,'ttt',2147483647,'new');
/*!40000 ALTER TABLE `private_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `region`
--

DROP TABLE IF EXISTS `region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_country` int(11) NOT NULL,
  `name_ru` varchar(145) NOT NULL,
  `name_en` varchar(145) DEFAULT NULL,
  `for_capital_city` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lat` float(10,6) NOT NULL,
  `lng` float(10,6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_region_country_idx` (`id_country`),
  CONSTRAINT `id_region_country` FOREIGN KEY (`id_country`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `region`
--

LOCK TABLES `region` WRITE;
/*!40000 ALTER TABLE `region` DISABLE KEYS */;
INSERT INTO `region` VALUES (2,1,'Республика Башкортостан','Respublika_bashkortostan',0,54.231216,56.164528),(3,2,'Днепропетровская Область','Dnipropetrovsk Oblast',0,48.464718,35.046185),(4,2,'город Киев','Gorod_kiev',0,50.450100,30.523399),(5,3,'Брашов','Brashov',0,45.666668,25.616667),(6,1,'город Москва','Gorod_moskva',0,55.755825,37.617298),(7,2,'Харьковская область','Harkovskaya_oblast',0,49.993500,36.230385),(8,4,'Варна','Varna',0,43.214050,27.914734),(9,1,'Volgogradskaya oblast\'','Volgogradskaya oblast\'',0,49.760452,45.000000),(10,5,'Камагуэй','Kamaguey',0,21.392603,-77.905319),(11,2,'Донецкая Область','Donetsk Oblast',0,48.015884,37.802849),(12,2,'Львовская Область','Lviv Oblast',0,49.839684,24.029716),(13,1,'Республика Башкортостан','Republic of Bashkortostan',0,54.231216,56.164528),(14,1,'Московская область','Moskovskaya_oblast',0,55.340397,38.291763),(15,6,'Тамил Наду','Tamil Nadu',0,11.127123,78.656891),(16,7,'Захс','Sachsen',0,51.104542,13.201738),(17,1,'Республика Башкортостан','Respublika Bashkortostan',0,54.231216,56.164528),(18,1,'Рязанская область','Ryazanskaya oblast\'',0,54.387596,41.259567),(19,1,'Амурская область','Amurskaya_oblast',0,54.603508,127.480171),(20,1,'Москва','Moscow',0,55.755825,37.617298),(21,8,'Клайпедский Уезд','Klaipėda County',0,55.668697,21.424137),(22,2,'Киевская город','Kyiv city',0,50.450100,30.523399),(23,9,'Нью-Йорк','New York',0,40.712784,-74.005943),(24,2,'Кировоградская область','Kirovogradskaya_oblast',0,48.507935,32.262318),(25,2,'Житомирская область','Zhitomirskaya_oblast',0,50.254650,28.658667),(26,2,'Харьковская Обл.','Kharkiv Oblast',0,49.993500,36.230385),(27,10,'Иль-де-Франс','Île-de-France',0,48.849918,2.637041),(28,2,'Запорожская область','Zaporozhskaya_oblast',0,47.838799,35.139568),(29,2,'Волынская область','Volynskaya_oblast',0,50.747234,25.325382),(30,7,'Бавария','Bavaria',0,48.790447,11.497889),(31,9,'Алабама','Alabama',0,32.318230,-86.902298),(32,1,'Красноярский край','Krasnoyarskiy kray',0,64.247978,95.110420),(33,2,'Луганская область','Luganskaya_oblast',0,48.574039,39.307816),(34,11,'Софала','Sofala',0,-33.081970,149.696045),(35,7,'Северный Рейн-Вестфалия','Nordrhein-Westfalen',0,51.433235,7.661594),(36,1,'Кировская Область','Kirov Oblast',0,58.419853,50.209724),(37,1,'Нижегородская Область','Nizhny Novgorod Oblast',0,55.799515,44.029678),(38,2,'Киевская область','Kyivs\'ka oblast',0,50.052952,30.766712),(39,1,'Белгородская область','Belgorodskaya oblast\'',0,50.710693,37.753338),(40,2,'Одесская Область','Odessa Oblast',0,46.484585,30.732599),(41,1,'Краснодарский край','Krasnodarskiy kray',0,45.641529,39.705597),(42,2,'Mykolaivs кабыл области','Mykolaivs\'ka oblast',0,46.975033,31.994583),(43,12,'Северный Андрос','North Andros',0,24.706381,-78.019539),(44,1,'Кабардино-Балкарии','Kabardino-Balkaria',0,43.393246,43.562851),(45,1,'Кабардино-Балкарская Республика','Kabardino-balkarskaya_respublika',0,43.393246,43.562851),(46,1,'Московская область','Moskovskaya oblast\'',0,55.340397,38.291763),(47,9,'Луизиана','Louisiana',0,30.984299,-91.962334),(48,10,'Лорейн','Lorraine',0,48.874424,6.208093),(49,1,'Ставропольский край','Stavropolskiy kray',0,44.668098,43.520214),(50,9,'Округ Колумбия','District of Columbia',0,38.907192,-77.036873),(51,14,'Дублин','Dublin',0,53.349804,-6.260310),(52,13,'Нью-Джерси','Nyu-dzhersi',0,40.058323,-74.405663),(53,13,'Вирджиния','Virdzhiniya',0,37.431572,-78.656891),(54,13,'Мэриленд','Merilend',0,39.045753,-76.641273),(55,16,'Англия','Angliya',0,52.355518,-1.174320),(56,1,'Ростовская область','Rostovskaya oblast\'',0,47.685326,41.825893),(57,13,'Арканзас','Arkanzas',0,35.201050,-91.831833),(58,1,'Санкт-Петербург','Saint Petersburg',0,27.773056,-82.639999),(59,9,'Калифорния','California',0,36.778259,-119.417931),(60,9,'Вашингтон','Washington',0,38.907192,-77.036873),(61,17,'Нордюрланд-Эйстра','Nordyurland-eystra',0,65.970703,-18.532694),(62,1,'Тамбовская Область','Tambov Oblast',0,52.641659,41.421646),(63,1,'Самарская Область','Samara Oblast',0,53.418385,50.472553),(64,9,'Аризона','Arizona',0,34.048927,-111.093735),(65,18,'Ойо','Oyo',0,7.842958,3.936844),(66,9,'Вермонт','Vermont',0,44.558804,-72.577843),(67,9,'Канзас','Kansas',0,39.011902,-98.484245),(68,9,'Иллинойс','Illinois',0,40.633125,-89.398529),(69,1,'Свердловская область','Sverdlovskaya oblast\'',0,59.007736,61.931622),(70,1,'Тамбовская область','Tambovskaya oblast\'',0,52.641659,41.421646),(71,1,'Республика Адыгея','Respublika Adygeya',0,44.822914,40.175446),(72,1,'В Хабаровском крае','Khabarovskiy kray',0,50.588844,135.000000),(73,1,'Липецкая Область','Lipetsk Oblast',0,52.526470,39.203228),(74,1,'Ханты-Мансийский автономный округ','Khanty-Mansiyskiy avtonomnyy okrug',0,62.228706,70.641006),(75,1,'Волгоградская область','Volgogradskaya_oblast',0,49.760452,45.000000),(76,2,'Черкас-ул. областная','Cherkas\'ka oblast',0,49.444431,32.059769),(77,2,'Ул. Полтавская обл.','Poltavs\'ka oblast',0,49.588268,34.551418),(78,1,'Ростовская Область','Rostov Oblast',0,47.685326,41.825893),(79,9,'Оклахома','Oklahoma',0,35.467560,-97.516426),(80,1,'Приморский край','Primorskiy kray',0,45.052563,135.000000),(81,2,'Zhytomyrs кабыл области','Zhytomyrs\'ka oblast',0,50.254650,28.658667),(82,1,'Чеченская','Чеченская',0,43.221165,44.751709),(83,1,'Курская Область','Kursk Oblast',0,51.763405,35.381180),(84,20,'Англия','England',0,52.355518,-1.174320),(85,1,'Московская Область','Moscow Oblast',0,55.340397,38.291763),(86,1,'Челябинская Область','Chelyabinsk Oblast',0,54.431942,60.878895),(87,10,'Рона-Альпы','Rhône-Alpes',0,45.169579,5.450282),(88,2,'Закарпатской обл.','Zakarpats\'ka oblast',0,48.620800,22.287884),(89,1,'Калининградская Область','Kaliningrad Oblast',0,54.823528,21.481615),(90,10,'Бордовый','Burgundy',0,47.052505,4.383722),(91,6,'Уттар-Прадеш','Uttar Pradesh',0,26.846708,80.946159),(92,2,'Ивано-Франковск ул. областная','Ivano-Frankivs\'ka oblast',0,48.922634,24.711117),(93,1,'Воронежская Обл.','Voronezh Oblast',0,50.858971,39.864437),(94,21,'Гродненская область','Grodnenskaya_oblast',0,53.659996,25.344856),(95,2,'Полтавская область','Poltavskaya_oblast',0,49.588268,34.551418),(96,2,'Закарпатская область','Zakarpatskaya_oblast',0,48.620800,22.287884),(97,1,'Оренбургская область','Orenburgskaya_oblast',0,51.763405,54.618820),(98,2,'Николаевская область','Nikolaevskaya_oblast',0,46.975033,31.994583),(99,2,'Запорізька область','Zaporiz\'ka oblast',0,47.838799,35.139568),(100,1,'Тверская область','Tverskaya oblast\'',0,57.002167,33.985313),(101,22,'Восточный Кейп','Eastern Cape',0,-32.296841,26.419390),(102,23,'Западная Австралия','Western Australia',0,-27.672817,121.628311),(103,29,'Северная Голландия','North Holland',0,52.520588,4.788474),(104,30,'Województwo нижнесилезское','Województwo dolnośląskie',0,51.133987,16.884195),(105,31,'Аичи-Кен','Aichi-ken',0,35.180187,136.906570),(106,31,'Токио-в','Tōkyō-to',0,35.689487,139.691711),(107,31,'Хего-кэн','Hyōgo-ken',0,34.691269,135.183075),(108,32,'Синьцзян','Xinjiang',0,43.793026,87.627701),(109,33,'Дубай','Dubai',0,25.204849,55.270782),(110,34,'Анкара','Ankara',0,39.933365,32.859741),(111,35,'Пенджаб','Punjab',0,31.147129,75.341217),(112,31,'Осака-фу','Ōsaka-fu',0,34.686298,135.519669),(113,17,'Северо-восток','Northeast',0,38.909760,-76.975830),(114,50,'Северо-восток','Northeast',0,38.909760,-76.975830);
/*!40000 ALTER TABLE `region` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `id` char(32) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
INSERT INTO `session` VALUES ('jri53m755pg95ktf82820a1pb2',1464278356,''),('pjkj3ejf1brbaehkdf7mo77k12',1464279303,'');
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_user`
--

DROP TABLE IF EXISTS `status_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `createDatatime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_user_idx` (`id_user`),
  CONSTRAINT `id_user_status` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_user`
--

LOCK TABLES `status_user` WRITE;
/*!40000 ALTER TABLE `status_user` DISABLE KEYS */;
INSERT INTO `status_user` VALUES (1,3,'asdasdasd','2014-10-21 07:37:00'),(5,118,'30','2014-10-21 07:41:22'),(6,247,'Проч','2015-03-03 16:39:53');
/*!40000 ALTER TABLE `status_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theme`
--

DROP TABLE IF EXISTS `theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme`
--

LOCK TABLES `theme` WRITE;
/*!40000 ALTER TABLE `theme` DISABLE KEYS */;
INSERT INTO `theme` VALUES (-1,'Общая тема'),(1,'ГАИ'),(2,'Авто'),(3,'Здравоохранение'),(4,'Отдых'),(5,'Образование');
/*!40000 ALTER TABLE `theme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type`
--

DROP TABLE IF EXISTS `type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_ru` varchar(100) NOT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type`
--

LOCK TABLES `type` WRITE;
/*!40000 ALTER TABLE `type` DISABLE KEYS */;
INSERT INTO `type` VALUES (1,'Точка','Point'),(2,'Ломаная','LineString');
/*!40000 ALTER TABLE `type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_avatar` int(11) NOT NULL DEFAULT '1',
  `login` varchar(45) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `family` varchar(155) DEFAULT NULL,
  `sex` varchar(7) DEFAULT NULL,
  `age` int(11) NOT NULL,
  `about` varchar(500) NOT NULL,
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `soc_register` varchar(45) DEFAULT NULL,
  `soc_id` varchar(22) DEFAULT NULL COMMENT 'id в соцсети',
  `telephone` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `city` varchar(80) NOT NULL,
  `online` int(11) NOT NULL DEFAULT '0',
  `active` varchar(1) NOT NULL DEFAULT 'n',
  `confirm_date` varchar(20) NOT NULL,
  `confirm_code` varchar(50) NOT NULL,
  `anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL,
  `last_city_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_avatar_idx` (`id_avatar`)
) ENGINE=InnoDB AUTO_INCREMENT=359 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,2,'Vitek25','$1$Aolxow3K$YlnUWYl4gsU7X5EY9GEH20','dfsfgfdgsdsdsdss','Карла Маasdasd','m',123,'sadasdasd','2014-07-17 13:12:54',NULL,NULL,'sdfsdfdfgdfg','asdasdasd@asdasd.sd','sadasdasdasd',0,'n','0000-00-00 00:00:00','',0,'asdasdasd',0),(4,1,'root','vjzrbwmrf*3','Петр',NULL,NULL,0,'','2014-08-14 11:51:48',NULL,NULL,'','ivan@mail.ru','',0,'n','0000-00-00 00:00:00','',0,'',0),(5,1,'test','$1$e/6lagRY$wMDsyYIj4sgSpPkuRxarY1','Ольга',NULL,NULL,0,'','2014-08-14 12:24:38',NULL,NULL,'','olga@mail.ru','',1,'n','1420548131','Волк',0,'',0),(7,1,'test2','$1$hJsuCE3y$6qqkCn8tdjqLawNn9SCNR0','test2',NULL,NULL,0,'','2014-08-14 12:54:59',NULL,NULL,'','','',0,'n','0000-00-00 00:00:00','',0,'',0),(77,1,'+380502276956','$1$DwAj/pOU$uRg1VB2XrdwhHLbtcuwQy1','+380502276956',NULL,NULL,0,'','2014-09-05 08:11:34',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(78,1,'ivan','$1$5jsbFXnI$JchD5.CNoxQPESLYN0dDe0','ivan',NULL,NULL,0,'','2014-09-08 08:44:40',NULL,NULL,'','','',0,'n','1410165880','Волк',0,'',0),(81,1,'lafat.88@gmail.com','$1$sPb/k.2O$Tsqy.19bZKfzPRbA3LKh./','Малый Гавнюк','Гавнюкович',NULL,0,'','2014-09-08 11:46:13',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(82,1,'pm@digitalpromo.com.ua','$1$zt1/xzNl$9WcY2vbkCgryE9Uya.jOm1','Дмитрий','Щур','М',29,'Все хорошо!','2014-09-08 11:48:35',NULL,NULL,'+380934595241','pm@digitalpromo.com.ua','Днепропетровск',1,'y','0000-00-00 00:00:00','',0,'',0),(97,1,'buteyc@mail.ru','$1$Gfi8wTZY$2sQB9.P670vNEYt3ulFX/1','buteyc@mail.ru',NULL,NULL,0,'','2014-09-09 12:20:18',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(102,1,'[object Object]','$1$D36Zk2LV$cwQK3bCutHYYQxMvzO0Jh0','[object Object]',NULL,NULL,0,'','2014-09-26 09:48:19',NULL,NULL,'','','',1,'n','1411724899','Жираф',0,'',0),(103,1,'sdfsdfsdf','$1$7iWa8y5y$onTh9emVeYwMH5K.YLy1q.','sdfsdfsdf',NULL,NULL,0,'','2014-09-26 11:48:21',NULL,NULL,'','','',0,'n','1411732101','Кенгуру',0,'',0),(104,1,'ssss','$1$gXhnRnzc$3Tl02MOsLZNx0LAGru44l0','ssss',NULL,NULL,0,'','2014-09-26 11:50:15',NULL,NULL,'','','',0,'n','1411732215','Кенгуру',0,'',0),(105,1,'sdf','$1$s0SskyRY$kZSut2vO/YlBdGFFz9W.G1','sdf',NULL,NULL,0,'','2014-09-26 11:51:05',NULL,NULL,'','','',0,'n','1411732265','Рыба',0,'',0),(106,1,'sdsdfsdf','$1$RLL0cM.A$s4e/Txsb.bljSfObQyFKg1','sdsdfsdf',NULL,NULL,0,'','2014-09-26 11:55:54',NULL,NULL,'','','',0,'n','1411732554','Жираф',0,'',0),(107,1,'sdfs','$1$OEJvAqnj$OjHAMJn5cfkjnPE5giRe1/','sdfs',NULL,NULL,0,'','2014-09-26 11:56:19',NULL,NULL,'','','',0,'n','1411732579','Кенгуру',0,'',0),(108,1,'s','$1$bfZN9iRY$pOIMIUCmP/jeItJkU9Jyg/','s',NULL,NULL,0,'','2014-09-26 11:56:43',NULL,NULL,'','','',0,'n','1411745923','Слон',0,'',0),(109,1,'sdfsf','$1$dUlwtGGL$ew8Lpxdv0N2DYGYeqFtuo/','sdfsf',NULL,NULL,0,'','2014-09-26 12:04:39',NULL,NULL,'','','',0,'n','1411733079','Кенгуру',0,'',0),(115,1,'+380934595241','$1$n6SECZAL$Qr6LU3iA8v17wzCXXIqBQ/','+380934595241',NULL,NULL,0,'','2014-09-26 14:07:23',NULL,NULL,'','','',0,'n','1411740443','Слон',0,'',0),(116,1,'Shcerbinin1a@gmail.com','$1$R8IUxTzA$Ifi41rUPOHvhwBFih6kob.','Shcerbinin1a@gmail.com',NULL,NULL,0,'','2014-09-29 07:42:27',NULL,NULL,'','','',0,'n','1411976547','Кенгуру',0,'',0),(117,29,'viktorshatalov','$1$TH1VRwhC$2IzCCYVA7esfMl6B.8DWc.','Viktor','Shatalov','м',39,'','2014-09-30 08:48:51','7866623',NULL,'','','Николаев',1,'y','','',0,'',0),(118,30,'tttttttttttt','$1$RALW55YT$dbIrxG5JDFndpSLRyVgfA1','#уваа','4сааа','м',41,'Тесталлвлвлвлвлвовллчвд адалал вллвво влвлв влвлвлвл влвлвл','2014-09-30 10:15:34','12365478',NULL,'','','Днепропетровск',0,'y','','',0,'3077373',0),(140,1,'dima@asd.asd','$1$V.2CbmCK$YbxMqtMbaEKPKAolYLhnN/','dima@asd.asd',NULL,NULL,0,'','2014-10-01 09:57:44',NULL,NULL,'','','',0,'n','1412157464','Слон',0,'',0),(176,44,'ivan@i.ua','$1$Oo00PP/g$DHjXKXeT707QP8m2fbVf11','Иванe33j','Ковальчук','муж',28,'Что-то о себе Что-то о себе Что-то о себе Что-то о себе Что-то о себе Что-то о себе Что-то о себе','2014-10-01 14:26:49',NULL,NULL,'333-22-55','ivan@i.ua','Днепропетровск',1,'y','1426255956','Рыба',0,'На работе',0),(182,56,'archic2@mail.ru','$1$zFn5buWe$b6ZC4H346BIvgvxf.34x71','Артур','Камильянов','м',33,'тест2','2014-10-21 17:56:13',NULL,NULL,'+79276374844','archic2@mail.ru','Сатурн',0,'y','0000-00-00 00:00:00','',0,'спать охота',0),(183,45,'petr@i.ua','$1$FfOr5sH.$SrIos2tLZzO7gVfcdZGUO/','Петр','','',99,'','2014-11-06 10:08:25',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(184,1,'solut@i.ua','$1$5jiJeB9a$v/UG8VfeZYMqdDyTIN5JH0','solut@i.ua',NULL,NULL,0,'','2014-12-09 09:35:29',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',0),(185,1,'123456','$1$gB2OuaLG$tFC9yCqntN5nP6Lz0iIU4.','123456',NULL,NULL,0,'','2015-01-06 13:40:45',NULL,NULL,'','','',0,'n','1420548045','Заяц',0,'',0),(186,1,'050123456','$1$5HPoNnqJ$IKOrgOHZXvQ2e.wzc1HiL1','050123456',NULL,NULL,0,'','2015-01-06 13:41:09',NULL,NULL,'','','',0,'n','1420548069','Жираф',0,'',0),(187,1,'test1','$1$qn0Dbo6T$byOOr/iVfnCmYKlcxyBx00','test1',NULL,NULL,0,'','2015-01-06 13:43:24',NULL,NULL,'','','',0,'n','1420548204','Слон',0,'',0),(190,46,'dima14480@mail.ru','$1$Chv/fs0G$Y7KFvL9eOReCLQrumGGs4.','Анан','Ананасов','',0,'','2015-01-06 13:46:35',NULL,NULL,'','','1',1,'y','0000-00-00 00:00:00','',0,'',0),(191,1,'п','$1$XJ93USbu$lsWXfFfRIfR3q/QCT5GK51','п',NULL,NULL,0,'','2015-01-06 14:01:54',NULL,NULL,'','','',0,'n','1420549314','Кенгуру',0,'',0),(194,49,'!3b![','$1$18xeUq5a$AoFrWhKB3HmbOshvrm.yT/','!3b![','!3b![','!3b![',0,'!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![!3b![','2015-01-06 15:33:00',NULL,NULL,'!3b![','!3b![','!3b![',0,'y','1421161840','Волк',0,'Все красиво когда у папы ксива',0),(195,1,'\\ым','$1$Fmu.fvLW$XOjDFlF4NV1VRRJdV/Ljw/','\\ым',NULL,NULL,0,'','2015-01-06 16:53:41',NULL,NULL,'','','',0,'n','1420559621','Кенгуру',0,'',0),(197,1,'0982953408','$1$.N21qi/v$otlxWbriJXDFKcKI4wU980','0982953408',NULL,NULL,0,'','2015-01-08 08:36:52',NULL,NULL,'','','',0,'n','1421237934','Рыба',0,'',0),(198,1,'123123546','$1$9.ysyMqg$QmFML0P32SpJhOKzBtdrn.','123123546',NULL,NULL,0,'','2015-01-08 08:37:41',NULL,NULL,'','','',0,'n','1420702661','Слон',0,'',0),(199,47,'Пингвин','$1$UHLgOa9x$oJ4GozGrEHpUXJKs.tTdX1','Иван','Дорн','',0,'Бла-бла-бла','2015-01-08 10:55:53',NULL,NULL,'','dorn2016@mail.ua','',0,'y','0000-00-00 00:00:00','',0,'',0),(200,48,'dorn_2016@mail.ua','$1$rWzus35x$BUxD38I.AJNmkw6H3ya3L/','Петя','Собачник','',0,'','2015-01-08 11:04:34',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(202,1,'andrey_privet@mail.ua','$1$cO4KPm57$G2CSpEEyILrmJzMm/61QN.','andrey_privet@mail.ua',NULL,NULL,0,'','2015-01-09 10:48:46',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(208,1,'77736','social','Алексей','Дьяков','m',0,'','2015-01-09 11:53:51','vkontakte','11120092','','','Богуслав',1,'y','','',0,'',0),(210,54,'Виктор','social','Виктор','Константинов','m',0,'Витюшка','2015-01-13 14:43:00','vkontakte','219556745','Витюшка','','',0,'y','','',0,'Витюшка',0),(211,1,'Viktor','social','Viktor','Pustovalov','m',0,'Viktor','2015-01-13 14:52:59','facebook','420203304801782','Viktor','viktor.pustovalov92@gmail.com','',1,'y','','',0,'Viktor',0),(212,50,'тест','$1$ajXtIKmW$N2aQ6/f3usMLW9hIoQlqv1','тест','тест','тест',0,'тест','2015-01-13 16:15:40',NULL,NULL,'тест','тест','тест',0,'y','1421164055','Жираф',0,'тест',0),(213,1,'0638955009','$1$17hgrzAG$HZNcZ7BHimO543gJ85uQd1','0638955009',NULL,NULL,0,'','2015-01-14 13:37:55',NULL,NULL,'','','',0,'n','1421239075','Жираф',0,'',0),(214,51,'tyler7@mail.ru','$1$98y1xlfV$FWw2PeIeaWfqozLG1nBAx1','Привет-как-дела-тест-переоткрытия-для-всех','','',0,'','2015-01-14 14:02:50',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(215,55,'donsql0@gmail.com','$1$onXMIxiF$ZB/OLJ83T2VTLcODuUzzu.','Alex6','','',27,'','2015-01-14 14:38:33',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'wert',0),(216,53,'test-test-test-test@mail.ua','$1$L10hPg.O$6RS3CQq1lFuLA10oTviQf/','test-test-test-test@mail.ua','','',0,'','2015-01-23 14:47:33',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(217,1,'96190','social','Олег','Бабенцов','m',0,'','2015-01-26 13:01:27','vkontakte','4896125','','','Днепропетровск',1,'y','','',0,'',0),(221,57,'archic2','$1$kZhKH2L6$lVO2E/M7RuS4Gz7Vjd9il1','магазин','АлкоПрибор','m',0,'Совершенно разнообразные интересы','2015-01-30 14:10:06','mailru','4776518024971768565','+79276374844','zakupki@alcopribor.ru','',1,'y','','',0,'',0),(222,59,'artur@alcopribor.ru','$1$RzLy6AJL$/AFtEhzgBpxV/cHl7GAf..','artur@alcopribor.ru','','',0,'','2015-01-30 14:20:03',NULL,NULL,'','','Уфа',1,'y','0000-00-00 00:00:00','',0,'',0),(223,58,'10891','social','Артур','Камильянов','m',0,'','2015-02-01 10:09:57','vkontakte','2070483','','','Уфа',1,'y','','',0,'уиии',0),(224,1,'soprano-2016@mail.ru','$1$NTXZIBAf$vyo/GqgIPa68WlG6P8rYD1','Вася','Петин','муж',22,'фапфапфап','2015-02-02 09:16:28',NULL,NULL,'0931234567','','Днепр',0,'y','0000-00-00 00:00:00','',0,'всем привет',0),(225,1,'46778','social','Кирилл','Краузе','m',0,'','2015-02-02 13:08:43','vkontakte','48013648','','','Уфа',1,'y','','',0,'',0),(228,1,'soprano-2016@mail.ru','social','Tony','Soprano','m',24,'','2015-02-03 09:48:05','mailru','12944505761347482848','','soprano-2016@mail.ru','',0,'y','','',0,'',0),(229,1,'tonya-kulakova@mail.ru','$1$ob8cQvHM$P.LWEaBTxbnrBKV5Cy9U10','вася','вася','вася',0,'вася','2015-02-03 11:47:55',NULL,NULL,'вася','вася','вася',0,'y','0000-00-00 00:00:00','',0,'вася',0),(230,1,'test-test-test-test@mail.ua','social','test','test','m',5,'','2015-02-05 08:29:30','mailru','18420767231866537997','','test-test-test-test@mail.ua','',1,'y','','',0,'',0),(232,1,'Don_@ua.fm','$1$Ekqu8bUm$JVEcRIijik6s7aRmuqDw4/','Don_@ua.fm',NULL,NULL,0,'','2015-02-06 10:10:02',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',0),(233,1,'lenka_530@ukr.net','$1$Dp7ut8X2$q0MQug6mNVObKrCVqkaOI/','lenka_530@ukr.net',NULL,NULL,0,'','2015-02-09 11:37:29',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',0),(234,1,'qwer@ty.net','$1$e0iE2ehq$qaDHlMq0gYwcXm5Vh.fRU.','qwer@ty.net',NULL,NULL,0,'','2015-02-09 11:52:54',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',0),(235,1,'donsql@mail.ru','$1$lCFQxGb.$LyQjkf.T3/gS2sIeejy/x1','donsql@mail.ru',NULL,NULL,0,'','2015-02-09 12:00:35',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(236,1,'29982','social','Онлайн','Карта','m',0,'','2015-02-11 10:42:23','vkontakte','244317480','+79276374844','','Уфа',0,'y','','',0,'',0),(237,1,'viktor.konstantinov112@gmail.com','social','Viktor','Konstantinov','m',0,'','2015-02-15 23:13:13','facebook','1532565053698415','','viktor.konstantinov112@gmail.com','',0,'y','','',0,'',0),(238,1,'kukin2017@mail.ru','$1$NrokXCoV$fm8Hy8pI1lzMwoJHwm9ef.','kukin2017@mail.ru',NULL,NULL,0,'','2015-02-16 08:20:21',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(239,1,'dima14480@mail.ru','social','Dima','Ivanov','m',24,'','2015-02-18 13:17:07','mailru','17350031352360611550','','dima14480@mail.ru','',1,'y','','',0,'',0),(240,1,'test2764@mail.ru','$1$b3Ls73RA$qCNlIRaHOmQO5h7y/fRlF1','test2764@mail.ru',NULL,NULL,0,'','2015-02-25 21:32:27',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(241,1,'tyler7@mail.ru','social','Victor','V','m',22,'','2015-02-25 23:57:31','mailru','5758859005726787659','','tyler7@mail.ru','',1,'y','','',0,'',0),(243,1,'tested@m.ru','$1$RRCBZp0k$8yyGPYUd6xxeCECw6fHC41','Прог','Прог','м',52,'','2015-03-03 08:20:01',NULL,NULL,'','','Днепр',0,'y','0000-00-00 00:00:00','',0,'',0),(244,1,'tested2@m.ru','$1$cYv1XA3B$2nG/maWoiaqLd4thQvFlk.','Ролл','Ролл','м',25,'','2015-03-03 08:54:53',NULL,NULL,'','','Жопа',0,'y','0000-00-00 00:00:00','',0,'',0),(247,1,'tested3@m.ru','$1$XxOIrnjM$ebMDRtEZNPcveWY1kXQ4..','Саша Фф','Фам','ж',173,'Пааааа','2015-03-03 10:25:18',NULL,NULL,'','','Жопа',0,'y','1425461272','Жираф',0,'Проч55777',0),(249,1,'scherbinin1a@gmail.com','$1$ia.7O4Uy$LaAqs93SR9wbrbX1kjC/u/','scherbinin1a@gmail.com',NULL,NULL,0,'','2015-03-04 11:11:54',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',0),(250,61,'aleksandrshcherbinin','$1$oeCuJNuy$0n/ahyGnSLMmgFwPq.Vjj1','Александр','Щербинин','м',23,'','2015-03-05 06:34:03','vkontakte','16235534','','','Днепропетровск',1,'y','','',0,'',0),(253,1,'donsql0@gmail.com','$1$z8kxUhDf$vCzlIa7.qe2Epyfx13IP30','Алексей','Дьяков','м',0,'Параропри. Реар','2015-03-05 13:20:42','facebook','359815400876790','','donsql0@gmail.com','',0,'y','','',0,'',0),(254,62,'donsql@mail.ru','$1$dkxonN13$mkJYrxbqr2i.EWPJLHMjo.','Дьяков','Алексей','м',27,'','2015-03-05 13:28:21','mailru','14315605720357193514','','','',0,'y','','',0,'Я тут',0),(255,1,'dmitriy.shur85@gmail.com','$1$OHf4RecF$axCkcVhEd27UAnN4RzeL3.','Dmitriy','Shur','м',0,'','2015-03-06 09:19:59','facebook','1018065774890032','','dmitriy.shur85@gmail.com','',0,'y','','',0,'',0),(256,1,'dorn_2016@mail.ua','social','иван','дорн','m',22,'','2015-03-11 13:05:49','mailru','7685892707719482204','','dorn_2016@mail.ua','',0,'y','','',0,'',0),(257,63,'info@onlinemap.org','$1$uJVQeE2z$vARX98zHCj6G9guAwvKHp0','Админ карты','','любой',0,'','2015-03-12 19:06:14',NULL,NULL,'','info@onlinemap.org','Сыкфтыквкар',1,'y','0000-00-00 00:00:00','',0,'',0),(258,1,'89276374844','$1$hggsP492$R8IppHDCZRkqEvVZwByH00','89276374844',NULL,NULL,0,'','2015-03-17 10:22:29',NULL,NULL,'','','',0,'n','1426584149','Жираф',0,'',0),(259,1,'+79276374844','$1$yW1A0P8r$DqleraFVeRmaLB2bYrd.W0','+79276374844',NULL,NULL,0,'','2015-03-17 10:24:58',NULL,NULL,'','','',0,'n','1426584298','Кенгуру',0,'',0),(260,1,'+380675626258','$1$3DYJL.dZ$uLTKOtnwd9JmiKSyk1k4A1','+380675626258',NULL,NULL,0,'','2015-03-24 08:56:02',NULL,NULL,'','','',0,'n','1427183762','Заяц',0,'',0),(261,1,'+380939798564','$1$8LzMQp68$2a1PWMS8TC0KY1J58q6yy.','+380939798564',NULL,NULL,0,'','2015-03-24 08:57:03',NULL,NULL,'','','',0,'n','1427183823','Слон',0,'',0),(269,1,'kesedi@bk.ru','$1$onXMIxiF$ZB/OLJ83T2VTLcODuUzzu.','kesedi@bk.ru',NULL,'м',9999,'','2015-03-24 09:00:55',NULL,NULL,'','','Dnipro',1,'y','0000-00-00 00:00:00','',0,'Status',0),(270,64,'masternezavisimyy','$1$/TFkFRqn$G8TmN3dGjj0f9mf5lIOmp1','Мастер','Независимый','м',0,'','2015-03-26 13:11:50','vkontakte','282537142','','','Уфа',1,'y','','',0,'',0),(271,1,'nataliya.vankova@digitalpromo.com.ua','$1$onXMIxiF$ZB/OLJ83T2VTLcODuUzzu.','nataliya.vankova@digitalpromo.com.ua','','',0,'','2015-03-26 15:14:25',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(272,1,'asryabcev@gmail.com','$1$RjYQS7u/$Gm9ZaS7ymGF9m69xRylRC.','asryabcev@gmail.com',NULL,NULL,0,'','2015-04-01 14:15:16',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(273,1,'kyzakr@mail.ru','$1$BjKdBgZg$zIHnDgduwjc4ak0t4AgXQ1','Кузя','Кузя','',0,'','2015-04-20 14:31:47',NULL,NULL,'','kyzakr@mail.ru','',1,'y','0000-00-00 00:00:00','',0,'',0),(274,1,'nata.pozdeeva.ua@gmail.com','$1$5LSFFKE.$61wnol3m6Nxd/tNfpa4ru1','nata.pozdeeva.ua@gmail.com','','',26,'','2015-04-21 07:46:55',NULL,NULL,'','','Днепропетровск',1,'y','0000-00-00 00:00:00','',0,'',0),(275,1,'zhenja.97@gmail.com','$1$jQB5MLfX$0fTddailREtk2QGlilP2i.','zhenja.97@gmail.com','','',0,'','2015-04-21 09:47:40',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',3),(277,1,'0934455335','$1$XErWjsWf$h0NB9E1y7zRiqlMY.yqSi0',NULL,NULL,NULL,0,'','2015-04-21 10:08:22',NULL,NULL,'','','',0,'n','1429607302','Жираф',0,'',0),(279,1,'+380684011376','$1$sqOtTdRB$rEN7Cfzo.F0VUEcyNtRIw.',NULL,NULL,NULL,0,'','2015-04-21 15:11:36',NULL,NULL,'','','',0,'n','1429625496','Жираф',0,'',0),(280,1,'vi@lastmail.co','$1$/lGCS01m$/cbsTRb5AsKe/mVH0Jmji0',NULL,NULL,NULL,0,'','2015-04-22 14:07:56',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(281,1,'yui__hj@mail.ru','$1$YlDre2CD$4vpQu5TjiVZEDQeSmmfXD/',NULL,NULL,NULL,0,'','2015-04-23 11:59:58',NULL,NULL,'','','',0,'n','1429786798','Рыба',0,'',0),(282,1,'вапрол','$1$hifRr5jy$M5M3AY/Q13Q3ulLp8kY9X/',NULL,NULL,NULL,0,'','2015-04-27 15:57:17',NULL,NULL,'','','',0,'n','1430146637','Волк',0,'',0),(283,1,'фывапролд','$1$SHrBxyE3$FgBuPWCheFQAzGQsqYLhQ/',NULL,NULL,NULL,0,'','2015-04-27 15:57:46',NULL,NULL,'','','',0,'n','1430146666','Рыба',0,'',0),(284,71,'80126','social','Evgeny','Motovilov','m',0,'','2015-04-28 08:24:06','vkontakte','7180354','','','',1,'y','','',0,'',0),(285,1,'teke@divermail.com','$1$ZHEggSgr$6hGMHPvKvWOAy09IXba410',NULL,NULL,NULL,0,'','2015-04-28 14:34:21',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(286,1,'42551','social','Наталья','Крюковская','w',0,'','2015-04-29 12:59:27','vkontakte','6748533','','','Днепропетровск',0,'y','','',0,'',0),(287,1,'4017','social','Сергей','Ломбардов','m',0,'','2015-04-30 15:08:03','vkontakte','212246865','','','Уфа',1,'y','','',0,'',0),(288,1,'32106','social','Андрей','Матвеев','m',0,'','2015-05-01 11:23:01','vkontakte','149407597','','','Киров',1,'y','','',0,'',0),(289,65,'archic2@mail.ru','$1$IXkLnBSj$qzqzLmH1aZS7m8eORSf/g.','Артур','*','m',32,'мои интересы','2015-05-11 17:39:16','mailru','1677168507346435832','','archic2@mail.ru','',1,'y','','',0,'супер',0),(290,1,'artur@alcopribor.ru','social','Артур','Камильянов','m',0,'','2015-05-20 20:46:01','mailru','2088998254483351013','','artur@alcopribor.ru','',1,'y','','',0,'',0),(291,1,'26180','social','Артем','Курсаков','м',28,'','2015-05-21 07:44:41','vkontakte','304665212','','','Днепропетровск',1,'y','','',0,'',0),(292,72,'artem.kursakov@digitalpromo.com.ua','$1$uV4MVeNR$g5t97SISb.5HoYe3mnTur.','artem.kursakov@digitalpromo.com.ua','','м',0,'','2015-06-16 07:53:08',NULL,NULL,'','','Днепропетровск',1,'y','0000-00-00 00:00:00','',0,'',0),(293,1,'vicofujisu@landmail.co','$1$IKC.KZHU$uNLBYS/f8pRUm8k3iNQGZ1',NULL,NULL,NULL,0,'','2015-06-16 09:17:14',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(294,66,'vip.takeiteasy@mail.ru','$1$9UpvKjuQ$fmk5TU6EjzEQ84kEJUXaX1','Владимир','Логачев','m',23,'','2015-07-20 09:28:31','mailru','1290921325391659268','','vip.takeiteasy@mail.ru','',1,'y','','',0,'',0),(295,1,'vip.takeiteasy@gmail.com','$1$7Ws/s5Km$ceSoK5CexrlrMRFvx98O21','Vladimir','Logachov','m',0,'','2015-07-20 13:55:47','facebook','442038095967931','','vip.takeiteasy@gmail.com','',0,'y','','',0,'',0),(296,67,'vladimircorvis','$1$Mx9SEQIm$eZqLzNYSDIHIyHkLmCjWD.','Vladimir','Corvis','м',22,'','2015-09-22 06:08:42','vkontakte','5250929','','','Makeevka',0,'y','','',0,'',0),(297,68,'wwwpagetest@gmail.com','$1$2QkHa1iR$.XvHFGCi.83aSC7Les87g0','wwwpagetest@gmail.com','','',0,'','2015-10-06 07:04:00',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(298,1,'codeception.test@gmail.com','$1$xrwAeUBA$1XyGYFeqVPebDE9ZRok9Z.',NULL,NULL,NULL,0,'','2015-10-07 08:53:39',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(299,1,'test-ru@ro.ru','$1$ue4ncEx3$7T9eiltv23wg1ZJcN1KFV/',NULL,NULL,NULL,0,'','2015-10-07 09:31:22',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(300,1,'74570','social','Никита','Данилов','m',0,'','2015-10-22 09:36:12','vkontakte','292814671','','','Москва',1,'y','','',0,'',0),(301,1,'clavon@mail.ua','$1$Bqio5o7N$BlgfIOLTf5mLVCiC5bZmI/','Dizon','Clavon','м',54,'test','2015-10-28 07:33:09',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(302,69,'Mrbaks333@gmail.com','$1$qaQwCniN$1Vs/BaDb8OdkrId4z7OuV0','Mrbaks333@gmail.com','','',25,'','2015-11-01 12:14:21',NULL,NULL,'','','Одесса',1,'y','0000-00-00 00:00:00','',0,'',0),(303,1,'777london@bk.ru','$1$fK3amfbr$Z.cCnKSiyiw.z7.DsW18x.',NULL,NULL,NULL,0,'','2015-11-03 22:07:54',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',0),(304,1,'artem.kursakov@gmail.com','$1$xiWYAWxG$Pl11esD/.GW5O.Me8ivZb1',NULL,NULL,NULL,0,'','2015-11-09 09:29:29',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',0),(305,1,'pushnir@yandex.ru','$1$XSz37tDW$Tfp9UdRc6ZLr6o5VHm0RV.',NULL,NULL,NULL,0,'','2015-11-23 10:39:00',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(306,1,'mobidoc27@mail.ru','$1$evB4A0GM$QVo34gmdRMTcg/4lLOnka1',NULL,NULL,NULL,0,'','2015-11-27 10:30:31',NULL,NULL,'','','',0,'n','1448620231','Заяц',0,'',0),(307,1,'mobidoc27@mail.ru','social','Владислав','Редько','m',24,'','2015-11-27 10:32:34','mailru','7032214460366509249','','mobidoc27@mail.ru','',1,'y','','',0,'',0),(308,1,'mekisboun7@gmail.com','social','Владимир','Юхим','m',0,'','2015-12-01 14:12:19','facebook','966395143454919','','mekisboun7@gmail.com','',1,'y','','',0,'',0),(309,1,'redyafuck@mail.ru','social','Vlad','Redko','m',0,'','2015-12-02 14:55:15','facebook','932674086797641','','redyafuck@mail.ru','',1,'y','','',0,'',0),(310,70,'nikitaemelyanov','$1$toN2fpSb$Vqy4DqFaPBt8Hlsgl9Jyk.','Никита','Емельянов','m',0,'','2015-12-10 15:01:11','vkontakte','59980042','','','Днепропетровск',1,'y','','',0,'',0),(311,1,'nkt011094@gmail.com','social','Nikita','Emelyanov','m',0,'','2015-12-16 15:30:59','facebook','1524335784548218','','nkt011094@gmail.com','',1,'y','','',0,'',0),(312,1,'29789','social','Сергей','Домославский','m',0,'','2015-12-17 12:29:53','vkontakte','148333344','','','',1,'y','','',0,'',0),(314,1,'nkt011094@gmail.com','$1$sQtrP3V/$xCJvOeLZfb0yVxq6pvc1r/',NULL,NULL,NULL,0,'','2015-12-18 15:22:56',NULL,NULL,'','','',0,'y','1450452343','Кенгуру',0,'',0),(317,1,'tkn011094@gmail.com','$1$NWyMTTwm$y2Ic/EQYw6nuE/NYerTIW1',NULL,NULL,NULL,0,'','2015-12-24 12:26:26',NULL,NULL,'','','',0,'n','1450959986','Слон',0,'',0),(318,1,'81894','social','Никита','Краснодеревщик','m',0,'','2015-12-24 12:28:35','vkontakte','187729504','','','',1,'y','','',0,'',0),(319,1,'zakupki@alcopribor.ru','$1$xwoseMoH$IPlus8QJYCjrCUDP8hFCu0','zakupki@alcopribor.ru','','',0,'123123123','2016-01-15 14:00:41',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(320,1,'info@alcopribor.ru','social','магазин','АлкоПрибор','m',93,'','2016-01-18 13:57:01','mailru','14666307911905991088','','info@alcopribor.ru','',1,'y','','',0,'',0),(321,1,'50328','social','Андрей','Аксов','m',0,'','2016-02-22 20:27:30','vkontakte','32732438','','','Уфа',1,'y','','',0,'',0),(322,1,'+380977972635','$1$Rc8PPf1j$N9I5ZJIJamLS6dBhmGfzz.',NULL,NULL,NULL,0,'','2016-03-07 10:29:52',NULL,NULL,'','','',0,'n','1457346592','Заяц',0,'',0),(325,1,'vip.takeiteasy@gmail.com','$1$/fV5kgJd$CWwLyKLTvuctnEqoK1e5J1','sho ','proishodit','',0,'абжю крту чт','2016-03-09 10:15:30',NULL,NULL,'','','Днепропетровск',1,'y','1457539451','Слон',0,'',0),(326,1,'380934455555','$1$5msMTqFX$PMZOSGgRe99BOqaQ/04an1',NULL,NULL,NULL,0,'','2016-03-09 13:15:00',NULL,NULL,'','','',0,'n','1457529300','Жираф',0,'',0),(327,1,'380934455554','$1$hfDqCQ.L$718quHPq0nk9Hruj7DiY1.',NULL,NULL,NULL,0,'','2016-03-09 13:17:39',NULL,NULL,'','','',0,'n','1457529459','Заяц',0,'',0),(328,1,'380934455553','$1$zgunAFcI$lJe759YEHXsUjn25g9JHV.',NULL,NULL,NULL,0,'','2016-03-09 13:19:24',NULL,NULL,'','','',0,'n','1457529564','Заяц',0,'',0),(329,1,'380934455552','$1$vOftuKPo$U8Rq3tyJ2L4gxgZI1MaHf1',NULL,NULL,NULL,0,'','2016-03-09 13:21:33',NULL,NULL,'','','',0,'n','1457529693','Слон',0,'',0),(330,1,'380934455551','$1$/COMEF26$5G96pYejRnMkzAt0C1V.i0',NULL,NULL,NULL,0,'','2016-03-09 13:23:04',NULL,NULL,'','','',0,'n','1457529784','Рыба',0,'',0),(332,1,'380934455335','$1$0TwF5Hsg$G1f26PeeKzpQ01aafIGYf1',NULL,NULL,NULL,0,'','2016-03-09 13:30:01',NULL,NULL,'','','',0,'n','1457530201','Жираф',0,'',0),(335,1,'380993638959','$1$9VhBwv.F$FjNmfFkdz9wNEtLinvBvJ.',NULL,NULL,NULL,0,'','2016-03-09 15:28:13',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(336,1,'bigeguzok@flemail.ru','$1$5zdfi0UB$QV4Go2mJTFdKKSJ9geCV81',NULL,NULL,NULL,0,'','2016-03-09 16:02:39',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(337,1,'https://temp-mail.ru/','$1$O2WSStYC$BZKVmilDf9RR/SJ2QGqS40',NULL,NULL,NULL,0,'','2016-03-11 13:58:31',NULL,NULL,'','','',0,'n','1457704711','Волк',0,'',0),(338,1,'foxuw@dlemail.ru','$1$MmU7w4yf$CJOBCdBVfgNxFXLSE28YC1',NULL,NULL,NULL,0,'','2016-03-11 13:59:56',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',0),(339,1,'ruja@flemail.ru','$1$q.n22Mo/$KZ5EKPyqMEcQhvqKWNKFX.',NULL,NULL,NULL,0,'','2016-03-11 14:01:08',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',0),(340,1,'79276374844','$1$sf8B0sDh$7WsgGF5WM0mHSOiwqosAH.',NULL,NULL,NULL,0,'','2016-03-14 12:32:09',NULL,NULL,'','','',0,'n','1457958729','Рыба',0,'',0),(341,1,'+79178000731','$1$Gd2xzXZY$1jHQ65xrMGbk75XFtBkaH1',NULL,NULL,NULL,0,'','2016-03-14 12:36:27',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',0),(342,1,'380977972635','$1$d8I4QYFm$V8X6kmlV6dCGDcUrqvhra0','380977972635','','м',0,'Нннн','2016-03-14 12:50:23',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(343,1,'89177774750','$1$7aqA4ZBh$9Dw2oalpAUJxQ7r9GIBLz.',NULL,NULL,NULL,0,'','2016-03-14 12:55:15',NULL,NULL,'','','',0,'n','1457960115','Рыба',0,'',0),(344,1,'+79177774750','$1$RcvfRC3S$.DUbmpobYpsGZ4/RPbX8R0',NULL,NULL,NULL,0,'','2016-03-14 12:56:22',NULL,NULL,'','','',1,'y','0000-00-00 00:00:00','',0,'',0),(345,1,'1','$1$yfCr41nu$Doj5eVu9RpuRVahfeOXyJ/',NULL,NULL,NULL,0,'','2016-03-15 08:11:02',NULL,NULL,'','','',0,'n','1458029462','Жираф',0,'',0),(346,1,'11','$1$SlZMAiM.$96e4/cKoyZxWE/Z2Dbd9Z0',NULL,NULL,NULL,0,'','2016-03-15 08:11:08',NULL,NULL,'','','',0,'n','1458029468','Слон',0,'',0),(347,1,'111','$1$xOSlfsfs$j0H.E.1QdnRHZUkNeUksJ0',NULL,NULL,NULL,0,'','2016-03-15 08:11:10',NULL,NULL,'','','',0,'n','1458029470','Слон',0,'',0),(348,1,'1111','$1$j92fvSy/$kvbqysPU1TNUI6kAU6urL/',NULL,NULL,NULL,0,'','2016-03-15 08:11:14',NULL,NULL,'','','',0,'n','1458029474','Кенгуру',0,'',0),(349,1,'11111','$1$vXPCuSi3$Slw86fdiXMqwZOKOlAcJi/',NULL,NULL,NULL,0,'','2016-03-15 08:11:16',NULL,NULL,'','','',0,'n','1458029476','Жираф',0,'',0),(350,1,'111111','$1$9cOSBVPm$iwGV2Ya2PD.fOTnzT9tCR/',NULL,NULL,NULL,0,'','2016-03-15 08:11:18',NULL,NULL,'','','',0,'n','1458029478','Жираф',0,'',0),(351,1,'1111111','$1$Oy4o.erG$frgBbSx2W6UH/yR5XkdzF.',NULL,NULL,NULL,0,'','2016-03-15 08:11:20',NULL,NULL,'','','',0,'n','1458029480','Жираф',0,'',0),(352,1,'11111111','$1$tRDMPauZ$i2cvixPqUXMopWIvxZT4B0',NULL,NULL,NULL,0,'','2016-03-15 08:11:22',NULL,NULL,'','','',0,'n','1458029482','Рыба',0,'',0),(353,1,'111111111','$1$xYUzlWR2$sWcT./6zNKtzT0YqFk/Gw1',NULL,NULL,NULL,0,'','2016-03-15 08:11:24',NULL,NULL,'','','',0,'n','1458029484','Жираф',0,'',0),(354,1,'1111111111','$1$QP7xMi/U$2Tp8y7KZkI82cAHJPU6YS0',NULL,NULL,NULL,0,'','2016-03-15 08:11:26',NULL,NULL,'','','',0,'n','1458029486','Рыба',0,'',0),(355,73,'380979203676','$1$tdMFd4Or$UGDCy8dmD1rJp0V/IIvbd0','имя157','фамилия','пол',1,'Интересы, о себе:','2016-03-15 08:12:18',NULL,NULL,'телефон','Email','город',1,'y','0000-00-00 00:00:00','',0,'статус',0),(356,2,'opt@alcopribor.ru','$1$fepjXGMa$DcwE8zi3l1hEV8FNQvfca1',NULL,NULL,NULL,0,'','2016-03-31 07:07:37',NULL,NULL,'','','',0,'y','0000-00-00 00:00:00','',0,'',2),(357,1,'oplata@alcopribor.ru','$1$0X/OJvOY$7K1jjlT3L1Qu/XBioLNPM/','oplata@alcopribor.ru','','м',0,'','2016-03-31 07:13:00',NULL,NULL,'','','Уфа',1,'y','0000-00-00 00:00:00','',0,'fff',2),(358,1,'9','$1$i7oV1Z/Q$AuY3RKlKsofQyIg3gwn8E/',NULL,NULL,NULL,0,'','2016-05-26 15:15:00',NULL,NULL,'','','',0,'n','1464275700','Рыба',0,'',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-05-26 18:48:27
