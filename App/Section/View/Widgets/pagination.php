<?php 
    $page = $this->getCurrentPage();
    function urlWithPage($page) {
        $uri = $_SERVER['REQUEST_URI'];
        if (!strpos($uri, 'page')) {
            return !strpos($uri, '?') ? $uri . "?page=$page" : $uri . "&page=$page";
        }
        return preg_replace('/page=([0-9]+)/i', 'page=' . $page, $uri);
    }
?>
<div class="pagination" data-current="<?= $page; ?>" data-total="<?= $pages; ?>" aria-label="pagination">	
<?php if ($page !== 1): ?>        
    <a  class="obflink" data-obf="<?= base64_encode(urlWithPage($page - 1))?>" data-page="<?= $page - 1 ?>"><span class="icon material-symbols-rounded hover">&#xe2ea;</span></a>
<?php endif; ?>
<div class="pages">
    <?php if ($pages <= 5):
        for ($i = 1; $i <= $pages; $i++): ?>
            <div class="page <?= $page == $i ? 'current' : ''; ?>" >
                <a class="obflink" data-obf="<?= base64_encode(urlWithPage($i))?>" data-page="<?= $i; ?>"><?= $i; ?></a>
            </div>
        <?php endfor;
    else: 
        if ($page > ($pages - 5)): // Dernières pages ?>
            <div class="page <?= $page == 1 ? 'current' : ''; ?>">
                <a class="obflink"  data-obf="<?= base64_encode(urlWithPage(1))?>" data-page="<?= 1; ?>">1</a>
            </div>
            <div class="page"><span>...</span></div>		
            <?php for ($i = $pages - 4; $i <= $pages; $i++): ?>
                <div class="page <?= $page == $i ? 'current' : ''; ?>" >
                    <a class="obflink"  data-obf="<?= base64_encode(urlWithPage($i))?>" data-page="<?= $i; ?>"><?= $i; ?></a>
                </div>
            <?php endfor;
        else: // Premières pages
            for ($i = $page; $i < $page + 5; $i++): ?>
                <div class="page <?= $page == $i ? 'current' : ''; ?>" >
                    <a class="obflink"  data-obf="<?= base64_encode(urlWithPage($i))?>" data-page="<?= $i; ?>"><?= $i; ?></a>
                </div>
            <?php endfor; ?>

            <div class="page"><span>...</span></div>

            <div class="page <?= $page == $pages ? 'current' : ''; ?>">
                <a class="obflink"  data-obf="<?= base64_encode(urlWithPage($pages))?>" data-page="<?= $pages; ?>"><?= $pages; ?></a>
            </div>
        <?php endif;
    endif; ?>
</div>
<?php if ($page != $pages): // Ne pas aller supérieur à max pages ?>  
    <a class="obflink" data-obf="<?= base64_encode(urlWithPage($page + 1))?>" data-page="<?= $page + 1 ?>"><span class="icon material-symbols-rounded hover">&#xe5e1;</span></a>
<?php endif; ?>
</div>