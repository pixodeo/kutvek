<div class="row infos">
    <div class="col-s-12 col-m-9 col-m-center">
        <div class="row kk-infos">
            <?php foreach($items as $item): $item = (object)$item; ?>
                <a href="<?= $this->uri(
                    'products.section', 
                    ['queries' => ['slug' => $item->link]]); 
                ?>" class="info-card">
                    <img class="picto" src="<?= $item->icon; ?>">
                    <div class="body">
                        <h5 class="title"><?= $item->designation ?></h5>
                        <p><?= $item->body; ?></p>
                    </div>
                </a>    
            <?php endforeach; ?>
        </div>
    </div>
</div>