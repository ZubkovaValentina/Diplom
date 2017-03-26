<?php

require_once '../Config.php';
require_once '../log4php/Logger.php';

abstract class Model
{
	protected $db;
	protected $log;
	
	function __construct()
	{
		Logger::configure('../log_config.xml');
		$this->log = Logger::getLogger(__CLASS__);
		
		error_reporting(E_ALL & ~E_DEPRECATED);
		$this->db = mysql_connect(Config::DB_HOST, Config::DB_USER, Config::DB_PWD);
		mysql_select_db(Config::DB_NAME, $this->db);
		
		$this->log->debug("Test logger message");
	}
	
	function __destruct()
	{
		mysql_close($this->db);
	}
	
	/** Возвращает тип модели (таблицы в БД) — client, employee, etc. */
	abstract function getType();
	/** Возврашает заголовок окна */
	abstract function getTitle();
	/** Возвращает название страницы */
	abstract function getHeader();
	/** Возвращает список модели (записи соответствующей таблицы БД) */
	abstract function getData();
}
