<?php
declare(strict_types=1);
namespace App\Section\Behavior;

use Core\Behavior;
use App\Section\Domain\Graphics\GraphicKitTable;
use App\Model\Table\StoreTable;
use App\Model\Table\ReinsuranceTable;
use App\Section\Domain\CategoryTable;
use Core\Model\Table;

class Graphics extends Behavior {
	use \App\Library\Sanitize\TraitSanitize;

	private array $_queryParams = [];
	private false|int  $_store = false;
	private $_paging = true;
	protected array $_cards = [];
    private array $_items = [];    
    private $_currentPage = 1;
    private $_pages;
    protected $_itemsByPage = 30;    
    public $widgetCustom = '';
    public $filters = '';
    protected $_editorialContent;
    protected $_widgetPath;
    public $msgs;
    protected int $_category;
    
    public function __construct()
    {
    	$this->_path = dirname(dirname(__FILE__)); 
    	$this->_widgetPath = dirname(dirname(__FILE__)); 

    }

    public function setCategory(int $category):void{$this->_category = $category;}

    public function getTable(string $key): Table {
        if(!array_key_exists($key, $this->_tables)){
            $db = $this->_setDb();
            $table = match ($key) {
                'store' => new StoreTable($db),
                'reinsurance' => new ReinsuranceTable($db),
                'graphicKitTable'  => new GraphicKitTable($db),
                'category' => new CategoryTable($db),                  
            };            
            $table->setI18n($this->getI18n());
            $table->setCountry($this->getCountry());
            $table->setCurrency($this->getCurrency());
            $this->_tables[$key] = $table;
        }
        return $this->_tables[$key];
    }

    public function __invoke() { 
    	$this->_path = dirname(__FILE__,2); 
    	//$this->_responder = new GraphicsResponder(); 
    	$_queryParams = $this->getRequest()->getQueryParams();
        unset($_queryParams['url']);
        if (isset($_queryParams['page'])) {
            $this->_currentPage = $_queryParams['page'];
            unset($_queryParams['page']);

        }
        unset($_queryParams['dev']);
        unset($_queryParams['debug']);
        $this->_queryParams = array_merge($this->_queryParams,$_queryParams);
    	$xhr = $this->getRequest()->getHeaderLine('x-requested-with');
    	$this->setItems();
    	$this->paginate();
    	$this->setCards(); 
    	if($xhr === 'XMLHttpRequest'):
    			$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');
    			$cards = $this->getCards();
    			$this->_view = 'graphics.cards';
    			$partial = $this->partial(compact('cards'));
    			return json_encode(['cards'=> $partial, 'pagination' => $this->getPagination()],JSON_UNESCAPED_SLASHES|JSON_PRESERVE_ZERO_FRACTION|JSON_NUMERIC_CHECK|JSON_UNESCAPED_UNICODE);
    	else:
    		$this->_view = 'graphics'; 
            $this->_content = $this->partial();
            $this->_path = false;
    		$this->_layout = 'graphics';
    		$print =  $this->_print();
            $this->_path = dirname(__FILE__,1); 
            return $print;
    	endif;   	
    }

    public function setItems() {
    	$table = $this->getTable('graphicKitTable');
    	$table->setFilters($this->_queryParams);
    	$this->_items = $table->items(store: $this->getStore(), category: $this->_category);
    }

	public function setCards(){
		$table = $this->getTable('graphicKitTable');		     
        $this->_cards = $table->cards($this->_items);
             
	}

	public function getCards(): array {
		return $this->_cards;
	}

    public function getVehicles(){
        $table = $this->getTable('graphicKitTable');
        return $table->getVehicles();
    }

    public function categoryChilds(){
        $table = $this->getTable('category');
        return $table->childs($this->getContent()->category);
    }

	public function megamenu(){
		$table = $this->getTable('store');
        $items = $table->menu(); 
        $this->_path = dirname(dirname(__FILE__));          	
    	$this->_view = 'menu'; 
		$menu =  $this->partial(compact('items'));
		return $menu;        
	}

	public function footer(){
		$table = $this->getTable('store');
		$items = $table->footer();
		$table = $this->getTable('reinsurance');      	
		$infos = $table->companyInformations();
		$this->_path = dirname(dirname(dirname(__FILE__))); 
		$this->_view = 'partials.footer';
		return $this->partial(compact('items', 'infos'));		
	}


	public function filters(){
		$this->_path = dirname(dirname(__FILE__)); 
		$this->_view = 'graphics.filters';
		return $this->partial();		
	}

    public function setFlashMsg($msgs){
        $this->msgs = $msgs;
    }

    public function flashMsg(){
        return $this->msgs;
    }


	/**
     * Retourne les produits d'une page choisie à partir de la liste de tous les produits.
     *
     * @param array $products La liste des produits.
     * @param integer $page La page sélectionnée.
     * @param integer $productByPage Le nombre de produits à afficher.
     * @return array $pages (Nombre de pages) + $products (produits visibles sur la page).
     */
    public function paginate(): void
    {
        if (count($this->_items) === 0) return;
        $totalProducts = count($this->_items);
        $this->_pages = ceil($totalProducts / $this->_itemsByPage);
        if ($this->_currentPage > $this->_pages) {
            $this->_currentPage = 1;
        }
        $offset = ($this->_itemsByPage * $this->_currentPage) - ($this->_itemsByPage);
        $this->_items = array_slice($this->_items, $offset, $this->_itemsByPage);
    }

    public function getPagination(){
    	$this->_path = dirname(dirname(__FILE__)); 
    	$this->_view = 'widgets.pagination';
    	$pages = $this->_pages;
		return $this->partial(compact('pages'));
    }

	public function setQueryParams(array $params):void{$this->_queryParams = array_filter($params);}
	public function setStore(int $store):void{ $this->_store = $store;}
	public function getStore():int|false{return $this->_store;}
	public function setCurrentPage(int $page){$this->_currentPage = $page;}
	public function setContent($content):void{$this->_editorialContent = $content;}
	public function getContent(){return $this->_editorialContent;}
	public function getCurrentPage(){
		return $this->_currentPage;
	}
}