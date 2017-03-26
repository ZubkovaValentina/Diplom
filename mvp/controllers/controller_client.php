<?php

require_once 'controllers/Controller.php';
require_once 'models/model_client.php';

class Controller_Client extends Controller
{
	function __construct()
	{
		parent::__construct(new Model_Client());
	}
	
	function action()
	{
		$this->view->generate('list_view.html', null, $this->model);
	}
	
	function create()
	{
		$key = $this->getKeyValue();
	}
	
	function edit()
	{
		$key = $this->getKeyValue();
	}
}
