<?php 
    function urlWithPage($page) {
        $uri = $_SERVER['REQUEST_URI'];
        if (!strpos($uri, 'page')) {
            return !strpos($uri, '?') ? $uri . "?page=$page" : $uri . "&page=$page";
        }
        return preg_replace('/page=([0-9]+)/i', 'page=' . $page, $uri);
    }
?>
<div class="pagination" data-current="<?= $page; ?>" data-total="<?= $pages; ?>">
<?php if ($page !== 1): ?>        
    <a href="<?= urlWithPage($page - 1) ?>" class="page" data-page="<?= $page - 1 ?>"><span class="icon material-symbols-rounded hover">&#xe2ea;</span></a>
<?php endif; ?>
<div class="pages">
    <?php if ($pages <= 5):
        for ($i = 1; $i <= $pages; $i++): ?>
            <div class="page <?= $page == $i ? 'current' : ''; ?>" >
                <a href="<?= urlWithPage($i) ?>" data-page="<?= $i; ?>"><?= $i; ?></a>
            </div>
        <?php endfor;
    else: 
        if ($page > ($pages - 5)): // Dernières pages ?>
            <div class="page <?= $page == 1 ? 'current' : ''; ?>">
                <a href="<?= urlWithPage(1) ?>" data-page="<?= 1; ?>">1</a>
            </div>
            <div class="page"><span>...</span></div>		
            <?php for ($i = $pages - 4; $i <= $pages; $i++): ?>
                <div class="page <?= $page == $i ? 'current' : ''; ?>" >
                    <a href="<?= urlWithPage($i) ?>" data-page="<?= $i; ?>"><?= $i; ?></a>
                </div>
            <?php endfor;
        else: // Premières pages
            for ($i = $page; $i < $page + 5; $i++): ?>
                <div class="page <?= $page == $i ? 'current' : ''; ?>" >
                    <a href="<?= urlWithPage($i) ?>" data-page="<?= $i; ?>"><?= $i; ?></a>
                </div>
            <?php endfor; ?>

            <div class="page"><span>...</span></div>

            <div class="page <?= $page == $pages ? 'current' : ''; ?>">
                <a href="<?= urlWithPage($pages) ?>" data-page="<?= $pages; ?>"><?= $pages; ?></a>		
            </div>
        <?php endif;
    endif; ?>
</div>
<?php if ($page != $pages): // Ne pas aller supérieur à max pages ?>  
    <a href="<?= urlWithPage($page + 1) ?>" class="page" data-page="<?= $page + 1 ?>"><span class="icon material-symbols-rounded hover">&#xe5e1;</span></a>
<?php endif; ?>
</div>