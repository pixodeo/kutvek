<?php
namespace Core\Auth;
use Core\Database\Database;

class Acl {

    private $db;
    private $user_empty = false;
    
    public function __construct(Database $db){
        $this->db = $db;
    }

    


   public function check($permission,$userid,$group_id) {

     //we check the user permissions first
     if(!$this->user_permissions($permission,$userid)) {
        return false;
     }

     if(!$this->group_permissions($permission,$group_id) & $this->IsUserEmpty()) {
        return false;
     }

     return true;

   }

    public function user_permissions($permission,$userid) {
        $this->db->q("SELECT COUNT(*) AS count FROM user_permissions WHERE permission_name='$permission' AND userid='$userid' ");

        $f = $this->db->f();

        if($f['count']>0) {
            $this->db->q("SELECT * FROM user_permissions WHERE permission_name='$permission' AND userid='$userid' ");
            $f = $this->db->f();

            if($f['permission_type']==0) {
                return false;
            }   
            return true;
        }
        $this->setUserEmpty('true');
        return true;     
    }

    public function group_permissions($permission,$group_id) {
        $this->db->q("SELECT COUNT(*) AS count FROM group_permissions WHERE permission_name='$permission' AND group_id='$group_id' ");

        $f = $this->db->f();

        if($f['count']>0) {
            $this->db->q("SELECT * FROM group_permissions WHERE permission_name='$permission' AND group_id='$group_id' ");

            $f = $this->db->f();

            if($f['permission_type']==0) {
                return false;
            }
   
            return true;

        }
        return true;     
   }


    public function setUserEmpty($val) {
     $this->userEmpty = $val;
    }

    public function isUserEmpty() {
        return $this->userEmpty;
    }
}