<?php

class Model
{
	protected $data = array();
	/**
	 * Используем «магический» метод __get() http://php.net/manual/ru/language.oop5.overloading.php
	 */
	function __set($name, $value)
	{
		$this->data[$name] = $value;
	}
	
	function __get($name)
	{
		if(array_key_exists($name, $this->data)) return $this->data[$name];
		/* @TODO: Записать в лог эту досадную неприятность */
		return null;
	}
}
