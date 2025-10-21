<?php 
namespace App\Checkout\PSP;

interface ServiceProvider {

	public function create();

	public function capture();

	public function approve();
}