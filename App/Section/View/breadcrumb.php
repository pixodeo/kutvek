<?php $current = array_pop($breadcrumbs); ?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a class="obflink" data-obf="<?= base64_encode($this->uri('pages.index'))?>"><span class="material-symbols-rounded">&#xe88a;</span></a></li>
    <?php foreach($breadcrumbs as $b): ?>
		<li class="breadcrumb-item"><a class="obflink" data-ref="<?=$this->uri($b->slug)?>" data-obf="<?= base64_encode($this->uri($b->slug))?>"><?= $b->name;?></a></li>
     <?php endforeach; ?>
    
    <li class="breadcrumb-item active" aria-current="page"><?= $current->name; ?></li>
  </ol>
</nav>