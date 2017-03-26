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
		/*
		POST-запрос. Проверяем поля формы и, если все ОК,
		то добавляем в БД. Иначе выводим ошибку.	
		*/
		if(isset($_POST['submit']))
		{
		}
		/* GET-запрос. Выводим пустую форму. */
		else
		{
			$this->view->generate('client_view.html', null, $this->model);
		}
		
	}
	
	function edit()
	{
		$key = $this->getKeyValue();
	}
}
