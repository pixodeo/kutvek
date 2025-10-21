<div class="graphic-kit">
    <a href="<?= $this->uri('products.section', ['queries' => ['slug' => $kit->slug]]) ?>">
        <img src="<?= $kit->cover; ?>" alt="">
        <p class="title"><?= $kit->designation; ?></p>
    </a>
</div>