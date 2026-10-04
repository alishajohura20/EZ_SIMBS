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
) ENGINE=InnoDB AUTO_INCREMENT=1031 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 08:46:38'),(2,1,'product_created','products',1,NULL,'{\"name\":\"Test Product\",\"category_id\":\"1\",\"price\":10,\"cost\":5,\"min_stock\":3,\"reorder_level\":5,\"status\":\"active\"}','','','2026-09-07 08:55:04'),(3,1,'product_created','products',2,NULL,'{\"name\":\"Test Product2\",\"price\":10,\"cost\":5,\"min_stock\":3,\"reorder_level\":5,\"status\":\"active\"}','','','2026-09-07 08:55:40'),(4,1,'stock_in','inventory',2,NULL,'{\"qty\":10,\"branch_id\":1}','','','2026-09-07 08:55:40'),(5,1,'stock_out','inventory',2,NULL,'{\"qty\":4,\"branch_id\":1}','','','2026-09-07 08:55:40'),(6,1,'stock_adjustment','inventory',2,'{\"qty\":6}','{\"qty\":15,\"reason\":\"manual adjust\"}','','','2026-09-07 08:55:40'),(7,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:10:53'),(8,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:10:54'),(9,1,'unit_created','units',9,NULL,'{\"name\":\"chips\",\"symbol\":\"13\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:20:37'),(10,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:23:07'),(11,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:23:09'),(12,1,'brand_created','brands',1,NULL,'{\"name\":\"Bombay Sweets\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:25:11'),(13,1,'brand_created','brands',2,NULL,'{\"name\":\"boombay sweets\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:25:30'),(14,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:28:14'),(15,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:28:37'),(16,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:31:01'),(17,1,'brand_created','brands',3,NULL,'{\"name\":\"Pran\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:32:40'),(18,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:34:53'),(19,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:38:02'),(20,1,'brand_updated','brands',1,'{\"id\":1,\"name\":\"Bombay Sweets\",\"logo\":null,\"status\":\"active\",\"created_at\":\"2026-09-07 15:25:11\"}','{\"name\":\"Bombay Sweets Ltd\",\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 09:38:02'),(21,1,'brand_deleted','brands',999,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:38:02'),(22,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:38:17'),(23,1,'brand_deleted','brands',3,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:38:41'),(24,1,'brand_created','brands',4,NULL,'{\"name\":\"Akiz\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:39:11'),(25,1,'brand_updated','brands',4,'{\"id\":4,\"name\":\"Akiz\",\"logo\":null,\"status\":\"active\",\"created_at\":\"2026-09-07 15:39:11\"}','{\"name\":\"Anwar\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:39:27'),(26,1,'brand_deleted','brands',4,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:39:31'),(27,1,'brand_created','brands',5,NULL,'{\"name\":\"Khan \",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:40:52'),(28,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:43:22'),(29,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 09:44:10'),(30,1,'category_created','categories',3,NULL,'{\"name\":\"TestCatAdd\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 09:44:10'),(31,1,'category_created','categories',4,NULL,'{\"name\":\"Snacks\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:47:33'),(32,1,'category_updated','categories',4,'{\"id\":4,\"name\":\"Snacks\",\"parent_id\":null,\"status\":\"active\",\"created_at\":\"2026-09-07 15:47:33\"}','{\"name\":\"drinks\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:47:49'),(33,1,'category_deleted','categories',4,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:47:57'),(34,1,'unit_updated','units',7,'{\"id\":7,\"name\":\"Box\",\"symbol\":\"box\",\"created_at\":\"2026-09-07 14:46:09\"}','{\"name\":\"Circle\",\"symbol\":\"box\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:56:24'),(35,1,'unit_deleted','units',7,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:56:29'),(36,1,'unit_created','units',10,NULL,'{\"name\":\"box\",\"symbol\":\"box\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:56:40'),(37,1,'product_created','products',3,NULL,'{\"name\":\"Alooz\",\"sku\":\"\",\"category_id\":\"\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"price\":\"25\",\"cost\":\"\",\"barcode\":\"\",\"min_stock\":\"5\",\"reorder_level\":\"20\",\"status\":\"active\",\"description\":\"\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:58:21'),(38,1,'category_updated','categories',4,'{\"id\":4,\"name\":\"drinks\",\"parent_id\":null,\"status\":\"inactive\",\"created_at\":\"2026-09-07 15:47:33\"}','{\"name\":\"Snacks\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 09:59:03'),(39,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 10:00:50'),(40,1,'product_created','products',4,NULL,'{\"name\":\"Nokia3310\",\"sku\":\"TEST1\",\"price\":\"50\",\"cost\":\"30\",\"status\":\"active\",\"min_stock\":\"2\",\"reorder_level\":\"5\",\"description\":\"initial\"}','127.0.0.1','curl/8.18.0','2026-09-07 10:00:50'),(41,1,'product_updated','products',4,'{\"id\":4,\"sku\":\"TEST1\",\"name\":\"Nokia3310\",\"category_id\":null,\"brand_id\":null,\"unit_id\":null,\"image\":null,\"barcode\":\"TEST1\",\"qr_code\":null,\"description\":\"initial\",\"price\":\"50.00\",\"cost\":\"30.00\",\"min_stock\":2,\"reorder_level\":5,\"status\":\"active\",\"created_at\":\"2026-09-07 16:00:50\",\"updated_at\":\"2026-09-07 16:00:50\",\"category_name\":null,\"brand_name\":null,\"unit_name\":null,\"unit_symbol\":null}','{\"name\":\"Nokia 3310 Pro\",\"description\":\"updated desc\",\"price\":\"75\",\"cost\":\"40\",\"min_stock\":\"3\",\"reorder_level\":\"8\",\"status\":\"active\",\"sku\":\"TEST1\"}','127.0.0.1','curl/8.18.0','2026-09-07 10:03:03'),(42,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 10:03:14'),(43,1,'product_created','products',6,NULL,'{\"name\":\"TestSku\",\"sku\":\"TST1788775393\",\"price\":\"10\",\"cost\":\"5\",\"status\":\"active\",\"min_stock\":\"1\",\"reorder_level\":\"2\",\"category_id\":\"\",\"brand_id\":\"\",\"unit_id\":\"\",\"description\":\"first desc\"}','127.0.0.1','curl/8.18.0','2026-09-07 10:03:14'),(44,1,'product_updated','products',6,'{\"id\":6,\"sku\":\"TST1788775393\",\"name\":\"TestSku\",\"category_id\":null,\"brand_id\":null,\"unit_id\":null,\"image\":\"uploads\\/products\\/product_6_1788775394.png\",\"barcode\":\"TST1788775393\",\"qr_code\":null,\"description\":\"first desc\",\"price\":\"10.00\",\"cost\":\"5.00\",\"min_stock\":1,\"reorder_level\":2,\"status\":\"active\",\"created_at\":\"2026-09-07 16:03:14\",\"updated_at\":\"2026-09-07 16:03:14\",\"category_name\":null,\"brand_name\":null,\"unit_name\":null,\"unit_symbol\":null}','{\"name\":\"TestSku Edited\",\"description\":\"edited desc\",\"price\":\"12.5\",\"cost\":\"6\",\"min_stock\":\"4\",\"reorder_level\":\"9\",\"status\":\"inactive\",\"brand_id\":\"1\",\"sku\":\"TST1788775393\",\"image\":\"uploads\\/products\\/product_6_1788775394.png\"}','127.0.0.1','curl/8.18.0','2026-09-07 10:03:14'),(45,1,'product_updated','products',3,'{\"id\":3,\"sku\":\"SKU-D066BC\",\"name\":\"Alooz\",\"category_id\":null,\"brand_id\":1,\"unit_id\":8,\"image\":null,\"barcode\":\"SKU-D066BC\",\"qr_code\":null,\"description\":\"\",\"price\":\"25.00\",\"cost\":\"0.00\",\"min_stock\":5,\"reorder_level\":20,\"status\":\"active\",\"created_at\":\"2026-09-07 15:58:21\",\"updated_at\":\"2026-09-07 15:58:21\",\"category_name\":null,\"brand_name\":\"Bombay Sweets\",\"unit_name\":\"Pack\",\"unit_symbol\":\"pk\"}','{\"name\":\"Alooz\",\"price\":\"25.00\",\"cost\":\"0.00\",\"min_stock\":\"5\",\"reorder_level\":\"20\",\"status\":\"active\",\"category_id\":\"4\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"sku\":\"SKU-D066BC\",\"barcode\":\"SKU-D066BC\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 10:03:58'),(46,1,'product_created','products',7,NULL,'{\"name\":\"chanachur\",\"sku\":\"\",\"category_id\":\"4\",\"brand_id\":\"5\",\"unit_id\":\"8\",\"price\":\"20\",\"cost\":\"5\",\"barcode\":\"\",\"min_stock\":\"100\",\"reorder_level\":\"50\",\"status\":\"active\",\"description\":\"\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 10:05:43'),(47,1,'product_updated','products',7,'{\"id\":7,\"sku\":\"SKU-73DFBD\",\"name\":\"chanachur\",\"category_id\":4,\"brand_id\":5,\"unit_id\":8,\"image\":null,\"barcode\":\"SKU-73DFBD\",\"qr_code\":null,\"description\":\"\",\"price\":\"20.00\",\"cost\":\"5.00\",\"min_stock\":100,\"reorder_level\":50,\"status\":\"active\",\"created_at\":\"2026-09-07 16:05:43\",\"updated_at\":\"2026-09-07 16:05:43\",\"category_name\":\"Snacks\",\"brand_name\":\"Khan\",\"unit_name\":\"Pack\",\"unit_symbol\":\"pk\"}','{\"name\":\"chanachur\",\"price\":\"20.00\",\"cost\":\"5.00\",\"min_stock\":\"100\",\"reorder_level\":\"50\",\"status\":\"active\",\"category_id\":\"4\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"sku\":\"SKU-73DFBD\",\"barcode\":\"SKU-73DFBD\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 10:08:48'),(48,1,'product_updated','products',7,'{\"id\":7,\"sku\":\"SKU-73DFBD\",\"name\":\"chanachur\",\"category_id\":4,\"brand_id\":1,\"unit_id\":8,\"image\":null,\"barcode\":\"SKU-73DFBD\",\"qr_code\":null,\"description\":\"\",\"price\":\"20.00\",\"cost\":\"5.00\",\"min_stock\":100,\"reorder_level\":50,\"status\":\"active\",\"created_at\":\"2026-09-07 16:05:43\",\"updated_at\":\"2026-09-07 16:08:48\",\"category_name\":\"Snacks\",\"brand_name\":\"Bombay Sweets\",\"unit_name\":\"Pack\",\"unit_symbol\":\"pk\"}','{\"name\":\"chanachur\",\"price\":\"20.00\",\"cost\":\"5.00\",\"min_stock\":\"100\",\"reorder_level\":\"50\",\"status\":\"active\",\"category_id\":\"4\",\"brand_id\":\"5\",\"unit_id\":\"8\",\"sku\":\"SKU-73DFBD\",\"barcode\":\"SKU-73DFBD\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 10:15:52'),(49,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 10:18:15'),(50,1,'category_created','categories',5,NULL,'{\"name\":\"Drinks\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 10:18:25'),(51,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:23'),(52,1,'supplier_created','suppliers',1,NULL,'{\"company_name\":\"Acme Supplies\",\"contact_person\":\"John Doe\",\"email\":\"john@acme.com\",\"phone\":\"+1234567890\",\"address\":\"123 Supplier Rd\",\"tax_id\":\"TAX-123\",\"rating\":4.5,\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 11:00:23'),(53,1,'customer_created','customers',1,NULL,'{\"name\":\"Jane Customer\",\"phone\":\"+0987654321\",\"email\":\"jane@mail.com\",\"is_vip\":1,\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 11:00:23'),(54,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:39'),(55,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:45'),(56,1,'purchase_created','purchases',2,NULL,'{\"po_number\":\"PO-20260907-6088\"}','127.0.0.1','curl/8.18.0','2026-09-07 11:00:45'),(57,1,'purchase_pending','purchases',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:45'),(58,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:50'),(59,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(60,1,'purchase_pending','purchases',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(61,1,'purchase_approved','purchases',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(62,1,'purchase_received','purchases',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(63,1,'stock_in','inventory',7,NULL,'{\"qty\":10,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(64,1,'purchase_stock_received','purchases',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:00:56'),(65,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:01:18'),(66,1,'stock_out','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:01:19'),(67,1,'sale_created','sales',1,NULL,'{\"invoice_no\":\"INV-20260907-5657\",\"total\":44}','127.0.0.1','curl/8.18.0','2026-09-07 11:01:19'),(68,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:01:46'),(69,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:27'),(70,1,'stock_out','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:27'),(71,1,'sale_created','sales',2,NULL,'{\"invoice_no\":\"INV-20260907-7929\",\"total\":44}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:27'),(72,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:41'),(73,1,'stock_in','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:41'),(74,1,'sale_cancelled','sales',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:41'),(75,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:58'),(76,1,'supplier_created','suppliers',2,NULL,'{\"company_name\":\"TestCo\",\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(77,1,'customer_created','customers',2,NULL,'{\"name\":\"Test Person\",\"phone\":\"111\",\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(78,1,'supplier_deleted','suppliers',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(79,1,'customer_deleted','customers',2,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(80,1,'purchase_payment_added','purchases',2,NULL,'{\"amount\":30}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(81,1,'stock_out','inventory',7,NULL,'{\"qty\":1,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(82,1,'sale_created','sales',3,NULL,'{\"invoice_no\":\"INV-20260907-3771\",\"total\":22}','127.0.0.1','curl/8.18.0','2026-09-07 11:05:59'),(83,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:06:09'),(84,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:07:32'),(85,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:07:58'),(86,1,'stock_out','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:07:58'),(87,1,'sale_created','sales',4,NULL,'{\"invoice_no\":\"INV-20260907-0290\",\"total\":41.5}','127.0.0.1','curl/8.18.0','2026-09-07 11:07:58'),(88,1,'purchase_approved','purchases',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 11:08:22'),(89,1,'purchase_payment_added','purchases',2,NULL,'{\"amount\":1000}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 11:09:12'),(90,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:09:26'),(91,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:09:41'),(92,1,'sale_return','sales',4,NULL,'{\"return_total\":20}','127.0.0.1','curl/8.18.0','2026-09-07 11:09:41'),(93,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:10:51'),(94,1,'stock_out','inventory',7,NULL,'{\"qty\":1,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-07 11:10:51'),(95,1,'sale_created','sales',5,NULL,'{\"invoice_no\":\"INV-20260907-2950\",\"total\":22}','127.0.0.1','curl/8.18.0','2026-09-07 11:10:51'),(96,1,'sale_return','sales',5,NULL,'{\"return_total\":20}','127.0.0.1','curl/8.18.0','2026-09-07 11:10:52'),(97,1,'purchase_received','purchases',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 11:10:54'),(98,1,'purchase_stock_received','purchases',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-09-07 11:10:54'),(99,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 11:20:01'),(100,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 11:27:24'),(101,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 11:27:35'),(104,NULL,'login','users',3,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 12:00:02'),(106,NULL,'login','users',4,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-07 12:01:21'),(107,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:02:29'),(108,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:02:48'),(109,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:02:53'),(110,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:08:43'),(111,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:08:46'),(112,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:12:47'),(113,1,'branch_created','branches',2,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:40:12'),(114,1,'database_backup','settings',NULL,NULL,'{\"file\":\"ez_simbs_backup_2026-09-07_12-44-07.sql\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:44:07'),(115,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 12:45:04'),(116,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 13:04:55'),(117,1,'auto_reorder_created','purchases',3,NULL,'{\"product\":\"chanachur\",\"qty\":96,\"reason\":\"below_reorder_level\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 13:06:10'),(118,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 13:28:39'),(119,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 13:29:28'),(120,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 14:13:26'),(121,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 14:13:29'),(122,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 14:13:32'),(123,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 14:13:50'),(124,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 14:13:54'),(125,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 14:20:17'),(126,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 14:20:21'),(127,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 14:26:52'),(128,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 14:28:20'),(129,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 15:00:10'),(130,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-07 15:00:18'),(131,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-16 16:06:15'),(132,1,'product_created','products',8,NULL,'{\"name\":\"amer juice\",\"sku\":\"\",\"category_id\":\"5\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"price\":\"25\",\"cost\":\"8\",\"barcode\":\"907856674864\",\"min_stock\":\"100\",\"reorder_level\":\"20\",\"status\":\"active\",\"description\":\"\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-16 16:20:19'),(133,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-16 17:04:55'),(134,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-16 17:05:02'),(135,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-16 17:12:38'),(136,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-16 17:14:22'),(137,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-16 17:15:19'),(138,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-16 17:16:10'),(139,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-16 17:23:11'),(140,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-16 17:29:20'),(141,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-16 17:33:19'),(142,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 04:24:50'),(143,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 04:25:39'),(144,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 04:25:41'),(145,1,'product_updated','products',8,'{\"id\":8,\"sku\":\"SKU-3EF55A\",\"name\":\"amer juice\",\"category_id\":5,\"brand_id\":1,\"unit_id\":8,\"image\":null,\"barcode\":\"907856674864\",\"qr_code\":null,\"description\":\"\",\"price\":\"25.00\",\"cost\":\"8.00\",\"min_stock\":100,\"reorder_level\":20,\"status\":\"active\",\"created_at\":\"2026-09-16 22:20:19\",\"updated_at\":\"2026-09-16 22:20:19\",\"category_name\":\"Drinks\",\"brand_name\":\"Bombay Sweets\",\"unit_name\":\"Pack\",\"unit_symbol\":\"pk\"}','{\"name\":\"amer juice\",\"price\":\"25.00\",\"cost\":\"8.00\",\"min_stock\":\"100\",\"reorder_level\":\"20\",\"status\":\"active\",\"category_id\":\"5\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"sku\":\"SKU-3EF55A\",\"barcode\":\"907856674864\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 04:28:41'),(146,1,'product_created','products',9,NULL,'{\"name\":\"lays\",\"sku\":\"\",\"category_id\":\"4\",\"brand_id\":\"5\",\"unit_id\":\"9\",\"price\":\"50\",\"cost\":\"10\",\"barcode\":\"\",\"min_stock\":\"0\",\"reorder_level\":\"0\",\"status\":\"active\",\"description\":\"\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 04:31:48'),(147,1,'product_updated','products',9,'{\"id\":9,\"sku\":\"SKU-437D3B\",\"name\":\"lays\",\"category_id\":4,\"brand_id\":5,\"unit_id\":9,\"image\":null,\"barcode\":\"SKU-437D3B\",\"qr_code\":null,\"description\":\"\",\"price\":\"50.00\",\"cost\":\"10.00\",\"min_stock\":0,\"reorder_level\":0,\"status\":\"active\",\"created_at\":\"2026-09-17 10:31:48\",\"updated_at\":\"2026-09-17 10:41:52\",\"category_name\":\"Snacks\",\"brand_name\":\"Khan\",\"unit_name\":\"chips\",\"unit_symbol\":\"13\"}','{\"name\":\"lays\",\"price\":\"50.00\",\"cost\":\"10.00\",\"min_stock\":\"0\",\"reorder_level\":\"0\",\"status\":\"active\",\"category_id\":\"4\",\"brand_id\":\"5\",\"unit_id\":\"9\",\"sku\":\"SKU-437D3B\",\"barcode\":\"SKU-437D3B\",\"image\":\"uploads\\/products\\/product_9_1789620324.png\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 04:45:24'),(148,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 04:45:46'),(149,1,'product_created','products',10,NULL,'{\"name\":\"TesProduct\",\"sku\":\"SKU-TEST1\",\"price\":\"10\",\"cost\":\"5\",\"status\":\"active\",\"min_stock\":\"2\",\"reorder_level\":\"3\"}','127.0.0.1','curl/8.18.0','2026-09-17 04:45:47'),(150,1,'product_updated','products',10,'{\"id\":10,\"sku\":\"SKU-TEST1\",\"name\":\"TesProduct\",\"category_id\":null,\"brand_id\":null,\"unit_id\":null,\"image\":\"uploads\\/products\\/product_10_1789620347.png\",\"barcode\":\"SKU-TEST1\",\"qr_code\":null,\"description\":\"\",\"price\":\"10.00\",\"cost\":\"5.00\",\"min_stock\":2,\"reorder_level\":3,\"status\":\"active\",\"created_at\":\"2026-09-17 10:45:47\",\"updated_at\":\"2026-09-17 10:45:47\",\"category_name\":null,\"brand_name\":null,\"unit_name\":null,\"unit_symbol\":null}','{\"name\":\"TesProduct Updated\",\"price\":\"12\",\"image\":\"uploads\\/products\\/product_10_1789620353.png\"}','127.0.0.1','curl/8.18.0','2026-09-17 04:45:53'),(151,1,'product_updated','products',10,'{\"id\":10,\"sku\":\"SKU-TEST1\",\"name\":\"TesProduct Updated\",\"category_id\":null,\"brand_id\":null,\"unit_id\":null,\"image\":\"uploads\\/products\\/product_10_1789620353.png\",\"barcode\":\"SKU-TEST1\",\"qr_code\":null,\"description\":\"\",\"price\":\"12.00\",\"cost\":\"5.00\",\"min_stock\":2,\"reorder_level\":3,\"status\":\"active\",\"created_at\":\"2026-09-17 10:45:47\",\"updated_at\":\"2026-09-17 10:45:53\",\"category_name\":null,\"brand_name\":null,\"unit_name\":null,\"unit_symbol\":null}','{\"name\":\"TesProduct V3\",\"price\":\"13\",\"image\":\"uploads\\/products\\/product_10_1789620378.png\"}','127.0.0.1','curl/8.18.0','2026-09-17 04:46:18'),(152,1,'product_updated','products',10,'{\"id\":10,\"sku\":\"SKU-TEST1\",\"name\":\"TesProduct V3\",\"category_id\":null,\"brand_id\":null,\"unit_id\":null,\"image\":\"uploads\\/products\\/product_10_1789620378.png\",\"barcode\":\"SKU-TEST1\",\"qr_code\":null,\"description\":\"\",\"price\":\"13.00\",\"cost\":\"5.00\",\"min_stock\":2,\"reorder_level\":3,\"status\":\"active\",\"created_at\":\"2026-09-17 10:45:47\",\"updated_at\":\"2026-09-17 10:46:18\",\"category_name\":null,\"brand_name\":null,\"unit_name\":null,\"unit_symbol\":null}','{\"image\":\"uploads\\/products\\/product_10_1789620385.png\"}','127.0.0.1','curl/8.18.0','2026-09-17 04:46:25'),(153,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 04:52:22'),(154,1,'product_updated','products',9,'{\"id\":9,\"sku\":\"SKU-437D3B\",\"name\":\"lays\",\"category_id\":4,\"brand_id\":5,\"unit_id\":9,\"image\":\"uploads\\/products\\/product_9_1789620324.png\",\"barcode\":\"SKU-437D3B\",\"qr_code\":null,\"description\":\"\",\"price\":\"50.00\",\"cost\":\"10.00\",\"min_stock\":0,\"reorder_level\":0,\"status\":\"active\",\"created_at\":\"2026-09-17 10:31:48\",\"updated_at\":\"2026-09-17 10:45:24\",\"category_name\":\"Snacks\",\"brand_name\":\"Khan\",\"unit_name\":\"chips\",\"unit_symbol\":\"13\"}','{\"image\":\"uploads\\/products\\/product_9_1789620744.png\"}','127.0.0.1','curl/8.18.0','2026-09-17 04:52:24'),(155,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 04:54:02'),(156,1,'product_created','products',11,NULL,'{\"name\":\"ThrowAwayTest\",\"sku\":\"SKU-TMP-X\",\"price\":\"1\",\"status\":\"active\"}','127.0.0.1','curl/8.18.0','2026-09-17 04:54:02'),(157,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 04:55:14'),(158,1,'database_backup','settings',NULL,NULL,'{\"file\":\"ez_simbs_backup_2026-09-17_06-07-20.sql\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 06:07:20'),(159,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 06:50:14'),(160,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 06:50:30'),(161,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 07:06:09'),(162,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 07:08:49'),(163,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 07:08:59'),(164,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 07:09:14'),(165,1,'product_created','products',12,NULL,'{\"name\":\"chips\",\"sku\":\"\",\"category_id\":\"4\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"price\":\"30\",\"cost\":\"8\",\"barcode\":\"\",\"min_stock\":\"0\",\"reorder_level\":\"0\",\"status\":\"active\",\"description\":\"\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 07:11:04'),(166,1,'product_updated','products',12,'{\"id\":12,\"sku\":\"SKU-81646F\",\"name\":\"chips\",\"category_id\":4,\"brand_id\":1,\"unit_id\":8,\"image\":\"uploads\\/products\\/product_12_1789629064.png\",\"barcode\":\"SKU-81646F\",\"qr_code\":null,\"description\":\"\",\"price\":\"30.00\",\"cost\":\"8.00\",\"min_stock\":0,\"reorder_level\":0,\"status\":\"active\",\"created_at\":\"2026-09-17 13:11:04\",\"updated_at\":\"2026-09-17 13:11:04\",\"category_name\":\"Snacks\",\"brand_name\":\"Bombay Sweets\",\"unit_name\":\"Pack\",\"unit_symbol\":\"pk\"}','{\"name\":\"chips\",\"price\":\"30.00\",\"cost\":\"8.00\",\"min_stock\":\"0\",\"reorder_level\":\"0\",\"status\":\"inactive\",\"category_id\":\"4\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"sku\":\"SKU-81646F\",\"barcode\":\"SKU-81646F\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 07:11:59'),(167,1,'category_created','categories',6,NULL,'{\"name\":\"chocolates\",\"parent_id\":\"\",\"status\":\"active\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 07:14:54'),(168,1,'branch_created','branches',3,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 07:18:59'),(169,1,'branch_updated','branches',3,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 07:19:11'),(170,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 07:22:31'),(171,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:26:42'),(172,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:30:06'),(173,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:31:51'),(174,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:31:55'),(175,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:35:12'),(176,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:37:26'),(177,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 10:38:55'),(178,5,'order_created','orders',3,NULL,'{\"order_no\":\"ORD-20260917-80662\",\"total\":220}','127.0.0.1','curl/8.18.0','2026-09-17 10:40:03'),(179,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 10:41:31'),(180,NULL,'password_reset','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 10:42:39'),(181,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 10:42:40'),(182,5,'review_added','products',9,NULL,'{\"rating\":5}','127.0.0.1','curl/8.18.0','2026-09-17 10:43:32'),(183,NULL,'user_registered','users',6,NULL,'{\"role\":\"manager\"}','127.0.0.1','curl/8.18.0','2026-09-17 10:45:44'),(184,NULL,'user_registered','users',7,NULL,'{\"role\":\"customer\"}','127.0.0.1','curl/8.18.0','2026-09-17 10:45:44'),(185,8,'login','users',8,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 10:48:31'),(186,9,'login','users',9,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 10:48:31'),(187,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 10:48:31'),(188,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 10:48:32'),(189,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 10:48:32'),(190,8,'login','users',8,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:52:46'),(191,8,'logout','users',8,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:53:09'),(192,9,'login','users',9,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:53:28'),(193,9,'logout','users',9,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:53:57'),(194,10,'login','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:54:07'),(195,10,'logout','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:54:39'),(196,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 10:55:22'),(197,5,'logout','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:04:57'),(198,NULL,'user_registered','users',11,NULL,'{\"role\":\"customer\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:07:37'),(199,11,'login','users',11,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:07:52'),(200,11,'logout','users',11,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:08:00'),(201,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:08:11'),(202,8,'login','users',8,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:08:12'),(203,NULL,'password_reset','users',11,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:09:03'),(204,11,'login','users',11,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:09:22'),(205,11,'logout','users',11,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:09:32'),(206,1,'session_terminated','user_sessions',3,'{\"user_id\":8}',NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:10:39'),(207,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:11:01'),(208,1,'session_terminated','user_sessions',2,'{\"user_id\":1}',NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:11:15'),(209,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:12:27'),(210,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:12:33'),(211,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:12:33'),(212,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:14:38'),(213,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:14:38'),(214,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:14:39'),(215,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:19:05'),(216,1,'product_updated','products',12,'{\"id\":12,\"sku\":\"SKU-81646F\",\"name\":\"chips\",\"category_id\":4,\"brand_id\":1,\"unit_id\":8,\"image\":\"uploads\\/products\\/product_12_1789629064.png\",\"barcode\":\"SKU-81646F\",\"qr_code\":null,\"description\":\"\",\"price\":\"30.00\",\"cost\":\"8.00\",\"min_stock\":0,\"reorder_level\":0,\"status\":\"inactive\",\"created_at\":\"2026-09-17 13:11:04\",\"updated_at\":\"2026-09-17 13:11:59\",\"category_name\":\"Snacks\",\"brand_name\":\"Bombay Sweets\",\"unit_name\":\"Pack\",\"unit_symbol\":\"pk\"}','{\"name\":\"chips\",\"price\":\"30.00\",\"cost\":\"8.00\",\"min_stock\":\"0\",\"reorder_level\":\"0\",\"status\":\"active\",\"category_id\":\"4\",\"brand_id\":\"1\",\"unit_id\":\"8\",\"sku\":\"SKU-81646F\",\"barcode\":\"SKU-81646F\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:19:35'),(217,1,'product_deleted','products',12,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:19:43'),(218,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 11:20:03'),(219,1,'session_terminated','user_sessions',10,'{\"user_id\":1}',NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:20:58'),(220,NULL,'logout','users',NULL,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:25:38'),(221,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:46:55'),(222,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 11:47:15'),(223,NULL,'store_registered','stores',2,NULL,'{\"store_name\":\"Test Corner Shop\",\"owner\":\"Test Owner\"}','127.0.0.1','curl/8.18.0','2026-09-17 12:41:41'),(224,NULL,'login','users',12,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 12:41:41'),(225,8,'login','users',8,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 12:43:04'),(226,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 12:43:05'),(227,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 12:43:16'),(228,8,'login','users',8,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 12:43:16'),(229,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:45:07'),(230,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:45:07'),(231,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:45:08'),(232,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:45:08'),(233,1,'session_terminated','user_sessions',14,'{\"user_id\":5}',NULL,'127.0.0.1','node','2026-09-17 12:45:08'),(234,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:45:08'),(235,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:45:08'),(236,1,'session_terminate_others','user_sessions',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:45:09'),(237,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:14'),(238,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:15'),(239,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:15'),(240,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:15'),(241,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:16'),(242,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:16'),(243,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:17'),(244,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:17'),(245,1,'session_terminated','user_sessions',17,'{\"user_id\":8}',NULL,'127.0.0.1','node','2026-09-17 12:46:17'),(246,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:17'),(247,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:18'),(248,1,'session_terminate_others','user_sessions',1,NULL,NULL,'127.0.0.1','node','2026-09-17 12:46:18'),(249,NULL,'store_registered','stores',3,NULL,'{\"store_name\":\"linea\",\"owner\":\"Alisha Johura\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 12:47:08'),(250,13,'login','users',13,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 12:47:09'),(251,NULL,'store_registered','stores',4,NULL,'{\"store_name\":\"Flow Test Store 1789649886\",\"owner\":\"Test Owner\"}','127.0.0.1','curl/8.18.0','2026-09-17 12:58:07'),(252,NULL,'login','users',14,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 12:58:25'),(253,13,'logout','users',13,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 12:59:41'),(254,13,'login','users',13,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 13:00:42'),(255,8,'login','users',8,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:11:33'),(256,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:11:34'),(257,1,'logout','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:11:34'),(258,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:11:50'),(259,1,'logout','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:11:50'),(260,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:11:56'),(261,NULL,'user_registered','users',15,NULL,'{\"role\":\"cashier\"}','127.0.0.1','curl/8.18.0','2026-09-17 13:12:03'),(262,NULL,'login','users',15,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:12:08'),(263,13,'logout','users',13,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 13:12:47'),(264,13,'login','users',13,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 13:13:45'),(265,13,'logout','users',13,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 13:17:43'),(266,13,'login','users',13,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 13:18:16'),(267,13,'logout','users',13,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 13:40:49'),(268,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:46:23'),(269,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:46:23'),(270,NULL,'user_registered','users',16,NULL,'{\"role\":\"customer\"}','127.0.0.1','curl/8.18.0','2026-09-17 13:46:33'),(271,NULL,'user_registered','users',17,NULL,'{\"role\":\"customer\"}','127.0.0.1','curl/8.18.0','2026-09-17 13:46:34'),(272,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:46:38'),(273,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:46:38'),(274,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:46:45'),(275,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:46:52'),(276,8,'login','users',8,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 13:46:52'),(277,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 13:48:41'),(278,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 13:48:41'),(279,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 13:48:42'),(280,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 13:48:45'),(281,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 13:48:46'),(282,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 13:48:46'),(283,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 13:48:46'),(284,1,'session_terminated','user_sessions',18,'{\"user_id\":10}',NULL,'127.0.0.1','node','2026-09-17 13:48:46'),(285,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 13:48:46'),(286,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 13:48:47'),(287,1,'session_terminate_others','user_sessions',1,NULL,NULL,'127.0.0.1','node','2026-09-17 13:48:47'),(288,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:04:31'),(289,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:09:53'),(290,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:09:56'),(291,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 14:19:05'),(292,13,'login','users',13,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 14:24:52'),(293,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:25:15'),(294,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:25:58'),(295,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:26:30'),(296,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:27:05'),(297,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:27:16'),(298,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:27:28'),(299,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:27:34'),(300,8,'login','users',8,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:27:46'),(301,8,'logout','users',8,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:28:30'),(302,9,'login','users',9,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:28:40'),(303,9,'logout','users',9,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:29:17'),(304,10,'login','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:29:30'),(305,10,'logout','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:30:21'),(306,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:30:32'),(307,5,'logout','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:30:54'),(308,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:44:59'),(309,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:45:15'),(310,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 14:46:27'),(311,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 15:04:09'),(312,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 15:17:19'),(313,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 15:24:59'),(314,8,'login','users',8,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 15:25:07'),(315,8,'login','users',8,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 15:31:18'),(316,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 15:31:23'),(317,8,'logout','users',8,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 15:34:42'),(318,9,'login','users',9,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 15:34:48'),(319,9,'logout','users',9,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 15:35:47'),(320,8,'login','users',8,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 15:35:51'),(321,8,'login','users',8,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 15:37:36'),(322,8,'logout','users',8,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 15:41:38'),(323,9,'login','users',9,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 15:41:47'),(324,9,'login','users',9,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 15:42:40'),(325,9,'login','users',9,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 15:46:43'),(326,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 15:57:02'),(327,1,'product_created','products',14,NULL,'{\"name\":\"Multi-Branch Test Item\",\"price\":50,\"cost\":20,\"min_stock\":2,\"reorder_level\":3}','127.0.0.1','curl/8.18.0','2026-09-17 15:57:02'),(328,1,'product_created','products',15,NULL,'{\"name\":\"Multi-Branch Test Item\",\"price\":50,\"cost\":20,\"min_stock\":2,\"reorder_level\":3}','127.0.0.1','curl/8.18.0','2026-09-17 15:58:30'),(329,1,'stock_in','inventory',15,NULL,'{\"qty\":10,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-17 15:58:44'),(330,1,'stock_in','inventory',15,NULL,'{\"qty\":5,\"branch_id\":3}','127.0.0.1','curl/8.18.0','2026-09-17 15:58:44'),(331,1,'stock_adjustment','inventory',15,'{\"qty\":0}','{\"qty\":7,\"reason\":\"restock\"}','127.0.0.1','curl/8.18.0','2026-09-17 15:58:44'),(332,1,'stock_out','inventory',15,NULL,'{\"qty\":3,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-17 15:58:59'),(333,1,'stock_in','inventory',15,NULL,'{\"qty\":3,\"branch_id\":3}','127.0.0.1','curl/8.18.0','2026-09-17 15:58:59'),(334,1,'stock_transfer','inventory',15,NULL,'{\"from\":1,\"to\":3,\"qty\":3}','127.0.0.1','curl/8.18.0','2026-09-17 15:58:59'),(335,1,'stock_out','inventory',15,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-17 15:59:32'),(336,1,'sale_created','sales',11,NULL,'{\"invoice_no\":\"INV-20260917-6919\",\"total\":0}','127.0.0.1','curl/8.18.0','2026-09-17 15:59:32'),(337,9,'login','users',9,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:10:56'),(338,9,'login','users',9,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:11:08'),(339,9,'logout','users',9,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:12:28'),(340,10,'login','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:12:34'),(341,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:14:30'),(342,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:17:18'),(343,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:20:16'),(344,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:20:39'),(345,10,'stock_out','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-17 16:20:39'),(346,10,'sale_created','sales',12,NULL,'{\"invoice_no\":\"INV-20260917-4768\",\"total\":220}','127.0.0.1','curl/8.18.0','2026-09-17 16:20:39'),(347,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:22:10'),(348,10,'stock_out','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-17 16:22:10'),(349,10,'sale_created','sales',14,NULL,'{\"invoice_no\":\"INV-20260917-1017\",\"total\":220}','127.0.0.1','curl/8.18.0','2026-09-17 16:22:10'),(350,10,'stock_out','inventory',7,NULL,'{\"qty\":1,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-17 16:22:10'),(351,10,'sale_created','sales',15,NULL,'{\"invoice_no\":\"INV-20260917-2276\",\"total\":110}','127.0.0.1','curl/8.18.0','2026-09-17 16:22:10'),(352,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:22:29'),(353,10,'stock_out','inventory',7,NULL,'{\"qty\":2,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-17 16:22:29'),(354,10,'sale_created','sales',17,NULL,'{\"invoice_no\":\"INV-20260917-6460\",\"total\":220}','127.0.0.1','curl/8.18.0','2026-09-17 16:22:29'),(355,10,'stock_out','inventory',7,NULL,'{\"qty\":1,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-17 16:22:29'),(356,10,'sale_created','sales',18,NULL,'{\"invoice_no\":\"INV-20260917-2905\",\"total\":110}','127.0.0.1','curl/8.18.0','2026-09-17 16:22:29'),(357,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:23:19'),(358,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:23:19'),(359,9,'login','users',9,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:23:19'),(360,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:24:19'),(361,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:24:19'),(362,10,'login','users',10,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:28:09'),(363,10,'stock_out','inventory',7,NULL,'{\"qty\":1,\"branch_id\":1}','127.0.0.1','curl/8.18.0','2026-09-17 16:28:09'),(364,10,'sale_created','sales',19,NULL,'{\"invoice_no\":\"INV-20260917-8757\",\"total\":110}','127.0.0.1','curl/8.18.0','2026-09-17 16:28:09'),(365,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:28:10'),(366,10,'logout','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:29:44'),(367,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:30:21'),(368,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:39:04'),(369,5,'profile_updated','users',5,NULL,'{\"name\":\"Test Customer\",\"phone\":\"+8801700000000\"}','127.0.0.1','curl/8.18.0','2026-09-17 16:39:10'),(370,5,'profile_updated','users',5,NULL,'{\"name\":\"Test Customer\",\"phone\":\"+8801700000000\",\"avatar\":\"uploads\\/avatars\\/user_5_1789663156.png\"}','127.0.0.1','curl/8.18.0','2026-09-17 16:39:16'),(371,5,'password_changed','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:39:20'),(372,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:39:24'),(373,5,'password_changed','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:39:24'),(374,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:39:48'),(375,10,'login','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:40:36'),(376,10,'logout','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:42:10'),(377,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:44:37'),(378,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:45:44'),(379,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:48:57'),(380,9,'login','users',9,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:49:34'),(381,9,'logout','users',9,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:50:17'),(382,10,'login','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:50:22'),(383,10,'logout','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:50:29'),(384,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:50:34'),(385,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 16:57:42'),(386,5,'order_created','orders',4,NULL,'{\"order_no\":\"ORD-20260917-78243\",\"total\":154}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 16:58:49'),(387,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 16:58:57'),(388,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 16:58:59'),(389,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 16:59:01'),(390,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 16:59:02'),(391,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 16:59:06'),(392,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 16:59:11'),(393,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 16:59:14'),(394,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 16:59:16'),(395,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 16:59:18'),(396,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 16:59:27'),(397,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 17:01:44'),(398,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 17:02:39'),(399,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 17:02:41'),(400,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/152.0.0.0 Safari/537.36','2026-09-17 17:02:44'),(401,5,'order_created','orders',5,NULL,'{\"order_no\":\"ORD-20260917-13111\",\"total\":55}','127.0.0.1','curl/8.18.0','2026-09-17 17:03:30'),(402,5,'profile_updated','users',5,NULL,'{\"name\":\"Test Customer\",\"phone\":\"\",\"avatar\":\"uploads\\/avatars\\/user_5_1789664797.png\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 17:06:37'),(403,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:52'),(404,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:52'),(405,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:52'),(406,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:53'),(407,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:53'),(408,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:53'),(409,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:54'),(410,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:54'),(411,1,'session_terminated','user_sessions',20,'{\"user_id\":8}',NULL,'127.0.0.1','node','2026-09-17 17:07:54'),(412,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:54'),(413,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:55'),(414,1,'session_terminate_others','user_sessions',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:07:55'),(415,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:00'),(416,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:01'),(417,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:01'),(418,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:01'),(419,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:02'),(420,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:02'),(421,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:02'),(422,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:02'),(423,1,'session_terminated','user_sessions',40,'{\"user_id\":8}',NULL,'127.0.0.1','node','2026-09-17 17:08:02'),(424,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:03'),(425,1,'login','users',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:03'),(426,1,'session_terminate_others','user_sessions',1,NULL,NULL,'127.0.0.1','node','2026-09-17 17:08:03'),(427,5,'logout','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 17:11:50'),(428,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 17:11:59'),(429,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 17:32:33'),(430,5,'logout','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 17:37:19'),(431,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 17:37:30'),(432,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 17:42:07'),(433,5,'order_created','orders',6,NULL,'{\"order_no\":\"ORD-20260917-32373\",\"total\":110}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:155.0) Gecko/20100101 Firefox/155.0','2026-09-17 17:46:49'),(434,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 17:49:26'),(435,5,'login','users',5,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-17 17:55:42'),(436,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:156.0) Gecko/20100101 Firefox/156.0','2026-09-28 12:39:04'),(437,1,'session_terminated','user_sessions',155,'{\"user_id\":1}',NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:156.0) Gecko/20100101 Firefox/156.0','2026-09-28 12:40:09'),(438,1,'login','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:156.0) Gecko/20100101 Firefox/156.0','2026-09-28 12:40:15'),(1001,1001,'user.login','users',1001,NULL,'role=admin store=1 branch=1','103.10.12.4','Chrome 124.0 / Windows','2026-09-27 04:02:00'),(1002,1002,'user.login','users',1002,NULL,'role=manager store=1 branch=2','103.10.12.7','Firefox 126.0 / macOS','2026-09-27 03:41:00'),(1003,1003,'user.login','users',1003,NULL,'role=branch_manager store=1 branch=3','103.10.12.11','Edge 124.0 / Windows','2026-09-26 12:12:00'),(1004,1004,'user.login','users',1004,NULL,'role=cashier store=1 branch=1','103.10.12.15','Chrome 124.0 / Windows','2026-09-27 05:30:00'),(1005,1005,'user.login','users',1005,NULL,'role=customer store=1','59.42.10.3','Chrome Mobile 124.0 / iPhone','2026-09-25 14:05:00'),(1006,10,'sale.create','sales',1020,'NULL','total=715.00 status=completed method=mobile','103.10.12.15','Chrome 124.0 / Windows','2026-09-27 06:15:00'),(1007,10,'sale.create','sales',1017,'NULL','total=275.00 status=completed method=mixed','103.10.12.15','Chrome 124.0 / Windows','2026-09-20 03:50:00'),(1008,10,'sale.hold','sales',1008,'status=completed','status=held','103.10.12.15','Chrome 124.0 / Windows','2026-08-26 13:44:00'),(1009,10,'sale.cancel','sales',1019,'status=completed','status=cancelled','103.10.12.15','Chrome 124.0 / Windows','2026-09-25 09:25:00'),(1010,10,'sale.return','sales',1004,'status=completed','status=returned','103.10.12.15','Chrome 124.0 / Windows','2026-08-12 05:00:00'),(1011,1,'purchase.approve','purchases',1003,'status=pending','status=approved','103.10.12.4','Chrome 124.0 / Windows','2026-06-11 04:05:00'),(1012,1,'purchase.receive','purchases',1004,'status=approved','status=received','103.10.12.4','Chrome 124.0 / Windows','2026-06-15 05:00:00'),(1013,8,'purchase.create','purchases',1017,'NULL','status=draft total=2250.00','103.10.12.7','Firefox 126.0 / macOS','2026-08-03 02:30:00'),(1014,1,'purchase.cancel','purchases',1016,'status=pending','status=cancelled','103.10.12.4','Chrome 124.0 / Windows','2026-08-01 04:00:00'),(1015,9,'stock.adjustment','products',1003,'qty=62','qty=60','103.10.12.11','Edge 124.0 / Windows','2026-09-20 11:00:00'),(1016,9,'stock.damage','products',1009,'qty=16','qty=15','103.10.12.11','Edge 124.0 / Windows','2026-09-21 05:30:00'),(1017,1,'stock.transfer','inventory',1004,'branch=1 qty=45','branch=2 qty=65','103.10.12.4','Chrome 124.0 / Windows','2026-08-05 08:20:00'),(1018,1,'product.update','products',1020,'status=active','status=inactive','103.10.12.4','Chrome 124.0 / Windows','2026-07-30 03:19:00'),(1019,1,'order.create','orders',1016,'NULL','total=5280.00 status=delivered','59.42.10.3','Chrome Mobile 124.0 / iPhone','2026-09-20 05:00:00'),(1020,1,'review.create','product_reviews',1012,'NULL','rating=5 status=approved','59.42.10.3','Chrome Mobile 124.0 / iPhone','2026-09-12 06:00:00'),(1023,NULL,'login','users',1001,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-28 13:22:37'),(1024,NULL,'login','users',1005,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-28 13:23:01'),(1025,1,'product_updated','products',1003,'{\"id\":1003,\"sku\":\"SEED-SKU-1003\",\"name\":\"Red Bull Energy Drink 250ml\",\"category_id\":1003,\"brand_id\":1019,\"unit_id\":1010,\"image\":\"assets\\/images\\/products\\/seed-03.svg\",\"barcode\":\"8801000001003\",\"qr_code\":null,\"description\":\"Sugar-free energy drink, 250ml slim can.\",\"price\":\"220.00\",\"cost\":\"185.00\",\"min_stock\":8,\"reorder_level\":20,\"total_stock\":108,\"branch_count\":2,\"status\":\"active\",\"created_at\":\"2026-06-26 09:02:00\",\"updated_at\":\"2026-09-19 09:02:00\",\"category_name\":\"Energy Drinks\",\"brand_name\":\"Red Bull\",\"unit_name\":\"Can\",\"unit_symbol\":\"can\"}','{\"name\":\"Red Bull Energy Drink 250ml\",\"description\":\"Sugar-free energy drink, 250ml slim can.\",\"price\":\"220.00\",\"cost\":\"185.00\",\"min_stock\":\"8\",\"reorder_level\":\"20\",\"status\":\"active\",\"category_id\":\"1003\",\"brand_id\":\"1019\",\"unit_id\":\"1010\",\"sku\":\"SEED-SKU-1003\",\"barcode\":\"8801000001003\",\"image\":\"uploads\\/products\\/product_1003_1790602264.png\"}','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:156.0) Gecko/20100101 Firefox/156.0','2026-09-28 13:31:04'),(1026,1,'login','users',1,NULL,NULL,'127.0.0.1','curl/8.18.0','2026-09-28 14:05:52'),(1027,1,'logout','users',1,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:156.0) Gecko/20100101 Firefox/156.0','2026-09-28 15:20:46'),(1028,10,'login','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:156.0) Gecko/20100101 Firefox/156.0','2026-09-28 15:20:52'),(1029,10,'logout','users',10,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:156.0) Gecko/20100101 Firefox/156.0','2026-09-28 16:05:31'),(1030,5,'login','users',5,NULL,NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:156.0) Gecko/20100101 Firefox/156.0','2026-09-28 16:05:35');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `position` enum('hero','mid','bottom') DEFAULT 'hero',
  `sort_order` int DEFAULT '0',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1025 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
INSERT INTO `banners` VALUES (1,'Run your store — online & offline','Inventory, billing, POS and a web storefront — all synced in one powerful dashboard.',NULL,NULL,'hero',1,'active','2026-09-17 10:29:35'),(2,'From counter to doorstep, covered','Real-time stock, instant invoicing and a web storefront your customers will love.',NULL,NULL,'hero',2,'active','2026-09-17 10:29:35'),(1001,'Seed Banner - Big Sale','Up to 50% off - this row is INACTIVE, activate it to test the hero slider.','assets/images/products/seed-01.svg','store.php','hero',10,'inactive','2026-06-30 04:00:00'),(1002,'Seed Banner - New Arrivals','Fresh products just landed - INACTIVE by design.','assets/images/products/seed-02.svg','store.php','hero',20,'inactive','2026-06-30 04:01:00'),(1003,'Seed Banner - Groceries','Everyday essentials at low prices.','assets/images/products/seed-03.svg','store.php?category=1','hero',30,'inactive','2026-06-30 04:02:00'),(1004,'Seed Banner - Beverages','Cold drinks for hot days.','assets/images/products/seed-04.svg','store.php?category=1001','hero',40,'inactive','2026-06-30 04:03:00'),(1005,'Seed Banner - Snacks','Snacks and chips bundle deals.','assets/images/products/seed-05.svg','store.php?category=1005','hero',50,'inactive','2026-06-30 04:04:00'),(1006,'Seed Banner - Personal Care','Care for the whole family.','assets/images/products/seed-06.svg','store.php?category=1011','hero',60,'inactive','2026-06-30 04:05:00'),(1007,'Seed Banner - Electronics','Phones and accessories at the best price.','assets/images/products/seed-07.svg','store.php?category=1017','hero',70,'inactive','2026-06-30 04:06:00'),(1008,'Seed Banner - Dairy','Milk powder and frozen treats.','assets/images/products/seed-08.svg','store.php?category=1008','hero',80,'inactive','2026-06-30 04:07:00'),(1009,'Seed Banner - Household','Cleaning supplies for a spotless home.','assets/images/products/seed-09.svg','store.php?category=1015','hero',90,'inactive','2026-06-30 04:08:00'),(1010,'Seed Banner - Stationery','Notebooks and pens for the office.','assets/images/products/seed-10.svg','store.php?category=1019','hero',100,'inactive','2026-06-30 04:09:00'),(1011,'Seed Banner - VIP Members','Exclusive discounts for VIP customers.','assets/images/products/seed-11.svg','dashboard.php','mid',110,'inactive','2026-06-30 04:10:00'),(1012,'Seed Banner - Free Delivery','Free delivery on orders over 2000.','assets/images/products/seed-12.svg','store.php','mid',120,'inactive','2026-06-30 04:11:00'),(1013,'Seed Banner - Loyalty Points','Earn points on every purchase.','assets/images/products/seed-13.svg','dashboard.php','mid',130,'inactive','2026-06-30 04:12:00'),(1014,'Seed Banner - Mobile Accessories','Cases, earbuds and drives.','assets/images/products/seed-14.svg','store.php?category=1018','mid',140,'inactive','2026-06-30 04:13:00'),(1015,'Seed Banner - Weekly Deals','Deals refreshed every Monday.','assets/images/products/seed-15.svg','store.php','mid',150,'inactive','2026-06-30 04:14:00'),(1016,'Seed Banner - New Store Outlet','Now open in Banani.','assets/images/products/seed-16.svg','store.php','mid',160,'inactive','2026-06-30 04:15:00'),(1017,'Seed Banner - Bulk Orders','Special rates on bulk buying.','assets/images/products/seed-17.svg','store.php','mid',170,'inactive','2026-06-30 04:16:00'),(1018,'Seed Banner - Return Policy','7 day no-quibble returns.','assets/images/products/seed-18.svg','store.php','bottom',180,'inactive','2026-06-30 04:17:00'),(1019,'Seed Banner - Support','Need help? We are on WhatsApp.','assets/images/products/seed-19.svg','dashboard.php','bottom',190,'inactive','2026-06-30 04:18:00'),(1020,'Seed Banner - Thank You','See you again soon.','assets/images/products/seed-20.svg','store.php','bottom',200,'inactive','2026-06-30 04:19:00'),(1021,'Run your store — online & offline','Inventory, billing, POS and a web storefront — all synced in one powerful dashboard.',NULL,NULL,'hero',1,'active','2026-09-28 15:11:55'),(1022,'From counter to doorstep, covered','Real-time stock, instant invoicing and a web storefront your customers will love.',NULL,NULL,'hero',2,'active','2026-09-28 15:11:55'),(1023,'Run your store — online & offline','Inventory, billing, POS and a web storefront — all synced in one powerful dashboard.',NULL,NULL,'hero',1,'active','2026-09-28 15:12:07'),(1024,'From counter to doorstep, covered','Real-time stock, instant invoicing and a web storefront your customers will love.',NULL,NULL,'hero',2,'active','2026-09-28 15:12:07');
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `manager_id` int DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`),
  KEY `idx_branch_store` (`store_id`),
  CONSTRAINT `branches_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_branch_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,1,'Main Branch','123 Business St, City','+1234567890',NULL,'active','2026-09-07 08:46:09'),(2,1,'Mirpur','','',NULL,'active','2026-09-07 12:40:12'),(3,1,'khilgaon','','',NULL,'active','2026-09-17 07:18:59'),(5,3,'linea (Main)','','011222888',NULL,'active','2026-09-17 12:47:08'),(1001,1,'Banani Outlet','House 12, Road 5, Banani, Dhaka-1213','+8801711000101',9,'active','2026-06-01 03:00:00'),(1002,1,'Uttara Outlet','Sector 7, Uttara, Dhaka-1230','+8801711000102',9,'active','2026-06-01 03:05:00'),(1003,1,'Warehouse (Main)','Dhanmondi Road 27, Dhaka-1209','+8801711000103',8,'active','2026-06-01 03:10:00'),(1004,1,'Old Closed Outlet','Mirpur DOHS, Dhaka-1216','+8801711000104',NULL,'inactive','2026-06-01 03:15:00'),(1005,1001,'Sunrise Main','House 12, Road 5, Banani, Dhaka-1213','+8801711000105',1006,'active','2026-06-02 03:00:00'),(1006,1001,'Sunrise Gulshan','Gulshan 2, Dhaka-1212','+8801711000106',1006,'active','2026-06-02 03:05:00'),(1007,1001,'Sunrise Uttara','Sector 7, Uttara, Dhaka-1230','+8801711000107',1006,'active','2026-06-02 03:10:00'),(1008,1002,'Blue Ocean Main','Plot 88, Agrabad C/A, Chittagong-4000','+8801711000108',1007,'active','2026-06-03 03:00:00'),(1009,1002,'Blue Ocean Agrabad','CDA Avenue, Chittagong-4000','+8801711000109',1007,'active','2026-06-03 03:05:00'),(1010,1003,'Green Leaf Main','House 7, Dhanmondi Road 27, Dhaka-1209','+8801711000110',1008,'active','2026-06-04 03:00:00'),(1011,1003,'Green Leaf Dhanmondi','Road 27, Dhaka-1209','+8801711000111',1008,'active','2026-06-04 03:05:00'),(1012,1004,'Star Electronics Main','Shop 210, Bashundhara City, Dhaka-1222','+8801711000112',1009,'active','2026-06-05 03:00:00'),(1013,1004,'Star Electronics Banani','Banani, Dhaka-1213','+8801711000113',1009,'active','2026-06-05 03:05:00'),(1014,1005,'City Fresh Main','Kachukhet Mor, Mirpur DOHS, Dhaka-1216','+8801711000114',1010,'active','2026-06-06 03:00:00'),(1015,1006,'Apex Hardware Main','Naya Paltan, Islampur, Dhaka-1100','+8801711000115',1011,'active','2026-06-07 03:00:00'),(1016,1007,'Silver Fashion Main','Bashundhara City Level 3, Dhaka-1222','+8801711000116',1012,'active','2026-06-08 03:00:00'),(1017,1008,'Metro Pharmacy Main','Kachfiruz, Shyamoli, Dhaka-1207','+8801711000117',1013,'active','2026-06-09 03:00:00'),(1018,1001,'Sunrise Warehouse','Jashore Sadar, Jashore-7400','+8801711000118',1015,'active','2026-06-10 03:00:00'),(1019,1002,'Blue Ocean Sylhet (Closed)','Zindabazar, Sylhet-3100','+8801711000119',NULL,'inactive','2026-06-11 03:00:00'),(1020,1001,'Sunrise Test Branch','Test Road, Test Area, Dhaka-1000','+8801711000120',NULL,'inactive','2026-06-12 03:00:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Bombay Sweets',NULL,'active','2026-09-07 09:25:11'),(2,'boombay sweets',NULL,'active','2026-09-07 09:25:30'),(3,'Pran',NULL,'inactive','2026-09-07 09:32:40'),(4,'Anwar',NULL,'inactive','2026-09-07 09:39:11'),(5,'Khan',NULL,'active','2026-09-07 09:40:52'),(1001,'Nestle',NULL,'active','2026-06-25 04:00:00'),(1002,'Coca-Cola',NULL,'active','2026-06-25 04:01:00'),(1003,'PepsiCo',NULL,'active','2026-06-25 04:02:00'),(1004,'PRAN',NULL,'active','2026-06-25 04:03:00'),(1005,'Square',NULL,'active','2026-06-25 04:04:00'),(1006,'Rownd',NULL,'active','2026-06-25 04:05:00'),(1007,'Aarong',NULL,'active','2026-06-25 04:06:00'),(1008,'Marico',NULL,'active','2026-06-25 04:07:00'),(1009,'Unilever',NULL,'active','2026-06-25 04:08:00'),(1010,'Pepsodent',NULL,'active','2026-06-25 04:09:00'),(1011,'Savlon',NULL,'active','2026-06-25 04:10:00'),(1012,'Dettol',NULL,'active','2026-06-25 04:11:00'),(1013,'Rin',NULL,'active','2026-06-25 04:12:00'),(1014,'Lux',NULL,'active','2026-06-25 04:13:00'),(1015,'Gillette',NULL,'active','2026-06-25 04:14:00'),(1016,'Vivo',NULL,'active','2026-06-25 04:15:00'),(1017,'Realme',NULL,'active','2026-06-25 04:16:00'),(1018,'Samsung',NULL,'active','2026-06-25 04:17:00'),(1019,'Red Bull',NULL,'active','2026-06-25 04:18:00'),(1020,'Generic Brand (Inactive)',NULL,'inactive','2026-06-25 04:19:00');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_cart` (`user_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (1,5,'2026-09-17 10:38:56','2026-09-17 11:43:08'),(1001,1001,'2026-09-27 04:05:00','2026-09-27 04:05:00'),(1002,1002,'2026-09-27 03:45:00','2026-09-27 03:45:00'),(1003,1003,'2026-09-26 12:20:00','2026-09-26 12:20:00'),(1004,1004,'2026-09-27 05:35:00','2026-09-27 05:35:00'),(1005,1005,'2026-09-27 02:30:00','2026-09-27 04:45:00'),(1006,1006,'2026-09-24 03:00:00','2026-09-24 03:00:00'),(1007,1007,'2026-09-23 11:30:00','2026-09-23 11:30:00'),(1008,1008,'2026-09-22 06:10:00','2026-09-22 06:10:00'),(1009,1009,'2026-09-21 10:00:00','2026-09-21 10:00:00'),(1010,1010,'2026-09-20 13:40:00','2026-09-20 13:40:00'),(1011,1011,'2026-09-19 04:20:00','2026-09-19 04:20:00'),(1012,1012,'2026-09-18 08:10:00','2026-09-18 08:10:00'),(1013,1013,'2026-09-17 10:35:00','2026-09-17 10:35:00'),(1014,1014,'2026-09-16 07:45:00','2026-09-16 07:45:00'),(1015,1015,'2026-09-15 02:00:00','2026-09-15 02:00:00'),(1016,1016,'2026-09-27 01:20:00','2026-09-27 03:15:00'),(1017,1017,'2026-09-13 05:15:00','2026-09-13 05:15:00'),(1018,1018,'2026-09-12 03:00:00','2026-09-12 03:00:00'),(1019,1019,'2026-09-11 04:00:00','2026-09-11 04:00:00'),(1020,1020,'2026-09-10 08:00:00','2026-09-10 08:00:00');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_product` (`cart_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (1001,1005,1001,2,'2026-09-27 02:30:00'),(1002,1005,1003,1,'2026-09-27 02:31:00'),(1003,1005,1006,3,'2026-09-27 02:33:00'),(1004,1005,1010,1,'2026-09-27 02:35:00'),(1005,1005,1013,2,'2026-09-27 02:36:00'),(1006,1006,1002,6,'2026-09-24 03:00:00'),(1007,1006,1007,4,'2026-09-24 03:02:00'),(1008,1006,1008,1,'2026-09-24 03:04:00'),(1009,1006,1009,2,'2026-09-24 03:05:00'),(1010,1006,1011,1,'2026-09-24 03:07:00'),(1011,1012,1004,2,'2026-09-18 08:10:00'),(1012,1012,1012,2,'2026-09-18 08:12:00'),(1013,1012,1013,1,'2026-09-18 08:14:00'),(1014,1012,1015,1,'2026-09-18 08:15:00'),(1015,1012,1016,3,'2026-09-18 08:17:00'),(1016,1014,1003,2,'2026-09-16 07:45:00'),(1017,1014,1005,4,'2026-09-16 07:47:00'),(1018,1014,1014,1,'2026-09-16 07:49:00'),(1019,1016,1001,1,'2026-09-27 01:20:00'),(1020,1016,1006,2,'2026-09-27 01:22:00');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (4,'Snacks',NULL,'active','2026-09-07 09:47:33'),(5,'Drinks',NULL,'active','2026-09-07 10:18:25'),(6,'chocolates',NULL,'active','2026-09-17 07:14:54'),(1001,'Beverages',NULL,'active','2026-06-25 03:00:00'),(1002,'Soft Drinks',1001,'active','2026-06-25 03:01:00'),(1003,'Energy Drinks',1001,'active','2026-06-25 03:02:00'),(1004,'Milk & Juice',1001,'active','2026-06-25 03:03:00'),(1005,'Packaged Foods',NULL,'active','2026-06-25 03:04:00'),(1006,'Snacks & Chips',1005,'active','2026-06-25 03:05:00'),(1007,'Noodles',1005,'active','2026-06-25 03:06:00'),(1008,'Dairy & Frozen',NULL,'active','2026-06-25 03:07:00'),(1009,'Milk Powder',1008,'active','2026-06-25 03:08:00'),(1010,'Frozen Treats',1008,'active','2026-06-25 03:09:00'),(1011,'Personal Care',NULL,'active','2026-06-25 03:10:00'),(1012,'Hair Care',1011,'active','2026-06-25 03:11:00'),(1013,'Oral Care',1011,'active','2026-06-25 03:12:00'),(1014,'Body Care',1011,'active','2026-06-25 03:13:00'),(1015,'Household',NULL,'active','2026-06-25 03:14:00'),(1016,'Cleaning Supplies',1015,'active','2026-06-25 03:15:00'),(1017,'Electronics',NULL,'active','2026-06-25 03:16:00'),(1018,'Mobile Accessories',1017,'active','2026-06-25 03:17:00'),(1019,'Stationery',NULL,'active','2026-06-25 03:18:00'),(1020,'Discontinued Test Category',NULL,'inactive','2026-06-25 03:19:00');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `description` text,
  `type` enum('percent','fixed') DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `min_order` decimal(10,2) DEFAULT '0.00',
  `max_uses` int DEFAULT '0',
  `used_count` int DEFAULT '0',
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1001,'SEEDWELCOME20','20% off your first order','percent',20.00,500.00,100,3,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 06:00:00'),(1002,'SEEDWELCOME50','50% off for new customers','percent',50.00,1000.00,50,1,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 06:01:00'),(1003,'SEEDFLAT100','Flat 100 off on orders over 800','fixed',100.00,800.00,200,7,'2026-07-01 00:00:00','2026-11-30 23:59:59','active','2026-06-30 06:02:00'),(1004,'SEEDFLAT250','Flat 250 off on orders over 2000','fixed',250.00,2000.00,150,12,'2026-07-01 00:00:00','2026-11-30 23:59:59','active','2026-06-30 06:03:00'),(1005,'SEEDBEVER5','5% off on beverages','percent',5.00,200.00,300,22,'2026-07-01 00:00:00','2026-10-31 23:59:59','active','2026-06-30 06:04:00'),(1006,'SEEDSNACK10','10% off on snacks','percent',10.00,300.00,300,18,'2026-07-01 00:00:00','2026-10-31 23:59:59','active','2026-06-30 06:05:00'),(1007,'SEEDCARE15','15% off on personal care','percent',15.00,500.00,200,9,'2026-07-01 00:00:00','2026-12-15 23:59:59','active','2026-06-30 06:06:00'),(1008,'SEEDELEC200','Flat 200 off on electronics over 3000','fixed',200.00,3000.00,80,4,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 06:07:00'),(1009,'SEEDVIP25','25% off for VIP customers','percent',25.00,1000.00,60,15,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 06:08:00'),(1010,'SEEDFREESHIP','Free shipping on any order','fixed',60.00,0.00,500,40,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 06:09:00'),(1011,'SEEDBULK300','Flat 300 off on bulk orders over 5000','fixed',300.00,5000.00,40,2,'2026-07-01 00:00:00','2026-11-30 23:59:59','active','2026-06-30 06:10:00'),(1012,'SEEDSTUDENT12','12% student discount','percent',12.00,400.00,100,5,'2026-08-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 06:11:00'),(1013,'SEEDEID30','30% off for Eid Dhan','percent',30.00,1500.00,40,38,'2026-03-01 00:00:00','2026-04-15 23:59:59','inactive','2026-02-20 06:00:00'),(1014,'SEEDNEWYEAR30','30% off for New Year sale','percent',30.00,1000.00,40,25,'2026-01-01 00:00:00','2026-01-31 23:59:59','inactive','2025-12-20 06:00:00'),(1015,'SEEDEXPIRED10','Expired test coupon','percent',10.00,500.00,100,0,'2026-01-01 00:00:00','2026-02-01 23:59:59','inactive','2025-12-25 06:00:00'),(1016,'SEEDEXHAUSTED','Fully used test coupon','percent',40.00,2000.00,5,5,'2026-07-01 00:00:00','2026-12-31 23:59:59','active','2026-06-30 06:15:00'),(1017,'SEEDFUTURE20','Not started yet','percent',20.00,1000.00,50,0,'2027-01-01 00:00:00','2027-03-31 23:59:59','active','2026-06-30 06:16:00'),(1018,'SEEDNOEXPIRY18','18% off, no end date','percent',18.00,600.00,0,11,'2026-07-01 00:00:00',NULL,'active','2026-06-30 06:17:00'),(1019,'SEEDTESTONLY','Test coupon - do not advertise','fixed',10.00,100.00,10,2,'2026-07-01 00:00:00','2026-10-01 23:59:59','active','2026-06-30 06:18:00'),(1020,'SEEDDISABLED50','Disabled coupon - validation test','percent',50.00,100.00,100,0,'2026-07-01 00:00:00','2026-12-31 23:59:59','inactive','2026-06-30 06:19:00');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Jane Customer','+0987654321','jane@mail.com','','MEM-7D2B0A',14,0.00,1,'','active','2026-09-07 11:00:23','2026-09-07 11:07:58'),(1001,'Rashedul Islam','+8801811000001','rashedul@customer.test','House 5, Road 7, Dhanmondi, Dhaka-1209','SEED-MEM-1001',2450,1250.00,1,'VIP regular since 2024. Prefers WhatsApp updates.','active','2026-06-26 06:00:00','2026-09-20 06:00:00'),(1002,'Shahana Akter','+8801811000002','shahana@customer.test','Flat B3, Road 11, Banani, Dhaka-1213','SEED-MEM-1002',1830,0.00,1,'Wholesale buyer - small quantity discount applies.','active','2026-06-26 06:01:00','2026-09-19 06:01:00'),(1003,'Mahbubur Rahman','+8801811000003','mahbubur@customer.test','House 22, Mirpur DOHS, Dhaka-1216','SEED-MEM-1003',960,340.50,0,'Regular walk-in customer.','active','2026-06-26 06:02:00','2026-09-18 06:02:00'),(1004,'Nasrin Sultana','+8801811000004','nasrin@customer.test','Flat 4B, Shyamoli, Dhaka-1207','SEED-MEM-1004',720,0.00,0,NULL,'active','2026-06-26 06:03:00','2026-09-17 06:03:00'),(1005,'Jahangir Alam','+8801811000005','jahangir@customer.test','House 9, Uttara Sector 4, Dhaka-1230','SEED-MEM-1005',1480,0.00,1,'Often buys in bulk on weekends.','active','2026-06-26 06:04:00','2026-09-16 06:04:00'),(1006,'Rokeya Begum','+8801811000006','rokeya@customer.test','House 41, Kazipara, Mirpur, Dhaka-1216','SEED-MEM-1006',540,75.00,0,NULL,'active','2026-06-26 06:05:00','2026-09-15 06:05:00'),(1007,'Sohel Rana','+8801811000007','sohel@customer.test','House 17, Agrabad, Chittagong-4000','SEED-MEM-1007',1120,0.00,0,NULL,'active','2026-06-26 06:06:00','2026-09-14 06:06:00'),(1008,'Tanvir Ahmed (Walk-in)',NULL,NULL,NULL,'SEED-MEM-1008',60,0.00,0,'No phone / email on file - cash customer.','active','2026-06-26 06:07:00','2026-09-13 06:07:00'),(1009,'Farhana Yasmin','+8801811000009','farhana@customer.test','Flat 7A, Lalmatia, Dhaka-1207','SEED-MEM-1009',890,2150.00,0,'Owes a running balance - settle at next visit.','active','2026-06-26 06:08:00','2026-09-12 06:08:00'),(1010,'Kamrul Hasan','+8801811000010','kamrul@customer.test','House 30, Zindabazar, Sylhet-3100','SEED-MEM-1010',410,0.00,0,NULL,'active','2026-06-26 06:09:00','2026-09-11 06:09:00'),(1011,'Lutfun Nahar','+8801811000011','lutfun@customer.test','House 3, Boyra More, Khulna-9100','SEED-MEM-1011',330,90.00,0,NULL,'active','2026-06-26 06:10:00','2026-09-10 06:10:00'),(1012,'Shafiqur Rahman','+8801811000012','shafiqur@customer.test','House 14, Sherpur Road, Bogura-5400','SEED-MEM-1012',275,0.00,0,NULL,'active','2026-06-26 06:11:00','2026-09-09 06:11:00'),(1013,'Morjina Khatun','+8801811000013','morjina@customer.test','House 55, Tongi Bazar, Gazipur-1710','SEED-MEM-1013',195,0.00,0,NULL,'active','2026-06-26 06:12:00','2026-09-08 06:12:00'),(1014,'Alamgir Hossain','+8801811000014','alamgir@customer.test','House 8, Gulshan 2, Dhaka-1212','SEED-MEM-1014',1580,480.00,1,'Corporate buyer - monthly invoice.','active','2026-06-26 06:13:00','2026-09-07 06:13:00'),(1015,'Rubina Parvin','+8801811000015','rubina@customer.test','Flat 2C, Kalabagan, Dhaka-1207','SEED-MEM-1015',150,0.00,0,NULL,'active','2026-06-26 06:14:00','2026-09-06 06:14:00'),(1016,'Sadiqur Rahman','+8801811000016','sadiqur@customer.test','House 19, Cantonment, Dhaka-1202','SEED-MEM-1016',95,0.00,0,NULL,'active','2026-06-26 06:15:00','2026-09-05 06:15:00'),(1017,'Nusrat Jahan (Customer)','+8801811000017','nusrat.c@customer.test','House 12, Bashundhara, Dhaka-1222','SEED-MEM-1017',70,0.00,0,NULL,'active','2026-06-26 06:16:00','2026-09-04 06:16:00'),(1018,'Test Customer - Inactive','+8801811000018','inactive.cust@customer.test','Test Address 1','SEED-MEM-1018',0,0.00,0,'Dormant account - used to test the active/inactive filter.','inactive','2026-06-26 06:17:00','2026-07-20 06:17:00'),(1019,'Test Customer - VIP No Notes','+8801811000019','vip.cust@customer.test','Test Address 2','SEED-MEM-1019',5000,0.00,1,NULL,'active','2026-06-26 06:18:00','2026-09-03 06:18:00'),(1020,'Walk-in Cash Customer','+8801811000020',NULL,NULL,'SEED-MEM-1020',25,0.00,0,'Minimal record - phone only.','active','2026-06-26 06:19:00','2026-09-02 06:19:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES (3,3,1,0,5,20,'2026-09-07 09:58:21'),(6,7,1,4,100,50,'2026-09-17 16:28:17'),(7,8,1,0,100,20,'2026-09-16 16:20:19'),(8,9,1,0,0,0,'2026-09-17 04:31:48'),(11,12,1,0,0,0,'2026-09-17 07:11:04'),(1001,1001,1,95,10,25,'2026-09-27 03:00:00'),(1002,1001,2,60,10,20,'2026-09-27 03:00:00'),(1003,1002,1,45,10,25,'2026-09-27 03:00:00'),(1004,1002,2,35,10,20,'2026-09-27 03:00:00'),(1005,1003,1,60,8,20,'2026-09-27 03:00:00'),(1006,1003,2,48,8,15,'2026-09-27 03:00:00'),(1007,1004,1,50,12,30,'2026-09-27 03:00:00'),(1008,1004,2,34,12,25,'2026-09-27 03:00:00'),(1009,1005,1,108,15,35,'2026-09-27 03:00:00'),(1010,1006,1,95,20,50,'2026-09-27 03:00:00'),(1011,1007,1,40,25,60,'2026-09-27 03:00:00'),(1012,1008,1,72,6,12,'2026-09-27 03:00:00'),(1013,1009,1,15,12,25,'2026-09-27 03:00:00'),(1014,1010,1,62,10,20,'2026-09-27 03:00:00'),(1015,1011,1,5,30,80,'2026-09-27 03:00:00'),(1016,1012,1,5,30,80,'2026-09-27 03:00:00'),(1017,1013,1,30,12,25,'2026-09-27 03:00:00'),(1018,1014,1,0,3,6,'2026-09-27 03:00:00'),(1019,1015,1,0,4,8,'2026-09-27 03:00:00'),(1020,1016,1,55,6,12,'2026-09-27 03:00:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1001,1001,'invoices/SEED-INV-20260731-0001.pdf','{\"invoice\":\"SEED-INV-20260731-0001\",\"total\":214.50,\"date\":\"2026-07-31 10:12:00\"}','2026-07-31 04:12:05'),(1002,1002,'invoices/SEED-INV-20260803-0002.pdf','{\"invoice\":\"SEED-INV-20260803-0002\",\"total\":330.00,\"date\":\"2026-08-03 15:40:00\"}','2026-08-03 09:40:05'),(1003,1003,'invoices/SEED-INV-20260806-0003.pdf','{\"invoice\":\"SEED-INV-20260806-0003\",\"total\":270.00,\"date\":\"2026-08-06 12:05:00\"}','2026-08-06 06:05:05'),(1004,1004,'invoices/SEED-INV-20260810-0004.pdf','{\"invoice\":\"SEED-INV-20260810-0004\",\"total\":198.00,\"date\":\"2026-08-10 18:22:00\"}','2026-08-10 12:22:05'),(1005,1005,'invoices/SEED-INV-20260814-0005.pdf','{\"invoice\":\"SEED-INV-20260814-0005\",\"total\":440.00,\"date\":\"2026-08-14 14:30:00\"}','2026-08-14 08:30:05'),(1006,1006,'invoices/SEED-INV-20260818-0006.pdf','{\"invoice\":\"SEED-INV-20260818-0006\",\"total\":627.00,\"date\":\"2026-08-18 09:55:00\"}','2026-08-18 03:55:05'),(1007,1007,'invoices/SEED-INV-20260822-0007.pdf','{\"invoice\":\"SEED-INV-20260822-0007\",\"total\":165.00,\"date\":\"2026-08-22 16:10:00\"}','2026-08-22 10:10:05'),(1008,1008,NULL,'{\"invoice\":\"SEED-INV-20260826-0008\",\"total\":467.00,\"date\":\"2026-08-26 19:44:00\"}','2026-08-26 13:44:05'),(1009,1009,'invoices/SEED-INV-20260830-0009.pdf','{\"invoice\":\"SEED-INV-20260830-0009\",\"total\":550.00,\"date\":\"2026-08-30 11:20:00\"}','2026-08-30 05:20:05'),(1010,1010,'invoices/SEED-INV-20260903-0010.pdf','{\"invoice\":\"SEED-INV-20260903-0010\",\"total\":633.60,\"date\":\"2026-09-03 13:35:00\"}','2026-09-03 07:35:05'),(1011,1011,'invoices/SEED-INV-20260906-0011.pdf','{\"invoice\":\"SEED-INV-20260906-0011\",\"total\":220.00,\"date\":\"2026-09-06 10:05:00\"}','2026-09-06 04:05:05'),(1012,1012,'invoices/SEED-INV-20260909-0012.pdf','{\"invoice\":\"SEED-INV-20260909-0012\",\"total\":19800.00,\"date\":\"2026-09-09 17:50:00\"}','2026-09-09 11:50:05'),(1013,1013,'invoices/SEED-INV-20260912-0013.pdf','{\"invoice\":\"SEED-INV-20260912-0013\",\"total\":2475.00,\"date\":\"2026-09-12 12:15:00\"}','2026-09-12 06:15:05'),(1014,1014,'invoices/SEED-INV-20260914-0014.pdf','{\"invoice\":\"SEED-INV-20260914-0014\",\"total\":1056.00,\"date\":\"2026-09-14 10:40:00\"}','2026-09-14 04:40:05'),(1015,1015,'invoices/SEED-INV-20260916-0015.pdf','{\"invoice\":\"SEED-INV-20260916-0015\",\"total\":935.00,\"date\":\"2026-09-16 16:00:00\"}','2026-09-16 10:00:05'),(1016,1016,'invoices/SEED-INV-20260918-0016.pdf','{\"invoice\":\"SEED-INV-20260918-0016\",\"total\":5280.00,\"date\":\"2026-09-18 14:20:00\"}','2026-09-18 08:20:05'),(1017,1017,'invoices/SEED-INV-20260920-0017.pdf','{\"invoice\":\"SEED-INV-20260920-0017\",\"total\":275.00,\"date\":\"2026-09-20 09:50:00\"}','2026-09-20 03:50:05'),(1018,1018,'invoices/SEED-INV-20260922-0018.pdf','{\"invoice\":\"SEED-INV-20260922-0018\",\"total\":244.60,\"date\":\"2026-09-22 11:35:00\"}','2026-09-22 05:35:05'),(1019,1019,NULL,'{\"invoice\":\"SEED-INV-20260925-0019\",\"total\":385.00,\"date\":\"2026-09-25 15:10:00\"}','2026-09-25 09:10:05'),(1020,1020,'invoices/SEED-INV-20260927-0020.pdf','{\"invoice\":\"SEED-INV-20260927-0020\",\"total\":715.00,\"date\":\"2026-09-27 12:15:00\"}','2026-09-27 06:15:05');
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,NULL,'Low Stock Alert','chanachur is below reorder level (10 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:00:56'),(2,NULL,'Low Stock Alert','chanachur is below reorder level (8 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:01:19'),(3,NULL,'Low Stock Alert','chanachur is below reorder level (6 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:05:27'),(4,NULL,'Low Stock Alert','chanachur is below reorder level (8 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:05:41'),(5,NULL,'Low Stock Alert','chanachur is below reorder level (7 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:05:59'),(6,NULL,'Low Stock Alert','chanachur is below reorder level (5 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:07:58'),(7,NULL,'Low Stock Alert','chanachur is below reorder level (4 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:09:41'),(8,NULL,'Low Stock Alert','chanachur is below reorder level (3 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:10:51'),(9,NULL,'Low Stock Alert','chanachur is below reorder level (4 remaining)','warning',0,'http://127.0.0.1:8099/pages/inventory/index.php?product_id=7','2026-09-07 11:10:52'),(15,1,'Out of Stock','Alooz (SKU-D066BC) is OUT OF STOCK in Main Branch','danger',1,'http://localhost:8000/pages/inventory/','2026-09-17 07:10:00'),(16,1,'Out of Stock','amer juice (SKU-3EF55A) is OUT OF STOCK in Main Branch','danger',1,'http://localhost:8000/pages/inventory/','2026-09-17 07:10:00'),(17,1,'Out of Stock','lays (SKU-437D3B) is OUT OF STOCK in Main Branch','danger',1,'http://localhost:8000/pages/inventory/','2026-09-17 07:10:00'),(18,1,'Payment Due','PO #PO-20260907-5647 from Acme Supplies has outstanding balance of $480.00','warning',1,'http://localhost:8000/pages/purchases/view.php?id=3','2026-09-17 07:10:00'),(19,1,'Auto-Reorder Triggered','2 product(s) fell below reorder level. Purchase orders have been auto-created in draft status.','warning',1,'http://localhost:8000/pages/purchases/?status=draft','2026-09-17 07:10:00'),(20,NULL,'Low Stock Alert','Multi-Branch Test Item is below reorder level (3 remaining)','warning',0,'http://localhost:8000/pages/inventory/index.php?product_id=15','2026-09-17 15:59:32'),(21,NULL,'Low Stock Alert','chanachur is below reorder level (2 remaining)','warning',0,'http://localhost:8000/pages/inventory/index.php?product_id=7','2026-09-17 16:20:39'),(22,NULL,'Low Stock Alert','chanachur is below reorder level (2 remaining)','warning',0,'http://localhost:8000/pages/inventory/index.php?product_id=7','2026-09-17 16:22:10'),(23,NULL,'Low Stock Alert','chanachur is below reorder level (1 remaining)','warning',0,'http://localhost:8000/pages/inventory/index.php?product_id=7','2026-09-17 16:22:10'),(24,NULL,'Low Stock Alert','chanachur is below reorder level (2 remaining)','warning',0,'http://localhost:8000/pages/inventory/index.php?product_id=7','2026-09-17 16:22:29'),(25,NULL,'Low Stock Alert','chanachur is below reorder level (1 remaining)','warning',0,'http://localhost:8000/pages/inventory/index.php?product_id=7','2026-09-17 16:22:29'),(26,NULL,'Low Stock Alert','chanachur is below reorder level (3 remaining)','warning',0,'http://localhost:8000/pages/inventory/index.php?product_id=7','2026-09-17 16:28:09'),(1001,1001,'Low stock: Lux Beauty Soap','Product SEED-SKU-1011 has 5 pcs left, reorder level is 80.','warning',0,'inventory.php','2026-09-27 02:05:00'),(1002,1001,'Low stock: Dettol Antibacterial Soap','Product SEED-SKU-1012 has 5 pcs left, reorder level is 80.','warning',0,'inventory.php','2026-09-27 02:06:00'),(1003,1001,'Out of stock: Vivo Y21 Smartphone','SEED-SKU-1014 is at 0 qty across all branches.','danger',0,'inventory.php','2026-09-27 02:07:00'),(1004,1002,'Purchase order approved','SEED-PO-20260706-0010 approved for store 1.','success',1,'purchases.php','2026-07-05 03:50:00'),(1005,1002,'Purchase order cancelled','SEED-PO-20260730-0016 was cancelled - supplier closed.','danger',1,'purchases.php','2026-08-01 04:00:00'),(1006,1002,'Supplier rating updated','Premium Import House is now rated 4.95.','info',0,'suppliers.php','2026-08-28 04:20:00'),(1007,1003,'Branch transfer completed','20 units of SEED-SKU-1005 moved to branch 1005.','success',1,'inventory.php','2026-08-05 08:25:00'),(1008,1003,'Stock take discrepancy','Physical count found 2 missing SEED-SKU-1003 units.','warning',0,'inventory.php','2026-09-20 11:05:00'),(1009,1004,'Daily Z-report ready','Counter day summary is available for 2026-09-27.','info',0,'reports.php','2026-09-27 14:00:00'),(1010,1004,'Held sale needs attention','Sale SEED-INV-20260826-0008 is still on hold.','warning',0,'sales.php','2026-08-26 13:45:00'),(1011,1005,'Order shipped','Your order SEED-ORD-20260910-1012 has been shipped.','success',0,'orders.php','2026-09-11 09:30:00'),(1012,1005,'Return requested','Your return request for SEED-ORD-20260916-1014 is under review.','info',0,'orders.php','2026-09-17 04:00:00'),(1013,1005,'New coupon available','Use WELCOME20 for 20% off your next order.','success',0,'store.php','2026-09-01 02:00:00'),(1014,1006,'Purchase order submitted','SEED-PO-20260616-0005 submitted for approval.','info',1,'purchases.php','2026-06-16 04:45:00'),(1015,1007,'Payment received','Full payment received against SEED-PO-20260604-0002.','success',1,'purchases.php','2026-06-07 06:00:00'),(1016,1008,'Payment received','Full payment received against SEED-PO-20260714-0012.','success',1,'purchases.php','2026-07-16 04:20:00'),(1017,1009,'Product activated','SEED-SKU-1019 is now active and visible.','info',0,'products.php','2026-07-30 03:20:00'),(1018,1010,'New order received','Storefront order SEED-ORD-20260920-1016 needs confirmation.','warning',0,'orders.php','2026-09-20 05:00:00'),(1019,1011,'Review approved','Thanks - your review is now live on the product page.','success',1,'store.php','2026-09-12 06:00:00'),(1020,1012,'Loyalty points expiring','You have points expiring soon. Redeem before month end.','warning',0,'dashboard.php','2026-09-25 02:00:00');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (3,3,9,4,50.00,200.00),(4,4,7,7,20.00,140.00),(6,6,7,5,20.00,100.00),(1001,1001,1001,3,65.00,195.00),(1002,1002,1004,1,480.00,480.00),(1003,1003,1005,6,95.00,570.00),(1004,1004,1010,1,185.00,185.00),(1005,1005,1001,2,50.00,100.00),(1006,1006,1008,1,890.00,890.00),(1007,1007,1015,2,2450.00,4900.00),(1008,1008,1016,1,1250.00,1250.00),(1009,1009,1002,1,62.00,62.00),(1010,1010,1004,1,480.00,480.00),(1011,1011,1013,1,210.00,210.00),(1012,1012,1009,1,165.00,165.00),(1013,1013,1016,1,1250.00,1250.00),(1014,1014,1006,1,25.00,25.00),(1015,1015,1005,1,95.00,95.00),(1016,1016,1015,2,2450.00,4900.00),(1017,1017,1012,1,72.00,72.00),(1018,1018,1014,10,185.00,1850.00),(1019,1019,1008,1,890.00,890.00),(1020,1020,1006,15,25.00,375.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL,
  `user_id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `coupon_code` varchar(50) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT '0.00',
  `shipping` decimal(10,2) DEFAULT '0.00',
  `grand_total` decimal(10,2) DEFAULT '0.00',
  `payment_method` enum('cash','card','mobile','cod') DEFAULT 'cod',
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled','returned') DEFAULT 'pending',
  `shipping_address` text,
  `contact_phone` varchar(20) DEFAULT NULL,
  `notes` text,
  `is_pos` tinyint(1) DEFAULT '0',
  `sale_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `user_id` (`user_id`),
  KEY `customer_id` (`customer_id`),
  KEY `sale_id` (`sale_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (3,'ORD-20260917-80662',5,NULL,200.00,0.00,NULL,20.00,0.00,220.00,'cod','pending','123 Test St','+1111','',0,NULL,'2026-09-17 10:40:03','2026-09-17 10:40:03'),(4,'ORD-20260917-78243',5,NULL,140.00,0.00,NULL,14.00,0.00,154.00,'cod','pending','hiugbi','p00088','',0,NULL,'2026-09-17 16:58:49','2026-09-17 16:58:49'),(6,'ORD-20260917-32373',5,NULL,100.00,0.00,NULL,10.00,0.00,110.00,'cod','pending','jhgyf','gufiygui','',0,NULL,'2026-09-17 17:46:49','2026-09-17 17:46:49'),(1001,'SEED-ORD-20260810-1001',1005,1001,195.00,0.00,NULL,19.50,50.00,264.50,'cod','delivered','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Delivered on the same day.',0,NULL,'2026-08-10 03:20:00','2026-08-11 11:00:00'),(1002,'SEED-ORD-20260812-1002',1005,1001,480.00,96.00,'SEEDWELCOME20',38.40,0.00,422.40,'mobile','delivered','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Welcome coupon applied.',0,NULL,'2026-08-12 04:05:00','2026-08-13 10:30:00'),(1003,'SEED-ORD-20260815-1003',1016,NULL,570.00,0.00,NULL,57.00,60.00,687.00,'cod','delivered','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011',NULL,0,NULL,'2026-08-15 06:40:00','2026-08-16 09:00:00'),(1004,'SEED-ORD-20260818-1004',1016,NULL,185.00,0.00,NULL,18.50,60.00,263.50,'cod','delivered','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011',NULL,0,NULL,'2026-08-18 08:10:00','2026-08-19 06:00:00'),(1005,'SEED-ORD-20260821-1005',1016,NULL,100.00,5.00,'SEEDBEVER5',9.50,60.00,164.50,'card','delivered','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011',NULL,0,NULL,'2026-08-21 03:15:00','2026-08-22 05:45:00'),(1006,'SEED-ORD-20260824-1006',1005,1001,890.00,133.50,'SEEDCARE15',75.65,0.00,832.15,'mobile','delivered','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001',NULL,0,NULL,'2026-08-24 12:00:00','2026-08-26 04:30:00'),(1007,'SEED-ORD-20260827-1007',1005,1001,4900.00,0.00,NULL,490.00,0.00,5390.00,'cod','returned','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Earbuds returned - pairing issue.',0,NULL,'2026-08-27 14:15:00','2026-09-02 08:20:00'),(1008,'SEED-ORD-20260830-1008',1016,NULL,1250.00,0.00,NULL,125.00,0.00,1375.00,'cod','delivered','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011','Delivered 2026-09-01; the flash drive was later returned as defective.',0,NULL,'2026-08-30 05:00:00','2026-09-01 03:00:00'),(1009,'SEED-ORD-20260902-1009',1005,1001,62.00,0.00,NULL,6.20,60.00,128.20,'cod','shipped','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001',NULL,0,NULL,'2026-09-02 09:30:00','2026-09-03 02:45:00'),(1010,'SEED-ORD-20260904-1010',1016,NULL,480.00,0.00,NULL,48.00,60.00,588.00,'cod','shipped','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011',NULL,0,NULL,'2026-09-04 04:50:00','2026-09-05 03:30:00'),(1011,'SEED-ORD-20260906-1011',1005,1001,210.00,0.00,NULL,21.00,60.00,291.00,'mobile','processing','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Packed, awaiting courier pickup.',0,NULL,'2026-09-06 10:20:00','2026-09-07 04:00:00'),(1012,'SEED-ORD-20260908-1012',1016,NULL,165.00,0.00,NULL,16.50,60.00,241.50,'cod','processing','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011',NULL,0,NULL,'2026-09-08 07:35:00','2026-09-09 03:00:00'),(1013,'SEED-ORD-20260910-1013',1005,1001,1250.00,0.00,NULL,125.00,0.00,1375.00,'cod','confirmed','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Payment confirmed by COD collection.',0,NULL,'2026-09-10 03:00:00','2026-09-10 06:00:00'),(1014,'SEED-ORD-20260911-1014',1016,NULL,25.00,0.00,NULL,2.50,60.00,87.50,'cod','cancelled','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011','Cancelled by customer within 1 hour.',0,NULL,'2026-09-11 05:10:00','2026-09-11 06:00:00'),(1015,'SEED-ORD-20260912-1015',1005,1001,95.00,0.00,NULL,9.50,60.00,164.50,'mobile','cancelled','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Out of stock at the time of picking.',0,NULL,'2026-09-12 11:25:00','2026-09-13 04:00:00'),(1016,'SEED-ORD-20260913-1016',1005,1001,4900.00,980.00,'SEEDWELCOME20',392.00,0.00,4312.00,'card','pending','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001','Awaiting payment confirmation.',0,NULL,'2026-09-13 13:00:00','2026-09-13 13:00:00'),(1017,'SEED-ORD-20260914-1017',1016,NULL,72.00,0.00,NULL,7.20,60.00,139.20,'cod','pending','Flat B3, Road 11, Banani, Dhaka-1213','+8801712000011','New order, not yet processed.',0,NULL,'2026-09-14 02:20:00','2026-09-14 02:20:00'),(1018,'SEED-ORD-20260915-1018',1005,1001,1850.00,0.00,NULL,185.00,0.00,2035.00,'card','processing','House 5, Road 7, Dhanmondi, Dhaka-1209','+8801811000001',NULL,0,NULL,'2026-09-15 08:00:00','2026-09-16 03:00:00'),(1019,'SEED-POS-20260916-1019',1004,1005,890.00,40.00,NULL,85.00,0.00,935.00,'card','delivered','House 9, Uttara Sector 4, Dhaka-1230','+8801811000005','POS order mirrored from SEED-INV-20260916-0015.',1,1015,'2026-09-16 10:00:00','2026-09-16 10:05:00'),(1020,'SEED-POS-20260925-1020',1004,1008,375.00,25.00,NULL,35.00,0.00,385.00,'cash','cancelled',NULL,NULL,'POS order mirrored from SEED-INV-20260925-0019 (walk-in, cancelled sale).',1,1019,'2026-09-25 09:10:00','2026-09-25 09:20:00');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (19,1,'branches','manage'),(5,1,'products','create'),(8,1,'products','delete'),(6,1,'products','read'),(7,1,'products','update'),(13,1,'purchases','create'),(16,1,'purchases','delete'),(14,1,'purchases','read'),(15,1,'purchases','update'),(17,1,'reports','read'),(9,1,'sales','create'),(12,1,'sales','delete'),(10,1,'sales','read'),(11,1,'sales','update'),(18,1,'settings','manage'),(1,1,'users','create'),(4,1,'users','delete'),(2,1,'users','read'),(3,1,'users','update'),(1002,1001,'products','update'),(1001,1001,'users','read'),(1003,1002,'inventory','read'),(1004,1003,'activity_logs','read'),(1005,1004,'inventory','update'),(1006,1005,'purchases','create'),(1007,1006,'purchases','approve'),(1008,1007,'sales','create'),(1009,1008,'sales','read'),(1010,1009,'sales','create'),(1011,1010,'invoices','read'),(1012,1011,'users','update'),(1013,1012,'customers','read'),(1014,1013,'orders','update'),(1015,1014,'banners','manage'),(1016,1014,'coupons','manage'),(1017,1015,'products','update'),(1018,1016,'purchases','approve'),(1019,1017,'inventory','transfer'),(1020,1018,'reports','read');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_gallery`
--

DROP TABLE IF EXISTS `product_gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_gallery_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_gallery`
--

LOCK TABLES `product_gallery` WRITE;
/*!40000 ALTER TABLE `product_gallery` DISABLE KEYS */;
INSERT INTO `product_gallery` VALUES (1001,1001,'assets/images/products/seed-01.svg',1,'2026-06-30 05:00:00'),(1002,1002,'assets/images/products/seed-02.svg',1,'2026-06-30 05:01:00'),(1003,1003,'assets/images/products/seed-03.svg',1,'2026-06-30 05:02:00'),(1004,1004,'assets/images/products/seed-04.svg',1,'2026-06-30 05:03:00'),(1005,1005,'assets/images/products/seed-05.svg',1,'2026-06-30 05:04:00'),(1006,1006,'assets/images/products/seed-06.svg',1,'2026-06-30 05:05:00'),(1007,1007,'assets/images/products/seed-07.svg',1,'2026-06-30 05:06:00'),(1008,1008,'assets/images/products/seed-08.svg',1,'2026-06-30 05:07:00'),(1009,1009,'assets/images/products/seed-09.svg',1,'2026-06-30 05:08:00'),(1010,1010,'assets/images/products/seed-10.svg',1,'2026-06-30 05:09:00'),(1011,1011,'assets/images/products/seed-11.svg',1,'2026-06-30 05:10:00'),(1012,1012,'assets/images/products/seed-12.svg',1,'2026-06-30 05:11:00'),(1013,1013,'assets/images/products/seed-13.svg',1,'2026-06-30 05:12:00'),(1014,1014,'assets/images/products/seed-14.svg',1,'2026-06-30 05:13:00'),(1015,1015,'assets/images/products/seed-15.svg',1,'2026-06-30 05:14:00'),(1016,1016,'assets/images/products/seed-16.svg',1,'2026-06-30 05:15:00'),(1017,1017,'assets/images/products/seed-17.svg',1,'2026-06-30 05:16:00'),(1018,1018,'assets/images/products/seed-18.svg',1,'2026-06-30 05:17:00'),(1019,1019,'assets/images/products/seed-19.svg',1,'2026-06-30 05:18:00'),(1020,1020,'assets/images/products/seed-20.svg',1,'2026-06-30 05:19:00');
/*!40000 ALTER TABLE `product_gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` tinyint NOT NULL DEFAULT '5',
  `title` varchar(200) DEFAULT NULL,
  `comment` text,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product_review` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
INSERT INTO `product_reviews` VALUES (1,9,5,5,'Great!','Loved it','approved','2026-09-17 10:43:32'),(1001,1001,1005,5,'Great taste and price','Chilled and fresh. Will buy again.','approved','2026-07-06 05:00:00'),(1002,1002,1016,4,'Good but pricey','Tastes fine, price is on the higher side.','approved','2026-07-08 05:00:00'),(1003,1003,1005,5,'Energy boost works','Best pick for late night shifts.','approved','2026-07-10 05:00:00'),(1004,1004,1012,4,'Rich chocolate taste','Kids love it. A bit sweet for me.','approved','2026-07-12 05:00:00'),(1005,1005,1014,5,'Real mango flavour','Not too sweet, tastes like actual mango.','approved','2026-07-14 05:00:00'),(1006,1006,1005,3,'Chips are okay','Fine for the price but quite salty.','pending','2026-07-16 05:00:00'),(1007,1007,1017,4,'Good snack mix','The chanachur mix is properly spicy.','approved','2026-07-18 05:00:00'),(1008,1008,1016,5,'Milk powder is top quality','Dissolves smoothly, no lumps.','approved','2026-07-20 05:00:00'),(1009,1009,1012,2,'Leaked in the bag','Bottle cap was loose, half the oil leaked.','rejected','2026-07-22 05:00:00'),(1010,1010,1005,4,'Fresh breath','Works well, tube lasts about a month.','approved','2026-07-24 05:00:00'),(1011,1011,1014,3,'Soap is fine','Nothing special but the pack price is good.','approved','2026-07-26 05:00:00'),(1012,1012,1005,5,'Germ free feeling','Trustworthy brand, no complaints.','approved','2026-07-28 05:00:00'),(1013,1013,1016,4,'Hand wash is strong','Little goes a long way.','approved','2026-07-30 05:00:00'),(1014,1014,1012,5,'Excellent phone','Battery and camera are great for the price.','approved','2026-08-01 05:00:00'),(1015,1015,1014,4,'Sound is good','Earbuds are comfortable for long use.','approved','2026-08-03 05:00:00'),(1016,1016,1017,5,'Fast and reliable','Speeds are good, casing is sturdy.','approved','2026-08-05 05:00:00'),(1017,1017,1012,1,'Pages came loose','Binding was already broken when delivered.','rejected','2026-08-07 05:00:00'),(1018,1018,1016,4,'Pens write smoothly','Ink is dark, no smudging.','approved','2026-08-09 05:00:00'),(1019,1019,1014,2,'Stopped working in a month','Mouse sensor stopped tracking.','pending','2026-08-11 05:00:00'),(1020,1020,1017,4,'Nice gift hamper','Good presentation, packaging was solid.','approved','2026-08-13 05:00:00');
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
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
  `total_stock` int NOT NULL DEFAULT '0',
  `branch_count` int NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (3,'SKU-D066BC','Alooz',4,1,8,NULL,'SKU-D066BC',NULL,'',25.00,0.00,5,20,0,1,'active','2026-09-07 09:58:21','2026-09-17 15:55:07'),(7,'SKU-73DFBD','chanachur',4,5,8,NULL,'SKU-73DFBD',NULL,'',20.00,5.00,100,50,4,1,'active','2026-09-07 10:05:43','2026-09-17 16:28:17'),(8,'SKU-3EF55A','amer juice',5,1,8,NULL,'907856674864',NULL,'',25.00,8.00,100,20,0,1,'active','2026-09-16 16:20:19','2026-09-17 15:55:07'),(9,'SKU-437D3B','lays',4,5,9,'uploads/products/product_9_1789620324.png','SKU-437D3B',NULL,'',50.00,10.00,0,0,0,1,'active','2026-09-17 04:31:48','2026-09-17 15:55:07'),(12,'SKU-81646F','chips',4,1,8,'uploads/products/product_12_1789629064.png','SKU-81646F',NULL,'',30.00,8.00,0,0,0,1,'discontinued','2026-09-17 07:11:04','2026-09-17 15:55:07'),(1001,'SEED-SKU-1001','Coca-Cola Soft Drink 750ml',1002,1002,1001,'assets/images/products/seed-01.svg','8801000001001',NULL,'Chilled sparkling soft drink, 750ml PET bottle.',65.00,48.00,10,25,155,2,'active','2026-06-26 03:00:00','2026-09-20 03:00:00'),(1002,'SEED-SKU-1002','Pepsi Max Soft Drink 750ml',1002,1003,1001,'assets/images/products/seed-02.svg','8801000001002',NULL,'Zero-sugar cola, 750ml PET bottle.',62.00,45.00,10,25,80,2,'active','2026-06-26 03:01:00','2026-09-20 03:01:00'),(1003,'SEED-SKU-1003','Red Bull Energy Drink 250ml',1003,1019,1010,'assets/images/products/seed-03.svg','8801000001003',NULL,'Sugar-free energy drink, 250ml slim can.',220.00,185.00,8,20,108,2,'active','2026-06-26 03:02:00','2026-09-19 03:02:00'),(1004,'SEED-SKU-1004','Nestle Milo Powder 400g',1004,1001,1002,'assets/images/products/seed-04.svg','8801000001004',NULL,'Chocolate malt drink powder, 400g tin.',480.00,415.00,12,30,84,2,'active','2026-06-26 03:03:00','2026-09-18 03:03:00'),(1005,'SEED-SKU-1005','PRAN Mango Juice 1L',1004,1004,1001,'assets/images/products/seed-05.svg','8801000001005',NULL,'Mango fruit juice, 1L PET bottle.',95.00,72.00,15,35,108,1,'active','2026-06-26 03:04:00','2026-09-17 03:04:00'),(1006,'SEED-SKU-1006','Square Potato Chips 40g',1006,1005,1003,'assets/images/products/seed-06.svg','8801000001006',NULL,'Salted potato chips, 40g pack.',25.00,18.00,20,50,95,1,'active','2026-06-26 03:05:00','2026-09-16 03:05:00'),(1007,'SEED-SKU-1007','Rownd Chanachur 100g',1006,1006,1003,'assets/images/products/seed-07.svg','8801000001007',NULL,'Spicy mixed snack, 100g pack.',20.00,15.00,25,60,40,1,'active','2026-06-26 03:06:00','2026-09-15 03:06:00'),(1008,'SEED-SKU-1008','Aarong Full Cream Milk Powder 900g',1009,1007,1002,'assets/images/products/seed-08.svg','8801000001008',NULL,'Instant full cream milk powder, 900g tin.',890.00,760.00,6,12,72,1,'active','2026-06-26 03:07:00','2026-09-14 03:07:00'),(1009,'SEED-SKU-1009','Marico Shine Hair Oil 150ml',1012,1008,1005,'assets/images/products/seed-09.svg','8801000001009',NULL,'Non-greasy hair oil, 150ml bottle.',165.00,128.00,12,25,15,1,'active','2026-06-26 03:08:00','2026-09-13 03:08:00'),(1010,'SEED-SKU-1010','Pepsodent Toothpaste 200g',1013,1010,1006,'assets/images/products/seed-10.svg','8801000001010',NULL,'Cavity protection toothpaste, 200g tube.',185.00,140.00,10,20,62,1,'active','2026-06-26 03:09:00','2026-09-12 03:09:00'),(1011,'SEED-SKU-1011','Lux Beauty Soap 100g',1014,1014,1007,'assets/images/products/seed-11.svg','8801000001011',NULL,'Moisturising beauty soap, 100g x 6 pack.',48.00,36.00,30,80,5,1,'active','2026-06-26 03:10:00','2026-09-11 03:10:00'),(1012,'SEED-SKU-1012','Dettol Antibacterial Soap 100g',1014,1012,1007,'assets/images/products/seed-12.svg','8801000001012',NULL,'Antibacterial protection soap, 100g x 6 pack.',72.00,55.00,30,80,5,1,'active','2026-06-26 03:11:00','2026-09-11 03:11:00'),(1013,'SEED-SKU-1013','Savlon Hand Wash 250ml',1014,1011,1005,'assets/images/products/seed-13.svg','8801000001013',NULL,'Antibacterial hand wash, 250ml bottle.',210.00,165.00,12,25,30,1,'active','2026-06-26 03:12:00','2026-09-10 03:12:00'),(1014,'SEED-SKU-1014','Vivo Y21 Smartphone 128GB',1018,1016,1008,'assets/images/products/seed-14.svg','8801000001014',NULL,'5G smartphone, 128GB internal storage.',18500.00,17200.00,3,6,0,1,'active','2026-06-26 03:13:00','2026-09-09 03:13:00'),(1015,'SEED-SKU-1015','Realme Buds Wireless Earbuds',1018,1017,1008,'assets/images/products/seed-15.svg','8801000001015',NULL,'True wireless earbuds with charging case.',2450.00,2150.00,4,8,0,1,'active','2026-06-26 03:14:00','2026-09-09 03:14:00'),(1016,'SEED-SKU-1016','Samsung 64GB USB Flash Drive',1018,1018,1009,'assets/images/products/seed-16.svg','8801000001016',NULL,'USB 3.1 flash drive, 64GB.',1250.00,1080.00,6,12,55,1,'active','2026-06-26 03:15:00','2026-09-08 03:15:00'),(1017,'SEED-SKU-1017','A4 Notebook 200 Pages',1019,NULL,1004,'assets/images/products/seed-17.svg','8801000001017',NULL,'A4 ruled notebook, 200 pages. No brand assigned.',185.00,130.00,20,40,0,0,'active','2026-06-26 03:16:00','2026-09-08 03:16:00'),(1018,'SEED-SKU-1018','Ballpoint Pen Box of 12',1019,NULL,1007,'assets/images/products/seed-18.svg','8801000001018',NULL,'Blue ballpoint pens, box of 12. No brand assigned.',240.00,175.00,15,30,0,0,'active','2026-06-26 03:17:00','2026-09-08 03:17:00'),(1019,'SEED-SKU-1019','Discontinued Wireless Mouse',1018,1011,1008,'assets/images/products/seed-19.svg','8801000001019',NULL,'Legacy wireless mouse - discontinued line, hidden from lists.',650.00,480.00,0,0,0,0,'discontinued','2026-06-26 03:18:00','2026-07-30 03:18:00'),(1020,'SEED-SKU-1020','Seasonal Gift Hamper',1019,1014,1008,'assets/images/products/seed-20.svg','8801000001020',NULL,'Seasonal hamper, temporarily deactivated for stock take.',1450.00,1100.00,5,10,0,0,'inactive','2026-06-26 03:19:00','2026-07-30 03:19:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
INSERT INTO `purchase_items` VALUES (2,2,7,10,10,5.50,55.00),(3,3,7,96,0,5.00,480.00),(1001,1001,1001,100,100,48.00,4800.00),(1002,1002,1006,200,200,18.00,3600.00),(1003,1003,1005,120,0,72.00,8640.00),(1004,1004,1003,50,50,185.00,9250.00),(1005,1005,1011,300,0,36.00,10800.00),(1006,1005,1012,300,0,55.00,16500.00),(1007,1006,1009,60,60,128.00,7680.00),(1008,1007,1010,80,0,140.00,11200.00),(1009,1008,1013,90,90,165.00,14850.00),(1010,1008,1010,70,70,140.00,9800.00),(1011,1009,1016,25,0,1080.00,27000.00),(1012,1010,1014,6,6,17200.00,103200.00),(1013,1011,1015,30,30,2150.00,64500.00),(1014,1011,1016,20,20,1080.00,21600.00),(1015,1012,1004,40,0,415.00,16600.00),(1016,1013,1008,12,12,760.00,9120.00),(1017,1014,1003,20,20,185.00,3700.00),(1018,1015,1001,60,60,48.00,2880.00),(1019,1018,1006,100,0,18.00,1800.00),(1020,1017,1007,150,0,15.00,2250.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_payments`
--

LOCK TABLES `purchase_payments` WRITE;
/*!40000 ALTER TABLE `purchase_payments` DISABLE KEYS */;
INSERT INTO `purchase_payments` VALUES (1,2,30.00,'cash','','',1,'2026-09-07 11:05:59'),(2,2,1000.00,'cash','','',1,'2026-09-07 11:09:12'),(1001,1001,3000.00,'cash','CASH-0001','First instalment on delivery.',1,'2026-06-02 05:00:00'),(1002,1001,2280.00,'bank_transfer','TRF-0001','Balance settled by transfer.',1,'2026-06-02 05:05:00'),(1003,1002,2000.00,'cash','CASH-0002','Advance against delivery.',1,'2026-06-04 05:30:00'),(1004,1002,1960.00,'card','CARD-0002','Second part payment.',1,'2026-06-06 04:15:00'),(1005,1003,4752.00,'cash','CASH-0003','Half payment against approved PO.',1002,'2026-06-09 03:40:00'),(1006,1004,9250.00,'bank_transfer','TRF-0004','Full payment, tax-exempt invoice.',1,'2026-06-13 04:20:00'),(1007,1005,10000.00,'cash','CASH-0005','Token advance before approval.',1002,'2026-06-17 03:10:00'),(1008,1005,20030.00,'bank_transfer','TRF-0005','Balance on approval.',1,'2026-06-18 03:35:00'),(1009,1006,8448.00,'card','CARD-0006','Paid in full by card.',1,'2026-06-21 06:00:00'),(1010,1007,2000.00,'cash','CASH-0007','Part payment on pending PO.',1002,'2026-06-25 04:30:00'),(1011,1008,27115.00,'bank_transfer','TRF-0008','Full payment.',1,'2026-06-29 05:00:00'),(1012,1009,15000.00,'cash','CASH-0009','Part payment on approved PO.',1002,'2026-07-03 04:00:00'),(1013,1009,14700.00,'card','CARD-0009','Balance on card.',1002,'2026-07-04 09:20:00'),(1014,1010,113520.00,'bank_transfer','TRF-0010','Full payment for consignment order.',1,'2026-07-07 03:00:00'),(1015,1011,25000.00,'cash','CASH-0011','Instalment 1 of 3.',1,'2026-07-10 03:30:00'),(1016,1011,25000.00,'bank_transfer','TRF-0011','Instalment 2 of 3.',1,'2026-07-11 08:00:00'),(1017,1011,44710.00,'cash','CASH-0012','Instalment 3 of 3 - closes the balance.',1,'2026-07-14 11:25:00'),(1018,1012,5000.00,'cash','CASH-0013','Part payment on pending PO.',1002,'2026-07-15 04:10:00'),(1019,1013,10032.00,'cheque','CHQ-0014','Paid by crossed cheque #4417.',1,'2026-07-19 05:20:00'),(1020,1015,3168.00,'bank_transfer','TRF-0016','Full payment for confectionery.',1,'2026-07-27 03:40:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
INSERT INTO `purchases` VALUES (1,1,'PO-20260907-2537','received',0.00,0.00,0.00,0.00,0.00,'pending','Test PO',NULL,1,NULL,1,'2026-09-07 11:00:39','2026-09-07 05:10:54'),(2,1,'PO-20260907-6088','received',55.00,5.50,60.50,1030.00,0.00,'paid','Test PO',NULL,1,NULL,1,'2026-09-07 11:00:45','2026-09-07 11:09:12'),(3,1,'PO-20260907-5647','draft',0.00,0.00,480.00,0.00,480.00,'pending','Auto-generated reorder for chanachur (SKU-73DFBD) - Stock: 4, Reorder Level: 50',NULL,NULL,NULL,1,'2026-09-07 13:06:10','2026-09-07 13:06:10'),(1001,1001,'SEED-PO-20260601-0001','received',4800.00,480.00,5280.00,5280.00,0.00,'paid','Monthly restock for Main Branch beverage shelf.',NULL,1,8,1,'2026-06-01 04:00:00','2026-06-03 09:20:00'),(1002,1002,'SEED-PO-20260604-0002','received',3600.00,360.00,3960.00,3960.00,0.00,'paid','Chips and snacks - paid in two part payments.',NULL,1,8,1,'2026-06-04 05:20:00','2026-06-07 06:00:00'),(1003,1004,'SEED-PO-20260608-0003','approved',8640.00,864.00,9504.00,4752.00,4752.00,'partial','Bulk juice order awaiting goods receipt.',NULL,1002,8,1,'2026-06-08 03:35:00','2026-06-11 04:05:00'),(1004,1001,'SEED-PO-20260612-0004','received',9250.00,0.00,9250.00,9250.00,0.00,'paid','Energy drink clearance - tax exempt (resale certificate on file).',NULL,1,8,1,'2026-06-12 08:10:00','2026-06-15 05:00:00'),(1005,1003,'SEED-PO-20260616-0005','pending',27300.00,2730.00,30030.00,30030.00,0.00,'paid','Personal care bulk - submitted, awaiting approval.',NULL,1002,NULL,2,'2026-06-16 04:45:00','2026-06-18 03:30:00'),(1006,1008,'SEED-PO-20260620-0006','received',7680.00,768.00,8448.00,8448.00,0.00,'paid','Hair oil and personal care range.',NULL,1,8,1,'2026-06-20 07:00:00','2026-06-23 10:40:00'),(1007,1009,'SEED-PO-20260624-0007','pending',11200.00,1120.00,12320.00,2000.00,10320.00,'partial','Stationery restock - partially paid.',NULL,1002,NULL,1,'2026-06-24 03:15:00','2026-06-26 05:00:00'),(1008,1007,'SEED-PO-20260628-0008','received',24650.00,2465.00,27115.00,27115.00,0.00,'paid','Soap and hand wash - quarterly order.',NULL,1,8,2,'2026-06-28 09:30:00','2026-07-02 04:20:00'),(1009,1005,'SEED-PO-20260702-0009','approved',27000.00,2700.00,29700.00,29700.00,0.00,'paid','Electronics accessories - approved, goods not yet received.',NULL,1002,8,1,'2026-07-02 05:25:00','2026-07-05 03:50:00'),(1010,1005,'SEED-PO-20260706-0010','received',103200.00,10320.00,113520.00,113520.00,0.00,'paid','Smartphone consignment order.',NULL,1,8,1,'2026-07-06 04:10:00','2026-07-10 08:00:00'),(1011,1010,'SEED-PO-20260710-0011','received',86100.00,8610.00,94710.00,94710.00,0.00,'paid','Audio accessories - paid in three instalments.',NULL,1,8,1,'2026-07-10 03:00:00','2026-07-14 11:30:00'),(1012,1004,'SEED-PO-20260714-0012','pending',16600.00,1660.00,18260.00,5000.00,13260.00,'partial','Milo powder top-up - partial payment received.',NULL,1002,NULL,1,'2026-07-14 05:45:00','2026-07-16 04:20:00'),(1013,1011,'SEED-PO-20260718-0013','received',9120.00,912.00,10032.00,10032.00,0.00,'paid','Milk powder order paid by cheque.',NULL,1,8,3,'2026-07-18 03:30:00','2026-07-21 06:15:00'),(1014,1012,'SEED-PO-20260722-0014','received',3700.00,370.00,4070.00,0.00,4070.00,'pending','Rice and cereals - small order. Goods received, invoice still unpaid.',NULL,1002,8,1,'2026-07-22 04:00:00','2026-07-24 05:00:00'),(1015,1013,'SEED-PO-20260726-0015','received',2880.00,288.00,3168.00,3168.00,0.00,'paid','Confectionery restock.',NULL,1,8,1,'2026-07-26 07:40:00','2026-07-28 03:50:00'),(1016,1016,'SEED-PO-20260730-0016','cancelled',0.00,0.00,0.00,0.00,0.00,'pending','Cancelled - supplier closed down before confirmation. No items.',NULL,1,NULL,1,'2026-07-30 04:00:00','2026-08-01 04:00:00'),(1017,1014,'SEED-PO-20260803-0017','draft',2250.00,0.00,2250.00,0.00,2250.00,'pending','Draft for low stock snacks - still editable.',NULL,1,NULL,1,'2026-08-03 02:30:00','2026-08-03 02:30:00'),(1018,1015,'SEED-PO-20260807-0018','draft',1800.00,0.00,1800.00,0.00,1800.00,'pending','Draft PO with one line - test the edit / add items flow.',NULL,1002,NULL,1,'2026-08-07 10:20:00','2026-08-07 10:20:00'),(1019,1017,'SEED-PO-20260811-0019','draft',0.00,0.00,0.00,0.00,0.00,'pending','Empty draft - used to test the PO creation wizard from scratch.',NULL,1,NULL,1,'2026-08-11 03:15:00','2026-08-11 03:15:00'),(1020,1018,'SEED-PO-20260815-0020','draft',0.00,0.00,0.00,0.00,0.00,'pending','Empty draft for the no-rating test supplier.',NULL,1002,NULL,2,'2026-08-15 05:00:00','2026-08-15 05:00:00');
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_photos`
--

DROP TABLE IF EXISTS `return_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_photos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `return_kind` enum('sale','purchase') COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_id` int NOT NULL,
  `return_item_id` int DEFAULT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `idx_photo_return` (`return_kind`,`return_id`),
  KEY `idx_photo_item` (`return_item_id`),
  CONSTRAINT `return_photos_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_photos`
--

LOCK TABLES `return_photos` WRITE;
/*!40000 ALTER TABLE `return_photos` DISABLE KEYS */;
INSERT INTO `return_photos` VALUES (1001,'sale',1003,NULL,'uploads/returns/seed-1003-1.jpg',1016,'2026-08-25 04:05:00'),(1002,'sale',1003,NULL,'uploads/returns/seed-1003-2.jpg',1016,'2026-08-25 04:06:00'),(1003,'sale',1003,NULL,'uploads/returns/seed-1003-3.jpg',1016,'2026-08-25 04:07:00'),(1004,'sale',1004,NULL,'uploads/returns/seed-1004-1.jpg',1005,'2026-08-16 05:05:00'),(1005,'sale',1004,NULL,'uploads/returns/seed-1004-2.jpg',1005,'2026-08-16 05:06:00'),(1006,'sale',1004,NULL,'uploads/returns/seed-1004-3.jpg',1005,'2026-08-16 05:07:00'),(1007,'sale',1005,NULL,'uploads/returns/seed-1005-1.jpg',1016,'2026-08-20 06:05:00'),(1008,'sale',1005,NULL,'uploads/returns/seed-1005-2.jpg',1016,'2026-08-20 06:06:00'),(1009,'sale',1005,NULL,'uploads/returns/seed-1005-3.jpg',1016,'2026-08-20 06:07:00'),(1010,'sale',1014,NULL,'uploads/returns/seed-1014-1.jpg',1005,'2026-08-15 05:05:00'),(1011,'sale',1014,NULL,'uploads/returns/seed-1014-2.jpg',1005,'2026-08-15 05:06:00'),(1012,'sale',1014,NULL,'uploads/returns/seed-1014-3.jpg',1005,'2026-08-15 05:07:00'),(1013,'sale',1015,NULL,'uploads/returns/seed-1015-1.jpg',1005,'2026-08-27 04:05:00'),(1014,'sale',1015,NULL,'uploads/returns/seed-1015-2.jpg',1005,'2026-08-27 04:06:00'),(1015,'sale',1015,NULL,'uploads/returns/seed-1015-3.jpg',1005,'2026-08-27 04:07:00'),(1016,'sale',1019,NULL,'uploads/returns/seed-1019-1.jpg',1016,'2026-09-05 09:05:00'),(1017,'sale',1019,NULL,'uploads/returns/seed-1019-2.jpg',1016,'2026-09-05 09:06:00'),(1018,'sale',1019,NULL,'uploads/returns/seed-1019-3.jpg',1016,'2026-09-05 09:07:00'),(1019,'sale',1019,NULL,'uploads/returns/seed-1019-4.jpg',1016,'2026-09-05 09:08:00'),(1020,'sale',1019,NULL,'uploads/returns/seed-1019-5.jpg',1016,'2026-09-05 09:09:00');
/*!40000 ALTER TABLE `return_photos` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','Full system access','2026-09-07 08:46:09'),(2,'manager','Reports, analytics, supplier & purchase management','2026-09-07 08:46:09'),(3,'branch_manager','Branch-specific inventory, sales, staff','2026-09-07 08:46:09'),(4,'cashier','POS, invoicing, customer lookup','2026-09-07 08:46:09'),(5,'customer','View own invoices, loyalty points, profile','2026-09-07 08:46:09'),(1001,'store_owner_admin','Store owner with full control of one store','2026-03-05 03:20:00'),(1002,'store_supervisor','Oversees daily store operations and staff','2026-03-05 03:20:00'),(1003,'inventory_auditor','Read-only access to stock counts and audit logs','2026-03-05 03:20:00'),(1004,'warehouse_operator','Handles goods receipt, transfer and damage entry','2026-03-05 03:20:00'),(1005,'purchase_officer','Raises purchase orders, cannot receive stock','2026-03-05 03:20:00'),(1006,'procurement_lead','Approves purchase orders and negotiates suppliers','2026-03-05 03:20:00'),(1007,'sales_associate','Counter sales assistant who assists the cashier','2026-03-05 03:20:00'),(1008,'senior_cashier','Full POS access plus end-of-day reconciliation','2026-03-05 03:20:00'),(1009,'junior_cashier','Limited POS access, no refunds or cancellations','2026-03-05 03:20:00'),(1010,'accountant','Invoicing, payments, ledgers and financial reports','2026-03-05 03:20:00'),(1011,'hr_manager','Staff records, roles and shift planning','2026-03-05 03:20:00'),(1012,'support_agent','Handles customer support tickets and returns','2026-03-05 03:20:00'),(1013,'delivery_agent','Manages delivery orders and dispatch','2026-03-05 03:20:00'),(1014,'marketing_officer','Banners, promotions, coupons and campaigns','2026-03-05 03:20:00'),(1015,'stock_controller','Sets reorder levels, min stock, approves transfers','2026-03-05 03:20:00'),(1016,'quality_inspector','Approves or rejects received goods','2026-03-05 03:20:00'),(1017,'logistics_manager','Inter-branch and inter-store logistics','2026-03-05 03:20:00'),(1018,'data_analyst','Read-only access to dashboards and reports','2026-03-05 03:20:00'),(1019,'product_specialist','Maintains catalog, categories and brands','2026-03-05 03:20:00'),(1020,'customer_support_lead','Leads customer support and the loyalty programme','2026-03-05 03:20:00');
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
  `returned_qty` int NOT NULL DEFAULT '0',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1023 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
INSERT INTO `sale_items` VALUES (1001,1001,1001,3,3,65.00,0.00,195.00),(1002,1002,1002,5,1,62.00,0.00,310.00),(1003,1003,1006,8,1,25.00,0.00,200.00),(1004,1004,1007,10,0,20.00,20.00,180.00),(1005,1005,1003,2,0,220.00,0.00,440.00),(1006,1006,1005,6,3,95.00,0.00,570.00),(1007,1007,1009,1,1,165.00,0.00,165.00),(1008,1008,1010,2,0,185.00,0.00,370.00),(1009,1009,1011,4,4,48.00,0.00,192.00),(1010,1010,1012,8,8,72.00,0.00,576.00),(1011,1011,1013,1,0,210.00,0.00,210.00),(1012,1012,1014,1,0,18500.00,500.00,18000.00),(1013,1013,1016,2,0,1250.00,0.00,2500.00),(1014,1014,1004,2,1,480.00,0.00,960.00),(1015,1015,1008,1,0,890.00,40.00,850.00),(1016,1016,1015,2,0,2450.00,0.00,4900.00),(1017,1017,1001,4,4,65.00,0.00,260.00),(1018,1018,1002,3,0,62.00,0.00,186.00),(1019,1019,1006,15,0,25.00,25.00,350.00),(1020,1020,1013,3,3,210.00,0.00,630.00),(1021,1009,1011,4,4,48.00,0.00,192.00),(1022,1009,1011,4,4,48.00,0.00,192.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_payments`
--

LOCK TABLES `sale_payments` WRITE;
/*!40000 ALTER TABLE `sale_payments` DISABLE KEYS */;
INSERT INTO `sale_payments` VALUES (1001,1001,214.50,'cash',NULL,NULL,10,'2026-07-31 04:12:00'),(1002,1002,330.00,'card','CARD-SEED-1002',NULL,10,'2026-08-03 09:40:00'),(1003,1003,270.00,'mobile','MOB-SEED-1003',NULL,10,'2026-08-06 06:05:00'),(1004,1005,440.00,'card','CARD-SEED-1005',NULL,10,'2026-08-14 08:30:00'),(1005,1006,627.00,'cash',NULL,NULL,10,'2026-08-18 03:55:00'),(1006,1007,165.00,'mobile','MOB-SEED-1007',NULL,10,'2026-08-22 10:10:00'),(1007,1009,550.00,'cash',NULL,NULL,10,'2026-08-30 05:20:00'),(1008,1010,633.60,'mobile','MOB-SEED-1010',NULL,10,'2026-09-03 07:35:00'),(1009,1011,220.00,'card','CARD-SEED-1011',NULL,10,'2026-09-06 04:05:00'),(1010,1012,19800.00,'card','CARD-SEED-1012','Corporate order',10,'2026-09-09 11:50:00'),(1011,1013,2475.00,'card','CARD-SEED-1013',NULL,10,'2026-09-12 06:15:00'),(1012,1014,1056.00,'cash',NULL,NULL,10,'2026-09-14 04:40:00'),(1013,1015,935.00,'card','CARD-SEED-1015',NULL,10,'2026-09-16 10:00:00'),(1014,1016,5280.00,'card','CARD-SEED-1016',NULL,10,'2026-09-18 08:20:00'),(1015,1017,150.00,'cash',NULL,'Part 1 of mixed payment',10,'2026-09-20 03:50:00'),(1016,1017,125.00,'card','CARD-SEED-1017','Part 2 of mixed payment',10,'2026-09-20 03:50:00'),(1017,1018,104.60,'mobile','MOB-SEED-1018','Part 1 of mixed payment',10,'2026-09-22 05:35:00'),(1018,1018,140.00,'card','CARD-SEED-1018','Part 2 of mixed payment',10,'2026-09-22 05:35:00'),(1019,1019,385.00,'cash',NULL,'Refund issued for cancelled sale',10,'2026-09-25 09:25:00'),(1020,1020,715.00,'mobile','MOB-SEED-1020',NULL,10,'2026-09-27 06:15:00');
/*!40000 ALTER TABLE `sale_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_return_items`
--

DROP TABLE IF EXISTS `sale_return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_return_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `return_id` int NOT NULL,
  `sale_item_id` int DEFAULT NULL,
  `order_item_id` int DEFAULT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `restock` tinyint(1) NOT NULL DEFAULT '1',
  `condition_note` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `idx_ri_return` (`return_id`),
  KEY `idx_ri_sale_item` (`sale_item_id`),
  KEY `idx_ri_product` (`product_id`),
  CONSTRAINT `sale_return_items_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_return_items_ibfk_2` FOREIGN KEY (`sale_item_id`) REFERENCES `sale_items` (`id`),
  CONSTRAINT `sale_return_items_ibfk_3` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`),
  CONSTRAINT `sale_return_items_ibfk_4` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1023 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_return_items`
--

LOCK TABLES `sale_return_items` WRITE;
/*!40000 ALTER TABLE `sale_return_items` DISABLE KEYS */;
INSERT INTO `sale_return_items` VALUES (1001,1001,1001,NULL,1001,3,65.00,0.00,195.00,0,'Lids dented - write off.'),(1002,1002,1002,NULL,1002,1,62.00,0.00,62.00,0,'Opened, half consumed.'),(1003,1003,NULL,1005,1001,2,50.00,0.00,100.00,0,NULL),(1004,1004,NULL,1002,1004,1,480.00,0.00,480.00,0,NULL),(1005,1005,NULL,1004,1010,1,185.00,0.00,185.00,0,NULL),(1006,1006,1014,NULL,1004,1,480.00,0.00,480.00,0,'Dented can.'),(1007,1007,1007,NULL,1009,1,165.00,0.00,165.00,0,'Bottle marked defective.'),(1008,1008,1020,NULL,1013,2,210.00,0.00,420.00,0,'Both bottles leaked.'),(1009,1009,1006,NULL,1005,3,95.00,0.00,285.00,0,'Packs torn.'),(1010,1010,1010,NULL,1012,8,72.00,0.00,576.00,1,'Sealed - resellable.'),(1011,1011,1009,NULL,1011,4,48.00,0.00,192.00,1,'Crate A resellable.'),(1012,1012,1003,NULL,1006,1,25.00,0.00,25.00,1,'Single pack resellable.'),(1013,1013,NULL,1003,1005,2,95.00,0.00,190.00,0,NULL),(1014,1014,NULL,1001,1001,1,65.00,0.00,65.00,0,NULL),(1015,1015,NULL,1006,1008,1,890.00,0.00,890.00,0,'Unit does not power on.'),(1016,1016,1014,NULL,1004,1,480.00,0.00,480.00,0,NULL),(1017,1017,1017,NULL,1001,4,65.00,0.00,260.00,0,'Bottles leaking.'),(1018,1018,1018,NULL,1002,3,62.00,0.00,186.00,0,NULL),(1019,1019,NULL,1008,1016,1,1250.00,0.00,1250.00,0,NULL),(1020,1020,1020,NULL,1013,1,210.00,0.00,210.00,0,'Third bottle slightly damaged.'),(1021,1011,1021,NULL,1011,4,48.00,0.00,192.00,1,'Crate B resellable.'),(1022,1011,1022,NULL,1011,4,48.00,0.00,192.00,0,'Crate C damaged - write off.');
/*!40000 ALTER TABLE `sale_return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_return_payments`
--

DROP TABLE IF EXISTS `sale_return_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_return_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `return_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('original','cash','card','mobile','customer_balance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'original',
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `processed_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `processed_by` (`processed_by`),
  KEY `idx_rp_return` (`return_id`),
  CONSTRAINT `sale_return_payments_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_return_payments_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1013 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_return_payments`
--

LOCK TABLES `sale_return_payments` WRITE;
/*!40000 ALTER TABLE `sale_return_payments` DISABLE KEYS */;
INSERT INTO `sale_return_payments` VALUES (1001,1001,214.50,'cash',NULL,NULL,10,'2026-08-02 05:12:00'),(1002,1002,66.00,'card','REFCARD-1002',NULL,10,'2026-08-15 07:15:00'),(1003,1006,528.00,'cash',NULL,NULL,10,'2026-09-16 04:45:00'),(1004,1007,165.00,'mobile','REFMOB-1007',NULL,10,'2026-08-24 03:30:00'),(1005,1008,476.67,'mobile','REFMOB-1008','First refund on SEED-INV-20260927-0020 (2 of 3 units).',10,'2026-09-28 04:00:00'),(1006,1009,313.50,'cash',NULL,NULL,10,'2026-08-23 09:40:00'),(1007,1010,633.60,'mobile','REFMOB-1010',NULL,10,'2026-09-05 05:40:00'),(1008,1011,550.00,'cash',NULL,NULL,10,'2026-09-10 04:45:00'),(1009,1012,33.75,'mobile','REFMOB-1012',NULL,10,'2026-08-09 05:20:00'),(1010,1015,832.15,'card','REFCARD-1015','Card refund to the original payment card.',10,'2026-08-28 05:00:00'),(1011,1017,275.00,'cash',NULL,NULL,10,'2026-09-22 03:30:00'),(1012,1020,238.33,'mobile','REFMOB-1020','Second refund on SEED-INV-20260927-0020 - closes the remaining balance.',10,'2026-09-30 03:30:00');
/*!40000 ALTER TABLE `sale_return_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_returns`
--

DROP TABLE IF EXISTS `sale_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_returns` (
  `id` int NOT NULL AUTO_INCREMENT,
  `return_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('pos','online') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pos',
  `sale_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `branch_id` int NOT NULL,
  `status` enum('requested','approved','rejected','received','refunded','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `reason_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `method` enum('original','cash','card','mobile','customer_balance') COLLATE utf8mb4_unicode_ci DEFAULT 'original',
  `subtotal` decimal(10,2) DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `tax` decimal(10,2) DEFAULT '0.00',
  `shipping` decimal(10,2) DEFAULT '0.00',
  `refund_total` decimal(10,2) DEFAULT '0.00',
  `loyalty_reversed` int NOT NULL DEFAULT '0',
  `exchange_sale_id` int DEFAULT NULL,
  `requested_by` int DEFAULT NULL,
  `requested_for` int DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `received_by` int DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `refunded_by` int DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `return_no` (`return_no`),
  KEY `customer_id` (`customer_id`),
  KEY `exchange_sale_id` (`exchange_sale_id`),
  KEY `idx_return_sale` (`sale_id`),
  KEY `idx_return_order` (`order_id`),
  KEY `idx_return_status` (`status`),
  KEY `idx_return_branch` (`branch_id`),
  KEY `idx_return_created` (`created_at`),
  CONSTRAINT `sale_returns_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  CONSTRAINT `sale_returns_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_returns_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_returns_ibfk_4` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `sale_returns_ibfk_5` FOREIGN KEY (`exchange_sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_returns`
--

LOCK TABLES `sale_returns` WRITE;
/*!40000 ALTER TABLE `sale_returns` DISABLE KEYS */;
INSERT INTO `sale_returns` VALUES (1001,'SEED-RET-1001','pos',1001,NULL,1001,1,'refunded','damaged',NULL,'original',195.00,0.00,19.50,0.00,214.50,21,NULL,10,NULL,10,'2026-08-02 05:00:00',10,'2026-08-02 05:10:00',10,'2026-08-02 05:12:00',NULL,'2026-08-02 05:00:00','2026-09-28 16:02:38'),(1002,'SEED-RET-1002','pos',1002,NULL,1002,1,'refunded','changed_mind',NULL,'original',62.00,2.00,6.00,0.00,66.00,6,NULL,10,NULL,10,'2026-08-15 07:00:00',10,'2026-08-15 07:10:00',10,'2026-08-15 07:15:00',NULL,'2026-08-15 07:00:00','2026-09-28 16:02:38'),(1003,'SEED-RET-1003','online',NULL,1005,NULL,1,'rejected','changed_mind',NULL,'original',100.00,5.00,9.50,60.00,164.50,0,NULL,NULL,1016,1,'2026-08-28 08:00:00',NULL,NULL,NULL,NULL,'Customer never sent the goods back; request rejected.','2026-08-25 04:00:00','2026-09-28 16:02:38'),(1004,'SEED-RET-1004','online',NULL,1002,1001,1,'requested','damaged',NULL,'original',480.00,96.00,38.40,0.00,422.40,0,NULL,NULL,1005,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-16 05:00:00','2026-09-28 16:02:38'),(1005,'SEED-RET-1005','online',NULL,1004,NULL,1,'requested','wrong_item',NULL,'original',185.00,0.00,18.50,60.00,263.50,0,NULL,NULL,1016,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-20 06:00:00','2026-09-28 16:02:38'),(1006,'SEED-RET-1006','pos',1014,NULL,1004,1,'refunded','not_as_described',NULL,'original',480.00,0.00,48.00,0.00,528.00,52,NULL,10,NULL,10,'2026-09-16 04:00:00',10,'2026-09-16 04:30:00',10,'2026-09-16 04:45:00',NULL,'2026-09-16 04:00:00','2026-09-28 16:02:38'),(1007,'SEED-RET-1007','pos',1007,NULL,1009,1,'refunded','defective',NULL,'original',165.00,15.00,15.00,0.00,165.00,16,NULL,10,NULL,10,'2026-08-24 03:00:00',10,'2026-08-24 03:20:00',10,'2026-08-24 03:30:00',NULL,'2026-08-24 03:00:00','2026-09-28 16:02:38'),(1008,'SEED-RET-1008','pos',1020,NULL,1009,1,'refunded','defective',NULL,'original',420.00,20.00,40.00,36.67,476.67,47,NULL,10,NULL,10,'2026-09-28 03:30:00',10,'2026-09-28 03:45:00',10,'2026-09-28 04:00:00',NULL,'2026-09-28 03:30:00','2026-09-28 16:02:38'),(1009,'SEED-RET-1009','pos',1006,NULL,1006,1,'refunded','other',NULL,'original',285.00,0.00,28.50,0.00,313.50,31,NULL,10,NULL,10,'2026-08-23 09:00:00',10,'2026-08-23 09:30:00',10,'2026-08-23 09:40:00',NULL,'2026-08-23 09:00:00','2026-09-28 16:02:38'),(1010,'SEED-RET-1010','pos',1010,NULL,1011,1,'refunded','defective',NULL,'original',576.00,0.00,57.60,0.00,633.60,63,NULL,10,NULL,10,'2026-09-05 05:00:00',10,'2026-09-05 05:30:00',10,'2026-09-05 05:40:00',NULL,'2026-09-05 05:00:00','2026-09-28 16:02:38'),(1011,'SEED-RET-1011','pos',1009,NULL,1010,1,'refunded','other',NULL,'original',576.00,76.00,50.00,0.00,550.00,55,NULL,10,NULL,10,'2026-09-10 04:00:00',10,'2026-09-10 04:15:00',10,'2026-09-10 04:45:00',NULL,'2026-09-10 04:00:00','2026-09-28 16:02:38'),(1012,'SEED-RET-1012','pos',1003,NULL,1003,1,'refunded','damaged',NULL,'original',25.00,0.00,2.50,6.25,33.75,3,NULL,10,NULL,10,'2026-08-08 06:00:00',10,'2026-08-09 05:00:00',10,'2026-08-09 05:20:00',NULL,'2026-08-08 06:00:00','2026-09-28 16:02:38'),(1013,'SEED-RET-1013','online',NULL,1003,NULL,1,'requested','not_as_described',NULL,'original',190.00,0.00,19.00,20.00,229.00,0,NULL,NULL,1016,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-20 03:00:00','2026-09-28 16:02:38'),(1014,'SEED-RET-1014','online',NULL,1001,1001,1,'rejected','damaged',NULL,'original',65.00,0.00,6.50,16.67,88.17,0,NULL,NULL,1005,1,'2026-08-16 04:00:00',NULL,NULL,NULL,NULL,'Damaged at delivery - refund declined; replacement issued via support ticket.','2026-08-15 05:00:00','2026-09-28 16:02:38'),(1015,'SEED-RET-1015','online',NULL,1006,1001,1,'refunded','defective',NULL,'original',890.00,133.50,75.65,0.00,832.15,0,NULL,NULL,1005,1,'2026-08-27 04:00:00',10,'2026-08-28 04:30:00',10,'2026-08-28 05:00:00',NULL,'2026-08-27 04:00:00','2026-09-28 16:02:38'),(1016,'SEED-RET-1016','pos',1014,NULL,1004,1,'approved','not_as_described',NULL,'original',480.00,0.00,48.00,0.00,528.00,0,NULL,10,NULL,10,'2026-09-18 04:30:00',NULL,NULL,NULL,NULL,NULL,'2026-09-18 04:30:00','2026-09-28 16:02:38'),(1017,'SEED-RET-1017','pos',1017,NULL,1005,1,'refunded','damaged',NULL,'original',260.00,10.00,25.00,0.00,275.00,27,NULL,10,NULL,10,'2026-09-22 03:00:00',10,'2026-09-22 03:15:00',10,'2026-09-22 03:30:00',NULL,'2026-09-22 03:00:00','2026-09-28 16:02:38'),(1018,'SEED-RET-1018','pos',1018,NULL,1006,1,'cancelled','other',NULL,'original',186.00,0.00,18.60,40.00,244.60,0,NULL,10,NULL,10,'2026-09-24 04:00:00',NULL,NULL,NULL,NULL,'VOIDED: duplicate return keyed in error.','2026-09-24 04:00:00','2026-09-28 16:02:38'),(1019,'SEED-RET-1019','online',NULL,1008,NULL,1,'requested','defective',NULL,'original',1250.00,0.00,125.00,0.00,1375.00,0,NULL,NULL,1016,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-05 09:00:00','2026-09-28 16:02:38'),(1020,'SEED-RET-1020','pos',1020,NULL,1009,1,'refunded','not_as_described',NULL,'original',210.00,10.00,20.00,18.33,238.33,23,NULL,10,NULL,10,'2026-09-30 03:00:00',10,'2026-09-30 03:15:00',10,'2026-09-30 03:30:00',NULL,'2026-09-30 03:00:00','2026-09-28 16:02:38');
/*!40000 ALTER TABLE `sale_returns` ENABLE KEYS */;
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
  `status` enum('completed','held','cancelled','partially_returned','returned') DEFAULT 'completed',
  `notes` text,
  `created_by` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `exchange_return_id` int DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (1001,1001,'SEED-INV-20260731-0001',195.00,0.00,19.50,0.00,214.50,'cash','returned','Counter sale - soft drinks.',10,1,NULL,'2026-07-31 04:12:00','2026-07-31 04:12:00'),(1002,1002,'SEED-INV-20260803-0002',310.00,10.00,30.00,0.00,330.00,'card','partially_returned','Card sale with line discount.',10,1,NULL,'2026-08-03 09:40:00','2026-08-03 09:40:00'),(1003,1003,'SEED-INV-20260806-0003',200.00,0.00,20.00,50.00,270.00,'mobile','partially_returned','Mobile payment plus delivery charge.',10,1,NULL,'2026-08-06 06:05:00','2026-08-06 06:05:00'),(1004,1004,'SEED-INV-20260810-0004',180.00,0.00,18.00,0.00,198.00,'cash','returned','Fully returned - goods damaged on delivery.',10,1,NULL,'2026-08-10 12:22:00','2026-08-12 05:00:00'),(1005,1005,'SEED-INV-20260814-0005',440.00,40.00,40.00,0.00,440.00,'card','completed','VIP customer discount applied.',10,1,NULL,'2026-08-14 08:30:00','2026-08-14 08:30:00'),(1006,1006,'SEED-INV-20260818-0006',570.00,0.00,57.00,0.00,627.00,'cash','partially_returned','Juice pack sale.',10,1,NULL,'2026-08-18 03:55:00','2026-08-18 03:55:00'),(1007,1009,'SEED-INV-20260822-0007',165.00,15.00,15.00,0.00,165.00,'mobile','returned','Running-balance customer purchase.',10,1,NULL,'2026-08-22 10:10:00','2026-08-22 10:10:00'),(1008,1004,'SEED-INV-20260826-0008',370.00,0.00,37.00,60.00,467.00,'card','held','Held sale - customer left to fetch cash. Never resumed.',10,1,NULL,'2026-08-26 13:44:00','2026-08-26 13:44:00'),(1009,1010,'SEED-INV-20260830-0009',576.00,76.00,50.00,0.00,550.00,'cash','returned','Promotional bundle discount.',10,1,NULL,'2026-08-30 05:20:00','2026-08-30 05:20:00'),(1010,1011,'SEED-INV-20260903-0010',576.00,0.00,57.60,0.00,633.60,'mobile','returned','Mobile payment.',10,1,NULL,'2026-09-03 07:35:00','2026-09-03 07:35:00'),(1011,1012,'SEED-INV-20260906-0011',210.00,10.00,20.00,0.00,220.00,'card','completed','Card sale.',10,1,NULL,'2026-09-06 04:05:00','2026-09-06 04:05:00'),(1012,1014,'SEED-INV-20260909-0012',18000.00,0.00,1800.00,0.00,19800.00,'card','completed','Corporate smartphone order.',10,1,NULL,'2026-09-09 11:50:00','2026-09-09 11:50:00'),(1013,1003,'SEED-INV-20260912-0013',2500.00,250.00,225.00,0.00,2475.00,'card','completed','Flash drive bulk order.',10,1,NULL,'2026-09-12 06:15:00','2026-09-12 06:15:00'),(1014,1004,'SEED-INV-20260914-0014',960.00,0.00,96.00,0.00,1056.00,'cash','partially_returned','Milk powder purchase.',10,1,NULL,'2026-09-14 04:40:00','2026-09-14 04:40:00'),(1015,1005,'SEED-INV-20260916-0015',850.00,0.00,85.00,0.00,935.00,'card','completed','Milk powder with line discount.',10,1,NULL,'2026-09-16 10:00:00','2026-09-16 10:00:00'),(1016,1007,'SEED-INV-20260918-0016',4900.00,100.00,480.00,0.00,5280.00,'card','completed','Wireless earbuds - VIP customer.',10,1,NULL,'2026-09-18 08:20:00','2026-09-18 08:20:00'),(1017,1005,'SEED-INV-20260920-0017',260.00,10.00,25.00,0.00,275.00,'mixed','returned','Mixed payment - see sale_payments 1016/1017.',10,1,NULL,'2026-09-20 03:50:00','2026-09-20 03:50:00'),(1018,1006,'SEED-INV-20260922-0018',186.00,0.00,18.60,40.00,244.60,'mixed','completed','Mixed payment with delivery charge.',10,1,NULL,'2026-09-22 05:35:00','2026-09-22 05:35:00'),(1019,1008,'SEED-INV-20260925-0019',350.00,0.00,35.00,0.00,385.00,'cash','cancelled','Cancelled at the counter - stock restored.',10,1,NULL,'2026-09-25 09:10:00','2026-09-25 09:25:00'),(1020,1009,'SEED-INV-20260927-0020',630.00,30.00,60.00,55.00,715.00,'mobile','returned','Hand wash order with express shipping.',10,1,NULL,'2026-09-27 06:15:00','2026-09-27 06:15:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=1027 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'company_name','EZ_SIMBS Store','company','2026-09-07 08:46:09','2026-09-07 08:46:09'),(2,'company_address','123 Business St, City','company','2026-09-07 08:46:09','2026-09-07 08:46:09'),(3,'company_phone','+1234567890','company','2026-09-07 08:46:09','2026-09-07 08:46:09'),(4,'company_email','info@ezsimbs.local','company','2026-09-07 08:46:09','2026-09-07 08:46:09'),(5,'tax_rate','10','billing','2026-09-07 08:46:09','2026-09-07 08:46:09'),(6,'currency','USD','billing','2026-09-07 08:46:09','2026-09-07 08:46:09'),(7,'currency_symbol','$','billing','2026-09-07 08:46:09','2026-09-07 08:46:09'),(8,'invoice_prefix','INV','billing','2026-09-07 08:46:09','2026-09-07 08:46:09'),(9,'po_prefix','PO','billing','2026-09-07 08:46:09','2026-09-07 08:46:09'),(10,'low_stock_threshold','10','inventory','2026-09-07 08:46:09','2026-09-07 08:46:09'),(11,'timezone','UTC','general','2026-09-07 08:46:09','2026-09-07 08:46:09'),(12,'theme','light','appearance','2026-09-07 08:46:09','2026-09-17 15:20:46'),(13,'company_tax_id','','general','2026-09-07 12:43:04','2026-09-07 12:43:04'),(1001,'SEED_storefront_enabled','1','general','2026-06-30 03:00:00','2026-06-30 03:00:00'),(1002,'SEED_storefront_currency','BDT','general','2026-06-30 03:01:00','2026-06-30 03:01:00'),(1003,'SEED_default_tax_rate','10','general','2026-06-30 03:02:00','2026-06-30 03:02:00'),(1004,'SEED_low_stock_alert_email','inventory@ezsimbs.test','notifications','2026-06-30 03:03:00','2026-06-30 03:03:00'),(1005,'SEED_login_max_attempts','5','security','2026-06-30 03:04:00','2026-06-30 03:04:00'),(1006,'SEED_login_lockout_minutes','15','security','2026-06-30 03:05:00','2026-06-30 03:05:00'),(1007,'SEED_session_lifetime_minutes','60','security','2026-06-30 03:06:00','2026-06-30 03:06:00'),(1008,'SEED_pos_receipt_footer','Thank you for shopping with EZ SIMBS!','pos','2026-06-30 03:07:00','2026-06-30 03:07:00'),(1009,'SEED_invoice_terms','Payment due within 15 days of invoice date.','general','2026-06-30 03:08:00','2026-06-30 03:08:00'),(1010,'SEED_barcode_prefix','SEED','inventory','2026-06-30 03:09:00','2026-06-30 03:09:00'),(1011,'SEED_sku_prefix','SEED-SKU-','inventory','2026-06-30 03:10:00','2026-06-30 03:10:00'),(1012,'SEED_invoice_prefix','SEED-INV-','general','2026-06-30 03:11:00','2026-06-30 03:11:00'),(1013,'SEED_po_prefix','SEED-PO-','general','2026-06-30 03:12:00','2026-06-30 03:12:00'),(1014,'SEED_order_prefix','SEED-ORD-','general','2026-06-30 03:13:00','2026-06-30 03:13:00'),(1015,'SEED_loyalty_points_per_taka','1','storefront','2026-06-30 03:14:00','2026-06-30 03:14:00'),(1016,'SEED_loyalty_min_redeem','500','storefront','2026-06-30 03:15:00','2026-06-30 03:15:00'),(1017,'SEED_free_shipping_threshold','2000','storefront','2026-06-30 03:16:00','2026-06-30 03:16:00'),(1018,'SEED_default_shipping_fee','60','storefront','2026-06-30 03:17:00','2026-06-30 03:17:00'),(1019,'SEED_test_mode','1','general','2026-06-30 03:18:00','2026-06-30 03:18:00'),(1020,'SEED_support_email','support@ezsimbs.test','general','2026-06-30 03:19:00','2026-06-30 03:19:00'),(1021,'return_window_days','14','returns','2026-09-28 15:11:21','2026-09-28 15:11:21'),(1022,'return_reasons','damaged,wrong_item,not_as_described,defective,changed_mind,other','returns','2026-09-28 15:11:21','2026-09-28 15:11:21'),(1023,'return_require_approval','1','returns','2026-09-28 15:11:21','2026-09-28 15:11:21'),(1024,'return_auto_restock','1','returns','2026-09-28 15:11:21','2026-09-28 15:11:21'),(1025,'return_allow_cashier_refund','1','returns','2026-09-28 15:11:21','2026-09-28 15:11:21'),(1026,'return_tender_lock','1','returns','2026-09-28 15:11:21','2026-09-28 15:11:21');
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
) ENGINE=InnoDB AUTO_INCREMENT=1024 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_logs`
--

LOCK TABLES `stock_logs` WRITE;
/*!40000 ALTER TABLE `stock_logs` DISABLE KEYS */;
INSERT INTO `stock_logs` VALUES (4,7,1,'in',10,2,'purchase','PO received',1,'2026-09-07 11:00:56'),(5,7,1,'out',2,1,'sale','Sale #INV-20260907-5657',1,'2026-09-07 11:01:19'),(6,7,1,'out',2,2,'sale','Sale #INV-20260907-7929',1,'2026-09-07 11:05:27'),(7,7,1,'in',2,2,'sale_cancelled','Cancelled sale #INV-20260907-7929',1,'2026-09-07 11:05:41'),(8,7,1,'out',1,3,'sale','Sale #INV-20260907-3771',1,'2026-09-07 11:05:59'),(9,7,1,'out',2,4,'sale','Sale #INV-20260907-0290',1,'2026-09-07 11:07:58'),(10,7,1,'return',1,4,'sale','Return from sale #INV-20260907-0290',1,'2026-09-07 11:09:41'),(11,7,1,'out',1,5,'sale','Sale #INV-20260907-2950',1,'2026-09-07 11:10:51'),(12,7,1,'return',1,5,'sale','Return from sale #INV-20260907-2950',1,'2026-09-07 11:10:52'),(1001,1001,1,'in',100,1001,'purchase','Goods received against SEED-PO-20260601-0001.',1,'2026-06-03 09:20:00'),(1002,1006,1,'in',200,1002,'purchase','Chips received - second part payment cleared.',1,'2026-06-07 06:00:00'),(1003,1003,1,'in',50,1004,'purchase','Energy drinks received - tax-exempt invoice.',1,'2026-06-15 05:00:00'),(1004,1008,1,'in',12,1013,'purchase','Milk powder received at warehouse branch.',8,'2026-07-21 06:15:00'),(1005,1010,1,'in',70,1008,'purchase','Soap consignment received - quarterly order.',1,'2026-07-02 04:20:00'),(1006,1014,1,'in',6,1010,'purchase','Smartphone consignment stock received.',1,'2026-07-10 08:00:00'),(1007,1016,1,'in',20,1011,'purchase','Flash drives received - paid in 3 instalments.',1,'2026-07-14 11:30:00'),(1008,1001,1,'out',3,1001,'sale','POS sale SEED-INV-20260731-0001.',10,'2026-07-31 04:12:00'),(1009,1002,1,'out',5,1002,'sale','POS sale SEED-INV-20260803-0002.',10,'2026-08-03 09:40:00'),(1010,1006,1,'out',8,1003,'sale','POS sale SEED-INV-20260806-0003.',10,'2026-08-06 06:05:00'),(1011,1007,1,'out',10,1004,'sale','POS sale - later returned, stock restored.',10,'2026-08-10 12:22:00'),(1012,1011,1,'out',12,1009,'sale','POS sale SEED-INV-20260830-0009. Sale 1008 is on hold and correctly produced NO stock movement.',10,'2026-08-30 04:05:00'),(1013,1016,1,'out',2,1013,'sale','Flash drive bulk order.',10,'2026-09-12 06:15:00'),(1014,1004,1,'out',2,1014,'sale','Milk powder purchase.',10,'2026-09-14 04:40:00'),(1015,1015,1,'out',2,1016,'sale','Earbuds sold to VIP customer.',10,'2026-09-18 08:20:00'),(1016,1003,1,'adjustment',-2,NULL,'stock_take','Physical count correction - 2 cans missing.',9,'2026-09-20 11:00:00'),(1017,1009,1,'damage',-1,NULL,'damage_write_off','Bottle leaked in storage - written off.',9,'2026-09-21 05:30:00'),(1018,1012,1,'return',8,1010,'sale_return','Dettol returned from SEED-INV-20260903-0010 (8 of the 8 units sold).',10,'2026-09-05 05:00:00'),(1019,1002,2,'transfer',20,NULL,'transfer','Transfer from branch 1 to branch 2 to clear stock (product 1002 is stocked at both branches).',1,'2026-08-05 08:20:00'),(1020,1011,1,'out',1,NULL,'manual_issue','Staff consumption - sample write-off.',1,'2026-09-23 04:45:00'),(1021,1011,1,'in',4,1011,'sale_return','Restock from SEED-RET-1011 line A - crate resellable.',10,'2026-09-10 04:30:00'),(1022,1011,1,'in',4,1011,'sale_return','Restock from SEED-RET-1011 line B - crate resellable.',10,'2026-09-10 04:30:00'),(1023,1006,1,'in',1,1012,'sale_return','Restock from SEED-RET-1012 - single pack resellable.',10,'2026-08-09 05:00:00');
/*!40000 ALTER TABLE `stock_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT '',
  `address` text,
  `currency` varchar(10) DEFAULT 'USD',
  `currency_symbol` varchar(5) DEFAULT '$',
  `plan` enum('free','pro') DEFAULT 'free',
  `status` enum('pending','active','suspended') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stores`
--

LOCK TABLES `stores` WRITE;
/*!40000 ALTER TABLE `stores` DISABLE KEYS */;
INSERT INTO `stores` VALUES (1,'EZ SIMBS Demo Store','Administrator','admin@ezsimbs.local','+1234567890','123 Business St, City','USD','$','free','active','2026-09-17 12:33:30','2026-09-17 12:33:30'),(3,'linea','Alisha Johura','alishajohura02@gmail.com','011222888','','USD','$','free','active','2026-09-17 12:47:08','2026-09-17 12:47:08'),(1001,'Sunrise Mega Mart','Arif Hossain','owner.sunrise@seed.test','+8801711000001','House 12, Road 5, Banani, Dhaka-1213','BDT','BDT','pro','active','2026-03-05 03:15:00','2026-03-05 03:15:00'),(1002,'Blue Ocean Superstore','Nusrat Jahan','owner.blueocean@seed.test','+8801711000002','Plot 88, Agrabad C/A, Chittagong-4000','BDT','BDT','pro','active','2026-03-08 04:20:00','2026-03-08 04:20:00'),(1003,'Green Leaf Grocery','Rakib Hasan','owner.greenleaf@seed.test','+8801711000003','House 7, Dhanmondi Road 27, Dhaka-1209','BDT','BDT','free','active','2026-03-11 05:05:00','2026-03-11 05:05:00'),(1004,'Star Electronics','Tanvir Ahmed','owner.starelec@seed.test','+8801711000004','Shop 210, Bashundhara City, Panthapath, Dhaka-1222','BDT','BDT','pro','active','2026-03-14 06:40:00','2026-03-14 06:40:00'),(1005,'City Fresh Market','Sadia Islam','owner.cityfresh@seed.test','+8801711000005','Kachukhet Mor, Mirpur DOHS, Dhaka-1216','BDT','BDT','free','active','2026-03-17 03:50:00','2026-03-17 03:50:00'),(1006,'Apex Hardware','Mahmudul Hasan','owner.apex@seed.test','+8801711000006','Naya Paltan, Islampur, Dhaka-1100','BDT','BDT','pro','pending','2026-03-20 08:10:00','2026-03-20 08:10:00'),(1007,'Silver Fashion House','Farhana Akter','owner.silver@seed.test','+8801711000007','Bashundhara City Level 3, Dhaka-1222','BDT','BDT','free','active','2026-03-23 09:30:00','2026-03-23 09:30:00'),(1008,'Metro Pharmacy','Kamrul Islam','owner.metro@seed.test','+8801711000008','Kachfiruz, Shyamoli, Dhaka-1207','BDT','BDT','pro','active','2026-03-26 02:45:00','2026-03-26 02:45:00'),(1009,'Sunrise Outlet Gulshan','Arif Hossain','owner.sunrise2@seed.test','+8801711000009','Gulshan 2, Dhaka-1212','BDT','BDT','free','active','2026-04-02 04:00:00','2026-04-02 04:00:00'),(1010,'Sunrise Outlet Uttara','Arif Hossain','owner.sunrise3@seed.test','+8801711000010','Sector 7, Uttara, Dhaka-1230','BDT','BDT','free','suspended','2026-04-05 05:20:00','2026-04-05 05:20:00'),(1011,'Blue Ocean Outlet Sylhet','Nusrat Jahan','owner.blueocean2@seed.test','+8801711000011','Zindabazar, Sylhet-3100','BDT','BDT','free','active','2026-04-09 07:35:00','2026-04-09 07:35:00'),(1012,'Green Leaf Outlet Mirpur','Rakib Hasan','owner.greenleaf2@seed.test','+8801711000012','Kazipara, Mirpur, Dhaka-1216','BDT','BDT','free','pending','2026-04-12 03:05:00','2026-04-12 03:05:00'),(1013,'Star Electronics Lalmatia','Tanvir Ahmed','owner.starelec2@seed.test','+8801711000013','Lalmatia, Dhaka-1207','BDT','BDT','free','active','2026-04-16 10:15:00','2026-04-16 10:15:00'),(1014,'City Fresh Express','Sadia Islam','owner.cityfresh2@seed.test','+8801711000014','Mohammadpur, Dhaka-1207','BDT','BDT','free','active','2026-04-20 04:40:00','2026-04-20 04:40:00'),(1015,'Apex Hardware Bogura','Mahmudul Hasan','owner.apex2@seed.test','+8801711000015','Sherpur Road, Bogura-5400','BDT','BDT','free','active','2026-04-24 06:00:00','2026-04-24 06:00:00'),(1016,'Silver Fashion Khulna','Farhana Akter','owner.silver2@seed.test','+8801711000016','Boyra More, Khulna-9100','BDT','BDT','free','suspended','2026-04-28 09:50:00','2026-04-28 09:50:00'),(1017,'Metro Pharmacy Tongi','Kamrul Islam','owner.metro2@seed.test','+8801711000017','Tongi, Gazipur-1710','BDT','BDT','free','active','2026-05-03 03:30:00','2026-05-03 03:30:00'),(1018,'Sunrise Warehouse','Arif Hossain','owner.sunrisewh@seed.test','+8801711000018','Jashore Sadar, Jashore-7400','BDT','BDT','pro','active','2026-05-07 08:25:00','2026-05-07 08:25:00'),(1019,'Test Suspended Store','Test Owner','owner.suspended@seed.test','+8801711000019','Test Address Line 1, Test City','USD','$','free','suspended','2026-05-12 05:11:00','2026-05-12 05:11:00'),(1020,'Test Pending Store','Test Owner','owner.pending@seed.test','+8801711000020','Test Address Line 2, Test City 2','USD','$','free','pending','2026-05-16 05:22:00','2026-05-16 05:22:00');
/*!40000 ALTER TABLE `stores` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'Acme Supplies','John Doe','john@acme.com','+1234567890','123 Supplier Rd','TAX-123',4.50,'active','2026-09-07 11:00:23','2026-09-07 11:00:23'),(1001,'Sunrise Foods & Beverages Ltd','Arif Hossain','sales@sunrisefoods.test','+8801712001001','Level 4, Bashundhara City, Dhaka-1222','BIN-1001-2233-44',4.80,'active','2026-06-26 04:00:00','2026-09-20 04:00:00'),(1002,'Blue Ocean Trading Co.','Nusrat Jahan','info@blueocean.test','+8801712001002','Plot 88, Agrabad C/A, Chittagong-4000','BIN-1002-5566-77',4.50,'active','2026-06-26 04:01:00','2026-09-18 04:01:00'),(1003,'Green Leaf Distributors','Rakib Hasan','contact@greenleaf.test','+8801712001003','House 7, Dhanmondi Road 27, Dhaka-1209','BIN-1003-8899-00',3.90,'active','2026-06-26 04:02:00','2026-09-16 04:02:00'),(1004,'Bengal FMCG Distributors','Tanvir Ahmed','sales@bengalfmcg.test','+8801712001004','Naya Paltan, Islampur, Dhaka-1100','BIN-1004-1122-33',4.20,'active','2026-06-26 04:03:00','2026-09-15 04:03:00'),(1005,'Star Electronics Supply','Sadia Islam','procurement@starelec.test','+8801712001005','Shop 210, Bashundhara City, Dhaka-1222','BIN-1005-4455-66',4.70,'active','2026-06-26 04:04:00','2026-09-14 04:04:00'),(1006,'Apex Hardware & Tools','Mahmudul Hasan','apex@hardwares.test','+8801712001006','Islampur Road, Dhaka-1100','BIN-1006-7788-99',3.50,'active','2026-06-26 04:05:00','2026-09-12 04:05:00'),(1007,'Silver Fashion Wholesale','Farhana Akter','wholesale@silverfashion.test','+8801712001007','Bashundhara City Level 3, Dhaka-1222','BIN-1007-0011-22',4.00,'active','2026-06-26 04:06:00','2026-09-10 04:06:00'),(1008,'Metro Pharma Supply','Kamrul Islam','orders@metropharma.test','+8801712001008','Shyamoli, Dhaka-1207','BIN-1008-3344-55',4.60,'active','2026-06-26 04:07:00','2026-09-09 04:07:00'),(1009,'Delta Paper Products','Rehana Parvin','hello@deltapaper.test','+8801712001009','Gulshan 1, Dhaka-1212','BIN-1009-6677-88',3.20,'active','2026-06-26 04:08:00','2026-09-08 04:08:00'),(1010,'Canton Electronics Import','Imran Kabir','import@cantonelec.test','+8801712001010','Jashore Sadar, Jashore-7400','BIN-1010-9900-11',4.10,'active','2026-06-26 04:09:00','2026-09-07 04:09:00'),(1011,'Meghna Consumer Products','Sumaiya Akter','sales@meghna.test','+8801712001011','Zindabazar, Sylhet-3100','BIN-1011-2233-44',3.80,'active','2026-06-26 04:10:00','2026-09-06 04:10:00'),(1012,'Jamuna Rice & Cereals','Mehedi Hasan','order@jamunarice.test','+8801712001012','Sherpur Road, Bogura-5400','BIN-1012-4455-66',4.30,'active','2026-06-26 04:11:00','2026-09-05 04:11:00'),(1013,'Padma Confectionery','Arif Hossain','order@padmaconf.test','+8801712001013','Boyra More, Khulna-9100','BIN-1013-5566-77',2.90,'active','2026-06-26 04:12:00','2026-09-04 04:12:00'),(1014,'Dhaka Packaging Supplies','Seed Admin','sales@dhakapack.test','+8801712001014','Tongi, Gazipur-1710','BIN-1014-7788-99',4.05,'active','2026-06-26 04:13:00','2026-09-03 04:13:00'),(1015,'Karnaphuli Chemicals','Seed Manager','supply@karnaphuli.test','+8801712001015','Kachfiruz, Chittagong-4000','BIN-1015-8899-00',3.00,'active','2026-06-26 04:14:00','2026-09-02 04:14:00'),(1016,'Old Contract Supplier (Closed)','Old Contact','old@contract.test','+8801712001016','Old Address, Old City','BIN-1016-1010-11',1.50,'inactive','2026-06-26 04:15:00','2026-07-15 04:15:00'),(1017,'Sungroud Transport & Freight','Imran Kabir','logistics@sungroud.test','+8801712001017','Jashore Sadar, Jashore-7400','BIN-1017-2121-32',4.40,'active','2026-06-26 04:16:00','2026-09-01 04:16:00'),(1018,'Test Supplier - No Rating','Test Person','test@supplier.test','+8801712001018','Test Street 1, Test City','BIN-1018-3131-43',0.00,'active','2026-06-26 04:17:00','2026-06-26 04:17:00'),(1019,'Test Supplier - Suspended','Test Person','suspended@supplier.test','+8801712001019','Test Street 2, Test City','BIN-1019-4141-54',2.00,'inactive','2026-06-26 04:18:00','2026-08-01 04:18:00'),(1020,'Premium Import House','Tanvir Ahmed','premium@import.test','+8801712001020','Level 8, Bashundhara City, Dhaka-1222','BIN-1020-5151-65',4.95,'active','2026-06-26 04:19:00','2026-08-28 04:19:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES (1,'Piece','pc','2026-09-07 08:46:09'),(2,'Kilogram','kg','2026-09-07 08:46:09'),(3,'Gram','g','2026-09-07 08:46:09'),(4,'Liter','L','2026-09-07 08:46:09'),(5,'Milliliter','mL','2026-09-07 08:46:09'),(6,'Meter','m','2026-09-07 08:46:09'),(8,'Pack','pk','2026-09-07 08:46:09'),(9,'chips','13','2026-09-07 09:20:37'),(10,'box','box','2026-09-07 09:56:40'),(1001,'Bottle','btl','2026-06-25 05:00:00'),(1002,'Carton','ctn','2026-06-25 05:01:00'),(1003,'Pack','pk','2026-06-25 05:02:00'),(1004,'Bag','bag','2026-06-25 05:03:00'),(1005,'Bottle (Small)','btl-s','2026-06-25 05:04:00'),(1006,'Tube','tube','2026-06-25 05:05:00'),(1007,'Pack of 6','pk6','2026-06-25 05:06:00'),(1008,'Set','set','2026-06-25 05:07:00'),(1009,'Piece (Small)','pcs','2026-06-25 05:08:00'),(1010,'Can','can','2026-06-25 05:09:00'),(1011,'Dozen','dz','2026-06-25 05:10:00'),(1012,'Roll','roll','2026-06-25 05:11:00'),(1013,'Sack (50kg)','sack50','2026-06-25 05:12:00'),(1014,'Box of 24','box24','2026-06-25 05:13:00'),(1015,'Gallon','gal','2026-06-25 05:14:00'),(1016,'Tray','tray','2026-06-25 05:15:00'),(1017,'Litre Bottle','lb','2026-06-25 05:16:00'),(1018,'Strip','strip','2026-06-25 05:17:00'),(1019,'Tablet Pack','tab','2026-06-25 05:18:00'),(1020,'Sack','sack','2026-06-25 05:19:00');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `device` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_activity` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  UNIQUE KEY `remember_token` (`remember_token`),
  KEY `idx_user_sessions_user` (`user_id`),
  KEY `idx_user_sessions_expires` (`expires_at`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1028 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessions`
--

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
INSERT INTO `user_sessions` VALUES (1001,1001,'seed1a0f3c9d2b4e5f6a7b8c9d0e1f2a3b4c','rseed1001aaaaaaaaaaaaaaaaaaaaaaaa','103.10.12.4','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0 Safari/537.36','Windows - Chrome','2026-09-27 10:02:00','2026-09-27 18:40:00','2026-10-04 10:02:00'),(1002,1001,'seed2b1f4d0e3c5f6a7b8c9d0e1f2a3b4c5d','rseed1002bbbbbbbbbbbbbbbbbbbbbbbb','103.10.12.4','Mozilla/5.0 (iPhone; CPU iPhone OS 17_4) Safari/604.1','iPhone - Safari','2026-09-26 08:15:00','2026-09-26 08:52:00','2026-09-27 08:15:00'),(1003,1002,'seed3c2a5e1f4d6a7b8c9d0e1f2a3b4c5d6e','rseed1003cccccccccccccccccccccccc','103.10.12.7','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Firefox/126.0','macOS - Firefox','2026-09-27 09:41:00','2026-09-27 17:05:00','2026-10-04 09:41:00'),(1004,1002,'seed4d3b6f2a5e7b8c9d0e1f2a3b4c5d6e7f','rseed1004dddddddddddddddddddddddd','49.12.44.9','Mozilla/5.0 (Linux; Android 14) Chrome Mobile 124.0','Android - Chrome','2026-09-20 12:00:00','2026-09-24 10:00:00','2026-09-21 12:00:00'),(1005,1003,'seed5e4c7a3b6f8c9d0e1f2a3b4c5d6e7f8a','rseed1005eeeeeeeeeeeeeeeeeeeeeeee','103.10.12.11','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Edge/124.0','Windows - Edge','2026-09-26 18:12:00','2026-09-26 21:30:00','2026-10-03 18:12:00'),(1006,1003,'seed6f5d8b4c7a9d0e1f2a3b4c5d6e7f8a9b','rseed1006ffffffffffffffffffffffff','103.10.12.11','Mozilla/5.0 (X11; Ubuntu; Linux x86_64) Chrome/124.0','Linux - Chrome','2026-08-30 14:20:00','2026-08-30 15:00:00','2026-08-31 14:20:00'),(1007,1004,'seed7a6e9c5d8b0e1f2a3b4c5d6e7f8a9b0c','rseed7000000000000000000000000000000','103.10.12.15','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-27 11:30:00','2026-09-27 19:12:00','2026-10-04 11:30:00'),(1008,1004,'seed8b7f0d6e9c1f2a3b4c5d6e7f8a9b0c1d','rseed8111111111111111111111111111111','103.10.12.16','Mozilla/5.0 (iPad; CPU OS 17_4) Safari/604.1','iPad - Safari','2026-09-22 09:45:00','2026-09-26 12:00:00','2026-10-22 09:45:00'),(1009,1005,'seed9c8a1e7f0d2a3b4c5d6e7f8a9b0c1d2e','rseed9222222222222222222222222222222','59.42.10.3','Mozilla/5.0 (iPhone; CPU iPhone OS 17_4) Chrome Mobile 124.0','iPhone - Chrome','2026-09-25 20:05:00','2026-09-27 09:00:00','2026-10-25 20:05:00'),(1010,1005,'seed0d9b2f8a1e3b4c5d6e7f8a9b0c1d2e3f','rseed9333333333333333333333333333333','59.42.10.3','Mozilla/5.0 (iPhone; CPU iPhone OS 17_4) Chrome Mobile 124.0','iPhone - Chrome','2026-08-14 16:00:00','2026-08-14 16:30:00','2026-08-15 16:00:00'),(1011,1006,'seed1e0c3a9b2f4c5d6e7f8a9b0c1d2e3f4a','rseed1044444444444444444444444444444','114.31.55.20','Mozilla/5.0 (Windows NT 11.0; Win64; x64) Chrome/125.0','Windows - Chrome','2026-09-24 08:55:00','2026-09-24 13:00:00','2026-10-24 08:55:00'),(1012,1007,'seed2f1d4b0c3a5d6e7f8a9b0c1d2e3f4a5b','rseed1155555555555555555555555555555','114.31.55.21','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-23 17:20:00','2026-09-23 18:00:00','2026-10-23 17:20:00'),(1013,1008,'seed3a2e5c1d4b6e7f8a9b0c1d2e3f4a5b6c','rseed1266666666666666666666666666666','114.31.55.22','Mozilla/5.0 (Linux; Android 14) Firefox/126.0','Android - Firefox','2026-09-22 12:00:00','2026-09-22 12:40:00','2026-10-22 12:00:00'),(1014,1009,'seed4b3f6d2e5c7f8a9b0c1d2e3f4a5b6c7d','rseed1377777777777777777777777777777','118.25.99.6','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-21 15:45:00','2026-09-21 16:30:00','2026-10-21 15:45:00'),(1015,1010,'seed5c4a7e3f6d8a9b0c1d2e3f4a5b6c7d8e','rseed1488888888888888888888888888888','118.25.99.7','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-20 19:30:00','2026-09-27 15:00:00','2026-10-20 19:30:00'),(1016,1011,'seed6d5b8f4a7e9b0c1d2e3f4a5b6c7d8e9f','rseed1599999999999999999999999999999','101.11.3.9','Mozilla/5.0 (Linux; Ubuntu 24.04) Chrome/125.0','Linux - Chrome','2026-09-19 10:10:00','2026-09-19 11:00:00','2026-10-19 10:10:00'),(1017,1012,'seed7e6c9a5b8f0c1d2e3f4a5b6c7d8e9f0a','rseed16aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','101.11.3.10','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-18 14:00:00','2026-09-18 14:45:00','2026-10-18 14:00:00'),(1018,1013,'seed8f7d0b6c9a1d2e3f4a5b6c7d8e9f0a1b','rseed17bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb','101.11.3.11','Mozilla/5.0 (Android 14) Chrome Mobile 124.0','Android - Chrome','2026-09-17 16:25:00','2026-09-17 16:55:00','2026-10-17 16:25:00'),(1019,1014,'seed9a8e1c7d0b2e3f4a5b6c7d8e9f0a1b2c','rseed18cccccccccccccccccccccccccccc','162.45.8.14','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/124.0','Windows - Chrome','2026-09-16 13:35:00','2026-09-27 09:20:00','2026-10-16 13:35:00'),(1020,1017,'seed0b9f2d8e1c3f4a5b6c7d8e9f0a1b2c3d','rseed19dddddddddddddddddddddddddddddddd','162.45.8.15','Mozilla/5.0 (X11; Linux x86_64) Firefox/125.0','Linux - Firefox','2026-09-13 11:05:00','2026-09-13 11:40:00','2026-10-13 11:05:00'),(1025,1,'e5456c223bd5bb2ad31b96978f002d73da68d3937bde6dd35b780724eb01fb31',NULL,'127.0.0.1','curl/8.18.0','Unknown · Unknown · Desktop','2026-09-28 20:05:52','2026-09-28 14:10:47','2026-10-05 14:05:52'),(1027,5,'21ae7a8933aee7081cefe5c3f8854ec3e6106c9478a8b60d8a4d7346a948dc60',NULL,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:156.0) Gecko/20100101 Firefox/156.0','Firefox · Linux · Desktop','2026-09-28 22:05:35','2026-09-28 16:05:35','2026-10-05 16:05:35');
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;
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
  `store_id` int DEFAULT NULL,
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
  KEY `idx_user_store` (`store_id`),
  CONSTRAINT `fk_user_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@ezsimbs.local','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1,1,1,NULL,NULL,'active','2026-09-28 14:05:52',0,NULL,NULL,'2026-09-07 08:46:09','2026-09-28 14:05:52'),(5,'Test Customer','customer@ezsimbs.local','$2y$12$NasVx3dqXgMY43lswjEG0.wLpkx7JjG.uhM4s2Y1r9HE9Ie0aCuL6',5,1,1,'','uploads/avatars/user_5_1789664797.png','active','2026-09-28 16:05:35',0,NULL,NULL,'2026-09-17 10:38:35','2026-09-28 16:05:35'),(8,'Manager','manager@ezsimbs.local','$2y$12$WdhNBO.bd3VaWRYYdu9.cOGuOB3J0YsTWlXZkmR0ApRAcMmLIlpSi',2,1,1,'+8801000000002',NULL,'active','2026-09-17 15:37:36',0,NULL,NULL,'2026-09-17 10:48:30','2026-09-17 15:37:36'),(9,'Branch Manager','branch@ezsimbs.local','$2y$12$WdhNBO.bd3VaWRYYdu9.cOGuOB3J0YsTWlXZkmR0ApRAcMmLIlpSi',3,1,1,'+8801000000003',NULL,'active','2026-09-17 16:49:33',0,NULL,NULL,'2026-09-17 10:48:30','2026-09-17 16:49:33'),(10,'Cashier','cashier@ezsimbs.local','$2y$12$WdhNBO.bd3VaWRYYdu9.cOGuOB3J0YsTWlXZkmR0ApRAcMmLIlpSi',4,1,1,'+8801000000004',NULL,'active','2026-09-28 15:20:52',0,NULL,NULL,'2026-09-17 10:48:30','2026-09-28 15:20:52'),(11,'Alisha Johura','alishajohura@gmail.com','$2y$12$1f0aIiTwrnMf3TxaSFYh6.c6HG4TxFsnQxzDUT9t.BwXTAZwKjkeG',5,NULL,1,'011222000',NULL,'active','2026-09-17 11:09:22',0,NULL,NULL,'2026-09-17 11:07:37','2026-09-17 12:41:19'),(13,'Alisha Johura','alishajohura02@gmail.com','$2y$12$ek7UrsW6rrIdqkqO8IKuy.4OyRB2H5Yiow0/zpyzD2sPNgBOmCa/W',1,5,3,'011222888',NULL,'active','2026-09-17 14:24:52',4,NULL,NULL,'2026-09-17 12:47:08','2026-09-17 16:49:02'),(1001,'Seed Admin','seed.admin@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1,1,1,'+8801700000001',NULL,'active','2026-09-27 10:02:00',0,NULL,NULL,'2026-06-15 03:00:00','2026-09-27 04:02:00'),(1002,'Seed Manager','seed.manager@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',2,2,1,'+8801700000002',NULL,'active','2026-09-27 09:41:00',0,NULL,NULL,'2026-06-15 03:00:00','2026-09-27 03:41:00'),(1003,'Seed Branch Manager','seed.branch@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',3,3,1,'+8801700000003',NULL,'active','2026-09-26 18:12:00',0,NULL,NULL,'2026-06-15 03:00:00','2026-09-26 12:12:00'),(1004,'Seed Cashier','seed.cashier@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',4,1,1,'+8801700000004',NULL,'active','2026-09-27 11:30:00',0,NULL,NULL,'2026-06-15 03:00:00','2026-09-27 05:30:00'),(1005,'Seed Customer','seed.customer@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',5,NULL,1,'+8801700000005',NULL,'active','2026-09-25 20:05:00',0,NULL,NULL,'2026-06-15 03:00:00','2026-09-25 14:05:00'),(1006,'Arif Hossain','arif@sunrise.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1001,1005,1001,'+8801712000001',NULL,'active','2026-09-24 08:55:00',0,NULL,NULL,'2026-06-16 03:00:00','2026-09-24 02:55:00'),(1007,'Nusrat Jahan','nusrat@blueocean.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',2,1008,1002,'+8801712000002',NULL,'active','2026-09-23 17:20:00',0,NULL,NULL,'2026-06-16 03:00:00','2026-09-23 11:20:00'),(1008,'Rakib Hasan','rakib@greenleaf.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1002,1010,1003,'+8801712000003',NULL,'active','2026-09-22 12:00:00',0,NULL,NULL,'2026-06-16 03:00:00','2026-09-22 06:00:00'),(1009,'Tanvir Ahmed','tanvir@starelec.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',3,1012,1004,'+8801712000004',NULL,'active','2026-09-21 15:45:00',0,NULL,NULL,'2026-06-17 03:00:00','2026-09-21 09:45:00'),(1010,'Sadia Islam','sadia@cityfresh.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',4,1014,1005,'+8801712000005',NULL,'active','2026-09-20 19:30:00',0,NULL,NULL,'2026-06-17 03:00:00','2026-09-20 13:30:00'),(1011,'Mahmudul Hasan','mahmudul@apex.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1003,1015,1006,'+8801712000006',NULL,'active','2026-09-19 10:10:00',0,NULL,NULL,'2026-06-17 03:00:00','2026-09-19 04:10:00'),(1012,'Farhana Akter','farhana@silver.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1005,1016,1007,'+8801712000007',NULL,'active','2026-09-18 14:00:00',0,NULL,NULL,'2026-06-18 03:00:00','2026-09-18 08:00:00'),(1013,'Kamrul Islam','kamrul@metro.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',4,1017,1008,'+8801712000008',NULL,'active','2026-09-17 16:25:00',0,NULL,NULL,'2026-06-18 03:00:00','2026-09-17 10:25:00'),(1014,'Rehana Parvin','rehana@sunrise.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1008,1006,1001,'+8801712000009',NULL,'active','2026-09-16 13:35:00',0,NULL,NULL,'2026-06-18 03:00:00','2026-09-16 07:35:00'),(1015,'Imran Kabir','imran@sunrise.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1004,1018,1001,'+8801712000010',NULL,'active','2026-09-15 07:50:00',0,NULL,NULL,'2026-06-19 03:00:00','2026-09-15 01:50:00'),(1016,'Sumaiya Akter','sumaiya@sunrise.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',5,NULL,1001,'+8801712000011',NULL,'active','2026-09-14 21:15:00',0,NULL,NULL,'2026-06-19 03:00:00','2026-09-14 15:15:00'),(1017,'Mehedi Hasan','mehedi@seed.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',1009,1007,1001,'+8801700000017',NULL,'active','2026-09-13 11:05:00',0,NULL,NULL,'2026-06-19 03:00:00','2026-09-13 05:05:00'),(1018,'Inactive Staff','inactive.user@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',2,2,1,'+8801700000018',NULL,'inactive','2026-07-30 10:00:00',0,NULL,NULL,'2026-06-20 03:00:00','2026-08-01 03:00:00'),(1019,'Locked User','locked.user@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',4,1,1,'+8801700000019',NULL,'locked','2026-09-10 08:00:00',5,'2026-09-28 12:00:00',NULL,'2026-06-20 03:00:00','2026-09-10 02:00:00'),(1020,'Recovery User','recovery.user@ezsimbs.test','$2y$12$LWgIGumS2nPudEEVGOqpeejnZ4XnBSwPlDLRb7IhYQGLPxGZZ6H3K',4,1,1,'+8801700000020',NULL,'active','2026-09-05 09:30:00',3,'2026-09-05 09:45:00',NULL,'2026-06-20 03:00:00','2026-09-05 03:45:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1021 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
INSERT INTO `wishlist` VALUES (1001,1005,1001,'2026-07-05 04:00:00'),(1002,1005,1003,'2026-07-06 04:00:00'),(1003,1005,1004,'2026-07-08 04:00:00'),(1004,1005,1006,'2026-07-10 04:00:00'),(1005,1016,1005,'2026-07-12 04:00:00'),(1006,1016,1008,'2026-07-14 04:00:00'),(1007,1016,1009,'2026-07-16 04:00:00'),(1008,1016,1010,'2026-07-18 04:00:00'),(1009,1012,1011,'2026-07-20 04:00:00'),(1010,1012,1012,'2026-07-22 04:00:00'),(1011,1012,1013,'2026-07-24 04:00:00'),(1012,1012,1007,'2026-07-26 04:00:00'),(1013,1014,1014,'2026-07-28 04:00:00'),(1014,1014,1015,'2026-07-30 04:00:00'),(1015,1014,1016,'2026-08-01 04:00:00'),(1016,1017,1002,'2026-08-03 04:00:00'),(1017,1017,1004,'2026-08-05 04:00:00'),(1018,1017,1006,'2026-08-07 04:00:00'),(1019,1017,1010,'2026-08-09 04:00:00'),(1020,1017,1013,'2026-08-11 04:00:00');
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'ez_simbs'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-28 22:37:55
