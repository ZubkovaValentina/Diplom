<?php

require_once 'controllers/Controller.php';
require_once 'models/Model.php';

class Controller_Provider extends Controller
{
	const TYPE_NAME = 'provider';
	const COL_FULL_NAME = 'name_organization';
	
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
			$row['name_organization'] = '';
			$row['provider_address'] = '';
			$row['mobile_phone'] = '';
			$row['fax'] = '';
			$row['INN'] = '';
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
		if(empty($_POST['INN'])) $error = 'ИНН';
		if(empty($_POST['fax'])) $error = 'Факс';
		if(empty($_POST['mobile_phone'])) $error = 'Номер телефона';
		if(empty($_POST['provider_address'])) $error = 'Адрес';
		if(empty($_POST['name_organization'])) $error = 'Название поставщика';
		
		$record = array();
		$record['name_organization'] = $this->db->real_escape_string($_POST['name_organization']);
		$record['provider_address'] = $this->db->real_escape_string($_POST['provider_address']);
		$record['mobile_phone'] = $this->db->real_escape_string($_POST['mobile_phone']);
		$record['fax'] = $this->db->real_escape_string($_POST['fax']);
		$record['INN'] = $this->db->real_escape_string($_POST['INN']);
		
		$key = $this->getKeyValue();
		$this->model->__set("is_edit", $key);
		
		if(!$error)
		{
			if($key)
			{
				$sql = "UPDATE `".$this->getType()."` SET 
						`name_organization`='".$record['name_organization']."',
						`provider_address`='".$record['provider_address']."',
						`mobile_phone`=".$record['mobile_phone'].",
						`fax`=".$record['fax'].",
						`INN`=".$record['INN']."
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
	
	/** @Override */
	function delete()
	{
		$key = $this->getKeyValue();
		$sql = 'UPDATE `detail` SET `key_provider`=NULL WHERE `key_provider`='.$key;
		$this->log->debug("delete SQL: $sql");
		$this->db->query($sql);
		
		parent::delete();
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
