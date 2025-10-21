<?php

namespace Core\Library;

trait TraitFiles { 

    /**
     * Les tableaux : name, type, tmp_name, error, size
     * @param string $filesNameArray
     * @return  mixed false|array un tableau d'objets 
     */
    public function organizeFiles(string $filesNameArray)
    {
        $organized = [];
        if(!isset($_FILES[$filesNameArray])) return false;
        $files = $_FILES[$filesNameArray];
        foreach($files['name'] as $k => $name)
        {
            $organized[$k] = (object)[
                'name'      => $name,
                'type'      => $files['type'][$k],
                'tmp_name'  => $files['tmp_name'][$k],
                'error'     => $files['error'][$k],
                'size'      => $files['size'][$k]
            ];
        }
        return $organized;
    }  

    public function organizeFile(string $fileName)
    {
        if(!isset($_FILES[$fileName])) return false; 
        
        return (object)$_FILES[$fileName];
        
    }
    
    public function uploadFile($file, $upload_dir)
    {
        //move_uploaded_file()
        if(!empty($file->name) && (int)$file->error === 0 && $file->size > 0)
        {
            // basename() peut empêcher les attaques de système de fichiers;       		
        	$name = basename($this->_sanitize($file->name));
            $destination = $upload_dir.DS.$name;
            // retourner le nom du fichier uniquement    
            if(move_uploaded_file($file->tmp_name, $destination) === true)                            
				return $name;
            throw new \Exception("Failed to move uploaded file.");
        }
    }    

    public function _sanitize($str) {
    	$str = str_replace(' ', '_', $str);
		$translit = array('Á'=>'A','À'=>'A','Â'=>'A','Ä'=>'A','Ã'=>'A','Å'=>'A','Ç'=>'C','É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Í'=>'I','Ï'=>'I','Î'=>'I','Ì'=>'I','Ñ'=>'N','Ó'=>'O','Ò'=>'O','Ô'=>'O','Ö'=>'O','Õ'=>'O','Ú'=>'U','Ù'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','á'=>'a','à'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y');
		$str = strtr($str, $translit);
		return mb_strtolower(preg_replace('#[^a-zA-Z0-9\-\._]#', '_', $str));
	}


    /**
     * Enregistre des fichiers dans un dossier précis /orders/:id/:item
     * @param  array    $files [description]
     * @param  int      $order [description]
     * @param  int|null $item  [description]
     * @return [type]          [description]
     */
    public function saveOrderFiles(int $order, array $files = array(),  ?int $item = null): array
    {
        $items_files = [];

        if($item !== null)
        {
            $upload_dir = ORDERS_DIR.DS.$order.DS.$item; 
            $url = URL_FILES_ORDERS.$order.'/'.$item.'/'; 
        } else {
            $upload_dir = ORDERS_DIR.DS.$order; 
            $url = URL_FILES_ORDERS.$order.'/'; 
        }

        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0755, true);
            chmod($upload_dir, 0755);
        }
        
        foreach($files as $file)
        {           
            // si on a un upload à true
            $upload = $this->uploadFile($file, $upload_dir);
            if($upload)
            {
                $items_files[] = $url.$upload;               
            }            
        }
        return $items_files; 
    }

    /**
     * Enregistre le kbis lors de la création/ modif de compte
     * @param  int    $user [description]
     * @param  object $file [description]
     * @return [type]       [description]
     */
    public function saveKBIS(int $user, object $file)
    {
        $kbis = null;
        $upload_dir = USERS_DIR.DS.$user; 
        $url = URL_FILES_USERS.$user.'/'; 
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0755, true);
            chmod($upload_dir, 0755);
        }
        $upload = $this->uploadFile($file, $upload_dir);
        if($upload)        
            $kbis = $url.$upload; 
        return $kbis;                      
    }
}