invoice | CREATE TABLE `invoice` (
  `key_invoice` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_number` int(20) NOT NULL,
  `date` date NOT NULL,
  `key_client` int(10) NOT NULL,
  `sum` decimal(10,2) NOT NULL,
  `accepted` char(20) NOT NULL,
  `passed` char(20) NOT NULL,
  `detail` varchar(32) NOT NULL,
  PRIMARY KEY (`key_invoice`),
  KEY `invoice_ibfk_1` (`key_client`),
  CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`key_client`) REFERENCES `client` (`key_client`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251 |


