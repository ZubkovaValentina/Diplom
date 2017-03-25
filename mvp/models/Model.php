<?php

require_once 'Config.php';

abstract class Model
{
	protected $db;
	
	function __construct()
	{
		error_reporting(E_ALL & ~E_DEPRECATED);
		$this->db = mysql_connect(Config::DB_HOST, Config::DB_USER, Config::DB_PWD);
		mysql_select_db(Config::DB_NAME, $this->db);
	}
	
	function __destruct()
	{
		mysql_close($this->db);
	}
	
	abstract function getTitle();
	abstract function getHeader();
	abstract function getData();
}
