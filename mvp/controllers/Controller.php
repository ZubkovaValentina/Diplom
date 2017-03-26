<?php

require_once 'views/View.php';

abstract class Controller
{
	const PARAM_KEY = 'key';
	
	protected $model;
	protected $view;
	
	function __construct($model)
	{
		$this->model = $model;
		$this->view = new View();
	}
	
	/**
		Вызывается для URI вида: /my_controller/
		Выдергивает записи из соответствующей таблицы
	*/
	abstract function action();
	
	/** Удаление записи из соответствующей таблицы */
	abstract function delete();
	
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
