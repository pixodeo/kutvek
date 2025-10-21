<header class="primary-header">
    <div class="message-bar">        
        <a href="https://www.youtube.com/user/kutvekkitgraphik" target="_blank"><img src="/img/pictos/youtube.png"></a>
        <a href="https://www.facebook.com/Kutvek" target="_blank"><img src="/img/pictos/facebook.png"></a>
        <a href="https://www.instagram.com/kutvek" target="_blank"><img src="/img/pictos/instagram.png"></a>
    </div>
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
                <span data-obf="<?= base64_encode($this->uri('identities.login')) ?>" class="action user-account">
                    <span class="click obf" id="user-name" data-i18n="login" data-obf="<?= base64_encode($this->uri('customers.dashboard')) ?>" data-ctrl="user.dashboard"><span class="icon material-symbols-rounded account-circle">&#xe853;</span></span>
                </span>
                <div class="action contact-us">
                    <label class="icon material-symbols-rounded" for="see-phone"></label>
                    <input type="checkbox" id="see-phone" hidden />
                    <a class="phone" href="tel:<?= $this->intlphone;?>"><span ><?=$this->phone; ?></span></a>
                </div>

                <div class="action trads-container">
                    <?= $this->widgetI18ns($slugs); ?>
                </div>
          
                
                <a href="#" class="menu click" data-ctrl="app.menu" data-target="main-nav"><span class="icon material-symbols-rounded">&#xe5d2;</span></a>
            </div>
        </div>
    </div>
    <?= $this->mainMenu(); ?>
</header>