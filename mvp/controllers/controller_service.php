<?php

require_once 'controllers/Controller.php';
require_once 'models/Model.php';

class Controller_Service extends Controller
{
	const TYPE_NAME = 'service';
	const COL_FULL_NAME = 'name_service';
	
	function __construct()
	{
		parent::__construct(new Model());
	}
	
	/** @Override */
	function all()
	{
		$list_of_items = array(); /* массив объектов Row */

		$sql = "SELECT `".$this->getKeyColumn()."`, `".$this->getNameColumn()."`, `price` FROM `".$this->getType()."` ORDER BY `".$this->getNameColumn()."`";
		$this->log->debug("Get service list: ".$sql);
		if($result = $this->db->query($sql))
		{
			/* извлечение массива */
			while($row = $result->fetch_assoc()) $list_of_items[] = $row;
			/* удаление выборки */
			$result->free();
			
			$this->model->__set('list', $list_of_items);
			$this->view->generate('list_service_view.html', null, $this);
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
				$this->log->error("Ошибка SQL: ".$this->db->error);
				return null;
			}
		}
		else
		{
			$row = array();
			$row['key_'.$this->getType()] = '';
			$row['name_service'] = '';
			$row['price'] = '';
			$this->model->__set("is_edit", false);
		}
		
		$this->model->__set("record", $row);
		$this->view->generate($this->getType().'_edit.html', null, $this);
	}
	
	/**
	 * @Override
	 */
	function save()
	{
		$this->model = new Model();
		
		$error = false;
		if(empty($_POST['name_service'])) $error = 'Вид работы';
		if(empty($_POST['price'])) $error = 'Стоимость';
		
		$record = array();
		$record['name_service'] = $this->db->real_escape_string($_POST['name_service']);
		$record['price'] = $this->db->real_escape_string($_POST['price']);
		
		$key = $this->getKeyValue();
		$this->model->__set("is_edit", $key);
		
		if(!$error)
		{
			if($key)
			{
				$sql = "UPDATE `".$this->getType()."` SET 
						`name_service`='".$record['name_service']."',
						`price`=".$record['price']."
						WHERE `".$this->getKeyColumn()."`=$key";
			}
			else
			{
				$sql = "INSERT INTO `".$this->getType()."`(
					`name_service`, `price`) VALUES(
					'".$record['name_service']."',
					".$record['price'].")";
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
		return 'key_'.$this->getType();
	}
	
	/** @Override */
	function getNameColumn()
	{
		return Controller_Service::COL_FULL_NAME;
	}
	
	function getTitle()
	{
		return 'Информация о видах работ';
	}
	
	function getHeader()
	{
		return 'Список видов работ';
	}
}
