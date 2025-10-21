<?php
declare(strict_types=1);
namespace Domain\Entity;
use Core\Domain\Entity;

class File extends Entity {
	public int $file_id;
	public $url; 
    public $type;
    public $product; 
  	public $id; 
    public $associate;
    public $cover;
    public $position;
    public $css;
    public $icon = 'link_off';
    public $icon_class = 'off';
    public $icon_cover = 'visibility_off';
    public $icon_cover_class = 'off'; 
    public $ext;
    public $w64;
    public $w360;
    public $w800;  

    public function __construct() {
        if($this->associate !== null) {
         $this->css = "associate";
         $this->icon = "link";
         $this->icon_class = "on";
        }

        if($this->cover != null) {
            $this->icon_cover = 'visibility';
            $this->icon_cover_class = 'on';
        }

        $dot = strrpos($this->url, '.');
        $filename = substr($this->url, 0, $dot);
        $this->ext = substr($this->url, $dot);
        $this->w48 =  "{$filename}_w48{$this->ext}";
        $this->w48d2x =  "{$filename}_w48d2x{$this->ext}";        
        $this->w64 =   "{$filename}_w64{$this->ext}";
        $this->w64d2x = "{$filename}_w64d2x{$this->ext}";
        $this->w72 =  "{$filename}_w72{$this->ext}";
        $this->w72d2x =  "{$filename}_w72d2x{$this->ext}";
        $this->w96 =  "{$filename}_w96{$this->ext}";
        $this->w96d2x =  "{$filename}_w96d2x{$this->ext}";
        $this->w296 =  "{$filename}_w296{$this->ext}";
        $this->w296d2x =  "{$filename}_w296d2x{$this->ext}";       
        $this->w120 =  "{$filename}_w120{$this->ext}";
        $this->w120d2x =  "{$filename}_w120d2x{$this->ext}";       
        $this->w360 =   "{$filename}_w360{$this->ext}";
        $this->w360d2x =  "{$filename}_w360d2x{$this->ext}";
        $this->w560 =  "{$filename}_w560{$this->ext}";
        $this->w560d2x =  "{$filename}_w560d2x{$this->ext}";
        $this->w800 =   "{$filename}_w800{$this->ext}";    
    }
}