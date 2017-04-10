CREATE TABLE `detail` (
  `key_detail` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_detail` varchar(255) NOT NULL,
  `manufacturer` varchar(255) NOT NULL,
  `car_model` varchar(255),
  `price` int unsigned NOT NULL,
  `kolvo` int(8) NOT NULL,
  `key_provider` INT,
  FOREIGN KEY (`key_provider`) REFERENCES `provider`(`key_provider`)
  );
