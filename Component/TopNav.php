<?php
declare(strict_types=1);
namespace Component;

use Core\Component;
use Domain\Table\Catalog;

final class TopNav extends Component {

	public function __invoke(){

		$table = new Catalog($this->_setDB());
		$table->setRoute($this->_route);
		$sql = "SELECT m_i.name, m_i.slug, m_i.active, m_i.obfuscated
		FROM menus m 
		JOIN menu_items m_i ON (m_i.menu = m.id AND  m_i.menu_top IS NOT NULL AND m_i.l10n = :l10n)
		WHERE m.workspace = 2 
		ORDER BY m.node_left;";
		$links = $table->query($sql, ['l10n' => $this->getL10nId()]);
		$this->_view = 'partials.top-nav';
		return $this->partial(compact('links'));

	}
}