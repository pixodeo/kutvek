<?php
declare(strict_types=1);
namespace Component;
use Core\Component;
use Domain\Table\Catalog;
use Library\HTML\TraitSanitize;

final class Footer extends Component {
	use TraitSanitize;
	public function __invoke(){

		$table = new Catalog($this->_setDB());
		$table->setRoute($this->_route);
		$sql = "SELECT f.id, f.name,  f_l10n.designation , f_l10n.body , f.area
		FROM website_footers f
		JOIN footer_l10ns f_l10n ON (f_l10n.footer = f.id AND f_l10n.l10n = :l10n)
		WHERE f.workspace = 2
		AND f.website = 5 
		AND f.depth = 0 
		AND f.position >= 0 
		ORDER BY f.position, f.node_left;";
		$items = $table->query($sql, ['l10n' => $this->getL10nId()]);
		$this->_view = 'partials.footer-1';
		return $this->partial(compact('items'));
	}
}