<?php
$nodes = [];
$func = function ($node) use (&$nodes) {
    $r = '';
    for ($i = 0; $i < count($nodes); $i++) {
        // ne pas se tromper de parent
        if ($nodes[$i]->node_left <  $node->node_left  && ($node->node_right <  $nodes[$i]->node_right && $node->node_right > $nodes[$i]->node_left)) {
            $nodes[$i]->leafs = $nodes[$i]->leafs - 1;
            if ($nodes[$i]->leafs == 0 && $nodes[$i]->node_left <  $node->node_left  && ($node->node_right <  $nodes[$i]->node_right && $node->node_right > $nodes[$i]->node_left)) {
                $r .= '</ul></li>';
            }
        }
    }
    return $r;
};
?>
<footer class="main-footer">
    <div class="footer-top">
        <?php foreach ($infos as $info): ?>
            <div class="info-card n">
                <a href="<?= $info->link ?>">
                    <img src="<?= $info->icon; ?>" alt="">
                    <div>
                        <p class="title"><?= $info->designation; ?></p>
                        <p><?= $info->body; ?></p>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="col-s-12 col-m-9 col-m-center">
        <div class="row top">
            <div id="newletter" class="col-s-12 col-l-4 bloc">
                <p class="h5" data-i18n="suscribe-newsletter">REÇOIS NOS OFFRES SPÉCIALES, ACTUALITÉS ET BIEN PLUS ...</p>
                <form action="#" method="POST">
                    <input type="email" name="email" id="email" data-i18n="give-email" placeholder="Entre ton adresse e-mail">
                    <button type="submit" disabled>OK!</button>
                </form>
            </div>
            <div class="col-s-12 col-l-4 bloc"><?= $this->socialsMedias(); ?></div>
            <div class="col-s-12 col-l-4 bloc">
                <p class="h5" data-i18n="listen-you">NOUS SOMMES À VOTRE ÉCOUTE</p>
                <p data-i18n="open-hours">Une équipe vous répond du lundi au vendredi de 9h00 à 18h00</p>
                <div class="flex contact">
                    <img src="/img/pictos/phone.png" />
                    <a class="phone" href="tel:<?=$this->intlphone;?>"><?=$this->phone;?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="red-line"></div>
    <div class="col-s-12 col-m-9 col-m-center">
        <nav id="main-links" style="color:white;">
            <ul class="row">
                <?php foreach ($items as $node):
                    $depth = (int)$node->depth;
                    if ($depth > 0) $uri = $node->external_link ?? $this->uri('products.section', ['queries' => ['slug' => $node->slug]]);                    
                ?>
                    <?php if ($node->leafs > 0) : $nodes[] = clone ($node); ?>
                        <li class="col-s-12 col-m-3">
                        <h5><?= $node->name; ?></h5>
                        <ul>
                    <?php else: ?>
                        <li>
                            <?php if ($node->obfuscated && $node->active) : ?>
                                <span class="obflink obf" data-obf="<?= base64_encode($node->slug) ?>">
                                    <?= $node->name; ?>
                                </span>
                            <?php elseif (!$node->active): ?>
                                <span><?= $node->name; ?></span>
                            <?php else : ?>
                                <a href="<?= $uri; ?>" data-slug="<?= $node->slug; ?>"><?= $node->name; ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                    <?= $func($node); ?>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
    <p class="logo"><img src="/img/charter/logo-footer.png" alt="Kutvek Logo" /></p>
</footer>