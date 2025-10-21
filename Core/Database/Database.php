<?php
declare(strict_types=1);
namespace Core\Database;

class Database {
	protected null|array $_constructorArgs = null;
	public function setConstructorArgs(array $args){ $this->_constructorArgs = $args; }
	public function unsetConstructorArgs(){ $this->_constructorArgs = null; }
	public function __debugInfo()
	{
		return [];
	}
}