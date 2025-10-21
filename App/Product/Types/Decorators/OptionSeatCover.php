<?php
declare(strict_types=1);
namespace App\Product\Types\Decorators;

use App\Product\Types\Decorators\Decorator;


final class OptionSeatCover extends Decorator  {

	public function seatCover(){
		return <<<EOT
			<div class="bloc-header" data-i18n="seat-cover">Housse de selle</div>
			<div>
				<p><span data-i18n="seat-cover-special-kit">Complète ton kit déco avec la housse de selle spécialement crée pour la gamme</span> xxx </p>
			</div>
		EOT;
	}

	public function widgetMiniPlates(){
		return $this->component->widgetMiniPlates();
	}

	public function widgetWheelHubsStickers(){
		return $this->component->widgetWheelHubsStickers();
	}

	public function options(){
		return $this->component->options();
	}

	public function popinOptions()
	{
		return $this->component->popinOptions();
	}

	public function miniPlatesInfo(){
		return $this->component->miniPlatesInfo();
	}

	public function wheelHubsInfo(){
		return $this->component->wheelHubsInfo();
	}


}