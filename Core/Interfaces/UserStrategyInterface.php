<?php
namespace Core\Interfaces;

interface UserStrategyInterface
{
    
	/**
	 * [fetchTasks description]
	 * @param  ModelInterface $model [description]
	 * @return [type]               [description]
	 */
	
    public function fetchTasks(): array;
	
	public function tasks(): array; 
    
	public function fetchOrders();

	public function searchByComNum(int $search);

	public function isAdmin(): bool;

	public function setAdmin($admin): void;

	public function setUser($user): void;

	public function getView(): string;

	
}