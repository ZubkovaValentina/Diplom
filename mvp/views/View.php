<?php

require_once 'Route.php';

class View
{
	const BASE_TEMPLATE_VIEW = 'base_template.html';
	
	/**
	 $content_view — то, что отображается на конкретной странице
	 $template_view — общий шаблон для всех страниц
	 $data — данные для отображения на конкретной странице
	 
	 Функцией include динамически подключается общий шаблон (вид), внутри которого
	 будет встраиваться вид для отображения контента конкретной страницы.
	*/
	function generate($content_view, $template_view = null, $model = null)
	{
		if($template_view === null) $template_view = View::BASE_TEMPLATE_VIEW;
		include Route::VIEWS.$template_view;
	}
}
