<?php

require_once 'controllers/Controller.php';
require_once 'controllers/controller_client.php';
require_once 'controllers/controller_detail.php';
require_once 'models/Model.php';

class Controller_Bill extends Controller
{
	const TYPE_NAME = 'my_order';
	const COL_FULL_NAME = 'key_order';
	
	function __construct()
	{
		parent::__construct(new Model());
	}
	
	/** @Override */
	function all()
	{
		$list_of_items = array(); /* массив объектов Row */

/*
		SELECT o.`key_order`, DATE(o.`date`), c.`key_client`, c.`full_name` FROM `order` AS o 
			LEFT JOIN `client` AS c ON o.key_client=c.key_client;	

*/	
		
		
		$sql = "SELECT o.`".$this->getKeyColumn()."`, DATE(o.`date`) AS `date`, c.`key_client`, c.`full_name` FROM `".$this->getType()."` AS o 
			LEFT JOIN `client` AS c ON o.key_client=c.key_client;";
		
		$this->log->debug("Get model list: ".$sql);
		if($result = $this->db->query($sql))
		{
			/* извлечение индексного массива */
			while($row = $result->fetch_assoc()) $list_of_items[] = $row;
			/* удаление выборки */
			$result->free();
			
			$this->model->__set('list', $list_of_items);
			$this->view->generate('list_bill_view.html', null, $this);
		}
		else
		{
			$this->log->error("Ошибка SQL: ".$this->db->error);
			return null;
		}
	}

	/**
	 * @Override
	 */
	function edit()
	{
		$key = $this->getKeyValue();
		/* В запросе указан key — значит это редактирование */
		if(!$key)
		{
			echo 'Не задан параметр счета';
			exit(0);
		}
		
		$sql = "
SELECT o.key_order,
	DATE(o.`date`) AS `date`, 
	c.key_client,
	c.full_name
	FROM `my_order` AS o
	LEFT JOIN client AS c ON c.key_client=o.key_client
	WHERE o.key_order=$key";
			
		if($result = $this->db->query($sql))
		{
			/* извлечение ассоциативного массива */
			$row = $result->fetch_assoc();
			if($row === null)
			{
				echo "Ошибка! Запись с ключом $key не найдена в таблице «".$this->getType()."»";
				exit(0);
			}
			else $this->model->__set("record", $row);
		
			/* удаление выборки */
			$result->free();
		}
		else
		{
			$this->log->error("Ошибка SQL: ".$this->db->error.' '.$sql);
			return null;
		}
			
		/* Выцепляем из БД список деталей для этого заказа */
			
		$details = array();
		$detail_sum = $this->getOrderLinks($key, 'detail', $details);
			
		$this->model->__set("detail_sum", $detail_sum);
		$this->model->__set("details", $details);

		/* Выцепляем из БД список работ для этого заказа */
			
		$services = array();
		$service_sum = $this->getOrderLinks($key, 'service', $services);
			
		$this->model->__set("service_sum", $service_sum);
		$this->model->__set("services", $services);
			
		$this->view->generate('bill.html', null, $this);
	}
		
	/**
	 * Выцепляем связанные с заказом данные из таблицы $link
	 * $key - номер заказа
	 * $link - название выцепляемой таблицы (detail, service)
	 * $links - массив строк выцепляемой таблицы
	 * возвращает сумму цен.
	 */
	 
	function getOrderLinks($key, $link, &$links)
	{
		$sql = "
SELECT
	od.id AS id,
	o.key_order, 
	d.key_$link AS key_$link, 
	d.name_$link AS name_$link, 
	d.price AS price
			
	FROM order_$link AS od 
		JOIN $link AS d ON d.key_$link=od.key_$link
		JOIN my_order AS o ON o.key_order=od.key_order
	WHERE od.key_order=".$key;
	
		$sum = 0;
		if($result = $this->db->query($sql))
		{
			while($row = $result->fetch_assoc())
			{
				$links[] = $row;
				$sum += $row['price'];
			}
			/* удаление выборки */
			$result->free();
		}
		else
		{
			$this->log->error("Ошибка SQL: ".$this->db->error);
			$this->log->error("SQL: ".$sql);
			return null;
		}
		return $sum;
	}
	
	function save() {}
	
	function getStringRepresentation()
	{
		return self::COL_FULL_NAME;
	}
	
	function getType()
	{
		return self::TYPE_NAME;
	}
	
	/** @Override */
	function getKeyColumn()
	{
		return 'key_order';
	}
	
	/** @Override */
	function getNameColumn()
	{
		return 'key_order';
	}
	
	function getTitle()
	{
		return 'Счета клиентов';
	}
	
	function getHeader()
	{
		return 'Счета';
	}
}
