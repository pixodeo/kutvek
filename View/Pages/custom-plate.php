<?php
use App\Component\Product\Behavior\CustomPlateBehavior;

$this->_behavior = new CustomPlateBehavior();
$this->_behavior->setI18n($this->getI18n());
$this->_behavior->setCurrency($this->app->getCurrency());
$this->_behavior->setCountry($this->app->getCountry());
$this->_behavior->loadTable();
$this->_behavior->setRouter($this->_router);
$content = $this->_behavior->content();
$options = $this->_behavior->options();
?>

<div class="col-s-12 col-l-6 col-l-offset-2 ">
    <h2><span class="warning">1.</span> <span data-i18n="your-vehicle">Infos sur ton véhicule</span></h2>
    <hr>
    <?= $this->_behavior->widgetFamilies(); ?>
    <?= $this->_behavior->widgetBrands(); ?>
    <?= $this->_behavior->widgetVehicles(); ?>
    <?= $this->_behavior->widgetMillesims(); ?>
    <div id="mx">
        <?=$this->_behavior->widgetKitMx();?>
    </div>
    <div id="quad" class="hide">
        <div id="plates-options">
            <div class="col-s-12 bloc" id="accessories">           
                <?= $this->_behavior->widgetPlatesSelect(); ?>
                    <div data-i18n="endurance-plate">
                        <h3>Plaques endurance</h3>
                        <p>Support PHD disponible pour les plaques Endurance</p>
                    </div>
                <?= $this->_behavior->widgetEndurancePlate(); ?>                   
            </div>
        </div>
    </div>
    <h2><span class="warning">2.</span> <span data-i18n="what-you-wish">Dis nous ce que tu souhaites</span></h2>
    <hr>
    <div class="bloc-infos">
        <p data-i18n="describe-what-you-wish">Donne-nous les informations qui vont nous permettre de créer le kit déco qui s'accorde le mieux à ton véhicule.</p>
    </div>
    <div class="field-wrapper column">
        <label class="required" for="custom-graphics-comment">Fais nous un court descriptif de ce que tu souhaites, comme les couleurs, le design...</label>
        <textarea name="item[comment]" data-i18n="custom-graphics-comment" id="custom-graphics-comment" class="field-input textarea" required form="addToCart"></textarea>
    </div>

    <div class="bloc-infos">
        <p data-i18n="pictures-vehicle">Tu peux nous envoyer une/des photo(s) de ton véhicule, cela nous permettra de voir sa couleur d'origine, et les carrénages.</p>
    </div>
    <div class="row">
        <div class="col-s-12 col-l-6 col-l-center">
            <div class="drop-zone" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" data-preview="vehicle-preview">
                <input type="file" id="vehicle-drop" class="onchange hide" data-ctrl="app.appendFile" data-preview="vehicle-preview" form="addToCart">
                <span><i data-i18n="drop-pictures-vehicle">Glisse les photos de ton véhicule</i> <label class="btn contained dark" for="vehicle-drop" data-i18n="click-here">Ou clique ici</label></span>
            </div>
        </div>
        <div class="col-s-12 wrapper-preview ">
            <div id="vehicle-preview" class="preview vehicle-preview"></div>
        </div>
    </div>

    <h2><span class="warning">3.</span> <span data-i18n="customize-kit">Nom, numéro de course et sponsors</span></h2>
    <hr>
    <ul class="tabs no-print">
        <li data-input="plate" class="active"><a href="#opt-plate-number" data-i18n="name-plus-number">Nom + numéro</a></li>
        <li data-input="plate-sponsor"><a href="#opt-sponsors">Sponsors</a></li>
    </ul>
    <div class="tabs_content">
        <div class="tab_content active" id="opt-plate-number" data-input="plate">
            <div class="bloc-infos">
                <p data-i18n="give-us-name-number">Nom et numéro de course (facultatif)</p>
            </div>
            <div class="row">
                <div class="col-s-12 col-l-6"><?= $this->_behavior->widgetRaceName(); ?></div>
                <div class="col-s-12 col-l-6"><?= $this->_behavior->widgetTypoRaceName(); ?></div>
                <div class="col-s-12 col-l-6"><?= $this->_behavior->widgetRaceNumber(); ?></div>
                <div class="col-s-12 col-l-6"><?= $this->_behavior->widgetTypoRaceNumber(); ?></div>
               
                <div class="col-s-12 col-l-6 col-l-push-6">
                    <?= $this->_behavior->widgetPlateColor(); ?>
                </div>

                <div class="col-s-12 col-l-6 col-l-push-6">
                    <?= $this->_behavior->widgetRaceNumberColor(); ?>
                </div>
                 <div class="col-s-12">
                    <hr>
                </div>
                <div class="col-s-12 col-l-9 col-l-pull-3"><?= $this->_behavior->widgetCFS(); ?></div>
                <div class="col-s-12 col-l-6 col-l-pull-6"><?= $this->_behavior->widgetRaceLogo(); ?>
                </div>
                
            </div>
        </div>
        <div class="tab_content" id="opt-sponsors" data-input="plate-sponsor">
            <div class="bloc-infos">
                <p data-i18n="give-us-sponsors">Si tu souhaites ajouter des sponsors sur le kit déco, indique-nous leur nom ou envoie un fichier à l'emplacement souhaité.
                    <small>(facultatif)</small>
                </p>
            </div>
            <div class="txt-center" id="template-sponsors"><img src="/img/sponsors/gabarits-plaques.png" /></div>

            <p class="info-plastics">
                <span class="icon material-symbols-rounded red">error</span>
                <span data-i18n="infos-logos">Pour l'envoi de vos logos personnels, préférez un format vectoriel (.ai ou .eps) pour un meilleur rendu à l'impression. Nos graphistes ajusteront les emplacements selon votre véhicule pour un rendu optimal</span>
            </p>
            <div class="grid-sponsors">
                <?php for ($i = 1; $i <= 4; $i++) : ?>
                    <p class="sponsor col-s-12 col-l-4">
                        <span class="place"><?= $i; ?></span>
                        <input class="field-input text" type="text" name="opts[sponsor][<?= $i; ?>]" data-i18n="sponsor-placeholder" placeholder="Nom du sponsor" />
                        <input class="file onchange" type="file" id="sp-<?= $i; ?>" data-place="<?= $i; ?>" data-ctrl="customPlate.uploadSponsor" />
                        <label for="sp-<?= $i; ?>">
                            <span class="icon material-symbols-rounded">download</span>
                        </label>
                        <span class="fileName"></span>
                    </p>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <h2><span class="warning">4.</span> <span data-i18n="choose-finish">Choisis une finition</span></h2>
    <hr>
    <div class="finish-desc" data-i18n="choose-finish-desc">
        <p>Choisis la finition qui protégera et sublimera ton kit de déco.</p><p>Si tu le souhaites, un effet premium comme le chrome et l'holographique est possible.</p>
        <p>Les couleurs peuvent varier en fonction de la luminosité avec un effet premium.</p>
        <p>Nous déconseillons une finition en mat pour les impressions chromées ou métalliques.</p><p><i class="material-symbols-rounded warning">&#xe88e;</i><a href="#effects-finish" class="click link" data-ctrl="app.modal" data-modal="effects-finish">Clique ici pour voir des exemples d'images.</a><p></div>
        <?= $this->_behavior->widgetPremium(); ?>
        <?= $this->_behavior->widgetFinish(); ?>     
</div>
<div class=" col-s-12 col-l-3 col-l-offset-1">
    <aside class="p-cart" id="p-cart">
        <header>
            <h4 class="p-cart-title">TOTAL</h4>
        </header>
        <div class="items">
            <p id="p-0">
                <span class="designation"></span>
                <span class="btns-group item-qty">
                    <button class="btn square click" data-ctrl="customPlate.decrease">-</button>
                    <input type="text" id="qty" class="btn square" value="1" name="item[qty]" form="addToCart" />
                    <button class="btn square click" data-ctrl="customPlate.increase">+</button>
                </span>

            </p>
            <div id="opts" class="options">
                <p class="type-opt" data-opt="STANDARD" data-id="2"></p>
                <p class="finish-opt" data-opt="Fini Brillant" data-id="4"></p>
                <p class="premium-opt" data-opt="premium" data-id="10" data-checked="0"></p>
                <p class="plate-sponsors" data-opt="" data-checked="0"></p>
                <p class="custom" data-opt="custom" data-checked="1" data-name="100% perso"><?= $content->text; ?></p>
            </div>
            <div id="selected-accessories">
            </div>
        </div>
        <footer>
            <hr>
            <p id="item-total" data-currency="<?= $this->getCurrency(); ?>" data-l10n="<?= $this->getL10n(); ?>" data-price="<?= $content->price; ?>"><?= $content->pricef; ?></p>
            <form action="<?= $this->uri('orders.addItem', [], 'POST'); ?>" id="addToCart" data-ctrl="customPlate.pushToCart" method="POST">
                <input type="hidden" name="item[product]" value="349525" />
                <input type="hidden" name="item[product_url]" form="addToCart" value="https://<?= $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"] ?>" />
                <input type="hidden" name="item[price][product]" id="price" value="<?= $content->price; ?>" />
                <input type="hidden" name="item[price][opts]" id="price-opts" value="0" />
                <input type="hidden" name="item[price][accessories]" id="price-accessories" value="0" />
                <input type="hidden" name="behavior" value="CustomPlateBehavior" />
                <input type="hidden" name="item[category]" value="62" />
                <input type="hidden" id="kit-type" value="<?= $content->price; ?>" />
                <input type="hidden" name="item[description]" id="description" value="Kit plaque 100% perso" />
                <input type="hidden" id="weight" name="item[weight]" value="500" />
                <input type="hidden" name="item[currency][designation]" id="currency" value="<?= $this->getCurrency(); ?>" />
                <input type="hidden" name="item[currency][id]" value="<?= $this->getCurrencyId(); ?>" />
                <input type="hidden" name="item[tax_included]" value="<?= $this->getCurrencyId(); ?>" />
                <input type="hidden" name="item[type]" value="7" />
                <button class="btn contained dark addToCart" type="submit" class="btn contained dark" id="add-to-cart">
                    <span class="text" data-i18n="add-to-cart">Ajouter au panier</span>
                    <span class="icon material-symbols-rounded load hidden">progress_activity</span>
                </button>
            </form>
        </footer>
    </aside>
</div>
<aside class="modal click" id="effects-finish" data-modal="effects-finish" data-ctrl="app.modal">
    <div class="popup">
        <header class="close">
            <p class="title" data-i18n="effect-finish-desc"></p>
            <a href="#effects-finish" class="click" data-modal="effects-finish" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
        </header>
        <div>
            <img src="/img/effects-and-finish.jpg" alt="Effects and finish info on graphics" />
        </div>
    </div>
</aside>
<pre>
<?php if ($_GET['debug'] == 1) { print_r($content); } ?>
</pre>