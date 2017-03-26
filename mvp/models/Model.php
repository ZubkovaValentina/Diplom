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
		$this->log = Logger::getLogger('myLogger');
		error_reporting(E_ALL & ~E_DEPRECATED);
		$this->db = mysql_connect(Config::DB_HOST, Config::DB_USER, Config::DB_PWD);
		mysql_select_db(Config::DB_NAME, $this->db);
		$this->log->debug("Test logger message");
	}
	
	function __destruct()
	{
		mysql_close($this->db);
	}
	
	abstract function getTitle();
	abstract function getHeader();
	abstract function getData();
}
