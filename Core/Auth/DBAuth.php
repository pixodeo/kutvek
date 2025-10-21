<?php

namespace Core\Auth;

use Core\Database\Database;

class DBAuth extends Auth{

    private $db;
    
    public function __construct(Database $db){
        $this->db = $db;
    }    

    public function getUserId(){
        if($this->logged()){
            return $_SESSION['auth'];
        }
        return false;
    }

    /**
     * @param $login
     * @param $password
     * @return boolean
     */
    public function login($login, $password){

        $user = $this->db->prepare("SELECT id,  pwd, email, CONCAT(lastname, ' ', firstname) AS fullname FROM user WHERE email = ?", [$login], null, true);
       
        if($user){
            
            if($user->pwd === sha1($password)){
                if(isset($_SESSION['admin']))               
                    unset($_SESSION['admin']);
                    $_SESSION['auth'] = $user;
                    return true;
            } 
        }
        return false;
    }

    public function adminLogin($username, $password)
    {
        $user = $this->db->prepare("SELECT * FROM administrateur WHERE username = ?", [$username], null, true);
        if($user){
            if($user->mdp === sha1($password)){
                unset($_SESSION['auth']);
                $_SESSION['admin'] = $user->id;
                return $user->id;
            }
        }
        return false;
    }    
    
    public function logout(){
        unset($_SESSION['auth']);
        header('Location: /');
        exit();
    }
}