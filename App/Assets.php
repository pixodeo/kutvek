<?php
declare(strict_types=1);
namespace App;
use Core\Action;

final class Assets extends Action {
	public function __invoke(string $file){
		echo $this->auto_version('/'.$file);
    	die();
	}
}