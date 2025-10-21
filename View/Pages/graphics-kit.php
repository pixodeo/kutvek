<?php $stores =$this->getGraphicKitStores(); ?>

<style>
    .grid-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.6rem;
    }

    .grid-item {
        width: 100%;
        height: auto;
        position: relative;
        display: flex;
        justify-content: start;
        align-items: center;
    }

    

    .grid-item  > span {
        position: absolute;
        font-size: 3.2rem;
        font-weight: 500;
        letter-spacing: .1rem;
        color: white;
        padding-left: 3.2rem;
        font-family: 'Oswald';
        text-transform: uppercase;
    }
    .custom {display:block; margin: 1.6rem 0 3.2rem;}
    @media only screen and (min-width: 1024px) {
    .grid-container {
        
        grid-template-columns: 1fr 1fr 1fr;
       
    }
    }
</style>

<h1 class="page-title"><?= $page->title; ?></h1>

<div class="col-s-12 col-l-9 col-l-center">
    <a href="#" class="custom">
        <img src="<?= $page->cover; ?>" alt="" />
    </a>    
    <div class="row grid-container">
        <?php foreach ($stores as $store): ?>          
            <a class="grid-item" href="<?= $store->slug; ?>">
                <img src="<?= $store->cover ?>" alt="">
                <span class="name"><?= $store->name; ?></span>
            </a>            
        <?php endforeach; ?>
    </div>
</div>