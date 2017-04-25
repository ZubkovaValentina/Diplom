<?php

require_once 'controllers/Controller.php';
require_once 'models/model_client.php';

class Controller_Client extends Controller
{
	const TYPE_NAME = 'client';
	const COL_FULL_NAME = 'full_name';
	
	function __construct()
	{
		parent::__construct(new Model_Client());
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
			$row['key_client'] = '';
			$row['full_name'] = '';
			$row['address'] = '';
			$row['mobile_phone'] = '';
			$row['series_p'] = '';
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
		if(empty($_POST['series_p'])) $error = 'Серия/номер паспорта';
		if(empty($_POST['mobile_phone'])) $error = 'Телефон';
		if(empty($_POST['address'])) $error = 'Адрес';
		if(empty($_POST['full_name'])) $error = 'ФИО клиента';
		
		$record = array();
		$record['series_p'] = $this->db->real_escape_string($_POST['series_p']);
		$record['mobile_phone'] = $this->db->real_escape_string($_POST['mobile_phone']);
		$record['address'] = $this->db->real_escape_string($_POST['address']);
		$record['full_name'] = $this->db->real_escape_string($_POST['full_name']);
		
		$key = $this->getKeyValue();
		$this->model->__set("is_edit", $key);
		
		if(!$error)
		{
			if($key)
			{
				$sql = "UPDATE `".$this->getType()."` SET 
						`full_name`='".$record['full_name']."', 
						`address`='".$record['address']."',
						`mobile_phone`=".$record['mobile_phone'].", 
						`series_p`=".$record['series_p']."
						WHERE `".$this->getKeyColumn()."`=$key";
			}
			else
			{
				$sql = "INSERT INTO `".$this->getType()."`(
					`full_name`, `address`, `mobile_phone`, `series_p`) VALUES(
					'".$record['full_name']."',
					'".$record['address']."',
					".$record['mobile_phone'].",
					".$record['series_p'].")";
			}
			
			echo $sql.' <br />';
			echo "key=$key";
			exit(0);
			
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
		return 'key_client';
	}
	
	/** @Override */
	function getNameColumn()
	{
		return 'full_name';
	}
	
	function getTitle()
	{
		return 'Информация о клиенте';
	}
	
	function getHeader()
	{
		return 'Список клиентов';
	}
}
