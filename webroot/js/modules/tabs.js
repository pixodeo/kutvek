const display_tab = function(a,event) {    
    event.preventDefault();
    //console.log(event);
    
    // on recupère la balise a
    let _target = event.target; 
    while (_target.nodeName !== 'A') {
        _target = _target.parentNode;
    }

    var li = _target.parentNode;
    var ul = li.parentNode;
    
    // l'elem parent (li) a déjà la class active
    if(li.classList.contains('active') || li.classList.contains('disabled')) return false;    

    let _current = ul.querySelector('li.active');
    let _current_tab_id = _current.querySelector('a').hash;
    _current.classList.remove('active'); 

    li.classList.add('active');

    let div = ul.nextElementSibling;

    // on retire .active sur le contenu tab_content .active
    // la div active c'est celle qui a en id l'href 
    const _current_tab = div.querySelector(_current_tab_id);    
    
    var tab_content_to_active =  div.querySelector(_target.getAttribute('href'));

    // on ajoute la class active sur le tab_content en rapport avec l'elem cliqué, sélection par son id qui correspond au href du lien _target
    // ! elem.href renvoi le lien absolu, utiliser getAttribute('href');    
    _current_tab.classList.remove('active'); 
    tab_content_to_active.classList.add('active');    
}

export  default display_tab;
