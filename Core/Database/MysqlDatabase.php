<?php
namespace Core\Database;
use \PDO;
class MysqlDatabase extends Database{

    private $db_username;
    private $db_password;
    private $db_dsn;
    private $pdo;
    private $charset = 'utf8mb4';
    protected $_fetchMode = false;

    public function __construct($db_name, $db_username, $db_password, $db_host, $db_port, $charset){
        if(null !==  $charset) $this->charset = $charset;         
        $this->db_dsn  = "mysql:dbname={$db_name};host={$db_host};port={$db_port};charset={$this->charset}";
        $this->db_username = $db_username;
        $this->db_password = $db_password;
    }

    private function _getPDO(){
        try {
            if($this->pdo === null){
            // $pdo = new PDO($dsn, $username, $password, array $options);
            $pdo = new PDO(
                $this->db_dsn,
                $this->db_username,
                $this->db_password,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET lc_time_names = \'fr_FR\''));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo = $pdo;
        }        
        return $this->pdo;

        } catch(\Exception $e) {
            return false;
        }
        if($this->pdo === null){
            // $pdo = new PDO($dsn, $username, $password, array $options);
            $pdo = new PDO(
                $this->db_dsn,
                $this->db_username,
                $this->db_password,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET lc_time_names = \'fr_FR\''));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo = $pdo;
        }        
        return $this->pdo;
    }

    public function query(string $statement, ?string $class_name, bool $one){
        if(!$this->_getPDO()) return [];
        $req = $this->_getPDO()->query($statement);
         
        if(strpos($statement, 'UPDATE') === 0 || strpos($statement, 'INSERT') === 0 ||  strpos($statement, 'DELETE') === 0) {return $req;}

        if($this->_fetchMode) {$req->setFetchMode($this->_fetchMode);}
        else {
            if($class_name === null){
            $req->setFetchMode(PDO::FETCH_OBJ);
            } else {
                $req->setFetchMode(PDO::FETCH_CLASS, $class_name,$this->_constructorArgs);
            }
        }

        if(!$one) {
            $datas = $req->fetchAll();
        } else {
            $datas = $req->fetch();
            
        }
        return $datas;
    }
    
    public function prepare(string $statement, array $attributes,  ?string $class_name, bool $one) {        
        if(!$this->_getPDO()) return [];
        $req = $this->_getPDO()->prepare($statement);
       //var_dump($statement);
        //var_dump($attributes);
        $res = $req->execute($attributes);
        if(
            strpos($statement, 'UPDATE') === 0 ||
            strpos($statement, 'INSERT') === 0 ||
            strpos($statement, 'DELETE') === 0
        ) {
            return $res;
        }
        
        if($this->_fetchMode) {$req->setFetchMode($this->_fetchMode);}
        else {
            if($class_name === null){
            $req->setFetchMode(PDO::FETCH_OBJ);
            } else {
                if($this->_constructorArgs !== null):
                    $req->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $class_name, $this->_constructorArgs);
                else:
                    $req->setFetchMode(PDO::FETCH_CLASS, $class_name);
                endif;
            }
        }   
        if(!$one) {
            $datas = $req->fetchAll();
        } else {
            $datas = $req->fetch();            
        }
        return $datas;
    }

    public function _prepare(string $statement) {
        if(!$this->_getPDO()) return [];
        return $this->_getPDO()->prepare($statement);
    }

    public function _query(string $statement, int $fetchMode = PDO::FETCH_OBJ) {
        if(!$this->_getPDO()) return [];
        return $this->_getPDO()->query($statement, $fetchMode);
    }

    public function lastInsertId(){
        if(!$this->_getPDO()) return [];
        return $this->_getPDO()->lastInsertId();
    }

    public function emulate_prepares(bool $choice = TRUE) {
        if(!$this->_getPDO()) return [];
        $this->_getPDO()->setAttribute(PDO::ATTR_EMULATE_PREPARES, $choice);
    } 

    public function beginTransaction() {
        if(!$this->_getPDO()) return [];
        $this->_getPDO()->beginTransaction();
    }

    public function commit() {
        if(!$this->_getPDO()) return [];
        $this->_getPDO()->commit();
    }
    
    public function rollback() {
        if(!$this->_getPDO()) return [];
        $this->_getPDO()->rollback();
    }

    public function setFetchMode(bool|int $fetchMode)
    {
        $this->_fetchMode = $fetchMode;
    }


}