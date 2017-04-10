<?php

require_once 'controllers/Controller.php';
require_once 'models/Model.php';

class Controller_Detail extends Controller
{
	const TYPE_NAME = 'detail';
	const COL_FULL_NAME = 'name_detail';
	
	function __construct()
	{
		parent::__construct(new Model());
	}
	
	function action()
	{
		$this->view->generate('list_view.html', null, $this->getList());
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
			$row['name_detail'] = '';
			$row['manufacturer'] = '';
			$row['car_model'] = '';
			$row['price'] = '';
			$row['kolvo'] = '';
			$row['key_provider'] = '';
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
		if(empty($_POST['name_detail'])) $error = 'Название';
		if(empty($_POST['manufacturer'])) $error = 'Производитель';
		if(empty($_POST['car_model'])) $error = 'Модель авто';
		if(empty($_POST['price'])) $error = 'Цена';
		if(empty($_POST['kolvo'])) $error = 'Количество';
		
		$record = array();
		$record['name_detail'] = $this->db->real_escape_string($_POST['name_detail']);
		$record['manufacturer'] = $this->db->real_escape_string($_POST['manufacturer']);
		$record['car_model'] = $this->db->real_escape_string($_POST['car_model']);
		$record['price'] = $this->db->real_escape_string($_POST['price']);
		$record['kolvo'] = $this->db->real_escape_string($_POST['kolvo']);
		
		if(isset($_POST['key_provider'])) $record['key_provider'] = $_POST['key_provider'][0];
		else $record['key_provider'] = 0;

		
		$key = $this->getKeyValue();
		$this->model->__set("is_edit", $key);
		
		if(!$error)
		{
			if($key)
			{
				$sql = "UPDATE `".$this->getType()."` SET 
						`name_detail`='".$record['name_detail']."',
						`manufacturer`='".$record['manufacturer']."',
						`car_model`='".$record['car_model']."',
						`price`=".$record['price'].",
						`kolvo`=".$record['kolvo']."
						WHERE `".$this->getKeyColumn()."`=$key";
			}
			else
			{
				$sql = "INSERT INTO `".$this->getType()."`(
					`name_organization`, `provider_address`, `mobile_phone`, `fax`, `INN`) VALUES(
					'".$record['name_organization']."',
					'".$record['provider_address']."',
					".$record['mobile_phone'].",
					".$record['fax'].",
					".$record['INN'].")";
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
		return Controller_Provider::COL_FULL_NAME;
	}
	
	function getTitle()
	{
		return 'Информация о поставщике';
	}
	
	function getHeader()
	{
		return 'Список поставщиков';
	}
}
