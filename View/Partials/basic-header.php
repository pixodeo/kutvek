<header class="primary-header">    
    <div class="infos">
        <div class="action contact-us click" data-ctrl="app.contact">
            <img src="/img/pictos/phone.png" class="picto phone" alt="">
            <a href="tel:<?= $this->intlphone;?>"><span class="phone"><?=$this->phone; ?></span></a>
        </div>
        <div class="trust">
            <?= $this->widgetTrustpilot(); ?>
        </div>
        <div class="action trads-container">
            <?= $this->widgetI18ns($slugs); ?>
        </div>
    </div>
    <div class="baseline">
        <a href="<?= $this->uri('pages.index', []) ?>" class="logo"><img class="logo-kutvek" src="<?= HALLOWEEN === 1 ? '/img/charter/logo_kutvek_orange.png' : '/img/charter/logo_kutvek.png';?>" alt="Logo KUTVEK"></a>
        <div class="red">
            <img class="logo-footer" src="/img/charter/logo-footer.png" />
            <?= $this->menuTop(); ?>
            <div class="user-actions">
                
                <div class="action contact-us">
                    <label class="icon material-symbols-rounded" for="see-phone"></label>
                    <input type="checkbox" id="see-phone" hidden />
                    <a class="phone" href="tel:<?= $this->intlphone;?>"><span ><?=$this->phone; ?></span></a>
                </div>

                <div class="action trads-container">
                    <?= $this->widgetI18ns($slugs); ?>
                </div>               
            </div>
        </div>
    </div>
</header>