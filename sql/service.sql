CREATE TABLE `service` (
  `key_service` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_service` VARCHAR(255) NOT NULL,
  `price` INT UNSIGNED NOT NULL
);

CREATE TABLE `order` (
	`key_order` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`date` DATE NOT NULL DEFAULT 'NOW()',
	`key_client` INT(10),
	FOREIGN KEY (`key_client`) REFERENCES `client`(`key_client`) ON DELETE SET NULL
);


CREATE TABLE `service_employee` (
  
);