<?php
declare(strict_types=1);
namespace Core\Interfaces;

use stdClass;

interface UrlQueryResult {
	public stdClass $queryResult{ get; set; }

	public function setQueryResult(stdClass $query): void;
}