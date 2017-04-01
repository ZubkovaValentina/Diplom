<?php

require_once 'controllers/Controller.php';
require_once 'models/Model.php';

class Controller_Main extends Controller
{
	function __construct()
	{
		parent::__construct(new Model());
	}
	
	/** @Override */
	function getTitle()
	{
		return 'Дипломный проект Валентины Зубковой';
	}
	
	/** @Override */
	function getHeader()
	{
		return 'Диспетчер';
	}
	
	/** @Override */
	function getStringRepresentation()
	{
		return null;
	}
	
	/** @Override */
	function getType()
	{
		return null;
	}
	
	/** @Override */
	function getKeyColumn()
	{
		return null;
	}
	
	/** @Override */
	function getNameColumn()
	{
		return null;
	}
	
	/** @Override */
	function all()
	{
		$this->view->generate('main_view.html', null, $this);
	}
	
	function delete() {}
	function create() {}
	function edit() {}
}
