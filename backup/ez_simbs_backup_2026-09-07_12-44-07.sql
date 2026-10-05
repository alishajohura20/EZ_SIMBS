mysqldump: [Warning] Using a password on the command line interface can be insecure.
-- MySQL dump 10.13  Distrib 8.4.11, for Linux (x86_64)
--
-- Host: localhost    Database: ez_simbs
-- ------------------------------------------------------
-- Server version	8.4.11-0ubuntu0.26.04.1

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

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `resource` varchar(100) DEFAULT NULL,
  `resource_id` int DEFAULT NULL,
  `old_value` text,
  `new_value` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 08:46:38'),(2,1,'product_created','products',1,NULL,'{\"name\":\"Test Product\",\"category_id\":\"1\",\"price\":10,\"cost\":5,\"min_stock\":3,\"reorder_level\":5,\"status\":\"active\"}','','','2026-09-07 08:55:04'),(3,1,'product_created','products',2,NULL,'{\"name\":\"Test Product2\",\"price\":10,\"cost\":5,\"min_stock\":3,\"reorder_level\":5,\"status\":\"active\"}','','','2026-09-07 08:55:40'),(4,1,'stock_in','inventory',2,NULL,'{\"qty\":10,\"branch_id\":1}','','','2026-09-07 08:55:40'),(5,1,'stock_out','inventory',2,NULL,'{\"qty\":4,\"branch_id\":1}','','','2026-09-07 08:55:40'),(6,1,'stock_adjustment','inventory',2,'{\"qty\":6}','{\"qty\":15,\"reason\":\"manual adjust\"}','','','2026-09-07 08:55:40'),(7,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:10:53'),(8,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:10:54'),(9,1,'unit_created','units',9,NULL,'{\"name\":\"chips\",\"symbol\":\"13\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:20:37'),(10,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:23:07'),(11,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:23:09'),(12,1,'brand_created','brands',1,NULL,'{\"name\":\"Bombay Sweets\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:25:11'),(13,1,'brand_created','brands',2,NULL,'{\"name\":\"boombay sweets\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:25:30'),(14,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:28:14'),(15,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:28:37'),(16,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:31:01'),(17,1,'brand_created','brands',3,NULL,'{\"name\":\"Pran\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:32:40'),(18,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:34:53'),(19,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:38:02'),(20,1,'brand_updated','brands',1,'{\"id\":1,\"name\":\"Bombay Sweets\",\"logo\":null,\"status\":\"active\",\"created_at\":\"2026-09-07 15:25:11\"}','{\"name\":\"Bombay Sweets Ltd\",\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 09:38:02'),(21,1,'brand_deleted','brands',999,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:38:02'),(22,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:38:17'),(23,1,'brand_deleted','brands',3,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:38:41'),(24,1,'brand_created','brands',4,NULL,'{\"name\":\"Akiz\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:39:11'),(25,1,'brand_updated','brands',4,'{\"id\":4,\"name\":\"Akiz\",\"logo\":null,\"status\":\"active\",\"created_at\":\"2026-09-07 15:39:11\"}','{\"name\":\"Anwar\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:39:27'),(26,1,'brand_deleted','brands',4,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:39:31'),(27,1,'brand_created','brands',5,NULL,'{\"name\":\"Khan \",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:40:52'),(28,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:43:22'),(29,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:44:10'),(30,1,'category_created','categories',3,NULL,'{\"name\":\"TestCatAdd\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 09:44:10'),(31,1,'category_created','categories',4,NULL,'{\"name\":\"Snacks\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:47:33'),(32,1,'category_updated','categories',4,'{\"id\":4,\"name\":\"Snacks\",\"parent_id\":null,\"status\":\"active\",\"created_at\":\"2026-09-07 15:47:33\"}','{\"name\":\"drinks\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:47:49'),(33,1,'category_deleted','categories',4,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:47:57'),(34,1,'unit_updated','units',7,'{\"id\":7,\"name\":\"Box\",\"symbol\":\"box\",\"created_at\":\"2026-09-07 14:46:09\"}','{\"name\":\"Circle\",\"symbol\":\"box\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:56:24'),(35,1,'unit_deleted','units',7,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:56:29'),(36,1,'unit_created','units',10,NULL,'{\"name\":\"box\",\"symbol\":\"box\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:56:40'),(37,1,'product_created','products',3,NULL,'{\"name\":\"Alooz\",\"sku\":\"\",\"category_id\":\"\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"price\":\"25\",\"cost\":\"\",\"barcode\":\"\",\"min_stock\":\"5\",\"reorder_level\":\"20\",\"status\":\"active\",\"description\":\"\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:58:21'),(38,1,'category_updated','categories',4,'{\"id\":4,\"name\":\"drinks\",\"parent_id\":null,\"status\":\"inactive\",\"created_at\":\"2026-09-07 15:47:33\"}','{\"name\":\"Snacks\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:59:03'),(39,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 10:00:50'),(40,1,'product_created','products',4,NULL,'{\"name\":\"Nokia3310\",\"sku\":\"TEST1\",\"price\":\"50\",\"cost\":\"30\",\"status\":\"active\",\"min_stock\":\"2\",\"reorder_level\":\"5\",\"description\":\"initial\"}','127.0.0.1','curl/8.18.0','2026-09-07 10:00:50'),(41,1,'product_updated','products',4,'{\"id\":4,\"sku\":\"TEST1\",\"name\":\"Nokia3310\",\"category_id\":null,\"brand_id\":null,\"unit_id\":null,\"image\":null,\"barcode\":\"TEST1\",\"qr_code\":null,\"description\":\"initial\",\"price\":\"50.00\",\"cost\":\"30.00\",\"min_stock\":2,\"reorder_level\":5,\"status\":\"active\",\"created_at\":\"2026-09-07 16:00:50\",\"updated_at\":\"2026-09-07 16:00:50\",\"category_name\":null,\"brand_name\":null,\"unit_name\":null,\"unit_symbol\":null}','{\"name\":\"Nokia 3310 Pro\",\"description\":\"updated desc\",\"price\":\"75\",\"cost\":\"40\",\"min_stock\":\"3\",\"reorder_level\":\"8\",\"status\":\"active\",\"sku\":\"TEST1\"}','127.0.0.1','curl/8.18.0','2026-09-07 10:03:03'),(42,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 10:03:14'),(43,1,'product_created','products',6,NULL,'{\"name\":\"TestSku\",\"sku\":\"TST1788775393\",\"price\":\"10\",\"cost\":\"5\",\"status\":\"active\",\"min_stock\":\"1\",\"reorder_level\":\"2\",\"category_id\":\"\",\"brand_id\":\"\",\"unit_id\":\"\",\"description\":\"first desc\"}','127.0.0.1','curl/8.18.0','2026-09-07 10:03:14'),(44,1,'product_updated','products',6,'{\"id\":6,\"sku\":\"TST1788775393\",\"name\":\"TestSku\",\"category_id\":null,\"brand_id\":null,\"unit_id\":null,\"image\":\"uploads\\/products\\/product_6_1788775394.png\",\"barcode\":\"TST1788775393\",\"qr_code\":null,\"description\":\"first desc\",\"price\":\"10.00\",\"cost\":\"5.00\",\"min_stock\":1,\"reorder_level\":2,\"status\":\"active\",\"created_at\":\"2026-09-07 16:03:14\",\"updated_at\":\"2026-09-07 16:03:14\",\"category_name\":null,\"brand_name\":null,\"unit_name\":null,\"unit_symbol\":null}','{\"name\":\"TestSku Edited\",\"description\":\"edited desc\",\"price\":\"12.5\",\"cost\":\"6\",\"min_stock\":\"4\",\"reorder_level\":\"9\",\"status\":\"inactive\",\"brand_id\":\"1\",\"sku\":\"TST1788775393\",\"image\":\"uploads\\/products\\/product_6_1788775394.png\"}','127.0.0.1','curl/8.18.0','2026-09-07 10:03:14'),(45,1,'product_updated','products',3,'{\"id\":3,\"sku\":\"SKU-D066BC\",\"name\":\"Alooz\",\"category_id\":null,\"brand_id\":1,\"unit_id\":8,\"image\":null,\"barcode\":\"SKU-D066BC\",\"qr_code\":null,\"description\":\"\",\"price\":\"25.00\",\"cost\":\"0.00\",\"min_stock\":5,\"reorder_level\":20,\"status\":\"active\",\"created_at\":\"2026-09-07 15:58:21\",\"updated_at\":\"2026-09-07 15:58:21\",\"category_name\":null,\"brand_name\":\"Bombay Sweets\",\"unit_name\":\"Pack\",\"unit_symbol\":\"pk\"}','{\"name\":\"Alooz\",\"price\":\"25.00\",\"cost\":\"0.00\",\"min_stock\":\"5\",\"reorder_level\":\"20\",\"status\":\"active\",\"category_id\":\"4\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"sku\":\"SKU-D066BC\",\"barcode\":\"SKU-D066BC\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 10:03:58'),(46,1,'product_created','products',7,NULL,'{\"name\":\"chanachur\",\"sku\":\"\",\"category_id\":\"4\",\"brand_id\":\"5\",\"unit_id\":\"8\",\"price\":\"20\",\"cost\":\"5\",\"barcode\":\"\",\"min_stock\":\"100\",\"reorder_level\":\"50\",\"status\":\"active\",\"description\":\"\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 10:05:43'),(47,1,'product_updated','products',7,'{\"id\":7,\"sku\":\"SKU-73DFBD\",\"name\":\"chanachur\",\"category_id\":4,\"brand_id\":5,\"unit_id\":8,\"image\":null,\"barcode\":\"SKU-73DFBD\",\"qr_code\":null,\"description\":\"\",\"price\":\"20.00\",\"cost\":\"5.00\",\"min_stock\":100,\"reorder_level\":50,\"status\":\"active\",\"created_at\":\"2026-09-07 16:05:43\",\"updated_at\":\"2026-09-07 16:05:43\",\"category_name\":\"Snacks\",\"brand_name\":\"Khan\",\"unit_name\":\"Pack\",\"unit_symbol\":\"pk\"}','{\"name\":\"chanachur\",\"price\":\"20.00\",\"cost\":\"5.00\",\"min_stock\":\"100\",\"reorder_level\":\"50\",\"status\":\"active\",\"category_id\":\"4\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"sku\":\"SKU-73DFBD\",\"barcode\":\"SKU-73DFBD\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 10:08:48'),(48,1,'product_updated','products',7,'{\"id\":7,\"sku\":\"SKU-73DFBD\",\"name\":\"chanachur\",\"category_id\":4,\"brand_id\":1,\"unit_id\":8,\"image\":null,\"barcode\":\"SKU-73DFBD\",\"qr_code\":null,\"description\":\"\",\"price\":\"20.00\",\"cost\":\"5.00\",\"min_stock\":100,\"reorder_level\":50,\"status\":\"active\",\"created_at\":\"2026-09-07 16:05:43\",\"updated_at\":\"2026-09-07 16:08:48\",\"category_name\":\"Snacks\",\"brand_name\":\"Bombay Sweets\",\"unit_name\":\"Pack\",\"unit_symbol\":\"pk\"}','{\"name\":\"chanachur\",\"price\":\"20.00\",\"cost\":\"5.00\",\"min_stock\":\"100\",\"reorder_level\":\"50\",\"status\":\"active\",\"category_id\":\"4\",\"brand_id\":\"5\",\"unit_id\":\"8\",\"sku\":\"SKU-73DFBD\",\"barcode\":\"SKU-73DFBD\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 10:15:52'),(49,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 10:18:15'),(50,1,'category_created','categories',5,NULL,'{\"name\":\"Drinks\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 10:18:25'),(51,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:23'),(52,1,'supplier_created','suppliers',1,NULL,'{\"company_name\":\"Acme Supplies\",\"contact_person\":\"John Doe\",\"email\":\"john@acme.com\",\"phone\":\"+1234567890\",\"address\":\"123 Supplier Rd\",\"tax_id\":\"TAX-123\",\"rating\":4.5,\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 11:00:23'),(53,1,'customer_created','customers',1,NULL,'{\"name\":\"Jane Customer\",\"phone\":\"+0987654321\",\"email\":\"jane@mail.com\",\"is_vip\":1,\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 11:00:23'),(54,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:39'),(55,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:45'),(56,1,'purchase_created','purchases',2,NULL,'{\"po_number\":\"PO-20260907-6088\"}','127.0.0.1','curl/8.18.0','2026-09-07 11:00:45'),(57,1,'purchase_pending','purchases',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:45'),(58,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:50'),(59,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(60,1,'purchase_pending','purchases',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(61,1,'purchase_approved','purchases',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(62,1,'purchase_received','purchases',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(63,1,'stock_in','inventory',7,NULL,'{\"qty\":10,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(64,1,'purchase_stock_received','purchases',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(65,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:01:18'),(66,1,'stock_out','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:01:19'),(67,1,'sale_created','sales',1,NULL,'{\"invoice_no\":\"INV-20260907-5657\",\"total\":44}','127.0.0.1','curl/8.18.0','2026-09-07 11:01:19'),(68,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:01:46'),(69,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:27'),(70,1,'stock_out','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:27'),(71,1,'sale_created','sales',2,NULL,'{\"invoice_no\":\"INV-20260907-7929\",\"total\":44}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:27'),(72,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:41'),(73,1,'stock_in','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:41'),(74,1,'sale_cancelled','sales',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:41'),(75,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:58'),(76,1,'supplier_created','suppliers',2,NULL,'{\"company_name\":\"TestCo\",\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(77,1,'customer_created','customers',2,NULL,'{\"name\":\"Test Person\",\"phone\":\"111\",\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(78,1,'supplier_deleted','suppliers',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(79,1,'customer_deleted','customers',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(80,1,'purchase_payment_added','purchases',2,NULL,'{\"amount\":30}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(81,1,'stock_out','inventory',7,NULL,'{\"qty\":1,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(82,1,'sale_created','sales',3,NULL,'{\"invoice_no\":\"INV-20260907-3771\",\"total\":22}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(83,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:06:09'),(84,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:07:32'),(85,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:07:58'),(86,1,'stock_out','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:07:58'),(87,1,'sale_created','sales',4,NULL,'{\"invoice_no\":\"INV-20260907-0290\",\"total\":41.5}','127.0.0.1','curl/8.18.0','2026-09-07 11:07:58'),(88,1,'purchase_approved','purchases',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 11:08:22'),(89,1,'purchase_payment_added','purchases',2,NULL,'{\"amount\":1000}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 11:09:12'),(90,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:09:26'),(91,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:09:41'),(92,1,'sale_return','sales',4,NULL,'{\"return_total\":20}','127.0.0.1','curl/8.18.0','2026-09-07 11:09:41'),(93,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:10:51'),(94,1,'stock_out','inventory',7,NULL,'{\"qty\":1,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:10:51'),(95,1,'sale_created','sales',5,NULL,'{\"invoice_no\":\"INV-20260907-2950\",\"total\":22}','127.0.0.1','curl/8.18.0','2026-09-07 11:10:51'),(96,1,'sale_return','sales',5,NULL,'{\"return_total\":20}','127.0.0.1','curl/8.18.0','2026-09-07 11:10:52'),(97,1,'purchase_received','purchases',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 11:10:54'),(98,1,'purchase_stock_received','purchases',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 11:10:54'),(99,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:20:01'),(100,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 11:27:24'),(101,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 11:27:35'),(104,NULL,'login','users',3,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 12:00:02'),(106,NULL,'login','users',4,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 12:01:21'),(107,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:02:29'),(108,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:02:48'),(109,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:02:53'),(110,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:08:43'),(111,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:08:46'),(112,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:12:47'),(113,1,'branch_created','branches',2,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:40:12');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `manager_id` int DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`),
  CONSTRAINT `branches_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'Main Branch','123 Business St, City','+1234567890',NULL,'active','2026-09-07 08:46:09'),(2,'Mirpur','','',NULL,'active','2026-09-07 12:40:12');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Bombay Sweets',NULL,'active','2026-09-07 09:25:11'),(2,'boombay sweets',NULL,'active','2026-09-07 09:25:30'),(3,'Pran',NULL,'inactive','2026-09-07 09:32:40'),(4,'Anwar',NULL,'inactive','2026-09-07 09:39:11'),(5,'Khan',NULL,'active','2026-09-07 09:40:52');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `parent_id` int DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (4,'Snacks',NULL,'active','2026-09-07 09:47:33'),(5,'Drinks',NULL,'active','2026-09-07 10:18:25');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text,
  `membership_id` varchar(50) DEFAULT NULL,
  `loyalty_points` int DEFAULT '0',
  `balance` decimal(10,2) DEFAULT '0.00',
  `is_vip` tinyint(1) DEFAULT '0',
  `notes` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `membership_id` (`membership_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Jane Customer','+0987654321','jane@mail.com','','MEM-7D2B0A',14,0.00,1,'','active','2026-09-07 11:00:23','2026-09-07 11:07:58');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `branch_id` int NOT NULL,
  `qty` int DEFAULT '0',
  `min_stock` int DEFAULT '0',
  `reorder_level` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_branch` (`product_id`,`branch_id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES (3,3,1,0,5,20,'2026-09-07 09:58:21'),(6,7,1,4,100,50,'2026-09-07 11:10:52');
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sale_id` int NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `message` text,
  `type` enum('info','warning','danger','success') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,NULL,'Low Stock Alert','chanachur is below reorder level (10 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:00:56'),(2,NULL,'Low Stock Alert','chanachur is below reorder level (8 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:01:19'),(3,NULL,'Low Stock Alert','chanachur is below reorder level (6 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:05:27'),(4,NULL,'Low Stock Alert','chanachur is below reorder level (8 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:05:41'),(5,NULL,'Low Stock Alert','chanachur is below reorder level (7 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:05:59'),(6,NULL,'Low Stock Alert','chanachur is below reorder level (5 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:07:58'),(7,NULL,'Low Stock Alert','chanachur is below reorder level (4 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:09:41'),(8,NULL,'Low Stock Alert','chanachur is below reorder level (3 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:10:51'),(9,NULL,'Low Stock Alert','chanachur is below reorder level (4 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:10:52');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `resource` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_permission` (`role_id`,`resource`,`action`),
  CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (19,1,'branches','manage'),(5,1,'products','create'),(8,1,'products','delete'),(6,1,'products','read'),(7,1,'products','update'),(13,1,'purchases','create'),(16,1,'purchases','delete'),(14,1,'purchases','read'),(15,1,'purchases','update'),(17,1,'reports','read'),(9,1,'sales','create'),(12,1,'sales','delete'),(10,1,'sales','read'),(11,1,'sales','update'),(18,1,'settings','manage'),(1,1,'users','create'),(4,1,'users','delete'),(2,1,'users','read'),(3,1,'users','update');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `unit_id` int DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `min_stock` int DEFAULT '0',
  `reorder_level` int DEFAULT '0',
  `status` enum('active','inactive','discontinued') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_ibfk_3` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (3,'SKU-D066BC','Alooz',4,1,8,NULL,'SKU-D066BC',NULL,'',25.00,0.00,5,20,'active','2026-09-07 09:58:21','2026-09-07 10:03:58'),(7,'SKU-73DFBD','chanachur',4,5,8,NULL,'SKU-73DFBD',NULL,'',20.00,5.00,100,50,'active','2026-09-07 10:05:43','2026-09-07 10:15:52');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `received_qty` int DEFAULT '0',
  `unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
INSERT INTO `purchase_items` VALUES (2,2,7,10,10,5.50,55.00);
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_payments`
--

DROP TABLE IF EXISTS `purchase_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','bank_transfer','cheque') DEFAULT 'cash',
  `reference` varchar(100) DEFAULT NULL,
  `notes` text,
  `paid_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `paid_by` (`paid_by`),
  CONSTRAINT `purchase_payments_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`),
  CONSTRAINT `purchase_payments_ibfk_2` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_payments`
--

LOCK TABLES `purchase_payments` WRITE;
/*!40000 ALTER TABLE `purchase_payments` DISABLE KEYS */;
INSERT INTO `purchase_payments` VALUES (1,2,30.00,'cash','','',1,'2026-09-07 11:05:59'),(2,2,1000.00,'cash','','',1,'2026-09-07 11:09:12');
/*!40000 ALTER TABLE `purchase_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `status` enum('draft','pending','approved','received','cancelled') DEFAULT 'draft',
  `subtotal` decimal(10,2) DEFAULT '0.00',
  `tax` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) DEFAULT '0.00',
  `paid` decimal(10,2) DEFAULT '0.00',
  `due` decimal(10,2) DEFAULT '0.00',
  `payment_status` enum('paid','pending','partial','due') DEFAULT 'pending',
  `notes` text,
  `invoice_file` varchar(255) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `po_number` (`po_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `created_by` (`created_by`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `purchases_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
INSERT INTO `purchases` VALUES (1,1,'PO-20260907-2537','received',0.00,0.00,0.00,0.00,0.00,'pending','Test PO',NULL,1,NULL,1,'2026-09-07 11:00:39','2026-09-07 05:10:54'),(2,1,'PO-20260907-6088','received',55.00,5.50,60.50,1030.00,0.00,'paid','Test PO',NULL,1,NULL,1,'2026-09-07 11:00:45','2026-09-07 11:09:12');
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','Full system access','2026-09-07 08:46:09'),(2,'manager','Reports, analytics, supplier & purchase management','2026-09-07 08:46:09'),(3,'branch_manager','Branch-specific inventory, sales, staff','2026-09-07 08:46:09'),(4,'cashier','POS, invoicing, customer lookup','2026-09-07 08:46:09'),(5,'customer','View own invoices, loyalty points, profile','2026-09-07 08:46:09');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sale_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_payments`
--

DROP TABLE IF EXISTS `sale_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sale_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','mobile','mixed') DEFAULT 'cash',
  `reference` varchar(100) DEFAULT NULL,
  `notes` text,
  `received_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `received_by` (`received_by`),
  CONSTRAINT `sale_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  CONSTRAINT `sale_payments_ibfk_2` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_payments`
--

LOCK TABLES `sale_payments` WRITE;
/*!40000 ALTER TABLE `sale_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `subtotal` decimal(10,2) DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `tax` decimal(10,2) DEFAULT '0.00',
  `shipping` decimal(10,2) DEFAULT '0.00',
  `grand_total` decimal(10,2) DEFAULT '0.00',
  `payment_method` enum('cash','card','mobile','mixed') DEFAULT 'cash',
  `status` enum('completed','held','cancelled','returned') DEFAULT 'completed',
  `notes` text,
  `created_by` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_no` (`invoice_no`),
  KEY `customer_id` (`customer_id`),
  KEY `created_by` (`created_by`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_group` varchar(50) DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'company_name','EZ_SIMBS Store','company','2026-09-07 08:46:09','2026-09-07 08:46:09'),(2,'company_address','123 Business St, City','company','2026-09-07 08:46:09','2026-09-07 08:46:09'),(3,'company_phone','+1234567890','company','2026-09-07 08:46:09','2026-09-07 08:46:09'),(4,'company_email','info@ezsimbs.local','company','2026-09-07 08:46:09','2026-09-07 08:46:09'),(5,'tax_rate','10','billing','2026-09-07 08:46:09','2026-09-07 08:46:09'),(6,'currency','USD','billing','2026-09-07 08:46:09','2026-09-07 08:46:09'),(7,'currency_symbol','$','billing','2026-09-07 08:46:09','2026-09-07 08:46:09'),(8,'invoice_prefix','INV','billing','2026-09-07 08:46:09','2026-09-07 08:46:09'),(9,'po_prefix','PO','billing','2026-09-07 08:46:09','2026-09-07 08:46:09'),(10,'low_stock_threshold','10','inventory','2026-09-07 08:46:09','2026-09-07 08:46:09'),(11,'timezone','UTC','general','2026-09-07 08:46:09','2026-09-07 08:46:09'),(12,'theme','light','appearance','2026-09-07 08:46:09','2026-09-07 12:43:44'),(13,'company_tax_id','','general','2026-09-07 12:43:04','2026-09-07 12:43:04');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_logs`
--

DROP TABLE IF EXISTS `stock_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `branch_id` int NOT NULL,
  `type` enum('in','out','adjustment','damage','return','transfer') NOT NULL,
  `qty` int NOT NULL,
  `reference_id` int DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `notes` text,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `branch_id` (`branch_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `stock_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_logs_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `stock_logs_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_logs`
--

LOCK TABLES `stock_logs` WRITE;
/*!40000 ALTER TABLE `stock_logs` DISABLE KEYS */;
INSERT INTO `stock_logs` VALUES (4,7,1,'in',10,2,'purchase','PO received',1,'2026-09-07 11:00:56'),(5,7,1,'out',2,1,'sale','Sale #INV-20260907-5657',1,'2026-09-07 11:01:19'),(6,7,1,'out',2,2,'sale','Sale #INV-20260907-7929',1,'2026-09-07 11:05:27'),(7,7,1,'in',2,2,'sale_cancelled','Cancelled sale #INV-20260907-7929',1,'2026-09-07 11:05:41'),(8,7,1,'out',1,3,'sale','Sale #INV-20260907-3771',1,'2026-09-07 11:05:59'),(9,7,1,'out',2,4,'sale','Sale #INV-20260907-0290',1,'2026-09-07 11:07:58'),(10,7,1,'return',1,4,'sale','Return from sale #INV-20260907-0290',1,'2026-09-07 11:09:41'),(11,7,1,'out',1,5,'sale','Sale #INV-20260907-2950',1,'2026-09-07 11:10:51'),(12,7,1,'return',1,5,'sale','Return from sale #INV-20260907-2950',1,'2026-09-07 11:10:52');
/*!40000 ALTER TABLE `stock_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(200) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `tax_id` varchar(50) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT '0.00',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'Acme Supplies','John Doe','john@acme.com','+1234567890','123 Supplier Rd','TAX-123',4.50,'active','2026-09-07 11:00:23','2026-09-07 11:00:23');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES (1,'Piece','pc','2026-09-07 08:46:09'),(2,'Kilogram','kg','2026-09-07 08:46:09'),(3,'Gram','g','2026-09-07 08:46:09'),(4,'Liter','L','2026-09-07 08:46:09'),(5,'Milliliter','mL','2026-09-07 08:46:09'),(6,'Meter','m','2026-09-07 08:46:09'),(8,'Pack','pk','2026-09-07 08:46:09'),(9,'chips','13','2026-09-07 09:20:37'),(10,'box','box','2026-09-07 09:56:40');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int NOT NULL,
  `branch_id` int DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','locked') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `failed_attempts` int DEFAULT '0',
  `lockout_until` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@ezsimbs.local','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1,1,NULL,NULL,'active','2026-09-07 12:12:47',0,NULL,NULL,'2026-09-07 08:46:09','2026-09-07 12:12:47');
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

-- Dump completed on 2026-09-07 18:44:07
