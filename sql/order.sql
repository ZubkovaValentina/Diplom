CREATE TABLE my_order (
	`key_order` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`key_client` INT(10),
	FOREIGN KEY (`key_client`) REFERENCES `client`(`key_client`) ON DELETE SET NULL
);

CREATE TABLE `order_detail` (
	`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`key_order` INT(10),
	`key_detail` INT(10),
	`key_employee` INT(10),
	FOREIGN KEY (`key_order`) REFERENCES my_order(`key_order`) ON DELETE SET NULL,
	FOREIGN KEY (`key_detail`) REFERENCES `detail`(`key_detail`) ON DELETE SET NULL
);

CREATE TABLE `order_service` (
	`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`key_order` INT(10),
	`key_service` INT(10),
	FOREIGN KEY (`key_order`) REFERENCES my_order(`key_order`) ON DELETE SET NULL,
	FOREIGN KEY (`key_service`) REFERENCES service(`key_service`) ON DELETE SET NULL
);


SELECT o.key_order, d.name_detail, d.price 
	FROM order_detail AS od 
	JOIN detail AS d ON d.key_detail=od.key_detail
	JOIN my_order AS o ON o.key_order=od.key_order
	WHERE od.key_order=2;
	
	
SELECT SUM(d.price) FROM order_detail AS od 
	JOIN detail AS d ON od.key_detail=d.key_detail
	WHERE od.key_order=2
	
/*
Проверка удаления детали и проверка суммы заказа.

1. Создаем временную деталь: */

INSERT INTO detail(name_detail, manufacturer, car_model, price, kolvo)
VALUES('тестовая деталь', 'я сам', 'ведро', 111, 10)

/* 2. Добавляем эту деталь в заказ №2 */

INSERT INTO order_detail(key_order, key_detail) VALUES(2, 3)

/* 3. Проверяем список деталей в заказе №2:  */

SELECT o.key_order, d.name_detail, d.price 
	FROM order_detail AS od 
	JOIN detail AS d ON d.key_detail=od.key_detail
	JOIN my_order AS o ON o.key_order=od.key_order
	WHERE od.key_order=2;
	
/* 4. Проверяем сумму заказа:  */

SELECT SUM(d.price) FROM order_detail AS od 
	JOIN detail AS d ON od.key_detail=d.key_detail
	WHERE od.key_order=2
	
/* 5. Удаляем тестовую деталь */

DELETE FROM detail WHERE key_detail=3;

/* Выполняем пункты 3 и 4, чтобы убедиться. */

