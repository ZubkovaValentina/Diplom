<?php

require_once 'controllers/Controller.php';
require_once 'controllers/controller_provider.php';
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
	
	/** @Override */
	function all()
	{
		$list_of_items = array(); /* массив объектов Row */

/*		
		SELECT d.key_detail, d.name_detail, p.key_provider, p.name_organization 
			FROM detail AS d 
			LEFT JOIN provider AS p ON d.key_provider=p.key_provider
*/	
		
		
		
		
		$sql = "SELECT d.".$this->getKeyColumn().", d.".$this->getNameColumn().", d.kolvo, d.price, d.key_provider, p.name_organization
					FROM `".$this->getType()."` AS d
					LEFT JOIN `provider` AS p
					ON d.key_provider=p.key_provider";
		$this->log->debug("Get model list: ".$sql);
		if($result = $this->db->query($sql))
		{
			/* извлечение индексного массива */
			while($row = $result->fetch_assoc()) $list_of_items[] = $row;
			/* удаление выборки */
			$result->free();
			
			$this->model->__set('list', $list_of_items);
			$this->view->generate('list_detail_view.html', null, $this);
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
			$row['name_detail'] = '';
			$row['manufacturer'] = '';
			$row['car_model'] = '';
			$row['price'] = '';
			$row['kolvo'] = '';
			$row['key_provider'] = '';
			$this->model->__set("is_edit", false);
		}
		
		$providers =$this->getProviders();
		
		$this->model->__set("record", $row);
		$this->model->__set("providers", $providers);
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
		
		
		$record = array();
		$record['name_detail'] = $this->db->real_escape_string($_POST['name_detail']);
		$record['manufacturer'] = $this->db->real_escape_string($_POST['manufacturer']);
		$record['car_model'] = $this->db->real_escape_string($_POST['car_model']);
		$record['price'] = $this->db->real_escape_string($_POST['price']);
		
		if(empty($_POST['kolvo'])) $record['kolvo'] = 0;
		else $record['kolvo'] = $this->db->real_escape_string($_POST['kolvo']);
		
		if(isset($_POST['key_provider'])) $record['key_provider'] = $_POST['key_provider'][0];
		else $record['key_provider'] = 'NULL';
		if($record['key_provider'] == 0) $record['key_provider'] = 'NULL';
		
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
						`kolvo`=".$record['kolvo'].",
						`key_provider`=".$record['key_provider']."
						WHERE `".$this->getKeyColumn()."`=$key";
			}
			else
			{
				$sql = "INSERT INTO `".$this->getType()."`(
					`name_detail`, `manufacturer`, `car_model`, `price`, `kolvo`, `key_provider`) VALUES(
					'".$record['name_detail']."',
					'".$record['manufacturer']."',
					'".$record['car_model']."',
					".$record['price'].",
					".$record['kolvo'].",
					".$record['key_provider'].")";
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
	
	function getProviders()
	{
		$providers = array();
		$sql = "SELECT `key_provider`, `".Controller_Provider::COL_FULL_NAME."` FROM `".Controller_Provider::TYPE_NAME."` ORDER BY `".Controller_Provider::COL_FULL_NAME."`";
		if($result = $this->db->query($sql))
		{
			while($row = $result->fetch_assoc()) $providers[] = $row;
			/* удаление выборки */
			$result->free();
		}
		else
		{
			$this->log->error("Ошибка SQL: ".$this->db->error);
			$this->log->error("SQL: ".$sql);
			return null;
		}
		
		return $providers;
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
		return Controller_Detail::COL_FULL_NAME;
	}
	
	function getTitle()
	{
		return 'Информация о деталях';
	}
	
	function getHeader()
	{
		return 'Список деталей';
	}
}
