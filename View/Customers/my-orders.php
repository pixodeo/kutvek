<div class="main-row">
<div class="row">
<div class="col-s-12">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=$this->uri('pages.index')?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?=$this->uri('customers.dashboard')?>">Espace Client</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mes commandes</li>
      </ol>
    </nav>
</div>    
<div class="col-s-12 col-l-9 col-l-center">                
    <ul class="tabs">
        <li class="active">
            <a href="#orders">
            <span data-i18n="orders-history"> Historique des commandes</span>&nbsp;
            <?= $this->form->select('year', 
                [
                'id' => 'filter-by-year',    
                'attributes' => [
                    'class="onchange field-input select"',
                    'data-ctrl="order.ordersByYear"',
                    'data-uri="'. str_replace($year, ':year', $url) .'"'
                ],
                'values' => [2025,2024,2023, 2022, 2021, 2020, 2019, 2018],
                'selected' => $year

                ]); ?>
            </a>
        </li>
    </ul>
    <div class="tabs_content">
        <div class="tab_content items active" id="orders" data-uri="<?= $url;?>">
            <ul class="tasks">
            </ul>                      
        </div>                
    </div>                 
</div>
</div>
</div>
<template id="task">
    <li class="task">
        <div>
            <span class="reference"></span>
            <p class="action">
                <a href="#" class="link bill hidden" target="_blank"><span class="icon material-symbols-rounded">&#xef6e;</span><span>Téléchargez la facture</span></a>
                <label for="" class="action dropdown pointer"><span class="material-symbols-rounded"></span></label>
            </p>            
        </div>
        <p><span class="designation"></span></p><input type="checkbox" name="dropdown" class="action dropdown"/>
        <div class="dropdown-content">           
            <ul class="tabs vertical">
                <li class="active"><a href="#info"><span class="icon material-symbols-rounded">&#xe88e;</span>Infos</a></li>
                <li><a href="#posts"><span class="icon material-symbols-rounded">&#xe0bf;</span><span data-i18n="our-chat">Notre discussion</span></a></li>
                <li><a href="#mockups" class="click"><span class="icon material-symbols-rounded">&#xe413;</span><span data-i18n="latest-mockups">Maquettes</span></a></li>
            </ul>
            <div class="tabs_content vertical">
                <div class="tab_content active" id="info"></div>
                <div class="tab_content" id="posts">
                    <div class="posts"></div>                   
                </div>
                <div class="tab_content" id="mockups">
                   
                    <a href="" class="mockup" target="_blank"></a>
                   
                </div>
            </div>
        </div>
    </li>    
</template>

<template id="post-tpl">
<div class="post" id="post">
    <p><span class="user"></span><span class="created">le 2023-01-12 13:55:37</span></p>
    <div class="body">
    </div>
</div>      
</template>

<template id="file-tpl">
    <div class="post file" id="file">
    <p><span class="user"></span><span class="created"></span></p>
    <div class="body">
        <p class="title"></p>
        <p class="size"></p>
        <p><span class="icon material-symbols-rounded"></span></p>
    </div>
    </div>
</template>
<template id="mockup-tpl">
    <img src="/img/blank.png" class="mockup" data-file="" alt="" />
</template>
<template id="img-tpl">
    <div class="post img" id="file">
    <p><span class="user"></span><span class="created"></span></p>
    <div class="body"></div>
    </div>
</template>