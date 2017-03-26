<?php

require_once 'models/Model.php';
require_once 'models/Row.php';

class Model_Client extends Model
{
	const TYPE_NAME = 'client';
	
	function getType()
	{
		return self::TYPE_NAME;
	}
	
	function getTitle()
	{
		return 'Информация о клиенте';
	}
	
	function getHeader()
	{
		return 'Список клиентов';
	}
	
	function getData()
	{
		$nr = mysql_query("SELECT `key_client`, `full_name` FROM `".$this->getType()."`") or die(mysql_error());
		$num = mysql_num_rows($nr);
		
		$rows = array();
		for($i = 0; $i < $num; ++$i)
		{
			$row = new Row();
			list($row->key, $row->name) = mysql_fetch_row($nr);
			$rows[] = $row;
		}
		return $rows;
	}
}
