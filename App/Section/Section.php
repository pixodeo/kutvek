<?php
declare(strict_types=1);
namespace App\Section;

use Core\Action;
use Core\Behavior as Behavior;
use Core\Model\Table;
use App\Section\Behavior\Graphics;
use App\Section\Domain\CategoryTable;
use App\Section\Domain\ContentEntity;
use Psr\Http\Message\ServerRequestInterface;

final class Section extends Action {
	use \App\Library\TraitCookie;

    private  false|object $_customer = false;
    /**
     * parametres de la requête $query
     */
    private false|object $_params = false;    
    private false|ContentEntity $_editorial = false;
    private false|Behavior $_behavior = false;

	public function __construct(ServerRequestInterface $_request, string $i18n)
	{		
        $this->setRequest($_request);
        $this->setI18n($i18n);        
        $this->setCustomer();
	}

    public function setParams(object $params): void{
        $this->_params = $params;

    }

    public function getCategoryId():int {
        return $this->_params->id;
    }

    public function getParams(): false|object{ return $this->_params;}

    public function setCustomer(){
        $session_token = $this->getRequest()->getCookieParams()['session_token'] ?? false;
        if($session_token) $this->_customer = $this->getCookie($session_token);
    }
    
    public function isCustomerPro(): bool {        
        return $this->_customer ? $this->_customer->type === 'pro' : false;        
    }
    public function getContent(){
        if($this->_editorial) return $this->_editorial;
        //$url = $this->getRequest()->getUri()->getPath();
        //$url = trim($this->withoutPrefix($url),'/');
        //$slug = $this->trimUrl($url);
        $category = $this->getTable('CategoryTable');
        $this->_editorial = $category->getContent($this->getCategoryId());
        return $this->_editorial;
    }

    public function flashMsg(){
        $table = $this->getTable('CategoryTable');
        $sql = "SELECT body FROM msg_flash 
        WHERE _since <= current_timestamp()
        AND _until >= current_timestamp() 
        AND l10n = :l10n
        AND pro = :pro
         AND workspace = :wp; 
        ";
        $isPro = $this->isCustomerPro() ? 1 :0;  
        return $table->query($sql,['l10n'=>$this->getL10nId(), 'pro'=>$isPro, 'wp' => 2,]);
    }       

    public function loadBehavior(): false|Behavior {       
        switch($this->getContent()->behavior):
            case 'Graphics':
                $this->_behavior = new Graphics();
                $this->_behavior->setI18n($this->getI18n());
                $this->_behavior->setCountry($this->getCountry());
                $this->_behavior->setCurrency($this->getCurrency());
                $this->_behavior->setRequest($this->getRequest());
                $this->_behavior->setRouter($this->getRouter()); 
                
                $filters = [
                    'family' => $this->getContent()->family,
                    'brand' => $this->getContent()->brand,
                    'model' => $this->getContent()->model                    
                ]; 
                $this->_behavior->setQueryParams($filters);
                $this->_behavior->setStore($this->getParams()->d_store ?? false);
                $this->_behavior->setContent($this->getContent()); 
                $this->_behavior->setCustomer();
                $this->_behavior->setBreadcrumb($this->_breadcrumbs());
                $this->_behavior->setFlashMsg($this->flashMsg());
                break;            
        endswitch;
        return $this->_behavior;
    }

    public function categoryChilds(){
        $table = $this->getTable('category');
        return $table->childs($this->getContent()->category);
    }

    public function getTable(string $key): Table {
        if(!array_key_exists($key, $this->_tables)){
            $db = $this->_setDb();
            $table = match ($key) {
                'CategoryTable' => new CategoryTable($db),                 
            };            
            $table->setI18n($this->getI18n());
            $table->setCountry($this->getCountry());
            $table->setCurrency($this->getCurrency());
            $this->_tables[$key] = $table;
        }
        return $this->_tables[$key];
    }

    private function _breadcrumbs(): string {
        $url = $this->getRequest()->getUri()->getPath();        
        $slug = $this->trimUrl($url);
        $category = $this->getTable('CategoryTable');
        $breadcrumbs = $category->breadcrumbs($this->getContent()->category);
        $this->_path = dirname(__FILE__);
        $this->_view = 'breadcrumb';
        return $this->partial(compact('breadcrumbs'));

    }    
}