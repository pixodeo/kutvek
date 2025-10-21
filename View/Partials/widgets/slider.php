<div class="slides">
    <div class="action left click" data-ctrl="slider.slide" data-side="left"><span class="material-symbols-rounded ">&#xe2ea;</span></div>
    <div class="slider">
        <div class="container" data-translate="0" data-cards="<?= count($products) ?>" data-lcards="0">
            <?php foreach ($products as $product) : ?>
                <a class="card" href="<?= $product->url ?>">
                    <figure>
                        <?php if (isset($product->visual)) : ?>
                            <img class="visual" src="<?= $product->visual; ?>" alt="" />
                        <?php else : ?>
                            <img class="visual" src="/img/blank.png" alt=""/>
                        <?php endif; ?>
                        <hr>
                        <figcaption>
                            <p class="item designation"><?= $product->designation; ?></p>
                            <p class="prices">
                                <span class="price block"><?= $product->price_0; ?></span>
                            </p>
                        </figcaption>
                    </figure>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="action right click" data-ctrl="slider.slide" data-side="right"><span class="material-symbols-rounded ">&#xe5e1;</span></div>
</div>