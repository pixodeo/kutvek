<div class="mobile-msg">
    <?php 
        $msgs = $this->flashMsg();
        foreach($msgs as $msg):
    ?>
    <div class="msg"><?=$msg->body;?></div>
    <?php endforeach; ?>
</div>
<header class="primary-header">
    <div class="message-bar">
        <div class="desktop-msg">
        <?php 
            $msgs = $this->flashMsg();
            foreach($msgs as $msg):
        ?>
        <div class="msg"><?=$msg->body;?></div>
        <?php endforeach; ?>
        </div>
        <a href="https://www.youtube.com/user/kutvekkitgraphik" target="_blank"><img src="/img/pictos/youtube.png"></a>
        <a href="https://www.facebook.com/Kutvek" target="_blank"><img src="/img/pictos/facebook.png"></a>
        <a href="https://www.instagram.com/kutvek" target="_blank"><img src="/img/pictos/instagram.png"></a>
    </div>
    <div class="infos">
        <div>
            <div class="trust"><?= $this->widgetTrustpilot(); ?></div>
            <div class="action contact-us click" data-ctrl="app.contact">
                <img src="/img/pictos/phone.png" class="picto phone" alt="">
                <a href="tel:<?= $this->intlphone;?>"><span class="phone"><?=$this->phone; ?></span></a>
            </div>
        </div>
        <div>
            <span data-i18n="lang" class="choose-lang">Langue</span>
            <div class="action trads-container">
                <?= $this->widgetI18ns($slugs); ?>
            </div>            
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
                <?php $obf = $this->uri('orders.cart') ?>
                <span id="cart-btn" class="cart action click" data-ctrl="cart.overview" data-obf="<?= base64_encode($obf) ?>">
                    <span class="icon material-symbols-rounded">&#xe8cc;</span>
                    <span class="counter"><span id="nbItems"></span></span>
                </span>
                <a href="#" class="menu click" data-ctrl="app.menu" data-target="main-nav"><span class="icon material-symbols-rounded">&#xe5d2;</span></a>
            </div>
        </div>
    </div>
    <?= $this->mainMenu(); ?>
</header>