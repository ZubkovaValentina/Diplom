<?php

require_once 'controllers/Controller.php';
require_once 'models/model_main.php';

class Controller_Main extends Controller
{
	function __construct()
	{
		parent::__construct(new Model_Main());
	}
	
	function action()
	{
		$this->view->generate('main_view.html', null, $this->model);
	}
}
