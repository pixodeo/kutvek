<?php
declare(strict_types=1);
namespace App\Section\Behavior;

use Core\Behavior;
use App\Section\Domain\Graphics\GraphicKitTable;
use Core\Model\Table;

class GraphicFilters extends Behavior 
{
	use \App\Library\Sanitize\TraitSanitize;
	private array $_queryParams = [];
	private false|int  $_store = false;
	private $_paging = true;
	protected array $_cards = [];
    private array $_items = [];    
    private $_currentPage = 1;
    private $_pages;
    protected $_itemsByPage = 30;
    public $breadcrumd = '';
    public $widgetCustom = '';
    public $filters = '';
    protected $_editorialContent;
    protected $_widgetPath;
    
    public function __construct()
    {
    	$this->_path = dirname(dirname(__FILE__)); 
    	$this->_widgetPath = dirname(dirname(__FILE__));
    }

    public function __invoke(string $filter){
    	$table = $this->getTable('graphicKitTable');
    	$table->setFilters($this->_queryParams);
    	return $table->getFilterData($filter);
    }

    public function getTable(string $key): Table {
        if(!array_key_exists($key, $this->_tables)){
            $db = $this->_setDb();
            $table = match ($key) {                
                'graphicKitTable'  => new GraphicKitTable($db),                  
            };            
            $table->setI18n($this->getI18n());
            $table->setCountry($this->getCountry());
            $table->setCurrency($this->getCurrency());
            $this->_tables[$key] = $table;
        }
        return $this->_tables[$key];
    }

	public function setQueryParams(array $params):void{$this->_queryParams = array_filter($params);}
	public function setStore(int $store):void{ $this->_store = $store;}
	public function getStore():int|false{return $this->_store;}	
	public function setContent($content):void{$this->_editorialContent = $content;}
	public function getContent(){return $this->_editorialContent;}
}