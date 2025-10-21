<?php if($_GET['debug']) $class = '';else $class = 'hide'; ?>
<div class="preferences-selector">    
    <span data-i18n="country">Pays</span>
<div class="click  " data-ctrl="app.modal" data-modal="countries" data-fetch="<?= $this->uri('locale.countriesCurrenciesList', [], 'GET'); ?>">    
    <img src="<?= $currentCountry->flag; ?>">
    <span class="currency"><?= $currentCurrency; ?></span>
</div>
</div>
<pre><?php print_r($this->cookie)?></pre>
<pre><?php print_r($currentCountry); ?></pre>
<pre><?php print_r($countries); ?></pre>