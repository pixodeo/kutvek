<?php
declare(strict_types=1);
namespace Core\Domain;

use Core\Database\Database;
use Core\Library\TraitL10n;
use Core\Routing\{RouteInterface,TraitRequest};
use Psr\Http\Message\ServerRequestInterface;

class Table 
{
    use TraitL10n, TraitRequest;
    
    protected $table;
    
    protected $entity = null; 
    protected RouteInterface $_route;   
    protected ServerRequestInterface $_request; 
    protected null|array $_constructorArgs = null;

    public function __construct(protected Database $db)
    {
        $this->db = $db;
        if (is_null($this->table)) {
            $parts = explode('\\', get_class($this));
            $class_name = end($parts);            
            $this->table = strtolower(str_replace('Table', '', $class_name));
        }
    }

    public function setConstructorArgs(array $args){
        $this->_constructorArgs = $args;
        if($this->db instanceof Database) $this->db->setConstructorArgs($args);
    }

    public function unsetConstructorArgs(){
        $this->_constructorArgs = null;
        if($this->db instanceof Database) $this->db->unsetConstructorArgs();
    }

    public function setTable($table){
            $this->table = $table;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function setRoute(RouteInterface $route): void {$this->_route = $route;}   
   

    /** Penser à faire un insert multiple values  */
    public function create(array $fields = [], bool $one = true){ 
        $columns = implode(', ', array_keys($fields));
        $placeHolder = $this->placeHolder($fields);
        return $this->query("INSERT INTO `{$this->table}` ({$columns}) VALUES ($placeHolder) ;", array_values($fields), $one);      
    }

    public function update($id, $fields = array(), $cond = 'id'){
        $sql_parts = [];
        $attributes = [];
        foreach($fields as $k => $v){
            $sql_parts[] = "$k = ?";
            $attributes[] = $v;
        }
        $attributes[] = $id;
        $sql_part = implode(', ', $sql_parts);
        return $this->query("UPDATE {$this->table} SET $sql_part WHERE {$cond} = ?", $attributes, true);
    }
   
    public function delete($id, $cond = 'id'){
        return $this->query("DELETE FROM {$this->table} WHERE {$cond} = ?", [$id], true);
    }

   

    /**
     * Excécute la requête via pdo , requête préparée ou query 
     * @param  string  $statement  la requete (SELECT, UPDATE...)
     * @param  array  $attributes 
     * @param  boolean $one        si TRUE, plusieurs résultats
     * @param  [type]  $class_name [description]
     * @return [type]              [description]
     */
    public function query(string $statement, ?array $attributes = NULL, bool $one = FALSE){
        // Toujours mettre $class à true pour construire des entités en rapport
        if($this->entity !== null)
        {             
            $reflexion = new \ReflectionClass($this);            
            $class_name = $reflexion->getNamespaceName(). '\\' . $this->entity;
            $class_name = str_replace('Table', 'Entity', $class_name);              
        }            
        else
            $class_name = NULL;

        if($attributes){

            return $this->db->prepare(
                $statement,
                $attributes,
                $class_name,
                $one
            );
        } else {
            return $this->db->query(
                $statement,
                $class_name,
                $one
            );
        }
    }

    public function lastInsertId(){
        return $this->db->lastInsertId();
    }

    public function beginTransaction() {
        
        $this->db->beginTransaction();
    }

    public function commit() {
        
        $this->db->commit();
    }
    
    public function rollback() {
        
        $this->db->rollback();
    }

    public function placeHolder($data):string {
        $place_holders = implode(',', array_fill(0, count($data), '?'));  
        return $place_holders;
    }

    public function emulate_prepares(bool $choice = TRUE) {
        $this->db->emulate_prepares($choice);
    }

    public function set_marquers($marquer) {
        return $marquer . ' = ?';
    } 

    public function namedPlaceHolder($datas, ?string $prefix = null){
        $place_holders = array();       
        foreach($datas as $k => $data){
            if($prefix !== null)
                $place_holders[] = ':'. $prefix.$k;
            else
                $place_holders[] = ':'.$k;            
        };
        $values = array_combine($place_holders, $datas);
        $place_holder = implode(',', $place_holders); 

        return (object)array('place_holder' => $place_holder, 'values' => $values);
    }

    public function updatePlaceholder($datas, ?string $prefix = null){
        $keys = array(); 
              
        foreach($datas as $k => $data){
            if($prefix !== null)
                $keys[] = "{$k} = :{$prefix}{$k}";
            else
                $keys[] = "{$k} = :{$k}";         
        };
        //$values = array_combine($keys, $datas);
        $place_holder = implode(',', $keys); 

        return $place_holder;
    }

    public function namedPlaceHolder2($datas, ?string $prefix = null){
        $place_holders = array();   
        $keys = [];
        

        foreach($datas as $k => $data){
            if($prefix !== null){
                $place_holders[] = ':'. $prefix.'_'.$k;
                $keys[] = ':'. $prefix.'_'.$k;
            }                
            else {
                $place_holders[] = ':'.$k;                
                $keys[] = ':'.$k;
            }
        };
        $values = array_combine($keys, $datas);
        $place_holder = implode(', ', $place_holders); 

        return (object)array('place_holder' => $place_holder, 'values' => $values);
    }

    public function prepare(string $statement)
    {
        return $this->db->_prepare($statement);
    }

    public function setMarquers(array $fields = [])
    {
        $sql_parts = [];
        
        foreach($fields as $k => $v){
            $sql_parts[] = "{$k} = ?";
            
        }
        //$attributes[] = $id;
        return implode(', ', $sql_parts);
    }

    public function setFetchMode(bool|int $fetchMode){
        $this->db->setFetchMode($fetchMode);
        return $this;
    }

    protected function _setFetchMode(int $mode, $column ) {
        $this->db->setFetchMode($fetchMode);
    }

    public function call(ServerRequestInterface $request) { }

    public function getMiddlewares(): array {return []; }

    public function getMatches(): array { return [];}


}