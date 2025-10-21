<nav class="secondary-nav">
    <ul class="flex">
        <?php foreach($items as $node): $uri =  $this->uri('products.section', ['queries' => [ 'slug' => $node->slug]]);?>
            <li>
             <?php if ($node->obfuscated && $node->active) : ?>
            <span class="obflink obf" data-obf="<?= base64_encode($uri) ?>" >
                <?= $node->name; ?>
            </span>
        <?php elseif (!$node->active): ?>
            <span><?= $node->name; ?></span>
        <?php else : ?>
            <a href="<?= $uri; ?>" data-slug="<?= $node->slug; ?>"><?= $node->name; ?></a>
        <?php endif; ?>
         </li>   
        <?php endforeach; ?>
    </ul>
</nav>