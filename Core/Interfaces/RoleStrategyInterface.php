<?php
namespace Core\Interfaces;

interface RoleStrategyInterface
{
    
	/**
	 * [fetchTasks description]
	 * @param  ModelInterface $model [description]
	 * @return [type]               [description]
	 */
	
    public function fetchTasks(ModelInterface $model): array;
	
	public function tasks(ModelInterface $model): array; 
    
	public function fetchOrders(ModelInterface $model);

	public function searchByComNum(ModelInterface $model, int $search);

	public function isAdmin(): bool;

	public function setAdmin($admin): void;

	public function setUser($user): void;

	public function getView(): string;
}