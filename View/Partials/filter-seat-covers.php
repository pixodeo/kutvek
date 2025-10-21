<div class="row filter-js">
    <?php if (count($cards) > 0) : ?>
        <span class="btn contained dark filters" data-i18n="filters">Filtres</span>
        <form class="bloc-widgets-filter filter-form" id="filters-form">
            <div class="widget-filter">
                <div class="title accordion_tabs">
                    <span data-i18n="family">Famille / Univers</span>
                    <input type="checkbox" id="family" class="filter" data-uri="<?= $this->uri('products.filters', ['queries' => ['filter' => 'family', 'slug' => $current_slug, 'depth' => $depth]]); ?>" data-modal="family" />
                    <label for="family" class="pointer"><span class="material-symbols-rounded filters">&#xe5cf;</span></label>
                    <div>
                        <ul></ul>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
