<?php
namespace Library;

use Exception;
use Core\Http\Message\UploadedFile;

trait TraitFiles {
    public function moveUploadedFile(string $directory, UploadedFile $uploadedFile, bool|string $basename = false )
    {        
        try {
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);            
            if(!is_dir($directory)){
                mkdir($directory, 0755, true);
                chmod($directory, 0755);
            }
            if(!$basename) $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
            $filename = sprintf('%s.%0.8s', $basename, $extension);
            $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);   
            return $filename;        
        } catch(Exception $e){
            return $e->getMessage();
        }               
    }
}