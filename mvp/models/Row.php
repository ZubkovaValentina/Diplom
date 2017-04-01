<?php

/**
 immutable
*/
class Row
{
	private $key;
	private $name;
	
	function __construct($key, $name)
	{
		$this->key = $key;
		$this->name = $name;
	}
	
	public function getKey()
	{
		return $this->key;
	}
	
	public function getName()
	{
		return $this->name;
	}
}
