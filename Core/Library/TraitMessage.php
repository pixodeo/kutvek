<?php
namespace Core\Library;
trait TraitMessage {

	public function setFlash(string $message, ?string $type) : void {
        $type = isset($type) ? $type : 'success';
        $flash = '<div class="alert alert-$type">'.$message.'</div>';        
        $_SESSION['flash'] = $flash;
    }

	public function hasFlashes() {
        return isset($_SESSION['flash']);
    }

	public function getFlashes($key = null) {

        if($this->hasFlashes()) {            
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
	}
}

