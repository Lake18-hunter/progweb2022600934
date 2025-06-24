-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: calificaciones
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `edad` int NOT NULL,
  `grado` enum('1','2','3','4','5','6') DEFAULT NULL,
  `turno` enum('Matutino','Vespertino') DEFAULT NULL,
  `grupo` enum('A','B','C') DEFAULT NULL,
  `calificacion_espanol` tinyint unsigned DEFAULT NULL,
  `calificacion_matematicas` tinyint unsigned DEFAULT NULL,
  `calificacion_ingles` tinyint unsigned DEFAULT NULL,
  `calificacion_historia` tinyint unsigned DEFAULT NULL,
  `calificacion_computacion` tinyint unsigned DEFAULT NULL,
  `calificacion_geografia` tinyint unsigned DEFAULT NULL,
  `calificacion_biologia` tinyint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `alumnos_chk_1` CHECK ((`calificacion_espanol` between 1 and 10)),
  CONSTRAINT `alumnos_chk_2` CHECK ((`calificacion_matematicas` between 1 and 10)),
  CONSTRAINT `alumnos_chk_3` CHECK ((`calificacion_ingles` between 1 and 10)),
  CONSTRAINT `alumnos_chk_4` CHECK ((`calificacion_historia` between 1 and 10)),
  CONSTRAINT `alumnos_chk_5` CHECK ((`calificacion_computacion` between 1 and 10)),
  CONSTRAINT `alumnos_chk_6` CHECK ((`calificacion_geografia` between 1 and 10)),
  CONSTRAINT `alumnos_chk_7` CHECK ((`calificacion_biologia` between 1 and 10))
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumnos`
--

LOCK TABLES `alumnos` WRITE;
/*!40000 ALTER TABLE `alumnos` DISABLE KEYS */;
INSERT INTO `alumnos` VALUES (1,'Miguel Jasso',12,'6','Matutino','A',9,8,7,10,9,9,8),(2,'German Gil',12,'6','Matutino','A',10,9,8,10,9,8,8),(3,'Manuel Cisneros',12,'6','Matutino','A',9,6,8,7,9,8,9),(4,'Sam Gonzáles',12,'6','Matutino','A',10,10,8,7,7,10,10),(6,'Rogelio García',12,'6','Matutino','A',NULL,NULL,NULL,NULL,7,NULL,NULL),(8,'Leonardo Ballesteros',12,'6','Matutino','A',NULL,NULL,NULL,NULL,5,NULL,NULL),(10,'Fabiola Parra',12,'6','Matutino','B',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'Angel Jasso',12,'6','Matutino','B',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'Allan Barrera',11,'5','Matutino','B',NULL,NULL,NULL,NULL,NULL,9,NULL),(13,'Secil Sandoval',12,'6','Matutino','A',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `alumnos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-24  8:39:56
