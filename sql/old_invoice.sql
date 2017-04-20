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


SELECT
	od.id,
	od.key_order
	/*
	d.key_detail,
	d.name_detail
/*	p.name_organization */
	FROM order_detail AS od
	/*
	LEFT JOIN detail AS d ON
			d.key_detail=od.key_detail
			/*
		LEFT JOIN provider AS p ON
			p.key_provider=d.key_provider
			*/
	WHERE od.key_order=3;

SELECT
	od.id AS id,
	d.key_detail AS key_detail,
	d.name_detail AS name_detail,
	p.key_provider AS key_provider,
	p.name_organization AS name_organization,
	e.key_employee AS key_employee,
	e.full_name AS full_name
FROM order_detail AS od
	LEFT JOIN detail AS d ON d.key_detail=od.key_detail
	LEFT JOIN provider AS p ON p.key_provider=d.key_provider
	LEFT JOIN employee AS e ON e.key_employee=od.key_employee
WHERE od.key_order=3;
