<div class="col-s-12 col-m-10 col-l-9 col-m-center col-l-center">
    <h1 class="page-title"><?= $page->title; ?></h1>
    
    <?php if (isset($page->cover)): ?>
        <img class="cover" src="<?= $page->cover ;?>" alt="<?= $page->title ?>">
    <?php endif; ?>
    
    <div class="section-description">
        <!-- <div id="show-more" class="section-content"> -->
            <?= $page->content; ?>
            <!-- <p class="reading">
                <a class="click show-more" href="#show-more" data-ctrl="app.showMore" data-i18n="show-more">Lire la suite</a>
            </p>
            <p class="reading">
                <a href="#show-more" class="click" data-ctrl="app.showLess" data-i18n="show-less">Réduire</a>
            </p>
        </div> -->
    </div>
    <?= $this->widgetCustomKit($page->slug); ?>
    <?= $this->widgetDiscoverGraphicKit($page->slug); ?>
</div>