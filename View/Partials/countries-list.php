<div class="country-search">
    <span class="icon material-symbols-rounded">&#xe8b6;</span>
    <input type="text" id="country-input" class="onkeyup" data-ctrl="locale.countrySearch" data-uri="<?= DOMAIN . $this->uri('locale.countrySearch', [], 'GET'); ?>" autofocus="autofocus">
</div>
<ul class="country-currency" id="country-list" data-url="<?= $this->uri('customers.countryCurrency', [], 'POST'); ?>">
    <?php foreach ($countries as $country) : ?>
        <?php if ($country->country_iso === $currentCountry->country_iso) : ?>
            <li class="click selected" data-country="<?= $country->country_iso ?>" data-currency="<?= $country->currency_lib ?>" data-ctrl="locale.countryCurrency">
                <img src="<?= $country->flag; ?>" alt="">
                <span class="name"><?=$country->name;?></span>
                <span>(<?= $country->currency_lib . ' ' . $country->currency_symbol; ?>)</span>
            </li>
        <?php else : ?>
            <li class="click" data-country="<?= $country->country_iso ?>" data-currency="<?= $country->currency_lib ?>" data-ctrl="locale.countryCurrency">
                <img src="<?= $country->flag; ?>" alt="">
                <span class="name"class="name"><?= $country->name; ?></span>
                <span>(<?= $country->currency_lib . ' ' . $country->currency_symbol; ?>)</span>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>