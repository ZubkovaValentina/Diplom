CREATE TABLE `provider` (
  `key_provider` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name_organization` varchar(255) NOT NULL,
  `provider_address` varchar(255) NOT NULL,
  `mobile_phone` int(11) NOT NULL,
  `fax` int(11) NOT NULL,
  `INN` int(10) NOT NULL);
  