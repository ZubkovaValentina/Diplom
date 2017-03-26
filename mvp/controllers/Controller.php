<?php

require_once 'views/View.php';
require_once '../Config.php';
require_once '../log4php/Logger.php';

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
	
	protected $db;
	protected $log;

	function __construct($model)
	{
		$this->model = $model;
		$this->view = new View();
		
		Logger::configure('../log_config.xml');
		$this->log = Logger::getLogger(__CLASS__);
		
		error_reporting(E_ALL & ~E_DEPRECATED);
		
		$this->db = new mysqli(Config::DB_HOST, Config::DB_USER, Config::DB_PWD, Config::DB_NAME);
		if($this->db->connect_errno)
		{
			$this->log->error("Соединение не удалось: ".$this->db->connect_error);
			exit();
		}
		$this->log->debug("Test logger message");
	}

	function __destruct()
	{
		$this->db->close();
	}
	
	/** Возвращает имя колонки таблицы БД, отвечающей за строковое представление модели */
	abstract function getStringRepresentation();
	/** Возвращает тип модели {my_type} */
	abstract function getType();
	/** Возврашает заголовок окна */
	abstract function getTitle();
	/** Возвращает название страницы */
	abstract function getHeader();

	/**
		Вызывается для URI вида: /{my_type}/
		Выдергивает записи из соответствующей таблицы {my_type}
	*/
	function all()
	{
		$all = array();
		$sql = "SELECT * FROM `".$this->getType()."`";
		$this->log->debug("Get model list: ".$sql);
		if($result = $this->db->query($sql))
		{
			/* извлечение ассоциативного массива */
			while($row = $result->fetch_assoc()) $all[] = $row;
			/* удаление выборки */
			$result->free();
			return $all;
		}
		else
		{
			$this->log->error("Ошибка SQL: ".$this->db->error);
			return null;
		}
	}
	
	/** 
	 * Удаление записи из соответствующей таблицы:
	 * DELETE FROM `{my_type}` WHERE `key_{my_type}`=$key
	 *
	 * Запрос: /{my_type}/delete/?key=$key
	 */
	function delete()
	{
		$key = $this->getKeyValue();
		$sql = "DELETE FROM `".$this->model->getType()."` WHERE `key_".$this->model->getType()."`=".$key;
		mysql_query($sql) or die(mysql_error());
		/* Редирект на список текущего типа {my_type} */
		header('Location: /'.$this->model->getType().'/');
	}
	
	/**
	 * Создание записи. Если GET — создаем пустую форму,
	 * если POST — создаем новую запись в БД:
	 * INSERT INTO `{my_type}` (`…`, `…`, ) VALUES ('…', '…', );
	 */
	abstract function create();
		
	/** 
	 * Редактирование записи. Если GET — создаем форму, заполненную значениями из БД.
	 * Ключ выборки берется из GET-запроса $_GET['key']:
	 * SELECT … FROM `{my_type}` WHERE `key_{my_type}`=$key
	 *
	 * Если POST — сохраняем запись в БД:
	 * UPDATE `{my_type}`
	 */
	abstract function edit();
	
	/** Возвращает название ключа в БД для таблицы {my_type} */
	function getKeyName()
	{
		return 'key_'.$this->model->getType();
	}
	
	/** Возвращает значение соответствующего {my_type} ключа из GET- или POST-запроса. */
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
