-- MySQL dump 10.13  Distrib 9.3.0, for macos13.7 (x86_64)
--
-- Host: 34.143.253.172    Database: yii_test
-- ------------------------------------------------------
-- Server version	9.5.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '200013bb-df04-11f0-beaf-f2fbfdad6722:1-514';

--
-- Table structure for table `auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_id` varchar(36) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `fk-auth_assignment-item_name` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_assignment`
--

LOCK TABLES `auth_assignment` WRITE;
/*!40000 ALTER TABLE `auth_assignment` DISABLE KEYS */;
INSERT INTO `auth_assignment` VALUES ('admin','4e2ce959-7598-4d9f-8947-e173ec894d25',1767249395),('creator','73b7ebcb-f798-4cce-b979-4b2f9b5d2371',1767249394),('user','10',1767272830),('user','4f7f7a35-566e-47df-a681-2b0669886620',1767256197),('user','5',1767271666),('user','9',1767272660),('user','bd829e0a-1ca9-4193-8fbf-f87e46a90526',1767249395);
/*!40000 ALTER TABLE `auth_assignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` smallint NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `idx-auth_item-type` (`type`),
  KEY `fk-auth_item-rule_name` (`rule_name`),
  CONSTRAINT `fk-auth_item-rule_name` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_item`
--

LOCK TABLES `auth_item` WRITE;
/*!40000 ALTER TABLE `auth_item` DISABLE KEYS */;
INSERT INTO `auth_item` VALUES ('admin',1,'Administrator - can manage content and users',NULL,NULL,1767249393,1767249393),('createPost',2,'Create a post',NULL,NULL,1767249392,1767249392),('creator',1,'Creator - full system access',NULL,NULL,1767249394,1767249394),('deletePost',2,'Delete post',NULL,NULL,1767249393,1767249393),('manageRoles',2,'Manage roles and permissions',NULL,NULL,1767249393,1767249393),('manageUsers',2,'Manage users',NULL,NULL,1767249393,1767249393),('systemSettings',2,'Access system settings',NULL,NULL,1767249393,1767249393),('updatePost',2,'Update post',NULL,NULL,1767249393,1767249393),('user',1,'Regular user - can view and create content',NULL,NULL,1767249393,1767249393),('viewPost',2,'View post',NULL,NULL,1767249392,1767249392);
/*!40000 ALTER TABLE `auth_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `fk-auth_item_child-child` (`child`),
  CONSTRAINT `fk-auth_item_child-child` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-auth_item_child-parent` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_item_child`
--

LOCK TABLES `auth_item_child` WRITE;
/*!40000 ALTER TABLE `auth_item_child` DISABLE KEYS */;
INSERT INTO `auth_item_child` VALUES ('creator','admin'),('user','createPost'),('admin','deletePost'),('creator','manageRoles'),('admin','manageUsers'),('creator','systemSettings'),('admin','updatePost'),('admin','user'),('user','viewPost');
/*!40000 ALTER TABLE `auth_item_child` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_rule`
--

LOCK TABLES `auth_rule` WRITE;
/*!40000 ALTER TABLE `auth_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration` (
  `version` varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration`
--

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` VALUES ('m000000_000000_base',1668528329),('m221111_144133_create_auth_item_child_table',1668528337),('m221115_150802_create_kelompok_table',1668528337),('m221115_152804_create_ruangan_table',1668528337),('m221115_152815_create_logistik_table',1668528337),('m221115_152830_create_peminjaman_table',1668528337),('m221115_152859_create_praktikum_table',1668528337),('m221115_153748_create_jenis_table',1668528337),('m221115_153757_create_satuan_table',1668528337),('m221218_033448_create_tempat_table',1672150109),('m221218_034129_create_dosen_table',1672150109),('m221218_034906_create_judul_table',1672150109),('m221218_035403_create_data_prodi_table',1672150109),('m221218_035902_create_ruangan_table',1672150110),('m221218_043010_create_barang_pinjam_table',1672150110),('m221220_075133_create_peminjaman_table',1672150110),('m221220_142130_create_barang_pinjam_table',1672150110),('m221220_170503_create_barang_pinjam_table',1672150110),('m221221_112538_add_foreign_key',1672150111),('m221221_121907_drop_column',1672150111),('m221221_123917_add_column_peminjaan',1672150111),('m221221_174958_create_barang_pinjam_table',1672150112),('m221222_021759_drop_columns',1672150112),('m221222_035941_table_barang_pinjam',1672150112),('m221227_135754_create_nama_gedung_table',1672150112),('m221227_141100_addColumn_no',1672150436),('m221227_141851_dropColumn_no',1672150768),('m221227_144900_create_bahan_table',1672152662),('m230103_030611_create_bahan_coba_table',1672715298),('m230103_043530_create_alat_table',1672807076),('m230103_044558_create_ruangan_table',1672895330),('m230103_163805_add_foreign_key',1672895674),('m230103_170136_create_gedung_table',1672906252),('m230104_040850_create_satuanr_table',1672805509),('m230105_161411_create_pegawai_table',1672935354),('m230105_162320_add_pegawai_colomn',1672935849),('m230105_173900_add_pegawai_colomn',1672940401),('m230105_175402_add_pegawai_colomn',1672941334),('m230105_175640_add_pegawai_colomn',1672943699),('m230105_184531_add_user_column',1672944519),('m230105_190518_create_biaya_penelitian_table',1672945552),('m230105_191306_create_responsible_table',1673031334),('m230106_190232_create_gedung_table',1673031785),('m230106_191349_create_anggota_table',1673032552),('m230106_191927_add_peminjaman_column',1673083345),('m230107_092305_add_peminjaman_column',1673083474),('m230108_032128_create_jenis_kegiatan_table',1673148178),('m230613_034327_add_column',1686627912),('m230613_035252_Relations',1686629650),('m230613_035704_Relations',1686629650),('m230613_120419_add_column',1686658231),('m230613_121337_add_foignkey',1686658667),('m230613_123839_add_foignkey',1686660428),('m230613_124816_add_foignkey',1686661428),('m230613_130542_add_foignkey',1686661657),('m230715_065922_full_migration',1689495441),('m260101_000001_create_users_table',1767249383),('m260101_000002_init_rbac',1767249384),('m260101_000003_seed_users',1767249390),('m260101_122412_add_access_role_to_users_table',1767273113),('m260101_131003_change_user_id_to_uuid',1767273137);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session` (
  `id` char(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `expire` int DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
INSERT INTO `session` VALUES ('sorfhc0g7f2vjns79t6uoj6js6',1692091071,_binary '__flash|a:0:{}__id|i:1;__authKey|s:32:\"HucjOcfeu3YVm8hSQYIXVn8smHtYTFxP\";__expire|i:1692609471;'),('td81e1ts34aprbr81hdpbbu454',1691129423,_binary '__flash|a:0:{}__id|i:1;__authKey|s:32:\"HucjOcfeu3YVm8hSQYIXVn8smHtYTFxP\";__expire|i:1691647823;');
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` varchar(36) NOT NULL,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `telegram_id` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `access_role` varchar(50) DEFAULT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`),
  KEY `idx-users-username` (`username`),
  KEY `idx-users-email` (`email`),
  KEY `idx-users-status` (`status`),
  KEY `idx-users-access_role` (`access_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('4e2ce959-7598-4d9f-8947-e173ec894d25','admin','vE9MSWPdzh8bSh9-exOPHTDNo03gxxQx','$2y$13$2USQABKzD5FcR1sHQa7H2eZUAxf6knoiOfOgqLA4RbIg/c0XD5R9C',NULL,'admin@example.com','Admin User',NULL,NULL,NULL,10,NULL,1767249388,1767249388),('4f7f7a35-566e-47df-a681-2b0669886620','nashehannafii','FEpUd7Aqbk6uCJzWjo3q2dehlvxFdGEP','$2y$13$CgubwfJQZL9y2Mgqhiewa.Doosklkcx2z3TRAbYA5XAhk7uzzUdrW',NULL,'nashehannafii@gmail.com','Nasheh Annafii','','','081231231233',10,'',1767256197,1767273925),('73b7ebcb-f798-4cce-b979-4b2f9b5d2371','creator','LLQRtDZzIPfrgn-hALzCLIrNwbZ7sljl','$2y$13$OALmTF2QjQ2sM8t8Dy7EO.6Zai5GIway1g1Pdtd9a4NuNaXXPUF06',NULL,'creator@example.com','Creator User',NULL,NULL,NULL,10,NULL,1767249386,1767249386),('bd829e0a-1ca9-4193-8fbf-f87e46a90526','user','3XwZkIsojuF3JHuR2w1uRpeSRvOpU3ZA','$2y$13$j/OHfQT84EtmCqBdF2yyuePGkuoI9vJYu5O6.r3FNqKIhraQlEbO.',NULL,'user@example.com','Regular User',NULL,NULL,NULL,10,NULL,1767249390,1767249390);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-07 16:36:44
