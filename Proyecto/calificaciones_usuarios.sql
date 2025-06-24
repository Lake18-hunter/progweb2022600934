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
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_usuario` enum('profesor','alumno','administrador') NOT NULL,
  `id_profesor` int DEFAULT NULL,
  `id_alumno` int DEFAULT NULL,
  `id_administrador` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `id_alumno` (`id_alumno`),
  KEY `id_profesor` (`id_profesor`),
  KEY `id_administrador` (`id_administrador`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id`),
  CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id`),
  CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`id_administrador`) REFERENCES `administrador` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin','1234','administrador',NULL,NULL,1),(2,'migueljasso','1234','alumno',NULL,1,NULL),(3,'germangil','5678','alumno',NULL,2,NULL),(4,'manuelcisneros','0110','alumno',NULL,3,NULL),(6,'profesorvaldepenia','1234','profesor',1,NULL,NULL),(8,'samgonzalez','0111','alumno',NULL,4,NULL),(9,'rogeliogarcia','1808','alumno',NULL,6,NULL),(11,'santiagosantiaguez','1234','profesor',2,NULL,NULL),(12,'leonardoballesteros','0102','alumno',NULL,8,NULL),(14,'fabiolaparra','0103','alumno',NULL,10,NULL),(15,'analaura','1001','profesor',3,NULL,NULL),(16,'nacirapinto','1234','profesor',4,NULL,NULL),(17,'miguelangel','1234','profesor',5,NULL,NULL),(18,'angeljasso','0000','alumno',NULL,11,NULL),(19,'allanbarrera','0104','alumno',NULL,12,NULL),(20,'secilsandoval','0512','alumno',NULL,13,NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-24  8:39:57
