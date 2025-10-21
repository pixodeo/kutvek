<?php
declare(strict_types=1);
namespace Library\HTML;

trait TraitPagination {

    protected $_itemsByPage = 30;
    protected $_currentPage = 1;
    protected array $_items = [];
    protected float $_pages;
    protected array $_slices = [];

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

        $this->_slices = array_slice($this->_items, $offset, $this->_itemsByPage);
    }

    public function setCurrentPage(int $page):void {
        $this->_currentPage = $page;
    }

    public function getCurrentPage():int {
        return $this->_currentPage;
    }

    public function getNumberOfPages():float {
        return $this->_pages ?? 0.00;
    }

    public function getSlices(){
        return $this->_slices;
    }
}