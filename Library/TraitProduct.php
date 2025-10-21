<?php
namespace Library;

use Library\HTML\Form;

trait TraitProduct {
	public int $id;
	public Form $form;
	public $product;
	public array $stores = [];
    public array $behaviors = [];
    public array $vehicles = [];
    public array $designs = [];
    public array $colors = [];
    public array $typesOfKit = [];
    public array $licenses = [];

}