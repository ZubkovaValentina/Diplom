<?php

require_once 'controllers/Controller.php';
require_once 'models/Model.php';

class Controller_Main extends Controller
{
	function __construct()
	{
		parent::__construct(new Model());
	}
	
	function getTitle()
	{
		return 'Дипломный проект Валентины Зубковой';
	}
	
	function getHeader()
	{
		return 'Диспетчер';
	}
	
	function getStringRepresentation()
	{
		return null;
	}
	
	function getType()
	{
		return null;
	}
	
	function all()
	{
		$this->view->generate('main_view.html', null, $this);
	}
}
