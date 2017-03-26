<?php

require_once '../Config.php';
require_once '../log4php/Logger.php';

class Route
{
	const MODELS = 'models/';
	const VIEWS = 'views/';
	const CONTROLLERS = 'controllers/';
	
	static function start()
	{
		Logger::configure('../log_config.xml');
		$log = Logger::getLogger(__CLASS__);
		
		// контроллер и действие по умолчанию
		$controller_name = 'Main';
		$action_name = 'action';
		
		// преобразовываем путь в массив путем разбиения пути на '/'
		// http://php.net/manual/ru/function.explode.php
		$routes = explode('/', $_SERVER['REQUEST_URI']);

		// получаем имя контроллера
		if(!empty($routes[1])) $controller_name = $routes[1];
		
		// получаем имя экшена
		if(!empty($routes[2])) $action_name = $routes[2];

		// добавляем префиксы
		$model_name = 'Model_'.$controller_name;
		$controller_name = 'Controller_'.$controller_name;

		// подцепляем файл с классом модели (файла модели может и не быть)
		$model_file = $model_name.'.php';
		$model_path = self::MODELS.$model_file;
		
		$log->debug("controller_name=$controller_name");
		$log->debug("action_name=$action_name");
		
		if(file_exists($model_path)) include self::MODELS.$model_file;

		// подцепляем файл с классом контроллера
		$controller_file = $controller_name.'.php';
		$controller_path = self::CONTROLLERS.$controller_file;
		if(file_exists($controller_path)) include self::CONTROLLERS.$controller_file;
		else
		{
			Route::ErrorPage404();
			return;
		}
		
		// создаем контроллер
		$controller = new $controller_name;
		$action = $action_name;
		
		// вызываем действие контроллера
		if(method_exists($controller, $action)) $controller->$action();
		else Route::ErrorPage404();
	}
	
	/* Показываем страницу с ошибкой 404 и выходим */
	static function ErrorPage404()
	{
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.1 404 Not Found');
		header("Status: 404 Not Found");
		include '404.html';
    }
}
