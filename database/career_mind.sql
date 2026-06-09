-- MySQL dump 10.13  Distrib 9.6.0, for macos14.8 (x86_64)
--
-- Host: localhost    Database: career_mind
-- ------------------------------------------------------
-- Server version	9.6.0

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
-- Current Database: `career_mind`
--

/*!40000 DROP DATABASE IF EXISTS `career_mind`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `career_mind` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `career_mind`;

--
-- Table structure for table `career_prediction_cache`
--

DROP TABLE IF EXISTS `career_prediction_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `career_prediction_cache` (
  `user_id` int NOT NULL,
  `prediction_data` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `confidence` decimal(5,4) DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'live',
  `last_refreshed` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_career_prediction_cache_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `career_prediction_cache`
--

LOCK TABLES `career_prediction_cache` WRITE;
/*!40000 ALTER TABLE `career_prediction_cache` DISABLE KEYS */;
INSERT INTO `career_prediction_cache` VALUES (1,'{\"model_version\":\"tfidf_v1\",\"prediction\":\"AI Engineer\",\"schema_version\":1,\"top_predictions\":[{\"education_match\":[\"BS Computer Science\",\"BS Data Science\"],\"label\":\"AI Engineer\",\"matched_interests\":[],\"matched_skills\":[],\"score\":0.361,\"summary\":\"Education context: BS Computer Science, BS Data Science\"},{\"education_match\":[\"BS Computer Science\",\"BS Software Engineering\"],\"label\":\"Software Developer\",\"matched_interests\":[],\"matched_skills\":[\"php\"],\"score\":0.2627,\"summary\":\"Matched skills: php | Education context: BS Computer Science, BS Software Engineering\"},{\"education_match\":[\"BS Computer Science\",\"BS Cyber Security\",\"BS Information Security\"],\"label\":\"Cybersecurity Analyst\",\"matched_interests\":[],\"matched_skills\":[],\"score\":0.1478,\"summary\":\"Education context: BS Computer Science, BS Cyber Security\"}]}','2026-03-30 09:12:36',0.3610,'live','2026-03-30 09:12:36'),(3,'{\"model_version\":\"tfidf_v1\",\"prediction\":\"Digital Marketer\",\"schema_version\":1,\"top_predictions\":[{\"education_match\":[\"BS Marketing\"],\"label\":\"Digital Marketer\",\"matched_interests\":[],\"matched_skills\":[\"content strategy\",\"seo\"],\"score\":0.4528,\"summary\":\"Matched skills: content strategy, seo | Education context: BS Marketing\"},{\"education_match\":[\"BS Accounting\",\"BS Business\",\"BS Business Analytics\",\"BS Computer Science\",\"BS Data Science\",\"BS Economics\",\"BS Finance\",\"BS Management\",\"BS Operations\",\"BS Statistics\"],\"label\":\"Data Analyst\",\"matched_interests\":[],\"matched_skills\":[],\"score\":0.1411,\"summary\":\"Education context: BS Accounting, BS Business\"},{\"education_match\":[\"BS Computer Science\",\"BS Data Science\"],\"label\":\"AI Engineer\",\"matched_interests\":[],\"matched_skills\":[],\"score\":0.1088,\"summary\":\"Education context: BS Computer Science, BS Data Science\"}]}','2026-02-10 12:02:34',NULL,'live','2026-02-10 12:48:26'),(5,'{\"model_version\":\"tfidf_v1\",\"prediction\":\"JavaScript Developer\",\"schema_version\":1,\"top_predictions\":[{\"education_match\":[\"BS Computer Science\",\"BS Software Engineering\"],\"label\":\"JavaScript Developer\",\"matched_interests\":[],\"matched_skills\":[\"javascript\",\"css\",\"node.js\",\"typescript\"],\"score\":0.0213,\"summary\":\"Matched skills: javascript, css, node.js, typescript | Education context: BS Computer Science, BS Software Engineering\"},{\"education_match\":[\"BS Computer Science\",\"BS Software Engineering\"],\"label\":\"Next.js Developer\",\"matched_interests\":[],\"matched_skills\":[\"typescript\",\"tailwind css\"],\"score\":0.0192,\"summary\":\"Matched skills: typescript, tailwind css | Education context: BS Computer Science, BS Software Engineering\"},{\"education_match\":[\"BS Computer Science\",\"BS Software Engineering\"],\"label\":\"Angular Developer\",\"matched_interests\":[],\"matched_skills\":[\"css\",\"typescript\",\"angular\",\"rxjs\"],\"score\":0.0177,\"summary\":\"Matched skills: css, typescript, angular, rxjs | Education context: BS Computer Science, BS Software Engineering\"}]}','2026-06-09 21:24:50',0.0213,'live','2026-06-09 21:24:50'),(9,'{\"model_version\":\"tfidf_v1\",\"prediction\":\"MERN Stack Developer\",\"schema_version\":1,\"top_predictions\":[{\"education_match\":[\"BS Computer Science\",\"BS Software Engineering\"],\"label\":\"MERN Stack Developer\",\"matched_interests\":[],\"matched_skills\":[\"react\",\"node.js\",\"express.js\"],\"score\":0.0227,\"summary\":\"Matched skills: react, node.js, express.js | Education context: BS Computer Science, BS Software Engineering\"},{\"education_match\":[\"BS Computer Science\",\"BS Software Engineering\"],\"label\":\"Node.js Developer\",\"matched_interests\":[],\"matched_skills\":[\"javascript\",\"node.js\",\"express.js\"],\"score\":0.0218,\"summary\":\"Matched skills: javascript, node.js, express.js | Education context: BS Computer Science, BS Software Engineering\"},{\"education_match\":[\"BS Computer Science\",\"BS Software Engineering\"],\"label\":\"MEAN Stack Developer\",\"matched_interests\":[],\"matched_skills\":[\"node.js\",\"express.js\"],\"score\":0.0197,\"summary\":\"Matched skills: node.js, express.js | Education context: BS Computer Science, BS Software Engineering\"}]}','2026-06-09 20:48:42',0.0227,'live','2026-06-09 20:48:42'),(10,'{\"model_version\":\"tfidf_v1\",\"prediction\":\"UI Developer\",\"schema_version\":1,\"top_predictions\":[{\"education_match\":[\"BS Computer Science\",\"BS Software Engineering\"],\"label\":\"UI Developer\",\"matched_interests\":[],\"matched_skills\":[\"javascript\",\"html\",\"css\",\"figma\",\"tailwind css\"],\"score\":0.0185,\"summary\":\"Matched skills: javascript, html, css, figma | Education context: BS Computer Science, BS Software Engineering\"},{\"education_match\":[\"BS Computer Science\",\"BS Software Engineering\"],\"label\":\"Shopify Developer\",\"matched_interests\":[],\"matched_skills\":[\"javascript\",\"html\",\"css\",\"liquid\",\"shopify\"],\"score\":0.0158,\"summary\":\"Matched skills: javascript, html, css, liquid | Education context: BS Computer Science, BS Software Engineering\"},{\"education_match\":[\"BS Computer Science\",\"BS Software Engineering\"],\"label\":\"Web Accessibility Specialist\",\"matched_interests\":[],\"matched_skills\":[\"javascript\",\"html\",\"css\",\"accessibility\"],\"score\":0.0139,\"summary\":\"Matched skills: javascript, html, css, accessibility | Education context: BS Computer Science, BS Software Engineering\"}]}','2026-06-09 21:12:52',0.0185,'live','2026-06-09 21:12:52');
/*!40000 ALTER TABLE `career_prediction_cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `career_recommendations`
--

DROP TABLE IF EXISTS `career_recommendations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `career_recommendations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `career_title` varchar(150) NOT NULL,
  `reason` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_career_recommendations_user` (`user_id`),
  CONSTRAINT `fk_career_recommendations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `career_recommendations`
--

LOCK TABLES `career_recommendations` WRITE;
/*!40000 ALTER TABLE `career_recommendations` DISABLE KEYS */;
INSERT INTO `career_recommendations` VALUES (33,3,'Digital Marketer','Matched skills: seo','2026-02-10 12:02:34'),(34,3,'python developer','Based on your profile and interests.','2026-02-10 12:02:34'),(35,3,'front end developer','Based on your profile and interests.','2026-02-10 12:02:34'),(36,3,'Software Developer','Based on your profile and interests.','2026-02-10 12:02:34'),(37,3,'Data Analyst','Based on your profile and interests.','2026-02-10 12:02:34'),(43,1,'Software Developer','Matched skills: php','2026-03-06 09:33:41'),(44,1,'python developer','Based on your profile and interests.','2026-03-06 09:33:41'),(45,1,'front end developer','Based on your profile and interests.','2026-03-06 09:33:41'),(46,1,'Data Analyst','Based on your profile and interests.','2026-03-06 09:33:41'),(47,1,'UI/UX Designer','Based on your profile and interests.','2026-03-06 09:33:41'),(123,10,'Tailwind CSS Developer','Matched skills: css, html, javascript, tailwind css','2026-06-09 21:12:52'),(124,10,'Deep Learning Engineer','Matched skills: deep learning','2026-06-09 21:12:52'),(125,10,'Shopify Developer','Matched skills: css, html, javascript, liquid','2026-06-09 21:12:52'),(126,10,'jQuery Developer','Matched skills: css, html, javascript, jquery','2026-06-09 21:12:52'),(127,10,'UI Developer','Matched skills: css, figma, html, javascript','2026-06-09 21:12:52'),(153,5,'Tailwind CSS Developer','Matched skills: css, javascript, tailwind css','2026-06-09 21:24:50'),(154,5,'Node.js Developer','Matched skills: javascript, node.js','2026-06-09 21:24:50'),(155,5,'UI Developer','Matched skills: css, figma, javascript, tailwind css','2026-06-09 21:24:50'),(156,5,'Angular Developer','Matched skills: angular, css, rxjs, typescript','2026-06-09 21:24:50'),(157,5,'Next.js Developer','Matched skills: tailwind css, typescript','2026-06-09 21:24:50');
/*!40000 ALTER TABLE `career_recommendations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `careers`
--

DROP TABLE IF EXISTS `careers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `careers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text,
  `required_skills` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `careers`
--

LOCK TABLES `careers` WRITE;
/*!40000 ALTER TABLE `careers` DISABLE KEYS */;
INSERT INTO `careers` VALUES (1,'React Developer','React Developer — works on modern, responsive web user interfaces.','React, JavaScript, TypeScript, HTML, CSS, REST API'),(2,'Angular Developer','Angular Developer — works on modern, responsive web user interfaces.','Angular, TypeScript, RxJS, HTML, CSS'),(3,'Vue.js Developer','Vue.js Developer — works on modern, responsive web user interfaces.','Vue.js, JavaScript, HTML, CSS, REST API'),(4,'Frontend Developer','Frontend Developer — works on modern, responsive web user interfaces.','HTML, CSS, JavaScript, React, Bootstrap'),(5,'JavaScript Developer','JavaScript Developer — works on modern, responsive web user interfaces.','JavaScript, TypeScript, HTML, CSS, Node.js'),(6,'Next.js Developer','Next.js Developer — works on modern, responsive web user interfaces.','Next.js, React, TypeScript, Tailwind CSS'),(7,'UI Developer','UI Developer — works on modern, responsive web user interfaces.','HTML, CSS, JavaScript, Tailwind CSS, Figma'),(8,'Tailwind CSS Developer','Tailwind CSS Developer — works on modern, responsive web user interfaces.','Tailwind CSS, HTML, CSS, JavaScript'),(9,'Web Accessibility Specialist','Web Accessibility Specialist — works on modern, responsive web user interfaces.','accessibility, HTML, CSS, JavaScript'),(10,'jQuery Developer','jQuery Developer — works on modern, responsive web user interfaces.','jQuery, JavaScript, HTML, CSS'),(11,'Python Developer','Python Developer — works on server-side application logic and APIs.','Python, Flask, FastAPI, SQL, Git'),(12,'Django Developer','Django Developer — works on server-side application logic and APIs.','Python, Django, REST API, PostgreSQL, Git'),(13,'Flask Developer','Flask Developer — works on server-side application logic and APIs.','Python, Flask, REST API, SQL'),(14,'FastAPI Developer','FastAPI Developer — works on server-side application logic and APIs.','Python, FastAPI, REST API, PostgreSQL'),(15,'Node.js Developer','Node.js Developer — works on server-side application logic and APIs.','Node.js, Express.js, JavaScript, MongoDB, REST API'),(16,'Express.js Developer','Express.js Developer — works on server-side application logic and APIs.','Express.js, Node.js, JavaScript, MongoDB'),(17,'PHP Developer','PHP Developer — works on server-side application logic and APIs.','PHP, MySQL, REST API, Git'),(18,'Laravel Developer','Laravel Developer — works on server-side application logic and APIs.','Laravel, PHP, MySQL, REST API'),(19,'Ruby on Rails Developer','Ruby on Rails Developer — works on server-side application logic and APIs.','Ruby, REST API, PostgreSQL, Git'),(20,'Java Backend Developer','Java Backend Developer — works on server-side application logic and APIs.','Java, Spring Boot, SQL, REST API'),(21,'Spring Boot Developer','Spring Boot Developer — works on server-side application logic and APIs.','Spring Boot, Java, REST API, PostgreSQL'),(22,'Go Developer','Go Developer — works on server-side application logic and APIs.','Go, REST API, Docker, PostgreSQL'),(23,'C# Developer','C# Developer — works on server-side application logic and APIs.','C#, .NET, SQL, REST API'),(24,'.NET Developer','.NET Developer — works on server-side application logic and APIs.','.NET, C#, Microsoft SQL Server, REST API'),(25,'Full Stack Developer','Full Stack Developer — works on full end-to-end web applications.','JavaScript, React, Node.js, SQL, Git'),(26,'MERN Stack Developer','MERN Stack Developer — works on full end-to-end web applications.','MongoDB, Express.js, React, Node.js'),(27,'MEAN Stack Developer','MEAN Stack Developer — works on full end-to-end web applications.','MongoDB, Express.js, Angular, Node.js'),(28,'WordPress Developer','WordPress Developer — works on full end-to-end web applications.','WordPress, PHP, MySQL, HTML, CSS'),(29,'Shopify Developer','Shopify Developer — works on full end-to-end web applications.','Shopify, Liquid, JavaScript, HTML, CSS'),(30,'Magento Developer','Magento Developer — works on full end-to-end web applications.','Magento, PHP, MySQL, JavaScript'),(31,'Drupal Developer','Drupal Developer — works on full end-to-end web applications.','Drupal, PHP, MySQL, HTML'),(32,'WooCommerce Developer','WooCommerce Developer — works on full end-to-end web applications.','WordPress, WooCommerce, PHP, CSS'),(33,'Webflow Developer','Webflow Developer — works on full end-to-end web applications.','Webflow, HTML, CSS, JavaScript'),(34,'Headless CMS Developer','Headless CMS Developer — works on full end-to-end web applications.','Next.js, React, GraphQL, REST API'),(35,'Jamstack Developer','Jamstack Developer — works on full end-to-end web applications.','Next.js, React, GraphQL, Tailwind CSS'),(36,'Android Developer','Android Developer — works on native and cross-platform mobile apps.','Kotlin, Java, Android, REST API, Firebase'),(37,'iOS Developer','iOS Developer — works on native and cross-platform mobile apps.','Swift, iOS, REST API, Firebase'),(38,'Flutter Developer','Flutter Developer — works on native and cross-platform mobile apps.','Flutter, Dart, REST API, Firebase'),(39,'React Native Developer','React Native Developer — works on native and cross-platform mobile apps.','React Native, React, JavaScript, REST API'),(40,'Kotlin Developer','Kotlin Developer — works on native and cross-platform mobile apps.','Kotlin, Android, REST API'),(41,'Swift Developer','Swift Developer — works on native and cross-platform mobile apps.','Swift, iOS, REST API'),(42,'Mobile App Developer','Mobile App Developer — works on native and cross-platform mobile apps.','Flutter, React Native, REST API, Firebase'),(43,'Ionic Developer','Ionic Developer — works on native and cross-platform mobile apps.','Ionic, Angular, TypeScript, REST API'),(44,'Game Developer','Game Developer — works on native and cross-platform mobile apps.','C#, Unity, C++, problem solving'),(45,'AR/VR Developer','AR/VR Developer — works on native and cross-platform mobile apps.','Unity, C#, computer vision basics, 3D'),(46,'Data Analyst','Data Analyst — works on data pipelines, analysis and insights.','SQL, Excel, Power BI, Tableau, Statistics'),(47,'Data Scientist','Data Scientist — works on data pipelines, analysis and insights.','Python, Pandas, Machine Learning, SQL, Statistics'),(48,'Data Engineer','Data Engineer — works on data pipelines, analysis and insights.','Python, SQL, Apache Spark, Airflow, dbt'),(49,'Business Intelligence Analyst','Business Intelligence Analyst — works on data pipelines, analysis and insights.','Power BI, SQL, Tableau, Excel'),(50,'BI Developer','BI Developer — works on data pipelines, analysis and insights.','Power BI, SQL, data modeling, dbt'),(51,'Analytics Engineer','Analytics Engineer — works on data pipelines, analysis and insights.','dbt, SQL, Python, data modeling'),(52,'Big Data Engineer','Big Data Engineer — works on data pipelines, analysis and insights.','Apache Spark, Hadoop, Python, SQL'),(53,'Data Architect','Data Architect — works on data pipelines, analysis and insights.','data modeling, SQL, data warehousing, ETL'),(54,'Statistician','Statistician — works on data pipelines, analysis and insights.','Statistics, R, Python, data visualization'),(55,'Marketing Analyst','Marketing Analyst — works on data pipelines, analysis and insights.','Google Analytics, SQL, Excel, data visualization'),(56,'Quantitative Analyst','Quantitative Analyst — works on data pipelines, analysis and insights.','Python, Statistics, R, data modeling'),(57,'Bioinformatics Analyst','Bioinformatics Analyst — works on data pipelines, analysis and insights.','Python, R, Statistics, data analysis'),(58,'AI Engineer','AI Engineer — works on machine-learning and AI systems.','Python, Machine Learning, Deep Learning, TensorFlow, PyTorch'),(59,'Machine Learning Engineer','Machine Learning Engineer — works on machine-learning and AI systems.','Python, Scikit-learn, Machine Learning, PyTorch, MLOps'),(60,'Deep Learning Engineer','Deep Learning Engineer — works on machine-learning and AI systems.','Python, Deep Learning, TensorFlow, PyTorch'),(61,'NLP Engineer','NLP Engineer — works on machine-learning and AI systems.','Python, NLP, PyTorch, Hugging Face'),(62,'Computer Vision Engineer','Computer Vision Engineer — works on machine-learning and AI systems.','Python, OpenCV, Deep Learning, PyTorch'),(63,'Generative AI Engineer','Generative AI Engineer — works on machine-learning and AI systems.','Python, LLMs, Prompt Engineering, Hugging Face'),(64,'AI Prompt Engineer','AI Prompt Engineer — works on machine-learning and AI systems.','Prompt Engineering, LLMs, Python'),(65,'MLOps Engineer','MLOps Engineer — works on machine-learning and AI systems.','MLOps, Docker, Kubernetes, Python, CI/CD'),(66,'Robotics Engineer','Robotics Engineer — works on machine-learning and AI systems.','Python, C++, computer vision basics, control systems'),(67,'DevOps Engineer','DevOps Engineer — works on cloud infrastructure and deployment automation.','Docker, Kubernetes, AWS, CI/CD, Linux'),(68,'Cloud Engineer','Cloud Engineer — works on cloud infrastructure and deployment automation.','AWS, Docker, Terraform, Linux'),(69,'AWS Solutions Architect','AWS Solutions Architect — works on cloud infrastructure and deployment automation.','AWS, Terraform, Docker, networking'),(70,'Azure Engineer','Azure Engineer — works on cloud infrastructure and deployment automation.','Azure, Docker, CI/CD, Terraform'),(71,'GCP Engineer','GCP Engineer — works on cloud infrastructure and deployment automation.','Google Cloud, Docker, Kubernetes, Terraform'),(72,'Site Reliability Engineer','Site Reliability Engineer — works on cloud infrastructure and deployment automation.','Kubernetes, Prometheus, Linux, CI/CD'),(73,'Platform Engineer','Platform Engineer — works on cloud infrastructure and deployment automation.','Kubernetes, Terraform, Docker, CI/CD'),(74,'Kubernetes Administrator','Kubernetes Administrator — works on cloud infrastructure and deployment automation.','Kubernetes, Docker, Linux, Helm'),(75,'Infrastructure Engineer','Infrastructure Engineer — works on cloud infrastructure and deployment automation.','Terraform, AWS, Linux, Ansible'),(76,'Release Engineer','Release Engineer — works on cloud infrastructure and deployment automation.','CI/CD, Jenkins, Git, Docker'),(77,'Cloud Security Engineer','Cloud Security Engineer — works on cloud infrastructure and deployment automation.','cloud security, AWS, Network Security, compliance'),(78,'Cybersecurity Analyst','Cybersecurity Analyst — works on securing systems, networks and applications.','Network Security, Splunk, Linux, Incident Response'),(79,'Security Engineer','Security Engineer — works on securing systems, networks and applications.','Network Security, Cryptography, Linux, cloud security'),(80,'Penetration Tester','Penetration Tester — works on securing systems, networks and applications.','Penetration Testing, Burp Suite, Kali Linux, Ethical Hacking'),(81,'SOC Analyst','SOC Analyst — works on securing systems, networks and applications.','SIEM, Splunk, Network Security, Incident Response'),(82,'Information Security Analyst','Information Security Analyst — works on securing systems, networks and applications.','Network Security, compliance, risk assessment'),(83,'Network Security Engineer','Network Security Engineer — works on securing systems, networks and applications.','Network Security, Firewalls, networking, Linux'),(84,'Application Security Engineer','Application Security Engineer — works on securing systems, networks and applications.','Penetration Testing, Burp Suite, API security'),(85,'Security Architect','Security Architect — works on securing systems, networks and applications.','Network Security, Cryptography, cloud security, compliance'),(86,'Incident Responder','Incident Responder — works on securing systems, networks and applications.','Incident Response, SIEM, Splunk, forensics'),(87,'Malware Analyst','Malware Analyst — works on securing systems, networks and applications.','malware analysis, reverse engineering, Kali Linux'),(88,'Ethical Hacker','Ethical Hacker — works on securing systems, networks and applications.','Ethical Hacking, Penetration Testing, Kali Linux, Burp Suite'),(89,'UI/UX Designer','UI/UX Designer — works on user-centred product and visual design.','Figma, Adobe XD, Wireframing, Prototyping, User Research'),(90,'Product Designer','Product Designer — works on user-centred product and visual design.','Figma, Prototyping, User Research, Design Systems'),(91,'UX Researcher','UX Researcher — works on user-centred product and visual design.','User Research, usability testing, Figma'),(92,'Graphic Designer','Graphic Designer — works on user-centred product and visual design.','Photoshop, Illustrator, Typography, Canva'),(93,'Visual Designer','Visual Designer — works on user-centred product and visual design.','Figma, Photoshop, Typography, color theory'),(94,'Interaction Designer','Interaction Designer — works on user-centred product and visual design.','Figma, Prototyping, Interaction Design'),(95,'Motion Designer','Motion Designer — works on user-centred product and visual design.','Adobe After Effects, motion, animation'),(96,'Brand Designer','Brand Designer — works on user-centred product and visual design.','Illustrator, brand guidelines, Typography'),(97,'Web Designer','Web Designer — works on user-centred product and visual design.','Figma, HTML, CSS, Webflow'),(98,'Design Systems Engineer','Design Systems Engineer — works on user-centred product and visual design.','Figma, Design Systems, HTML, CSS'),(99,'Digital Marketer','Digital Marketer — works on digital marketing and growth.','SEO, Google Ads, Google Analytics, content marketing'),(100,'SEO Specialist','SEO Specialist — works on digital marketing and growth.','SEO, Google Analytics, content strategy, keyword research'),(101,'Content Marketer','Content Marketer — works on digital marketing and growth.','content marketing, copywriting, SEO, content strategy'),(102,'Social Media Manager','Social Media Manager — works on digital marketing and growth.','Social Media Marketing, Canva, content strategy'),(103,'Performance Marketer','Performance Marketer — works on digital marketing and growth.','Meta Ads, Google Ads, conversion optimization'),(104,'Email Marketing Specialist','Email Marketing Specialist — works on digital marketing and growth.','Email Marketing, Mailchimp, HubSpot, copywriting'),(105,'Growth Marketer','Growth Marketer — works on digital marketing and growth.','Growth Marketing, Google Analytics, conversion optimization'),(106,'PPC Specialist','PPC Specialist — works on digital marketing and growth.','Google Ads, Meta Ads, conversion optimization'),(107,'Affiliate Marketing Manager','Affiliate Marketing Manager — works on digital marketing and growth.','Affiliate Marketing, SEO, Google Analytics'),(108,'Brand Manager','Brand Manager — works on digital marketing and growth.','Brand Strategy, content strategy, Communication'),(109,'Copywriter','Copywriter — works on digital marketing and growth.','copywriting, content strategy, SEO'),(110,'Content Strategist','Content Strategist — works on digital marketing and growth.','content strategy, SEO, content marketing'),(111,'Influencer Marketing Manager','Influencer Marketing Manager — works on digital marketing and growth.','Social Media Marketing, Brand Strategy, Communication'),(112,'Product Manager','Product Manager — works on product strategy, delivery and operations.','Project Management, Agile, Communication, Stakeholder Management'),(113,'Project Manager','Project Manager — works on product strategy, delivery and operations.','Project Management, Agile, Jira, Communication'),(114,'Scrum Master','Scrum Master — works on product strategy, delivery and operations.','Scrum, Agile, Jira, Communication'),(115,'Business Analyst','Business Analyst — works on product strategy, delivery and operations.','Business Analysis, SQL, Communication, Problem Solving'),(116,'Product Owner','Product Owner — works on product strategy, delivery and operations.','Agile, Scrum, Stakeholder Management'),(117,'Program Manager','Program Manager — works on product strategy, delivery and operations.','Project Management, Stakeholder Management, Communication'),(118,'Technical Program Manager','Technical Program Manager — works on product strategy, delivery and operations.','Project Management, Agile, Communication'),(119,'Agile Coach','Agile Coach — works on product strategy, delivery and operations.','Agile, Scrum, Communication, Leadership'),(120,'Operations Manager','Operations Manager — works on product strategy, delivery and operations.','Operations, Leadership, Communication'),(121,'Strategy Analyst','Strategy Analyst — works on product strategy, delivery and operations.','Business Analysis, Excel, Communication'),(122,'QA Engineer','QA Engineer — works on software quality assurance and test automation.','Selenium, Test Automation, Jira, Problem Solving'),(123,'Test Automation Engineer','Test Automation Engineer — works on software quality assurance and test automation.','Selenium, Test Automation, Python, CI/CD'),(124,'Manual QA Tester','Manual QA Tester — works on software quality assurance and test automation.','manual testing, Jira, test cases'),(125,'SDET','SDET — works on software quality assurance and test automation.','Test Automation, Selenium, Java, CI/CD'),(126,'Performance Test Engineer','Performance Test Engineer — works on software quality assurance and test automation.','JMeter, performance testing, Test Automation'),(127,'Quality Analyst','Quality Analyst — works on software quality assurance and test automation.','manual testing, test cases, Jira'),(128,'Database Administrator','Database Administrator — works on databases, networks and systems.','SQL, PostgreSQL, MySQL, Linux'),(129,'Database Developer','Database Developer — works on databases, networks and systems.','SQL, PostgreSQL, data modeling'),(130,'ETL Developer','ETL Developer — works on databases, networks and systems.','ETL, SQL, Python, data warehousing'),(131,'Systems Administrator','Systems Administrator — works on databases, networks and systems.','Linux, Bash, networking, Git'),(132,'Network Engineer','Network Engineer — works on databases, networks and systems.','networking, Linux, Firewalls'),(133,'Embedded Systems Engineer','Embedded Systems Engineer — works on databases, networks and systems.','C++, C, embedded, Linux'),(134,'IoT Developer','IoT Developer — works on databases, networks and systems.','Python, C++, embedded, REST API'),(135,'Blockchain Developer','Blockchain Developer — works on databases, networks and systems.','Solidity, blockchain, JavaScript, REST API'),(136,'Technical Writer','Technical Writer — works on emerging and specialised technology.','technical writing, Communication, Markdown'),(137,'Developer Advocate','Developer Advocate — works on emerging and specialised technology.','Communication, Python, public speaking'),(138,'Computer Graphics Engineer','Computer Graphics Engineer — works on emerging and specialised technology.','C++, OpenGL, 3D, mathematics'),(139,'Solutions Engineer','Solutions Engineer — works on emerging and specialised technology.','Communication, REST API, SQL, Problem Solving'),(140,'Sales Engineer','Sales Engineer — works on emerging and specialised technology.','Communication, REST API, Problem Solving'),(141,'IT Support Specialist','IT Support Specialist — works on emerging and specialised technology.','Linux, networking, troubleshooting, Communication');
/*!40000 ALTER TABLE `careers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cv_analyses`
--

DROP TABLE IF EXISTS `cv_analyses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cv_analyses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cv_id` int NOT NULL,
  `summary` text,
  `missing_skills` text,
  `feedback` text,
  `extracted_skills` text,
  `score` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cv_analyses_cv` (`cv_id`),
  CONSTRAINT `fk_cv_analyses_cv` FOREIGN KEY (`cv_id`) REFERENCES `cv_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cv_analyses`
--

LOCK TABLES `cv_analyses` WRITE;
/*!40000 ALTER TABLE `cv_analyses` DISABLE KEYS */;
INSERT INTO `cv_analyses` VALUES (9,9,'CV parsed successfully.','[\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"Communication\",\"content strategy\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,100,'2026-06-08 21:06:11'),(10,10,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"Communication\"]','[\"Add an Experience section with measurable achievements.\"]',NULL,88,'2026-06-08 21:15:13'),(11,11,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"Communication\"]','[\"Add an Experience section with measurable achievements.\"]',NULL,88,'2026-06-08 21:18:12'),(12,12,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"Communication\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,80,'2026-06-08 21:18:49'),(13,13,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"Communication\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,80,'2026-06-08 21:19:32'),(14,14,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"Communication\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,80,'2026-06-08 21:20:43'),(15,15,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"ai\",\"Ai automation\",\"analytics\\\"\"]','[\"Add more detail about projects and responsibilities.\",\"Include a dedicated Skills section for better matching.\",\"Add an Experience section with measurable achievements.\"]',NULL,50,'2026-06-08 21:23:32'),(16,16,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"ai\",\"Ai automation\",\"analytics\\\"\"]','[\"Add more detail about projects and responsibilities.\",\"Include a dedicated Skills section for better matching.\",\"Add an Experience section with measurable achievements.\"]',NULL,50,'2026-06-08 21:25:12'),(17,17,'CV parsed successfully.','[\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"content strategy\",\"Data Analysis\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,100,'2026-06-08 21:26:20'),(18,18,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"Communication\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,80,'2026-06-08 21:28:13'),(19,19,'CV parsed successfully.','[\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"content strategy\",\"Data Analysis\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,100,'2026-06-08 21:28:34'),(20,20,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"ai\",\"Ai automation\",\"analytics\\\"\"]','[\"Add more detail about projects and responsibilities.\",\"Include a dedicated Skills section for better matching.\",\"Add an Experience section with measurable achievements.\"]',NULL,50,'2026-06-08 21:50:21'),(21,21,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"ai\",\"Ai automation\",\"analytics\\\"\"]','[\"Add more detail about projects and responsibilities.\",\"Include a dedicated Skills section for better matching.\",\"Add an Experience section with measurable achievements.\"]',NULL,50,'2026-06-08 22:38:39'),(22,22,'CV parsed successfully.','[\".net\",\"\\\"digital growth\",\"ai\",\"Ai automation\",\"analytics\\\"\"]','[\"Add more detail about projects and responsibilities.\",\"Include a dedicated Skills section for better matching.\",\"Add an Experience section with measurable achievements.\"]',NULL,50,'2026-06-08 23:02:03'),(23,23,'CV parsed successfully.','[\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"content strategy\",\"Data Analysis\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,100,'2026-06-08 23:03:37'),(24,24,'CV parsed successfully.','[\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"content strategy\",\"Data Analysis\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,100,'2026-06-09 17:01:56'),(25,25,'CV parsed successfully.','[\"\\\"digital growth\",\"Ai automation\",\"analytics\\\"\",\"content strategy\",\"Data Analysis\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,100,'2026-06-09 17:05:18'),(26,26,'This file does not look like a CV/résumé. Please upload an actual resume with sections like Experience, Education, and Skills.','[]','[\"Ensure the CV is text-based (not scanned images).\",\"Use standard headings like Skills, Experience, Education.\"]',NULL,0,'2026-06-09 17:16:31'),(27,27,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,100,'2026-06-09 17:16:45'),(28,28,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:24:24'),(29,29,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:39:54'),(32,32,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:47:02'),(33,33,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:47:10'),(34,34,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:47:11'),(35,35,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:47:14'),(36,36,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:47:15'),(37,37,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:47:16'),(38,38,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:47:18'),(39,39,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:47:22'),(40,40,'This file does not look like a CV/résumé. Please upload an actual resume with sections like Experience, Education, and Skills.','[]','[\"Ensure the CV is text-based (not scanned images).\",\"Use standard headings like Skills, Experience, Education.\"]',NULL,0,'2026-06-09 17:51:44'),(41,41,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 17:54:15'),(42,42,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 18:30:54'),(43,43,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 18:42:39'),(44,44,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 18:43:17'),(45,45,'CV parsed successfully.','[\"sql\",\"git\",\"excel\",\"docker\",\"pandas\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 18:52:15'),(46,46,'CV parsed successfully.','[\"figma\",\"git\",\"excel\",\"docker\",\"pandas\"]','[\"Add more detail about projects and responsibilities.\"]',NULL,79,'2026-06-09 20:21:59'),(47,47,'CV parsed successfully.','[\"sql\",\"git\",\"excel\",\"docker\",\"pandas\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:32:22'),(48,48,'CV parsed successfully.','[\"sql\",\"git\",\"excel\",\"docker\",\"pandas\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:32:48'),(51,51,'CV parsed successfully.','[\"sql\",\"git\",\"excel\",\"docker\",\"pandas\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:39:43'),(52,52,'CV parsed successfully.','[\"figma\",\"git\",\"excel\",\"docker\",\"pandas\"]','[\"Add more detail about projects and responsibilities.\"]',NULL,79,'2026-06-09 20:39:56'),(53,53,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:40:06'),(57,57,'CV parsed successfully.','[\"git\",\"excel\",\"docker\",\"pandas\",\"sklearn\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:43:41'),(61,61,'CV parsed successfully.','[\"rest api\",\"sql\",\"communication\",\"linux\",\"docker\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:46:39'),(62,62,'CV parsed successfully.','[\"rest api\",\"communication\",\"linux\",\"docker\",\"ci\\/cd\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:48:06'),(63,63,'CV parsed successfully.','[\"rest api\",\"communication\",\"linux\",\"docker\",\"figma\"]','[\"Add more detail about projects and responsibilities.\"]',NULL,79,'2026-06-09 20:48:25'),(64,64,'CV parsed successfully.','[\"rest api\",\"sql\",\"communication\",\"linux\",\"docker\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:48:36'),(65,65,'CV parsed successfully.','[\"rest api\",\"sql\",\"communication\",\"linux\",\"docker\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:48:40'),(66,66,'CV parsed successfully.','[\"rest api\",\"sql\",\"communication\",\"linux\",\"docker\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:48:41'),(67,67,'CV parsed successfully.','[\"rest api\",\"sql\",\"communication\",\"linux\",\"docker\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:48:41'),(68,68,'CV parsed successfully.','[\"rest api\",\"python\",\"sql\",\"communication\",\"linux\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,83,'2026-06-09 20:52:05'),(69,69,'CV parsed successfully.','[\"rest api\",\"communication\",\"linux\",\"docker\",\"ci\\/cd\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,77,'2026-06-09 20:53:16'),(70,70,'CV parsed successfully.','[\"rest api\",\"communication\",\"linux\",\"docker\",\"figma\"]','[\"Add more detail about projects and responsibilities.\"]',NULL,79,'2026-06-09 20:53:24'),(71,71,'CV parsed successfully.','[\"rest api\",\"python\",\"sql\",\"communication\",\"linux\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,83,'2026-06-09 20:53:33'),(72,72,'CV parsed successfully.','[\"rest api\",\"python\",\"sql\",\"communication\",\"linux\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,83,'2026-06-09 20:58:56'),(75,75,'CV parsed successfully.','[\"rest api\",\"python\",\"sql\",\"communication\",\"linux\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]',NULL,83,'2026-06-09 21:04:53'),(78,78,'CV parsed successfully.','[\"rest api\",\"python\",\"sql\",\"communication\",\"linux\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]','[\"javascript\",\"html\",\"css\",\"figma\",\"git\",\"typescript\",\"tailwind css\",\"compliance\",\"bootstrap\",\"liquid\",\"accessibility\",\"jquery\",\"shopify\",\"Digital Marketing\",\"GitHub\"]',83,'2026-06-09 21:11:50'),(79,79,'CV parsed successfully.','[\"rest api\",\"python\",\"sql\",\"communication\",\"linux\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]','[\"javascript\",\"html\",\"css\",\"figma\",\"git\",\"typescript\",\"tailwind css\",\"compliance\",\"bootstrap\",\"liquid\",\"accessibility\",\"jquery\",\"shopify\",\"Digital Marketing\",\"GitHub\"]',83,'2026-06-09 21:12:12'),(80,80,'CV parsed successfully.','[\"rest api\",\"python\",\"sql\",\"communication\",\"linux\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]','[\"javascript\",\"html\",\"css\",\"figma\",\"git\",\"typescript\",\"tailwind css\",\"compliance\",\"bootstrap\",\"liquid\",\"accessibility\",\"jquery\",\"shopify\",\"Digital Marketing\",\"GitHub\"]',83,'2026-06-09 21:12:52'),(81,81,'CV parsed successfully.','[\"rest api\",\"python\",\"sql\",\"communication\",\"html\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]','[\"javascript\",\"css\",\"figma\",\"git\",\"node.js\",\"jira\",\"typescript\",\"tailwind css\",\"angular\",\"bootstrap\",\"rxjs\",\"analytics\",\"GitHub\"]',77,'2026-06-09 21:17:27'),(89,89,'CV parsed successfully.','[\"rest api\",\"python\",\"sql\",\"communication\",\"html\"]','[\"Your CV structure looks strong. Keep refining with clear metrics.\"]','[\"javascript\",\"css\",\"figma\",\"git\",\"node.js\",\"jira\",\"typescript\",\"tailwind css\",\"angular\",\"bootstrap\",\"rxjs\",\"analytics\",\"GitHub\"]',77,'2026-06-09 21:24:50');
/*!40000 ALTER TABLE `cv_analyses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cv_files`
--

DROP TABLE IF EXISTS `cv_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cv_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cv_files_user` (`user_id`),
  CONSTRAINT `fk_cv_files_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cv_files`
--

LOCK TABLES `cv_files` WRITE;
/*!40000 ALTER TABLE `cv_files` DISABLE KEYS */;
INSERT INTO `cv_files` VALUES (9,5,'Aoun Resume-1.pdf','/uploads/cv/5_cac7e1a44edb.pdf','2026-06-08 21:06:10'),(10,5,'Career_mind readme DOC.pdf','/uploads/cv/5_931c64216eeb.pdf','2026-06-08 21:15:13'),(11,5,'Career_mind readme DOC.pdf','/uploads/cv/5_3d9674c6b761.pdf','2026-06-08 21:18:12'),(12,5,'ChoiceRefill DTC Website Proposal ecom.pdf','/uploads/cv/5_afe248d7603b.pdf','2026-06-08 21:18:48'),(13,5,'ChoiceRefill DTC Website Proposal ecom.pdf','/uploads/cv/5_9403b41f6180.pdf','2026-06-08 21:19:31'),(14,5,'ChoiceRefill DTC Website Proposal.docx','/uploads/cv/5_a374b3fa7120.docx','2026-06-08 21:20:43'),(15,5,'RCPT-2026-00018.pdf','/uploads/cv/5_613783415750.pdf','2026-06-08 21:23:32'),(16,5,'RCPT-2026-00018.pdf','/uploads/cv/5_9bf9029dbc54.pdf','2026-06-08 21:25:12'),(17,5,'Aoun Resume.pdf','/uploads/cv/5_260779b17045.pdf','2026-06-08 21:26:20'),(18,5,'ChoiceRefill DTC Website Proposal.docx','/uploads/cv/5_d966bd3e7dce.docx','2026-06-08 21:28:13'),(19,5,'Aoun Resume.pdf','/uploads/cv/5_26dfc5e15d1f.pdf','2026-06-08 21:28:33'),(20,5,'RCPT-2026-00018.pdf','/uploads/cv/5_fdf4f5215c6a.pdf','2026-06-08 21:50:21'),(21,5,'RCPT-2026-00018.pdf','/uploads/cv/5_39e4a2c9ddff.pdf','2026-06-08 22:38:39'),(22,5,'RCPT-2026-00018.pdf','/uploads/cv/5_23a6ccbde354.pdf','2026-06-08 23:02:03'),(23,5,'Aoun Resume.pdf','/uploads/cv/5_3f5acb098931.pdf','2026-06-08 23:03:37'),(24,5,'Aoun Resume.pdf','/uploads/cv/5_cc7d0659f08d.pdf','2026-06-09 17:01:55'),(25,5,'Aoun Resume.pdf','/uploads/cv/5_dde7824b41d9.pdf','2026-06-09 17:05:17'),(26,5,'ChoiceRefill DTC Website Proposal.docx','/uploads/cv/5_3281fa78627b.docx','2026-06-09 17:16:31'),(27,5,'Aoun Resume.pdf','/uploads/cv/5_9eba8d211da7.pdf','2026-06-09 17:16:44'),(28,5,'Aoun Resume.pdf','/uploads/cv/5_1c4733350b73.pdf','2026-06-09 17:24:24'),(29,5,'Aoun Resume-1.pdf','/uploads/cv/5_0dcc0b9b19ee.pdf','2026-06-09 17:39:53'),(32,5,'Aoun Resume-1.pdf','/uploads/cv/5_8249e5a6595a.pdf','2026-06-09 17:47:02'),(33,5,'Aoun Resume-1.pdf','/uploads/cv/5_4ccac91095cc.pdf','2026-06-09 17:47:09'),(34,5,'Aoun Resume-1.pdf','/uploads/cv/5_0af2668d6672.pdf','2026-06-09 17:47:11'),(35,5,'Aoun Resume-1.pdf','/uploads/cv/5_cba018c3fbba.pdf','2026-06-09 17:47:13'),(36,5,'Aoun Resume-1.pdf','/uploads/cv/5_abbbbba56e4d.pdf','2026-06-09 17:47:15'),(37,5,'Aoun Resume-1.pdf','/uploads/cv/5_634e906d41ef.pdf','2026-06-09 17:47:16'),(38,5,'Aoun Resume-1.pdf','/uploads/cv/5_47724aa38e84.pdf','2026-06-09 17:47:18'),(39,5,'Aoun Resume-1.pdf','/uploads/cv/5_10e60be8e1dd.pdf','2026-06-09 17:47:22'),(40,5,'Career_mind readme DOC.pdf','/uploads/cv/5_62b6588b75b6.pdf','2026-06-09 17:51:44'),(41,5,'Aoun Resume-1.pdf','/uploads/cv/5_a5a36474bf49.pdf','2026-06-09 17:54:15'),(42,10,'Aoun Resume-1.pdf','/uploads/cv/10_5fee972fd769.pdf','2026-06-09 18:30:54'),(43,10,'Aoun Resume-1.pdf','/uploads/cv/10_979ef33d848e.pdf','2026-06-09 18:42:38'),(44,5,'Aoun Resume-1.pdf','/uploads/cv/5_d002878eb775.pdf','2026-06-09 18:43:17'),(45,10,'HamzaZahid.pdf','/uploads/cv/10_aef97a7e1086.pdf','2026-06-09 18:52:15'),(46,10,'Imran_Resume.pdf','/uploads/cv/10_bc0f5a2dce8f.pdf','2026-06-09 20:21:58'),(47,10,'HamzaZahid.pdf','/uploads/cv/10_a536640582f8.pdf','2026-06-09 20:32:22'),(48,10,'HamzaZahid.pdf','/uploads/cv/10_9b8c37593c1b.pdf','2026-06-09 20:32:47'),(51,5,'HamzaZahid.pdf','/uploads/cv/5_d5dbfcb8413e.pdf','2026-06-09 20:39:42'),(52,5,'Imran_Resume.pdf','/uploads/cv/5_58cafcd77232.pdf','2026-06-09 20:39:56'),(53,5,'Aoun Resume-1.pdf','/uploads/cv/5_5b424559b2e7.pdf','2026-06-09 20:40:05'),(57,5,'Aoun Resume-1.pdf','/uploads/cv/5_8efd73d220be.pdf','2026-06-09 20:43:41'),(61,5,'HamzaZahid.pdf','/uploads/cv/5_5d1aa07d95c7.pdf','2026-06-09 20:46:39'),(62,9,'Aoun Resume-1.pdf','/uploads/cv/9_4f3e70845ef0.pdf','2026-06-09 20:48:05'),(63,9,'Imran_Resume.pdf','/uploads/cv/9_77b17f58d9ea.pdf','2026-06-09 20:48:24'),(64,9,'HamzaZahid.pdf','/uploads/cv/9_ecf919782bb7.pdf','2026-06-09 20:48:36'),(65,9,'HamzaZahid.pdf','/uploads/cv/9_a4ccd11a25fc.pdf','2026-06-09 20:48:39'),(66,9,'HamzaZahid.pdf','/uploads/cv/9_2ea62cde960d.pdf','2026-06-09 20:48:41'),(67,9,'HamzaZahid.pdf','/uploads/cv/9_752e83b7fda8.pdf','2026-06-09 20:48:41'),(68,5,'Hamza_Shopify_Developer_CV.pdf','/uploads/cv/5_5459c7ebe53f.pdf','2026-06-09 20:52:05'),(69,5,'Aoun Resume-1.pdf','/uploads/cv/5_8e9512daf02b.pdf','2026-06-09 20:53:15'),(70,5,'Imran_Resume.pdf','/uploads/cv/5_4ccd401af6f8.pdf','2026-06-09 20:53:24'),(71,5,'Hamza_Shopify_Developer_CV.pdf','/uploads/cv/5_4a33bea9105a.pdf','2026-06-09 20:53:33'),(72,5,'Hamza_Shopify_Developer_CV.pdf','/uploads/cv/5_7e20b7320e56.pdf','2026-06-09 20:58:56'),(75,5,'Hamza_Shopify_Developer_CV.pdf','/uploads/cv/5_79c3ae77c386.pdf','2026-06-09 21:04:52'),(78,5,'Hamza_Shopify_Developer_CV.pdf','/uploads/cv/5_0cc3d0636d78.pdf','2026-06-09 21:11:50'),(79,5,'Hamza_Shopify_Developer_CV.pdf','/uploads/cv/5_d252f871a722.pdf','2026-06-09 21:12:12'),(80,10,'Hamza_Shopify_Developer_CV.pdf','/uploads/cv/10_0e4c945d0deb.pdf','2026-06-09 21:12:52'),(81,5,'Angular_Developer_CV.pdf','/uploads/cv/5_35caaefd779e.pdf','2026-06-09 21:17:27'),(89,5,'Angular_Developer_CV.pdf','/uploads/cv/5_5a8ad2e7c08a.pdf','2026-06-09 21:24:50');
/*!40000 ALTER TABLE `cv_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `interests`
--

DROP TABLE IF EXISTS `interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `interests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `interest_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `interest_name` (`interest_name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `interests`
--

LOCK TABLES `interests` WRITE;
/*!40000 ALTER TABLE `interests` DISABLE KEYS */;
INSERT INTO `interests` VALUES (4,'.net'),(12,'ai'),(5,'Ai automation'),(7,'Angular'),(14,'C#'),(8,'Cassandra'),(1,'coding'),(9,'Communication'),(11,'Data Analysis'),(10,'Deep Learning'),(6,'free rehna'),(3,'Mean'),(2,'MERN'),(17,'Network Security'),(15,'React'),(16,'shopify'),(13,'web development');
/*!40000 ALTER TABLE `interests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_recommendations`
--

DROP TABLE IF EXISTS `job_recommendations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_recommendations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `job_title` varchar(150) NOT NULL,
  `reason` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_job_recommendations_user` (`user_id`),
  CONSTRAINT `fk_job_recommendations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_recommendations`
--

LOCK TABLES `job_recommendations` WRITE;
/*!40000 ALTER TABLE `job_recommendations` DISABLE KEYS */;
INSERT INTO `job_recommendations` VALUES (33,3,'Content Marketing Intern','Matched skills: seo','2026-02-10 12:02:34'),(34,3,'python developer','Based on your profile and interests.','2026-02-10 12:02:34'),(35,3,'front end developer','Based on your profile and interests.','2026-02-10 12:02:34'),(36,3,'Junior Backend Developer','Based on your profile and interests.','2026-02-10 12:02:34'),(37,3,'Frontend Developer','Based on your profile and interests.','2026-02-10 12:02:34'),(43,1,'python developer','Based on your profile and interests.','2026-03-06 09:33:41'),(44,1,'front end developer','Based on your profile and interests.','2026-03-06 09:33:41'),(45,1,'Junior Backend Developer','Based on your profile and interests.','2026-03-06 09:33:41'),(46,1,'Frontend Developer','Based on your profile and interests.','2026-03-06 09:33:41'),(47,1,'Data Analyst Intern','Based on your profile and interests.','2026-03-06 09:33:41'),(119,10,'Tailwind CSS Developer','Matched skills: css, html, javascript, tailwind css','2026-06-09 21:12:52'),(120,10,'Entry Deep Learning Engineer','Matched skills: deep learning','2026-06-09 21:12:52'),(121,10,'Mid Shopify Developer','Matched skills: css, html, javascript, liquid','2026-06-09 21:12:52'),(122,10,'Junior jQuery Developer','Matched skills: css, html, javascript, jquery','2026-06-09 21:12:52'),(123,10,'Entry UI Developer','Matched skills: css, figma, html, javascript','2026-06-09 21:12:52'),(149,5,'Tailwind CSS Developer','Matched skills: css, javascript, tailwind css','2026-06-09 21:24:50'),(150,5,'Entry UI Developer','Matched skills: css, figma, javascript, tailwind css','2026-06-09 21:24:50'),(151,5,'Senior Node.js Developer','Matched skills: javascript, node.js','2026-06-09 21:24:50'),(152,5,'Entry Angular Developer','Matched skills: angular, css, rxjs, typescript','2026-06-09 21:24:50'),(153,5,'Mid Next.js Developer','Matched skills: tailwind css, typescript','2026-06-09 21:24:50');
/*!40000 ALTER TABLE `job_recommendations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `career_id` int DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `level` varchar(60) DEFAULT NULL,
  `location` varchar(120) DEFAULT NULL,
  `required_skills` text,
  PRIMARY KEY (`id`),
  KEY `fk_jobs_career` (`career_id`),
  CONSTRAINT `fk_jobs_career` FOREIGN KEY (`career_id`) REFERENCES `careers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,1,'React Developer','Intern','Remote','React, JavaScript, TypeScript, HTML, CSS, REST API'),(2,2,'Entry Angular Developer','Entry','Islamabad','Angular, TypeScript, RxJS, HTML, CSS'),(3,3,'Senior Vue.js Developer','Senior','Islamabad','Vue.js, JavaScript, HTML, CSS, REST API'),(4,4,'Entry Frontend Developer','Entry','Karachi','HTML, CSS, JavaScript, React, Bootstrap'),(5,5,'Entry JavaScript Developer','Entry','Hybrid','JavaScript, TypeScript, HTML, CSS, Node.js'),(6,6,'Mid Next.js Developer','Mid','Karachi','Next.js, React, TypeScript, Tailwind CSS'),(7,7,'Entry UI Developer','Entry','On-site','HTML, CSS, JavaScript, Tailwind CSS, Figma'),(8,8,'Tailwind CSS Developer','Intern','Karachi','Tailwind CSS, HTML, CSS, JavaScript'),(9,9,'Web Accessibility Specialist','Intern','Karachi','accessibility, HTML, CSS, JavaScript'),(10,10,'Junior jQuery Developer','Junior','Remote','jQuery, JavaScript, HTML, CSS'),(11,11,'Mid Python Developer','Mid','Remote','Python, Flask, FastAPI, SQL, Git'),(12,12,'Junior Django Developer','Junior','Hybrid','Python, Django, REST API, PostgreSQL, Git'),(13,13,'Mid Flask Developer','Mid','Hybrid','Python, Flask, REST API, SQL'),(14,14,'Entry FastAPI Developer','Entry','Islamabad','Python, FastAPI, REST API, PostgreSQL'),(15,15,'Senior Node.js Developer','Senior','Lahore','Node.js, Express.js, JavaScript, MongoDB, REST API'),(16,16,'Senior Express.js Developer','Senior','Hybrid','Express.js, Node.js, JavaScript, MongoDB'),(17,17,'PHP Developer','Intern','Islamabad','PHP, MySQL, REST API, Git'),(18,18,'Laravel Developer','Intern','Lahore','Laravel, PHP, MySQL, REST API'),(19,19,'Mid Ruby on Rails Developer','Mid','Remote','Ruby, REST API, PostgreSQL, Git'),(20,20,'Junior Java Backend Developer','Junior','Karachi','Java, Spring Boot, SQL, REST API'),(21,21,'Spring Boot Developer','Intern','Remote','Spring Boot, Java, REST API, PostgreSQL'),(22,22,'Go Developer','Intern','Hybrid','Go, REST API, Docker, PostgreSQL'),(23,23,'Junior C# Developer','Junior','Remote','C#, .NET, SQL, REST API'),(24,24,'Junior .NET Developer','Junior','Islamabad','.NET, C#, Microsoft SQL Server, REST API'),(25,25,'Full Stack Developer','Intern','Remote','JavaScript, React, Node.js, SQL, Git'),(26,26,'Senior MERN Stack Developer','Senior','Islamabad','MongoDB, Express.js, React, Node.js'),(27,27,'Mid MEAN Stack Developer','Mid','On-site','MongoDB, Express.js, Angular, Node.js'),(28,28,'Senior WordPress Developer','Senior','Hybrid','WordPress, PHP, MySQL, HTML, CSS'),(29,29,'Mid Shopify Developer','Mid','Hybrid','Shopify, Liquid, JavaScript, HTML, CSS'),(30,30,'Entry Magento Developer','Entry','Islamabad','Magento, PHP, MySQL, JavaScript'),(31,31,'Entry Drupal Developer','Entry','On-site','Drupal, PHP, MySQL, HTML'),(32,32,'WooCommerce Developer','Intern','Karachi','WordPress, WooCommerce, PHP, CSS'),(33,33,'Entry Webflow Developer','Entry','Remote','Webflow, HTML, CSS, JavaScript'),(34,34,'Mid Headless CMS Developer','Mid','On-site','Next.js, React, GraphQL, REST API'),(35,35,'Junior Jamstack Developer','Junior','Hybrid','Next.js, React, GraphQL, Tailwind CSS'),(36,36,'Mid Android Developer','Mid','Remote','Kotlin, Java, Android, REST API, Firebase'),(37,37,'Mid iOS Developer','Mid','Karachi','Swift, iOS, REST API, Firebase'),(38,38,'Senior Flutter Developer','Senior','Remote','Flutter, Dart, REST API, Firebase'),(39,39,'React Native Developer','Intern','Remote','React Native, React, JavaScript, REST API'),(40,40,'Kotlin Developer','Intern','On-site','Kotlin, Android, REST API'),(41,41,'Entry Swift Developer','Entry','Lahore','Swift, iOS, REST API'),(42,42,'Mid Mobile App Developer','Mid','Islamabad','Flutter, React Native, REST API, Firebase'),(43,43,'Entry Ionic Developer','Entry','Lahore','Ionic, Angular, TypeScript, REST API'),(44,44,'Junior Game Developer','Junior','Karachi','C#, Unity, C++, problem solving'),(45,45,'Entry AR/VR Developer','Entry','Lahore','Unity, C#, computer vision basics, 3D'),(46,46,'Junior Data Analyst','Junior','Remote','SQL, Excel, Power BI, Tableau, Statistics'),(47,47,'Data Scientist','Intern','Hybrid','Python, Pandas, Machine Learning, SQL, Statistics'),(48,48,'Entry Data Engineer','Entry','Islamabad','Python, SQL, Apache Spark, Airflow, dbt'),(49,49,'Senior Business Intelligence Analyst','Senior','Lahore','Power BI, SQL, Tableau, Excel'),(50,50,'BI Developer','Intern','On-site','Power BI, SQL, data modeling, dbt'),(51,51,'Analytics Engineer','Intern','Hybrid','dbt, SQL, Python, data modeling'),(52,52,'Big Data Engineer','Intern','Karachi','Apache Spark, Hadoop, Python, SQL'),(53,53,'Senior Data Architect','Senior','Remote','data modeling, SQL, data warehousing, ETL'),(54,54,'Junior Statistician','Junior','Islamabad','Statistics, R, Python, data visualization'),(55,55,'Entry Marketing Analyst','Entry','Lahore','Google Analytics, SQL, Excel, data visualization'),(56,56,'Senior Quantitative Analyst','Senior','On-site','Python, Statistics, R, data modeling'),(57,57,'Entry Bioinformatics Analyst','Entry','On-site','Python, R, Statistics, data analysis'),(58,58,'Junior AI Engineer','Junior','Lahore','Python, Machine Learning, Deep Learning, TensorFlow, PyTorch'),(59,59,'Entry Machine Learning Engineer','Entry','Islamabad','Python, Scikit-learn, Machine Learning, PyTorch, MLOps'),(60,60,'Entry Deep Learning Engineer','Entry','Islamabad','Python, Deep Learning, TensorFlow, PyTorch'),(61,61,'Entry NLP Engineer','Entry','Karachi','Python, NLP, PyTorch, Hugging Face'),(62,62,'Mid Computer Vision Engineer','Mid','On-site','Python, OpenCV, Deep Learning, PyTorch'),(63,63,'Mid Generative AI Engineer','Mid','On-site','Python, LLMs, Prompt Engineering, Hugging Face'),(64,64,'Mid AI Prompt Engineer','Mid','Lahore','Prompt Engineering, LLMs, Python'),(65,65,'Junior MLOps Engineer','Junior','Hybrid','MLOps, Docker, Kubernetes, Python, CI/CD'),(66,66,'Robotics Engineer','Intern','Islamabad','Python, C++, computer vision basics, control systems'),(67,67,'Mid DevOps Engineer','Mid','Lahore','Docker, Kubernetes, AWS, CI/CD, Linux'),(68,68,'Mid Cloud Engineer','Mid','Remote','AWS, Docker, Terraform, Linux'),(69,69,'Mid AWS Solutions Architect','Mid','Hybrid','AWS, Terraform, Docker, networking'),(70,70,'Junior Azure Engineer','Junior','Hybrid','Azure, Docker, CI/CD, Terraform'),(71,71,'Mid GCP Engineer','Mid','Karachi','Google Cloud, Docker, Kubernetes, Terraform'),(72,72,'Site Reliability Engineer','Intern','Karachi','Kubernetes, Prometheus, Linux, CI/CD'),(73,73,'Entry Platform Engineer','Entry','Hybrid','Kubernetes, Terraform, Docker, CI/CD'),(74,74,'Entry Kubernetes Administrator','Entry','Karachi','Kubernetes, Docker, Linux, Helm'),(75,75,'Mid Infrastructure Engineer','Mid','Lahore','Terraform, AWS, Linux, Ansible'),(76,76,'Senior Release Engineer','Senior','Islamabad','CI/CD, Jenkins, Git, Docker'),(77,77,'Entry Cloud Security Engineer','Entry','Karachi','cloud security, AWS, Network Security, compliance'),(78,78,'Entry Cybersecurity Analyst','Entry','On-site','Network Security, Splunk, Linux, Incident Response'),(79,79,'Mid Security Engineer','Mid','On-site','Network Security, Cryptography, Linux, cloud security'),(80,80,'Entry Penetration Tester','Entry','Lahore','Penetration Testing, Burp Suite, Kali Linux, Ethical Hacking'),(81,81,'Senior SOC Analyst','Senior','Lahore','SIEM, Splunk, Network Security, Incident Response'),(82,82,'Senior Information Security Analyst','Senior','Karachi','Network Security, compliance, risk assessment'),(83,83,'Entry Network Security Engineer','Entry','Islamabad','Network Security, Firewalls, networking, Linux'),(84,84,'Entry Application Security Engineer','Entry','Remote','Penetration Testing, Burp Suite, API security'),(85,85,'Junior Security Architect','Junior','Hybrid','Network Security, Cryptography, cloud security, compliance'),(86,86,'Senior Incident Responder','Senior','On-site','Incident Response, SIEM, Splunk, forensics'),(87,87,'Junior Malware Analyst','Junior','Islamabad','malware analysis, reverse engineering, Kali Linux'),(88,88,'Entry Ethical Hacker','Entry','Islamabad','Ethical Hacking, Penetration Testing, Kali Linux, Burp Suite'),(89,89,'Mid UI/UX Designer','Mid','Islamabad','Figma, Adobe XD, Wireframing, Prototyping, User Research'),(90,90,'Entry Product Designer','Entry','On-site','Figma, Prototyping, User Research, Design Systems'),(91,91,'Senior UX Researcher','Senior','Hybrid','User Research, usability testing, Figma'),(92,92,'Senior Graphic Designer','Senior','On-site','Photoshop, Illustrator, Typography, Canva'),(93,93,'Visual Designer','Intern','Hybrid','Figma, Photoshop, Typography, color theory'),(94,94,'Junior Interaction Designer','Junior','Lahore','Figma, Prototyping, Interaction Design'),(95,95,'Entry Motion Designer','Entry','Islamabad','Adobe After Effects, motion, animation'),(96,96,'Mid Brand Designer','Mid','Lahore','Illustrator, brand guidelines, Typography'),(97,97,'Web Designer','Intern','Islamabad','Figma, HTML, CSS, Webflow'),(98,98,'Senior Design Systems Engineer','Senior','Islamabad','Figma, Design Systems, HTML, CSS'),(99,99,'Digital Marketer','Intern','On-site','SEO, Google Ads, Google Analytics, content marketing'),(100,100,'Senior SEO Specialist','Senior','Remote','SEO, Google Analytics, content strategy, keyword research'),(101,101,'Entry Content Marketer','Entry','Islamabad','content marketing, copywriting, SEO, content strategy'),(102,102,'Senior Social Media Manager','Senior','Islamabad','Social Media Marketing, Canva, content strategy'),(103,103,'Junior Performance Marketer','Junior','Karachi','Meta Ads, Google Ads, conversion optimization'),(104,104,'Entry Email Marketing Specialist','Entry','Remote','Email Marketing, Mailchimp, HubSpot, copywriting'),(105,105,'Senior Growth Marketer','Senior','Karachi','Growth Marketing, Google Analytics, conversion optimization'),(106,106,'Junior PPC Specialist','Junior','On-site','Google Ads, Meta Ads, conversion optimization'),(107,107,'Entry Affiliate Marketing Manager','Entry','Lahore','Affiliate Marketing, SEO, Google Analytics'),(108,108,'Junior Brand Manager','Junior','Karachi','Brand Strategy, content strategy, Communication'),(109,109,'Entry Copywriter','Entry','Hybrid','copywriting, content strategy, SEO'),(110,110,'Mid Content Strategist','Mid','Islamabad','content strategy, SEO, content marketing'),(111,111,'Junior Influencer Marketing Manager','Junior','Hybrid','Social Media Marketing, Brand Strategy, Communication'),(112,112,'Product Manager','Intern','Hybrid','Project Management, Agile, Communication, Stakeholder Management'),(113,113,'Junior Project Manager','Junior','On-site','Project Management, Agile, Jira, Communication'),(114,114,'Senior Scrum Master','Senior','Lahore','Scrum, Agile, Jira, Communication'),(115,115,'Mid Business Analyst','Mid','Karachi','Business Analysis, SQL, Communication, Problem Solving'),(116,116,'Junior Product Owner','Junior','Lahore','Agile, Scrum, Stakeholder Management'),(117,117,'Senior Program Manager','Senior','On-site','Project Management, Stakeholder Management, Communication'),(118,118,'Mid Technical Program Manager','Mid','On-site','Project Management, Agile, Communication'),(119,119,'Mid Agile Coach','Mid','Hybrid','Agile, Scrum, Communication, Leadership'),(120,120,'Operations Manager','Intern','Lahore','Operations, Leadership, Communication'),(121,121,'Senior Strategy Analyst','Senior','Remote','Business Analysis, Excel, Communication'),(122,122,'Entry QA Engineer','Entry','Remote','Selenium, Test Automation, Jira, Problem Solving'),(123,123,'Junior Test Automation Engineer','Junior','On-site','Selenium, Test Automation, Python, CI/CD'),(124,124,'Junior Manual QA Tester','Junior','Remote','manual testing, Jira, test cases'),(125,125,'Entry SDET','Entry','Remote','Test Automation, Selenium, Java, CI/CD'),(126,126,'Entry Performance Test Engineer','Entry','Remote','JMeter, performance testing, Test Automation'),(127,127,'Entry Quality Analyst','Entry','Hybrid','manual testing, test cases, Jira'),(128,128,'Entry Database Administrator','Entry','Hybrid','SQL, PostgreSQL, MySQL, Linux'),(129,129,'Entry Database Developer','Entry','Karachi','SQL, PostgreSQL, data modeling'),(130,130,'Mid ETL Developer','Mid','Hybrid','ETL, SQL, Python, data warehousing'),(131,131,'Entry Systems Administrator','Entry','Karachi','Linux, Bash, networking, Git'),(132,132,'Junior Network Engineer','Junior','Karachi','networking, Linux, Firewalls'),(133,133,'Mid Embedded Systems Engineer','Mid','On-site','C++, C, embedded, Linux'),(134,134,'Entry IoT Developer','Entry','Karachi','Python, C++, embedded, REST API'),(135,135,'Entry Blockchain Developer','Entry','Hybrid','Solidity, blockchain, JavaScript, REST API'),(136,136,'Senior Technical Writer','Senior','On-site','technical writing, Communication, Markdown'),(137,137,'Mid Developer Advocate','Mid','Karachi','Communication, Python, public speaking'),(138,138,'Computer Graphics Engineer','Intern','Lahore','C++, OpenGL, 3D, mathematics'),(139,139,'Senior Solutions Engineer','Senior','On-site','Communication, REST API, SQL, Problem Solving'),(140,140,'Junior Sales Engineer','Junior','Hybrid','Communication, REST API, Problem Solving'),(141,141,'Senior IT Support Specialist','Senior','Lahore','Linux, networking, troubleshooting, Communication');
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profiles` (
  `user_id` int NOT NULL,
  `age` int DEFAULT NULL,
  `education_level` varchar(100) DEFAULT NULL,
  `institution` varchar(150) DEFAULT NULL,
  `graduation_year` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profiles`
--

LOCK TABLES `profiles` WRITE;
/*!40000 ALTER TABLE `profiles` DISABLE KEYS */;
INSERT INTO `profiles` VALUES (1,18,'BS computer science','NUST','2025'),(3,18,'Bachelor of Science in Marketing','University of Colorado Boulder','2024'),(5,23,'BS','xyz','2025'),(9,22,'BS SE','UMW','2025'),(10,23,'BS SE','UMW','2025');
/*!40000 ALTER TABLE `profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skills`
--

DROP TABLE IF EXISTS `skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `skills` (
  `id` int NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `skill_name` (`skill_name`)
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skills`
--

LOCK TABLES `skills` WRITE;
/*!40000 ALTER TABLE `skills` DISABLE KEYS */;
INSERT INTO `skills` VALUES (29,'.net'),(131,'Adobe After Effects'),(119,'Adobe XD'),(144,'Affiliate Marketing'),(149,'Agile'),(3,'ai'),(30,'Ai automation'),(87,'Airflow'),(25,'analytics'),(46,'Angular'),(71,'Ansible'),(85,'Apache Spark'),(67,'AWS'),(68,'Azure'),(74,'Bash'),(53,'Bootstrap'),(139,'Brand Strategy'),(113,'Burp Suite'),(35,'C#'),(34,'C++'),(128,'Canva'),(62,'Cassandra'),(73,'CI/CD'),(21,'Communication'),(101,'Computer Vision'),(133,'Content Marketing'),(23,'content strategy'),(143,'Conversion Optimization'),(134,'Copywriting'),(153,'Critical Thinking'),(115,'Cryptography'),(16,'CSS'),(107,'Cybersecurity'),(44,'Dart'),(19,'Data Analysis'),(93,'Data Cleaning'),(92,'Data Modeling'),(103,'Data Science'),(84,'Data Visualization'),(88,'dbt'),(95,'Deep Learning'),(78,'DevOps'),(26,'digital growth'),(145,'Digital Marketing'),(5,'Django'),(13,'Docker'),(63,'Elasticsearch'),(135,'Email Marketing'),(110,'Ethical Hacking'),(89,'ETL'),(79,'Excel'),(49,'Express.js'),(7,'FastAPI'),(18,'Figma'),(64,'Firebase'),(117,'Firewalls'),(6,'Flask'),(12,'Git'),(65,'GitHub'),(36,'Go'),(137,'Google Ads'),(90,'Google Analytics'),(69,'Google Cloud'),(77,'Grafana'),(56,'GraphQL'),(140,'Growth Marketing'),(86,'Hadoop'),(15,'HTML'),(141,'HubSpot'),(104,'Hugging Face'),(122,'Illustrator'),(116,'Incident Response'),(129,'InVision'),(32,'Java'),(17,'JavaScript'),(72,'Jenkins'),(151,'Jira'),(55,'jQuery'),(118,'Kali Linux'),(99,'Keras'),(39,'Kotlin'),(66,'Kubernetes'),(51,'Laravel'),(147,'Leadership'),(14,'Linux'),(105,'LLMs'),(91,'Looker'),(94,'Machine Learning'),(142,'Mailchimp'),(43,'MATLAB'),(28,'Mean'),(27,'MERN'),(138,'Meta Ads'),(61,'Microsoft SQL Server'),(2,'ml'),(57,'MongoDB'),(10,'MySQL'),(108,'Network Security'),(52,'Next.js'),(75,'Nginx'),(100,'NLP'),(48,'Node.js'),(83,'NumPy'),(102,'OpenCV'),(60,'Oracle'),(82,'Pandas'),(109,'Penetration Testing'),(121,'Photoshop'),(1,'php'),(11,'PostgreSQL'),(80,'Power BI'),(22,'Problem Solving'),(148,'Project Management'),(76,'Prometheus'),(106,'Prompt Engineering'),(126,'Prototyping'),(154,'Public Speaking'),(4,'Python'),(97,'PyTorch'),(42,'R'),(45,'React'),(58,'Redis'),(8,'REST API'),(37,'Ruby'),(38,'Rust'),(41,'Scala'),(98,'Scikit-learn'),(150,'Scrum'),(132,'SEM'),(24,'seo'),(180,'shopify'),(111,'SIEM'),(120,'Sketch'),(136,'Social Media Marketing'),(112,'Splunk'),(50,'Spring Boot'),(9,'SQL'),(59,'SQLite'),(155,'Stakeholder Management'),(20,'Statistics'),(40,'Swift'),(81,'Tableau'),(54,'Tailwind CSS'),(146,'Teamwork'),(96,'TensorFlow'),(70,'Terraform'),(152,'Time Management'),(33,'TypeScript'),(130,'Typography'),(123,'UI Design'),(127,'User Research'),(124,'UX Design'),(47,'Vue.js'),(125,'Wireframing'),(114,'Wireshark');
/*!40000 ALTER TABLE `skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_interests`
--

DROP TABLE IF EXISTS `user_interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_interests` (
  `user_id` int NOT NULL,
  `interest_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`interest_id`),
  KEY `fk_user_interests_interest` (`interest_id`),
  CONSTRAINT `fk_user_interests_interest` FOREIGN KEY (`interest_id`) REFERENCES `interests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_interests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_interests`
--

LOCK TABLES `user_interests` WRITE;
/*!40000 ALTER TABLE `user_interests` DISABLE KEYS */;
INSERT INTO `user_interests` VALUES (1,1),(3,1),(5,2),(5,3),(5,4),(5,5),(9,7),(10,7),(10,8),(10,9),(10,10),(10,11),(9,14),(9,15),(9,16),(9,17);
/*!40000 ALTER TABLE `user_interests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_skills`
--

DROP TABLE IF EXISTS `user_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_skills` (
  `user_id` int NOT NULL,
  `skill_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`skill_id`),
  KEY `fk_user_skills_skill` (`skill_id`),
  CONSTRAINT `fk_user_skills_skill` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_skills_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_skills`
--

LOCK TABLES `user_skills` WRITE;
/*!40000 ALTER TABLE `user_skills` DISABLE KEYS */;
INSERT INTO `user_skills` VALUES (1,1),(1,2),(1,3),(10,19),(10,21),(3,23),(3,24),(3,25),(3,26),(5,27),(5,28),(5,29),(5,30),(9,35),(9,45),(9,46),(10,46),(10,62),(10,95),(9,108),(9,180);
/*!40000 ALTER TABLE `user_skills` ENABLE KEYS */;
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
  `role` varchar(20) NOT NULL DEFAULT 'student',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'s','s1@gmail.com','$2y$10$/bLJ5v9U1ePFSDxZp7KWYepC.Y.cfAoCdymjCvhMdM5lHDhnAB.jK','student','2026-02-02 14:46:40'),(2,'admin','a1@gmail.com','$2y$10$hBx22aQXLF.ekVYLt9pLheQkpptz20SULZU9wJrv1rD/6MVEFAAti','admin','2026-02-02 16:59:02'),(3,'s2','s2@gmail.com','$2y$10$wbjTPjuX4.Sv/zIlzdw7q.z6z/f0Cv/B9Kr2JocADNxO/KDUSUr/C','student','2026-02-10 12:00:46'),(5,'Syed Aoun Naqvi','aouns6229@gmail.com','$2y$12$dpm.Tgbk82XsP75M364zhuruK0ZInwFnr.EcxYSjeJdiX670/Rv5i','student','2026-06-08 21:00:48'),(7,'Aoun Muhammad','aoun@ecomback.dev','$2y$12$FnPpWrvm/G.ZSBIrFBVwue1yT6J6z4Y3AxMzgJLAA9yz4OzxdNTRO','admin','2026-06-09 17:53:02'),(9,'Career Mind Admin','careermind@gmail.com','$2y$12$wMnUyXTOgRFoEI8ybtVNguHJ9thmw4FEEA.3OqhzQv90vVnqpkix2','admin','2026-06-09 18:05:09'),(10,'Aoun Work','aounwork1@gmail.com','$2y$12$cKYUpcYc7Z0eXGVjpQcZjOgML2d9ODXtPLi6qpw3RTvOn6B5mTqwS','student','2026-06-09 18:21:42');
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

-- Dump completed on 2026-06-09 21:29:24
