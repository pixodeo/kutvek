<?php
namespace Core\Model;

use Core\Database\Database;
use Core\Domain;

class Table extends Domain
{
    protected $table;
    protected $db;
    protected $entity = null;   

    public function __construct(Database $db)
    {
        $this->db = $db;
        if (is_null($this->table)) {
            $parts = explode('\\', get_class($this));
            $class_name = end($parts);
            //$this->table = strtolower(str_replace('Table', '', $class_name)) . 's';
            $this->table = strtolower(str_replace('Table', '', $class_name));
        }
    }

    public function setTable($table){
            $this->table = $table;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
    /**
     * Différence entre all() et find() : all permet une requête de type SELECT * FROM table;
     * Permet aussi la surcharge / redéfinition
     * @param array $fields
     * @param string|null $conds
     * @param boolean $class_name
     * @param array $attributes
     * @return void
     */
    public function all($fields = array(), ?string $conds = null , bool $class_name= FALSE, array $attributes = NULL ){
        // modifier le select *
        $columns = '*';
        if(!empty($fields)){
            $columns = '';
            foreach ($fields as $k => $field) {
                // si la valeur est un tableau, c'est qu'on souhaite appliquer des fonctions, des alias ... sur le champ              
               $columns .= $field.',';
            }
            // suppression de la dernière virgule
            $columns = substr($columns, 0, -1);
        }
        $sql = "SELECT {$columns} FROM  $this->table";
        if($conds !== null) {
            $sql .= " {$conds} ";
        }
        return $this->query($sql, $attributes, $one = false, $class_name);
    }
    
    public function list()
    {
        return $this->query("SELECT  FROM {$this->table}", null, false, false);
    }


    public function find($where = false, ?array $cond = [], ?array $fields = [], $order = null, $limit = null, bool $class_name = false ){
        $columns = '*';
        if(!empty($fields)){
            $columns = '';
            foreach ($fields as $field) {
               $columns .= $field.', ';
            }
            // suppression de la dernière virgule
            $columns = substr($columns, 0, -2);
        }
        if($where) {
            return $this->query("SELECT {$columns} FROM {$this->table} WHERE {$where} = ? {$order} {$limit}", [$cond], false, $class_name);
        } else {
            return $this->query("SELECT {$columns} FROM {$this->table}  {$order} {$limit}", null, false, $class_name);
        }
    }  
   
    public function findOne($where, $cond, $fields = array(), $order=null, $limit = null, bool $entity = false) {
        $columns = '*';
        if(!empty($fields)){
            $columns = '';
            foreach ($fields as $field) {
               $columns .= $this->table.'.'.$field.',';
            }
            // suppression de la dernière virgule
            $columns = substr($columns, 0, -1);
        }
        return $this->query("SELECT {$columns} FROM {$this->table} WHERE {$where} = ? {$order} {$limit}", [$cond], true, $entity);
    } 
   
    public function findVar($where, $cond, $column, $order=null, $limit = null) {
        $sql = $this->query("SELECT {$column} FROM {$this->table} WHERE {$where} = ? {$order} {$limit}", [$cond], true);  
        return $sql->{$column}?? null;
    }  


    /** Penser à faire un insert multiple values  */
    public function create($fields = [], bool $one = true){       

        if(!is_array($fields))
            $fields = (array) $fields;
                
        $columns = implode(', ', array_keys($fields));
        $placeHolder = $this->placeHolder($fields);
        return $this->query("INSERT INTO `{$this->table}` ({$columns}) VALUES ($placeHolder) ;", array_values($fields), $one);
        
        /*
        INSERT INTO table (nom_colonne_1, nom_colonne_2, ...) 
        VALUES ('valeur 1', 'valeur 2', ...),
        VALUES ('valeur 1', 'valeur 2', ...);
         */

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

    public function extract($key, $value, $datas = null){

        $records = $datas ? $datas : $this->all();

        $return = [];
        foreach($records as $v){
            $return[$v->$key] = $v->$value;
        }
        return $return;
    }

    /**
     * Excécute la requête via pdo , requête préparée ou query 
     * @param  string  $statement  la requete (SELECT, UPDATE...)
     * @param  array  $attributes 
     * @param  boolean $one        si TRUE, plusieurs résultats
     * @param  [type]  $class_name [description]
     * @return [type]              [description]
     */
    public function query(string $statement, ?array $attributes = NULL, bool $one = FALSE, bool $class_name=FALSE){
        // Toujours mettre $class à true pour construire des entités en rapport
        if($class_name || $this->entity !== null)
        { 
            if($class_name && $this->entity == null)  
            {
                $class_name = str_replace('Table', 'Entity', get_class($this));
            } else {
                $name = "{$this->entity}Entity";
                $class_name = str_replace((new \ReflectionClass($this))->getShortName(), $name, get_class($this));
                
            }

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

     

    

}