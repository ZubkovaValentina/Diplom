<?php

require_once 'views/View.php';

/**
 * Классический CRUD
 * https://ru.wikipedia.org/wiki/CRUD
 *
 * Обрабтка запросов вида:
 *
 * 1. Список записей из таблицы {my_type} 
 * /{my_type}/
 *
 * 2. Удаление записи с ключом key_{my_type}
 * /{my_type}/delete/?key_{my_type}={значение ключа}
 *
 * 3. Добавление новой записи в таблицу {my_type}
 * /{my_type}/create/
 
 
 */
abstract class Controller
{
	const PARAM_KEY = 'key';
	
	const ACTION_DELETE = 'delete';
	const ACTION_CREATE = 'create';
	const ACTION_EDIT = 'edit';
	
	
	protected $model;
	protected $view;
	
	function __construct($model)
	{
		$this->model = $model;
		$this->view = new View();
	}
	
	/**
		Вызывается для URI вида: /{my_controller}/
		Выдергивает записи из соответствующей таблицы
	*/
	abstract function action();
	
	/** 
	 * Удаление записи из соответствующей таблицы
	 * Запрос: /{my_type}/delete/?key={my_key_value}
	 */
	function delete()
	{
		$key = $this->getKeyValue();
		$sql = "DELETE FROM `".$this->model->getType()."` WHERE `key_".$this->model->getType()."`=".$key;
		mysql_query($sql) or die(mysql_error());
		header('Location: /client.html');
	}
	
	/** Создание записи */
	abstract function create();
		
	/** Редактирование записи */
	abstract function edit();
	
	/** Возвращает название ключа в БД для соответствующей таблицы */
	function getKeyName()
	{
		return 'key_'.$this->model->getType();
	}
	
	/** */
	static function getKeyValue()
	{
		$key = null;
		
		if(isset($_POST[Controller::PARAM_KEY])) $key = $_POST[Controller::PARAM_KEY];
		else if(isset($_GET[Controller::PARAM_KEY])) $key = $_GET[Controller::PARAM_KEY];
		else
		{
			// @TODO: Выбросить исключение
		}
		return $key;
	}
}
