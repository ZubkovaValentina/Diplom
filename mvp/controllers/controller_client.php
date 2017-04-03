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
		$this->log->debug("wefweferfg");
		$this->view->generate('list_view.html', null, $this->getList());
	}
	
	function create()
	{
		/*
		POST-запрос. Проверяем поля формы и, если все ОК,
		то добавляем в БД. Иначе выводим ошибку.	
		*/
		if(isset($_POST['submit']))
		{
		}
		/* GET-запрос. Выводим пустую форму. */
		else
		{
			$this->view->generate('client_view.html', null, $this);
		}
	}

	/**
	 * @Override
	 */
	function edit()
	{
		$key = $this->getKeyValue();
		$sql = "SELECT * FROM `".$this->getType()."` WHERE `".$this->getKeyColumn()."`=".$key;
		if($result = $this->db->query($sql))
		{
			$this->model = new Model();
			/* извлечение ассоциативного массива */
			$row = $result->fetch_assoc();
			if($row === null)
			{
				echo "Ошибка! Запись с ключом $key не найдена в таблице «".$this->getType()."»";
				exit(0);
			}
			$this->model->__set("record", $row);
			$this->model->__set("is_edit", true);
			/* удаление выборки */
			$result->free();
			$this->view->generate('client_edit.html', null, $this);
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
	function save()
	{
		$this->model = new Model();
		
		$error = false;
		if(empty($_POST['series_p'])) $error = 'Серия/номер паспорта';
		if(empty($_POST['mobile_phone'])) $error = 'Телефон';
		if(empty($_POST['address'])) $error = 'Адрес';
		if(empty($_POST['full_name'])) $error = 'ФИО сотрудника';
		
		$record = array();
		$record['series_p'] = $this->db->real_escape_string($_POST['series_p']);
		$record['mobile_phone'] = $this->db->real_escape_string($_POST['mobile_phone']);
		$record['address'] = $this->db->real_escape_string($_POST['address']);
		$record['full_name'] = $this->db->real_escape_string($_POST['full_name']);
		
		$key = $this->getKeyValue();
		
		if(!$error)
		{
			$sql = "UPDATE `".$this->getType()."` SET 
						`full_name`='".$record['full_name']."', 
						`address`='".$record['address']."',
						`mobile_phone`=".$record['mobile_phone'].", 
						`series_p`=".$record['series_p']."
						WHERE `".$this->getKeyColumn()."`=$key";
				
			$this->log->debug("update SQL: $sql");
			$this->db->query($sql);
			/* Редирект на список текущего типа {my_type} */
			header('Location: /'.$this->getType().'/');
			exit(0);
		}
		$this->model->__set('error', $error);
		
		$record['key_client'] = $key;
		$this->model->__set('record', $record);
		
		$this->view->generate('client_edit.html', null, $this);
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
