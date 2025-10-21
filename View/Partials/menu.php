<?php 
	$nodes = [];
	$func = function($node) use (&$nodes)
    {   
    	$r = '';   
        for ($i = 0; $i < count($nodes) ; $i++) {
            // ne pas se tromper de parent
            if( $nodes[$i]->node_left <  $node->node_left  && ($node->node_right <  $nodes[$i]->node_right && $node->node_right > $nodes[$i]->node_left)){
                $nodes[$i]->leafs = $nodes[$i]->leafs - 1;
                if($nodes[$i]->leafs == 0 && $nodes[$i]->node_left <  $node->node_left  && ($node->node_right <  $nodes[$i]->node_right && $node->node_right > $nodes[$i]->node_left))  
                {
                    $r .= '</ul></li>';
                }
            }             
        }
        return $r;
    };   
?>
<nav id="main-nav" class="main-nav">
	<?= $this->widgetCountryCurrencySelector(); ?>
	<span class="menu"><a href="#" class="click" data-ctrl="app.menu" data-target="main-nav"><i class="icon fas fa-times"></i></a></span>
	<ul>
		<?php foreach($items as $node): 
			$depth = (int)$node->depth; 
			//$uri =  $this->oldUri('products.section', ['queries' => [ 'slug' => $node->slug]]);
			$uri =  $this->uri('products.section', ['queries' => [ 'slug' => $node->slug]]);
		?>            
			<?php if($node->leafs > 0) : $nodes[] = clone($node); ?>         
			    <li style="order:<?= $node->position; ?>">
					<div>
						<?php if($node->active): ?>
						<a href="<?=  $uri; ?>" class="<?= $depth === 0 ? 'dropdown-item' : ''; ?>" >
							<span class="item-name"><?= $node->name;?></span>
						</a>
					<?php else: ?>						
							<span class="<?= $depth === 0 ? 'item-name dropdown-item' : 'item-name '; ?>" ><?= $node->name;?></span>						
					<?php endif; ?>
						<label for="node-<?= $node->id; ?>" class="depth-<?= $depth; ?>"><span class="material-symbols-rounded">&#xe5cf;</span></label>
					</div>					
		            <?php if($depth === 0): ?>
						<input class="onchange" data-ctrl="app.closeAllSections" type="checkbox" data-depth="<?= $node->depth; ?>" id="node-<?= $node->id; ?>">
						<ul class="dropdown-content">
		            <?php else: ?>
						<input class="onchange" data-ctrl="app.closeAllSections" type="checkbox" data-depth="<?= $node->depth; ?>" id="node-<?= $node->id; ?>">
		                <ul>
		            <?php endif; ?>
	        <?php else: ?>  

	            <li style="order:<?= $node->position; ?>">

	            	<?php if ($node->obfuscated && $node->active) : ?>
			            <span class="item-name obflink obf" data-obf="<?= base64_encode($node->slug) ?>">
			                <?= $node->name; ?>
			            </span>
			        <?php elseif (!$node->active): ?>
			            <span class="item-name"><?= $node->name; ?></span>
			        <?php else : ?>
			            <a href="<?= $uri; ?>"><?= $node->name; ?></a>
			        <?php endif; ?>
	            </li>
	        <?php endif; ?>
			<?= $func($node); ?>            
		<?php endforeach; ?>
	</ul>
</nav>