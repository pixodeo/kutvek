<?php 
class Cropper {
 public function visual($file,  $size, $ext, $dest, $density = 1, $snowmobile = 0) {  
 		try{
 			$quality = 100;
        if($snowmobile > 0){
            $original = IMG_SNOW . DS . 'ranges' . DS . $file . '.' . $ext;
            $output = WEBROOT.DS.$dest;
        } else {
            $original = IMAGES . DS . 'produits' . DS . 'original' . DS . $file . '.' . $ext;
        $output = WEBROOT.DS.$dest;
        }
        
        $img_infos = getimagesize($original);
        //echo '<pre>';
        //print_r($img_infos);        	
        //echo '</pre>';
        //return;
        $width = $img_infos[0]; 
        $height = $img_infos[1];
        $mime = $img_infos['mime'];        
         
        if(file_exists($dest)){
            header("Content-type: {$mime}", true, 200);
            header('Cache-Control: max-age=31536000');
            header_remove('Pragma');
            echo file_get_contents($output);
            exit();  
        }
        switch($size)  
        {
            case 'w36': 
                $_w = 36;              
                break;
            case 'w48': 
                $_w = 48;              
                break;
            case 'w64': 
                $_w = 64;              
                break;
            case 'w72': 
                $_w = 72;              
                break;
            case 'w296': 
                $_w = 296;              
                break;
            case 'w320': 
                $_w = 320;              
                break;
            case 'w120': 
                $_w = 120;              
                break;
            case 'w360': 
                $_w = 360;              
                break;
            case 'w600':
                $_w = 600; 
                break;
            case 'w768':
                $_w = 768; 
                break;
            case 'w800':
                $_w = 800; 
                break;                
            case 'w1024': 
                $_w = 1024;               
                break;
            case 'w1360': 
                $_w = 1360;               
                break;
            default:
                $_w = 360;
                break;
        }        
        $_h = ($_w * $height) / $width;
        $_h = round($_h);

        $density = $density == 2 ? 1.5 : ($density == 3 ? 2 : $density);
        switch($mime) {
            case 'image/gif' :
                $src_img = imagecreatefromgif($original);
                break;
            case 'image/png' :                
                $calc = imagecreatefrompng($original);
                $new_img = imagecreatetruecolor($_w*$density , $_h*$density);
                imagealphablending($new_img, false);
                imagesavealpha($new_img, true);
                imagecopyresampled($new_img , $calc, 0, 0, 0, 0, $_w*$density , $_h*$density, $width , $height); 
                imagedestroy($calc);
                // On entregistre la miniature
                $result = imagepng($new_img , $output);
                if(!$result){
                    throw new \Exception('Nothing to do  categories' . $mime . 'original : ' . $original);
                }
                break;        
            case 'image/jpeg' :
                //$src_img = imagecreatefromjpeg($original);
                $calc = imagecreatefromjpeg($original);
                $new_img = imagecreatetruecolor($_w*$density , $_h*$density);
                imagecopyresampled($new_img , $calc, 0, 0, 0, 0, $_w*$density , $_h*$density, $width , $height); 
                imagedestroy($calc);
                // On entregistre la miniature
                imagejpeg($new_img , $output, $quality);
                break;
            case 'image/webp' :
                //$src_img = imagecreatefromwebp($original);
                // fond
                $new_img = imagecreatetruecolor($_w*$density , $_h*$density);
                imagealphablending($new_img, false);

                $tr = imagecolorallocatealpha($new_img, 0, 0, 0, 255);

                imagefill($new_img, 0, 0, $tr);

                imagesavealpha($new_img, true);

                $calc = imagecreatefromwebp($original);
                //imagepalettetotruecolor($calc);
                imagealphablending($calc, false);
                imagesavealpha($calc, true);
                
                
                imagecopyresampled($new_img , $calc, 0, 0, 0, 0, $_w*$density , $_h*$density, $width , $height); 
                imagedestroy($calc);
                // On entregistre la miniature               
                imagewebp($new_img , $output, $quality);
                break;
            default:                
                throw new \Exception('Nothing to do  categories' . $mime);
                break;
        }
        header("Content-type: {$mime}");
        header('Cache-Control: max-age=31536000');
        header_remove('Pragma');
        echo file_get_contents($output);
        exit(); 

 		}catch(\Exception $e)  {
 			die($e);

 		}    
        

    } 

}
$cropper = new Cropper();
$snow = !empty($_GET['snowmobile']) ? 1 : 0;
$cropper->visual($_GET['file'],  $_GET['size'], $_GET['ext'], $_GET['dest'], $_GET['density'], $snow);