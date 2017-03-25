<?php

require_once 'views/View.php';

abstract class Controller
{
	protected $model;
	protected $view;
	
	function __construct($model)
	{
		$this->model = $model;
		$this->view = new View();
	}
	
	abstract function action();
}
