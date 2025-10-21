<?php
namespace Core\Component;

abstract class Context {
	abstract public function setStrategy(Strategy $strategy):void;
}