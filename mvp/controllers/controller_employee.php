<?php

require_once 'controllers/Controller.php';
require_once 'models/model_employee.php';

class Controller_Employee extends Controller
{
	const TYPE_NAME = 'employee';
	const COL_FULL_NAME = 'full_name';
	
	function __construct()
	{
		parent::__construct(new Model_Employee());
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
		$this->model = new Model();
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
			$row['full_name'] = '';
			$row['INN'] = '';
			$row['position'] = '';
			$row['birthday'] = '';
			$row['children'] = '';
			$row['education'] = '';
			$row['sex'] = '';
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
		if(empty($_POST['education'])) $error = 'Образование';
//		if(empty($_POST['children'])) $error = 'Кол-во детей';
		if(empty($_POST['birthday'])) $error = 'Дата рождения';
		if(empty($_POST['position'])) $error = 'Должность';
		if(empty($_POST['INN'])) $error = 'ИНН';
		if(empty($_POST['full_name'])) $error = 'ФИО сотрудника';
		
		$record = array();
		if(isset($_POST['sex'])) $record['sex'] = $_POST['sex'][0];
		else $record['sex'] = 0;
		$record['education'] = $this->db->real_escape_string($_POST['education']);
		if(empty($_POST['children'])) $record['children'] = 0;
		else $record['children'] = $this->db->real_escape_string($_POST['children']);
		$record['birthday'] = $this->db->real_escape_string($_POST['birthday']);
		$record['position'] = $this->db->real_escape_string($_POST['position']);
		$record['INN'] = $this->db->real_escape_string($_POST['INN']);
		$record['full_name'] = $this->db->real_escape_string($_POST['full_name']);
		
		$key = $this->getKeyValue();
		$this->model->__set("is_edit", $key);
		
		if(!$error)
		{
			if($key)
			{
				$sql = "UPDATE `".$this->getType()."` SET 
						`full_name`='".$record['full_name']."', 
						`INN`='".$record['INN']."',
						`position`='".$record['position']."',
						`birthday`='".$record['birthday']."',
						`children`=".$record['children'].",
						`education`='".$record['education']."',
						`sex`=".$record['sex']."
						WHERE `".$this->getKeyColumn()."`=$key";
			}
			else
			{
				$sql = "INSERT INTO `".$this->getType()."`(
					`full_name`, `INN`, `position`, `birthday`, `children`, `education`, `sex`) VALUES(
					'".$record['full_name']."',
					'".$record['INN']."',
					'".$record['position']."',
					'".$record['birthday']."',
					'".$record['children']."',
					'".$record['education']."',
					".$record['sex'].")";
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
		return 'full_name';
	}
	
	function getTitle()
	{
		return 'Информация о сотруднике';
	}
	
	function getHeader()
	{
		return 'Список сотрудников';
	}
}
