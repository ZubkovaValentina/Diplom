<?php

require_once 'models/Model.php';

class Model_Main extends Model
{
	function getType()
	{
		return null;
	}
	
	function getTitle()
	{
		return 'Дипломный проект Валентины Зубковой';
	}
	
	function getHeader()
	{
		return 'Диспетчер';
	}
	
	function getData()
	{
		return null;
	}
}
