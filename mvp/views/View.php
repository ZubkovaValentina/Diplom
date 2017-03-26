<?php

require_once 'Route.php';

class View
{
	/* Стандартный шаблон для всех страниц. */
	const BASE_TEMPLATE_VIEW = 'base_template.html';
	
	/**
	 $content_view — то, что отображается на конкретной странице
	 $template_view — общий шаблон для всех страниц
	 $controller — контроллер, через которого получаем данные для отображения на конкретной странице
	 
	 Функцией include динамически подключается общий шаблон (вид), внутри которого
	 будет встраиваться вид для отображения модели конкретной страницы.
	*/
	function generate($content_view, $template_view = null, $controller = null)
	{
		/* По умолчанию используем наш стандартный шаблон. */
		if($template_view === null) $template_view = View::BASE_TEMPLATE_VIEW;
		include Route::VIEWS.$template_view;
	}
}
