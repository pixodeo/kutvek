<?php $current = array_pop($breadcrumbs); ?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?=$this->uri('pages.index')?>"><span class="material-symbols-rounded">&#xe88a;</span></a></li>
    <?php foreach($breadcrumbs as $b): ?>
		<li class="breadcrumb-item"><a  href="<?=$this->uri($b->slug)?>"><?= $b->name;?></a></li>
     <?php endforeach; ?>
    
    <li class="breadcrumb-item active" aria-current="page"><?= $current->name; ?></li>
  </ol>
</nav>