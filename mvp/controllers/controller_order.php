<?php

require_once 'controllers/Controller.php';
require_once 'controllers/controller_client.php';
require_once 'models/Model.php';

class Controller_Order extends Controller
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
			$this->view->generate('list_order_view.html', null, $this);
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
		if($key)
		{
			$this->model->__set("is_edit", true);
			$sql = "SELECT * FROM `".$this->getType()."` WHERE `".$this->getKeyColumn()."`=".$key;
			
			if($result = $this->db->query($sql))
			{
				/* извлечение ассоциативного массива */
				$row = $result->fetch_assoc();
				if($row === null)
				{
					echo "Ошибка! Запись с ключом $key не найдена в таблице «".$this->getType()."»";
					exit(0);
				}
			
				/* удаление выборки */
				$result->free();
			}
			else
			{
				$this->log->error("Ошибка SQL: ".$this->db->error.' '.$sql);
				return null;
			}
			
			
			
			/* Выцепляем из БД список деталей для этого заказа */
			
			$sql = "SELECT o.key_order, 
			d.key_detail AS key_detail, 
			d.name_detail AS name_detail, 
			d.price AS price
			
	FROM order_detail AS od 
	JOIN detail AS d ON d.key_detail=od.key_detail
	JOIN my_order AS o ON o.key_order=od.key_order
	WHERE od.key_order=".$key;
			$details = array();
			$detail_sum = 0;
			if($result = $this->db->query($sql))
			{
				while($row1 = $result->fetch_assoc())
				{
					$details[] = $row1;
					$detail_sum += $row1['price'];
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
		}
		else
		{
			$row = array();
			$row['key_order'] = '';
			$row['date'] = '';
			$row['key_client'] = '';
			$this->model->__set("is_edit", false);
		}
		
		$clients =$this->getClients();
		
		$this->model->__set("record", $row);
		$this->model->__set("clients", $clients);
		
		$this->model->__set("detail_sum", $detail_sum);
		$this->model->__set("details", $details);
		
		
		$this->view->generate('order_edit.html', null, $this);
	}
	
	/**
	 * @Override
	 */
	function save()
	{
		$this->model = new Model();
		
		$error = false;
		
		$record = array();
		
		$record['key_provider'] = 0;
		if(isset($_POST['key_client'])) $record['key_client'] = $_POST['key_client'][0];
		
		if($record['key_client'] == 0) $error = 'Клиент';
		
		$key = $this->getKeyValue();
		$this->model->__set("is_edit", $key);
		
		if(!$error)
		{
			if($key)
			{
				$sql = "UPDATE `".$this->getType()."` SET 
						`key_client`=".$record['key_client']."
						WHERE `".$this->getKeyColumn()."`=$key";
			}
			else
			{
				$sql = "INSERT INTO `".$this->getType()."`(
					`key_client`) VALUES(
					".$record['key_client'].")";
			}
				
			$this->log->debug("update SQL: $sql");
			$this->db->query($sql);
			/* Редирект на список текущего типа {my_type} */
			header('Location: /'.$this->getType().'/');
			exit(0);
		}
		$this->model->__set('error', $error);
		
		$record['key_'.$this->getType()] = $key;
		$this->model->__set('record', $record);
		
		$this->view->generate($this->getType().'_edit.html', null, $this);
	}
	
	function getClients()
	{
		$clients = array();
		$sql = "SELECT `key_client`, `".Controller_Client::COL_FULL_NAME."` FROM `".Controller_Client::TYPE_NAME."` ORDER BY `".Controller_Client::COL_FULL_NAME."`";
		if($result = $this->db->query($sql))
		{
			while($row = $result->fetch_assoc()) $clients[] = $row;
			/* удаление выборки */
			$result->free();
		}
		else
		{
			$this->log->error("Ошибка SQL: ".$this->db->error);
			$this->log->error("SQL: ".$sql);
			return null;
		}
		
		return $clients;
	}
	
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
		return 'Информация о заказах';
	}
	
	function getHeader()
	{
		return 'Заказы';
	}
}
