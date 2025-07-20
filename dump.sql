-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: montink_erp
-- ------------------------------------------------------
-- Server version	8.0.42

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
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `variations` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_items_product_id_foreign` (`product_id`),
  KEY `cart_items_session_id_product_id_index` (`session_id`,`product_id`),
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (1,'i9CbSZaQ1r7Ow46AYDcz2hlJTiTljcmlQtKl1rWV',1,2,100.00,NULL,'2025-07-19 11:00:06','2025-07-19 11:00:06'),(12,'OZ18qUfQNMecDZyAVAKbs9bIv6sMoQvRY7wGhT2x',9,1,49.90,'{\"size\": \"M\", \"color\": \"Azul\"}','2025-07-19 11:33:56','2025-07-19 11:33:56'),(14,'z5SACMjOrceTMISRhWKkoK83hNtX3VaP75Po8YqI',14,1,49.90,'{\"size\": \"M\", \"color\": \"Azul\"}','2025-07-19 11:37:53','2025-07-19 11:37:53'),(20,'tDPLYJk3XXBzb6p6kaVTpz3e9kgQPCJOxqfCrvEC',15,1,49.90,'{\"size\": \"M\", \"color\": \"Azul\"}','2025-07-19 11:38:17','2025-07-19 11:38:17'),(26,'rBD5E9lmwA4s94qFdHgjVXH9BfJE3HlF4ZdTlrvy',29,3,79.90,NULL,'2025-07-19 16:50:31','2025-07-19 16:50:31'),(27,'QutRk8CyTjYnDZ16LFeCHbsSThXERkaSbmUqHZ89',29,2,79.90,NULL,'2025-07-19 16:50:53','2025-07-19 16:50:53'),(28,'IeFjIRexJQQ8MOHEBCp2P6uHkWgdp6mnHciWcwJF',29,2,79.90,NULL,'2025-07-19 16:52:47','2025-07-19 16:52:47'),(30,'oMeYMLRb0FT410Moe1mGKRT83UHouwcL8YE70dl3',29,5,79.90,NULL,'2025-07-19 17:00:06','2025-07-19 17:00:06');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('fixed','percentage') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `minimum_value` decimal(10,2) DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `used_count` int NOT NULL DEFAULT '0',
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`),
  KEY `coupons_code_index` (`code`),
  KEY `coupons_active_valid_from_valid_until_index` (`active`,`valid_from`,`valid_until`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'MINIMO100-1752935379','Desconto de 10% com pedido mínimo de R$ 100','percentage',10.00,100.00,NULL,0,NULL,NULL,1,'2025-07-19 11:29:47','2025-07-19 11:29:47'),(2,'EXPIRADO-1752935379','Cupom expirado','fixed',20.00,NULL,NULL,0,'2025-07-18','2025-07-18',1,'2025-07-19 11:29:48','2025-07-19 11:29:48'),(3,'LIMITE1-1752935379','Cupom com limite de 1 uso','fixed',10.00,NULL,1,1,NULL,NULL,1,'2025-07-19 11:29:48','2025-07-19 11:29:48'),(4,'MINIMO100-1752935621','Desconto de 10% com pedido mínimo de R$ 100','percentage',10.00,100.00,NULL,0,NULL,NULL,1,'2025-07-19 11:33:49','2025-07-19 11:33:49'),(5,'EXPIRADO-1752935621','Cupom expirado','fixed',20.00,NULL,NULL,0,'2025-07-18','2025-07-18',1,'2025-07-19 11:33:49','2025-07-19 11:33:49'),(6,'LIMITE1-1752935621','Cupom com limite de 1 uso','fixed',10.00,NULL,1,1,NULL,NULL,1,'2025-07-19 11:33:49','2025-07-19 11:33:51'),(7,'MINIMO100-1752935886','Desconto de 10% com pedido mínimo de R$ 100','percentage',10.00,100.00,NULL,0,NULL,NULL,1,'2025-07-19 11:38:14','2025-07-19 11:38:14'),(8,'EXPIRADO-1752935886','Cupom expirado','fixed',20.00,NULL,NULL,0,'2025-07-18','2025-07-18',1,'2025-07-19 11:38:14','2025-07-19 11:38:14'),(9,'LIMITE1-1752935886','Cupom com limite de 1 uso','fixed',10.00,NULL,1,1,NULL,NULL,1,'2025-07-19 11:38:14','2025-07-19 11:38:15'),(10,'DESCONTO20-1752935970','Desconto de R$ 20,00','fixed',20.00,100.00,NULL,0,NULL,NULL,1,'2025-07-19 11:39:33','2025-07-19 11:39:33'),(11,'NATAL10-1752935970','10% de desconto','percentage',10.00,NULL,5,0,'2025-01-01','2025-12-31',1,'2025-07-19 11:39:33','2025-07-19 11:39:33'),(12,'E2E-DISCOUNT-1752935970','Cupom E2E 15%','percentage',15.00,NULL,NULL,1,NULL,NULL,1,'2025-07-19 11:39:40','2025-07-19 11:39:40'),(13,'TESTE1752938537','Cupom de teste API','percentage',10.00,50.00,NULL,0,NULL,NULL,1,'2025-07-19 12:22:29','2025-07-19 12:22:29'),(14,'DESCONTO50','Desconto de R$ 50,00','fixed',50.00,100.00,10,0,'2025-01-01','2025-12-31',1,'2025-07-19 16:18:13','2025-07-19 16:18:13'),(15,'PROMO10','10% de desconto','percentage',10.00,50.00,100,1,'2025-01-01','2025-12-31',1,'2025-07-19 16:18:32','2025-07-19 16:21:57'),(16,'TESTE10','Cupom de 10% de desconto','percentage',10.00,50.00,NULL,1,NULL,NULL,1,'2025-07-19 16:51:38','2025-07-19 17:19:26'),(17,'TEST10OFF1753019540',NULL,'percentage',10.00,NULL,100,1,'2025-07-20','2025-08-19',1,'2025-07-20 10:52:25','2025-07-20 10:52:30');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2024_01_01_000001_create_products_table',1),(2,'2025_07_17_000001_create_cart_items_table',1),(3,'2025_07_17_094430_create_stock_table',1),(4,'2025_07_17_101711_create_orders_table',1),(5,'2025_07_17_101721_create_order_items_table',1),(6,'2025_07_17_111106_create_coupons_table',1),(7,'2025_07_17_130000_add_coupon_id_to_orders_table',1),(8,'2025_07_19_000002_add_default_to_used_count_in_coupons_table',2),(9,'2025_07_19_000003_remove_unique_product_id_from_stock_table',3),(10,'2025_07_19_000004_create_users_table',4),(11,'2025_07_19_000005_create_refresh_tokens_table',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `variations` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_order_id_product_id_index` (`order_id`,`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (3,3,17,'Produto Médio','MID-1752935886',2,80.00,160.00,NULL,'2025-07-19 11:38:15','2025-07-19 11:38:15'),(4,4,21,'Produto E2E Test','E2E-TEST-1752935970',2,150.00,300.00,NULL,'2025-07-19 11:39:40','2025-07-19 11:39:40'),(5,5,24,'Produto Teste','TEST-1752943826',2,99.99,199.98,NULL,'2025-07-19 13:50:27','2025-07-19 13:50:27'),(6,6,25,'Produto Teste','TEST-1752945160',2,99.99,199.98,NULL,'2025-07-19 14:12:41','2025-07-19 14:12:41'),(8,8,26,'Notebook Dell Inspiron','NOTE-DELL-001',1,3599.90,3599.90,'{\"size\": \"15 polegadas\", \"color\": \"Preto\"}','2025-07-19 16:21:57','2025-07-19 16:21:57'),(9,8,27,'Mouse Logitech MX Master','MOUSE-LOG-001',2,399.90,799.80,NULL,'2025-07-19 16:21:57','2025-07-19 16:21:57'),(10,9,29,'Camiseta Teste Carrinho','CAM-CART-TEST',2,79.90,159.80,NULL,'2025-07-19 16:54:11','2025-07-19 16:54:11'),(11,10,29,'Camiseta Teste Carrinho','CAM-CART-TEST',2,79.90,159.80,NULL,'2025-07-19 17:19:26','2025-07-19 17:19:26'),(12,10,30,'Notebook Gamer','NOTE-GAMER-001',1,5999.90,5999.90,NULL,'2025-07-19 17:19:26','2025-07-19 17:19:26'),(13,11,31,'Produto Teste 1753019540','TEST-SKU-1753019540',3,149.90,449.70,NULL,'2025-07-20 10:52:30','2025-07-20 10:52:30');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_cpf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_cep` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_complement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_neighborhood` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_state` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `coupon_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coupon_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_order_number_index` (`order_number`),
  KEY `orders_customer_email_index` (`customer_email`),
  KEY `orders_status_index` (`status`),
  KEY `orders_created_at_status_index` (`created_at`,`status`),
  KEY `orders_coupon_id_foreign` (`coupon_id`),
  CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (3,'ORD-20250719-0476','João da Silva','joao@example.com','(11) 98765-4321','123.456.789-00','01310-100','Av. Paulista, 1000','Apto 1001','Bela Vista','São Paulo','SP',160.00,10.00,15.00,165.00,'shipped','LIMITE1-1752935886',9,'tDPLYJk3XXBzb6p6kaVTpz3e9kgQPCJOxqfCrvEC','2025-07-19 11:38:15','2025-07-19 11:38:17'),(4,'ORD-20250719-7934','Maria Santos E2E','maria.e2e@test.com','(21) 99999-8888','987.654.321-00','20040-020','Rua do Teste, 123',NULL,'Centro','Rio de Janeiro','RJ',300.00,45.00,0.00,255.00,'pending','E2E-DISCOUNT-1752935970',12,'gHc4nVZFCBwV1S4Fwht5PznviRp2aQE3FPBlACDU','2025-07-19 11:39:40','2025-07-19 11:39:40'),(5,'ORD-20250719-0308','João Silva','joao@example.com','(11) 98765-4321','123.456.789-00','01310-100','Avenida Paulista, 1000',NULL,'Bela Vista','São Paulo','SP',199.98,0.00,20.00,219.98,'pending',NULL,NULL,'NDsVn3Ld7DQRpppFkdzpXwiFIsvIVVP3AscG2qtx','2025-07-19 13:50:27','2025-07-19 13:50:27'),(6,'ORD-20250719-9329','João Silva','joao@example.com','(11) 98765-4321','123.456.789-00','01310-100','Avenida Paulista, 1000',NULL,'Bela Vista','São Paulo','SP',199.98,0.00,20.00,219.98,'pending',NULL,NULL,'hzBYu8eWWY8HkshvSk0kYeLLVOKP8KnE9ODpX6qa','2025-07-19 14:12:41','2025-07-19 14:12:41'),(8,'ORD-20250719-4557','João Silva','joao@example.com','(11) 98765-4321','123.456.789-00','01310-100','Avenida Paulista, 1000','Apto 101','Bela Vista','São Paulo','SP',4399.70,439.97,0.00,3959.73,'pending','PROMO10',15,'ojsW9RWlyWP2eIyFcWEi2HAoMfwQeAyVcS02aim5','2025-07-19 16:21:57','2025-07-19 16:21:57'),(9,'ORD-20250719-7497','Test User Final','test.final@example.com','(11) 98765-4321','123.456.789-00','01310-100','Av Paulista, 1000','Apt 101','Bela Vista','São Paulo','SP',159.80,0.00,15.00,174.80,'shipped',NULL,NULL,'2dLD0eQnjZYJksBLoV1VaaofZ1FKfvkOBbpv7Eoh','2025-07-19 16:54:11','2025-07-19 16:55:07'),(10,'ORD-20250719-4660','Teste Completo','teste.completo@example.com','(11) 99999-8888','123.456.789-00','01310-100','Avenida Paulista, 1000','Apto 1001','Bela Vista','São Paulo','SP',6159.70,615.97,0.00,5543.73,'shipped','TESTE10',16,'4OpYL6WTic2PE28ZaWiwejb8S2JauHAWSBQqdZgZ','2025-07-19 17:19:26','2025-07-19 17:21:16'),(11,'ORD-20250720-8737','Test User 1753019540','test_1753019540@example.com','(11) 98765-4321','123.456.789-00','01310-100','Av. Paulista, 1000',NULL,'Bela Vista','São Paulo','SP',449.70,44.97,0.00,404.73,'shipped','TEST10OFF1753019540',17,'SOebngxTI45Jv0IS6zzPN4KoEmjfmTSKJq1KADuy','2025-07-20 10:52:30','2025-07-20 10:52:31');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `variations` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_active_name_index` (`active`,`name`),
  KEY `products_sku_index` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Produto Teste','Descrição do produto',100.00,'TEST001',1,NULL,'2025-07-19 10:59:56','2025-07-19 10:59:56'),(2,'Camiseta Personalizada','Camiseta 100% algodão',49.90,'CAM-PERS-1752935379',1,'[{\"size\": \"P\", \"color\": \"Azul\"}, {\"size\": \"M\", \"color\": \"Azul\"}, {\"size\": \"G\", \"color\": \"Azul\"}]','2025-07-19 11:29:42','2025-07-19 11:29:42'),(3,'Produto Barato',NULL,30.00,'CHEAP-1752935379',1,NULL,'2025-07-19 11:29:44','2025-07-19 11:29:44'),(4,'Produto Médio',NULL,80.00,'MID-1752935379',1,NULL,'2025-07-19 11:29:44','2025-07-19 11:29:44'),(5,'Produto Caro',NULL,250.00,'EXP-1752935379',1,NULL,'2025-07-19 11:29:45','2025-07-19 11:29:45'),(6,'Produto com Variação',NULL,100.00,'VAR-TEST-1752935417',1,'[{\"size\": \"P\", \"color\": \"Azul\"}, {\"size\": \"M\", \"color\": \"Azul\"}]','2025-07-19 11:30:18','2025-07-19 11:30:18'),(7,'Produto com Variação',NULL,100.00,'VAR-TEST-1752935428',1,'[{\"size\": \"P\", \"color\": \"Azul\"}, {\"size\": \"M\", \"color\": \"Azul\"}]','2025-07-19 11:30:30','2025-07-19 11:30:30'),(8,'Camiseta Teste Variações',NULL,49.90,'CAM-VAR-1752935513',1,'[{\"size\": \"P\", \"color\": \"Azul\"}, {\"size\": \"M\", \"color\": \"Azul\"}, {\"size\": \"G\", \"color\": \"Azul\"}]','2025-07-19 11:31:56','2025-07-19 11:31:56'),(9,'Camiseta Personalizada','Camiseta 100% algodão',49.90,'CAM-PERS-1752935621',1,'[{\"size\": \"P\", \"color\": \"Azul\"}, {\"size\": \"M\", \"color\": \"Azul\"}, {\"size\": \"G\", \"color\": \"Azul\"}]','2025-07-19 11:33:44','2025-07-19 11:33:44'),(10,'Produto Barato',NULL,30.00,'CHEAP-1752935621',1,NULL,'2025-07-19 11:33:46','2025-07-19 11:33:46'),(11,'Produto Médio',NULL,80.00,'MID-1752935621',1,NULL,'2025-07-19 11:33:46','2025-07-19 11:33:46'),(12,'Produto Caro',NULL,250.00,'EXP-1752935621',1,NULL,'2025-07-19 11:33:46','2025-07-19 11:33:46'),(13,'Produto Simples','Produto sem variações',100.00,'SIMPLE-1752935869',1,NULL,'2025-07-19 11:37:51','2025-07-19 11:37:51'),(14,'Camiseta com Variações',NULL,49.90,'VAR-1752935869',1,'[{\"size\": \"P\", \"color\": \"Azul\"}, {\"size\": \"M\", \"color\": \"Azul\"}, {\"size\": \"G\", \"color\": \"Azul\"}]','2025-07-19 11:37:53','2025-07-19 11:37:53'),(15,'Camiseta Personalizada','Camiseta 100% algodão',49.90,'CAM-PERS-1752935886',1,'[{\"size\": \"P\", \"color\": \"Azul\"}, {\"size\": \"M\", \"color\": \"Azul\"}, {\"size\": \"G\", \"color\": \"Azul\"}]','2025-07-19 11:38:07','2025-07-19 11:38:07'),(16,'Produto Barato',NULL,30.00,'CHEAP-1752935886',1,NULL,'2025-07-19 11:38:07','2025-07-19 11:38:07'),(17,'Produto Médio',NULL,80.00,'MID-1752935886',1,NULL,'2025-07-19 11:38:08','2025-07-19 11:38:08'),(18,'Produto Caro',NULL,250.00,'EXP-1752935886',1,NULL,'2025-07-19 11:38:08','2025-07-19 11:38:08'),(19,'Notebook Dell XPS Atualizado','Notebook Premium com processador Intel i7',4799.90,'DELL-XPS-1752935970',1,'[{\"size\": \"15 polegadas\", \"color\": \"Prata\"}, {\"size\": \"13 polegadas\", \"color\": \"Preto\"}]','2025-07-19 11:39:32','2025-07-19 11:39:32'),(21,'Produto E2E Test','Produto para teste E2E',150.00,'E2E-TEST-1752935970',1,NULL,'2025-07-19 11:39:39','2025-07-19 11:39:39'),(22,'Notebook Test API','Notebook para testes de API',2999.90,'NOTE-API-1752938537',1,NULL,'2025-07-19 12:22:22','2025-07-19 12:22:22'),(23,'Mouse Wireless',NULL,89.90,'MOUSE-1752938537',1,NULL,'2025-07-19 12:22:24','2025-07-19 12:22:24'),(24,'Produto Teste','Descrição do produto',99.99,'TEST-1752943826',1,NULL,'2025-07-19 13:50:26','2025-07-19 13:50:26'),(25,'Produto Teste','Descrição do produto',99.99,'TEST-1752945160',1,NULL,'2025-07-19 14:12:41','2025-07-19 14:12:41'),(26,'Notebook Dell Inspiron','Notebook para desenvolvimento com 16GB RAM',3599.90,'NOTE-DELL-001',1,'[{\"size\": \"14 polegadas\", \"color\": \"Prata\"}, {\"size\": \"15 polegadas\", \"color\": \"Preto\"}]','2025-07-19 16:15:51','2025-07-19 16:15:51'),(27,'Mouse Logitech MX Master','Mouse profissional sem fio',399.90,'MOUSE-LOG-001',1,NULL,'2025-07-19 16:16:07','2025-07-19 16:16:07'),(28,'Produto Teste Final','Descrição do produto teste final',299.99,'TEST-FINAL-001',1,'{\"Cor\": [\"Azul\", \"Vermelho\", \"Verde\"], \"Tamanho\": [\"P\", \"M\", \"G\"]}','2025-07-19 16:48:34','2025-07-19 16:48:34'),(29,'Camiseta Teste Carrinho','Camiseta para teste do carrinho',79.90,'CAM-CART-TEST',1,NULL,'2025-07-19 16:50:19','2025-07-19 16:50:19'),(30,'Notebook Gamer','Notebook para jogos com RTX 4060',5999.90,'NOTE-GAMER-001',1,NULL,'2025-07-19 17:16:39','2025-07-19 17:16:39'),(31,'Produto Teste 1753019540','Descrição atualizada',149.90,'TEST-SKU-1753019540',1,NULL,'2025-07-20 10:52:24','2025-07-20 10:52:24');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refresh_tokens`
--

DROP TABLE IF EXISTS `refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `refresh_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `refresh_tokens_token_unique` (`token`),
  KEY `refresh_tokens_user_id_revoked_index` (`user_id`,`revoked`),
  KEY `refresh_tokens_token_index` (`token`),
  KEY `refresh_tokens_expires_at_index` (`expires_at`),
  CONSTRAINT `refresh_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refresh_tokens`
--

LOCK TABLES `refresh_tokens` WRITE;
/*!40000 ALTER TABLE `refresh_tokens` DISABLE KEYS */;
INSERT INTO `refresh_tokens` VALUES (1,3,'6029004dd2cbf2911b6d62871fd88afb9d523f228fddfbce6f6a3d09a0f9c89acb3fbeb519996463','2025-08-18 13:29:54',1,NULL,NULL,'2025-07-19 13:29:54','2025-07-19 13:32:00'),(2,3,'c64aec473c24614dd228533c54ce7b9d23419567e77054e0fc1ebf06940b5a9b704e16437cb29f89','2025-08-18 13:32:00',1,'172.18.0.1','curl/8.5.0','2025-07-19 13:32:00','2025-07-19 13:34:17'),(3,3,'570af6a51cf033c2fe850271609547c4885bd754c24ea83f9e50d61a4927be82dcfec862339a3ae3','2025-08-18 13:34:17',1,'172.18.0.1','curl/8.5.0','2025-07-19 13:34:17','2025-07-19 13:34:38'),(4,4,'5a5d98afae70058228d015f72947975aaa2578132bf576addd1ceda2446924af53699a81ef1349ea','2025-08-18 13:50:20',1,NULL,NULL,'2025-07-19 13:50:20','2025-07-19 13:50:26'),(5,3,'819874e7ed425cb0df154df01c69fa5eda28550a8e66e8a70d398b6432aed543d01c7e2f44025e05','2025-08-18 13:50:25',1,'172.18.0.1','curl/8.5.0','2025-07-19 13:50:25','2025-07-19 14:12:39'),(6,4,'ee84d35a95135b3610eba997970ef001b785dc008d30428f1ba50cbc96be2afc598f0fe46690da4e','2025-08-18 13:50:26',0,NULL,NULL,'2025-07-19 13:50:26','2025-07-19 13:50:26'),(7,5,'dbcbf5a8d9ce3ec0a8901febc75d7ffaff6124827ad16d0703aa4ae9ee9df9f62d2a0754afe90de9','2025-08-18 14:12:36',1,NULL,NULL,'2025-07-19 14:12:36','2025-07-19 14:12:40'),(8,3,'cd07e184ecc5352f9e69a117c04e73778895f186c0489e12872919df5a9ad48fbf219c7dd6a9ed4d','2025-08-18 14:12:39',0,'172.18.0.1','curl/8.5.0','2025-07-19 14:12:39','2025-07-19 14:12:39'),(9,5,'6a1585d3a47ba1b030a0cca8b9e79dc6d968fdf8f73190fb2f3e63250274ea1ffa6f1e42ffc96a7d','2025-08-18 14:12:40',0,NULL,NULL,'2025-07-19 14:12:40','2025-07-19 14:12:40'),(10,6,'0b43dfe9b03f33499b6ded27fa490f1fd429b3a89bb65b8fb5e363f6cc2596e0023607c177b9e54e','2025-08-18 16:13:23',1,NULL,NULL,'2025-07-19 16:13:23','2025-07-19 16:14:21'),(11,6,'00a49d192041aa1e843e05426fbe59938e9df4fdc188d9a7178e82915684ba96a225b7a83868dcf5','2025-08-18 16:14:21',0,'172.18.0.1','curl/8.5.0','2025-07-19 16:14:21','2025-07-19 16:14:21'),(12,7,'4975a58ba382a4b5eb94240ece45abf3ecf25804dda7f35ffec59e2d28bb7c4cfa666f4cba0254c7','2025-08-18 16:47:23',0,NULL,NULL,'2025-07-19 16:47:23','2025-07-19 16:47:23'),(13,8,'78d75d9fba0e6a7ac3c7ec6addff2fbe51a1c6805c0f1b07828e284f6df24d2107eb8b5c395dc222','2025-08-18 17:11:56',0,NULL,NULL,'2025-07-19 17:11:56','2025-07-19 17:11:56'),(14,9,'eb04b0f260bc66267c1d5276f1dd94e9b06e4465e4cb2fff4f682da7d19449f903e1f5fde0cbb401','2025-08-19 08:17:48',0,NULL,NULL,'2025-07-20 08:17:48','2025-07-20 08:17:48');
/*!40000 ALTER TABLE `refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock`
--

DROP TABLE IF EXISTS `stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `variations` json DEFAULT NULL,
  `quantity` int NOT NULL,
  `reserved` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_product_id_foreign` (`product_id`),
  CONSTRAINT `stock_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock`
--

LOCK TABLES `stock` WRITE;
/*!40000 ALTER TABLE `stock` DISABLE KEYS */;
INSERT INTO `stock` VALUES (1,1,NULL,100,0,'2025-07-19 10:59:56','2025-07-19 10:59:56'),(2,2,'{\"size\": \"P\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:29:42','2025-07-19 11:29:42'),(4,3,NULL,100,0,'2025-07-19 11:29:44','2025-07-19 11:29:44'),(5,4,NULL,100,2,'2025-07-19 11:29:44','2025-07-19 11:29:48'),(6,5,NULL,100,0,'2025-07-19 11:29:45','2025-07-19 11:29:45'),(7,6,'{\"size\": \"P\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:30:18','2025-07-19 11:30:18'),(9,7,'{\"size\": \"P\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:30:30','2025-07-19 11:30:30'),(11,8,'{\"size\": \"P\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:31:56','2025-07-19 11:31:56'),(12,8,'{\"size\": \"M\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:31:56','2025-07-19 11:31:56'),(13,8,'{\"size\": \"G\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:31:56','2025-07-19 11:31:56'),(14,9,'{\"size\": \"P\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:33:44','2025-07-19 11:33:44'),(15,9,'{\"size\": \"M\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:33:44','2025-07-19 11:33:44'),(16,9,'{\"size\": \"G\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:33:44','2025-07-19 11:33:44'),(17,10,NULL,100,0,'2025-07-19 11:33:46','2025-07-19 11:33:46'),(18,11,NULL,100,2,'2025-07-19 11:33:46','2025-07-19 11:33:51'),(19,12,NULL,100,0,'2025-07-19 11:33:46','2025-07-19 11:33:46'),(20,13,NULL,100,0,'2025-07-19 11:37:52','2025-07-19 11:37:52'),(21,14,'{\"size\": \"P\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:37:53','2025-07-19 11:37:53'),(22,14,'{\"size\": \"M\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:37:53','2025-07-19 11:37:53'),(23,14,'{\"size\": \"G\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:37:53','2025-07-19 11:37:53'),(24,15,'{\"size\": \"P\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:38:07','2025-07-19 11:38:07'),(25,15,'{\"size\": \"M\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:38:07','2025-07-19 11:38:07'),(26,15,'{\"size\": \"G\", \"color\": \"Azul\"}',100,0,'2025-07-19 11:38:07','2025-07-19 11:38:07'),(27,16,NULL,100,0,'2025-07-19 11:38:07','2025-07-19 11:38:07'),(28,17,NULL,100,2,'2025-07-19 11:38:08','2025-07-19 11:38:15'),(29,18,NULL,100,0,'2025-07-19 11:38:08','2025-07-19 11:38:08'),(30,19,'{\"size\": \"15 polegadas\", \"color\": \"Prata\"}',100,0,'2025-07-19 11:39:32','2025-07-19 11:39:32'),(31,19,'{\"size\": \"13 polegadas\", \"color\": \"Preto\"}',100,0,'2025-07-19 11:39:32','2025-07-19 11:39:32'),(33,21,NULL,100,2,'2025-07-19 11:39:39','2025-07-19 11:39:40'),(34,22,NULL,100,0,'2025-07-19 12:22:22','2025-07-19 12:22:22'),(35,23,NULL,100,0,'2025-07-19 12:22:24','2025-07-19 12:22:24'),(36,24,NULL,100,2,'2025-07-19 13:50:26','2025-07-19 13:50:27'),(37,25,NULL,100,2,'2025-07-19 14:12:41','2025-07-19 14:12:41'),(38,26,'{\"size\": \"14 polegadas\", \"color\": \"Prata\"}',100,0,'2025-07-19 16:15:51','2025-07-19 16:15:51'),(39,26,'{\"size\": \"15 polegadas\", \"color\": \"Preto\"}',100,1,'2025-07-19 16:15:51','2025-07-19 16:21:57'),(40,27,NULL,100,2,'2025-07-19 16:16:07','2025-07-19 16:21:57'),(41,28,'[\"Azul\", \"Vermelho\", \"Verde\"]',100,0,'2025-07-19 16:48:34','2025-07-19 16:48:34'),(42,28,'[\"P\", \"M\", \"G\"]',100,0,'2025-07-19 16:48:34','2025-07-19 16:48:34'),(43,29,NULL,100,4,'2025-07-19 16:50:19','2025-07-19 17:19:26'),(44,30,NULL,100,1,'2025-07-19 17:16:39','2025-07-19 17:19:26'),(45,31,NULL,100,0,'2025-07-20 10:52:24','2025-07-20 10:52:31');
/*!40000 ALTER TABLE `stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_email_index` (`email`),
  KEY `users_active_email_index` (`active`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'Test User','test@example.com',NULL,'$2y$12$a8pGb0WS7oONI1dXpZ55PeatUfqGJWcdiJ4kPSXLPqR9ZS.d8eqam',1,'2025-07-19 13:29:54','2025-07-19 13:29:54'),(4,'Test User','test1752943816@example.com',NULL,'$2y$12$nkPLsBLSDVTTDxUYm41Sq.wc7WoeyXvpKeCs082nHdvKQLm65ytJa',1,'2025-07-19 13:50:20','2025-07-19 13:50:20'),(5,'Test User','test1752945153@example.com',NULL,'$2y$12$WKRzGMaycI5BHir9dtAi0e5Fj.ruqa8BeOEfo1gjv5eiwtBisKcBS',1,'2025-07-19 14:12:36','2025-07-19 14:12:36'),(6,'João Silva','joao@example.com',NULL,'$2y$12$dfpKK.EPNLvSh9WBOe.KheDe833Y9nHE8x9irzTFegRzIjHt7G84e',1,'2025-07-19 16:13:23','2025-07-19 16:13:23'),(7,'Test User Final','test.final@example.com',NULL,'$2y$12$pFwRrpYHaxLDVgCrOY8JNuwo/4cyJWYlsuHK3bmj1H9oqHqY6yNgO',1,'2025-07-19 16:47:23','2025-07-19 16:47:23'),(8,'Teste Completo','teste.completo@example.com',NULL,'$2y$12$S9kSsIEWNf/vnrCMYvJsV.RUxiE4WHeCb8qX3AhLvWlxcI96VOU0K',1,'2025-07-19 17:11:56','2025-07-19 17:11:56'),(9,'Test User','test1753010265@example.com',NULL,'$2y$12$xWOyi349TaHxYMZqTeX5XujzdocyF29YQHFR8oIn7KensPPhZ61vu',1,'2025-07-20 08:17:48','2025-07-20 08:17:48');
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

-- Dump completed on 2025-07-20 14:11:12
