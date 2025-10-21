<footer class="main-footer">
	<div class="grid-footer">
		<?php foreach ($items  as $item):?>
			<?php if($item->id === 12): ?>
			<div class="item-footer" style="display: inline-flex;grid-area: 1 / 2 / 1 / 4;justify-content: center; flex-direction: column;align-items: center;">
				<p><?=$item->designation;?></p>
				<?= $this->specialchars_decode($item->body);?>	
			</div>
			<?php else: ?>
				<div class="item-footer" style="grid-area:<?=$item->area;?>">
				<p><?=$item->designation;?></p>
				<?= $this->specialchars_decode($item->body);?>	
			</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>	
	<img class="logo-footer" src="https://www.kutvek-kitgraphik.com/img/charter/logo-footer.png" alt="">
</footer>