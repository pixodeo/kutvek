<?php
namespace Core\Component;

use Core\Interfaces\ContextInterface;

abstract class Component {
	
	const VIEW_PATH = null;
	protected $_context;

	public function getContext(): Context {return $this->_context;}

	public function setContext(Context $context): void{$this->_context = $context;}
}